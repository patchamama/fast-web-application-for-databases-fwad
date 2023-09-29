<?php
// File           domains.inc.php / ibWebAdmin
// Purpose        functions working with domains, included from accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <03/01/28 20:46:12 lb>
//
// $Id: domains.inc.php,v 1.8 2004/04/22 19:05:38 lbrueckner Exp $


//
// get the properties of all defined domains
//
function get_domain_definitions(&$olddomains){
    global $dbhandle;
    global $s_charsets;
    
    $sql  = 'SELECT  F.RDB$FIELD_NAME AS DNAME,'
                  .' F.RDB$FIELD_TYPE AS FTYPE,'
                  .' F.RDB$FIELD_SUB_TYPE AS STYPE,'
                  .' F.RDB$FIELD_LENGTH AS FLEN,'
                  .' F.RDB$FIELD_PRECISION AS FPREC,'
                  .' F.RDB$FIELD_SCALE AS FSCALE,'
                  .' F.RDB$SEGMENT_LENGTH AS SEGLEN,'
                  .' F.RDB$CHARACTER_SET_ID AS CHARID,'
                  .' F.RDB$COLLATION_ID AS COLLID,'
                  .' F.RDB$NULL_FLAG AS NFLAG,'
                  .' F.RDB$DEFAULT_SOURCE AS DSOURCE,'
                  .' F.RDB$VALIDATION_SOURCE AS VSOURCE'
            .' FROM  RDB$FIELDS F '
           .' WHERE  (RDB$SYSTEM_FLAG=0 OR RDB$SYSTEM_FLAG IS NULL)'
             ." AND  RDB\$FIELD_NAME NOT STARTING WITH 'RDB\$'"
           .' ORDER  BY F.RDB$FIELD_NAME';
    $res = ibase_query($dbhandle, $sql) or db_error(__FILE__, __LINE__, $sql);

    $domains = array();
    while ($obj = ibase_fetch_object($res)) {
        $dname = trim($obj->DNAME);
        $stype = (isset($obj->STYPE)) ? $obj->STYPE : 0;
        $domains[$dname]['type'] = get_datatype($obj->FTYPE, $stype);

        if ($stype != 0) {
            $domains[$dname]['stype'] = $stype;
        }

        if ($domains[$dname]['type'] == 'VARCHAR' || $domains[$dname]['type'] == 'CHARACTER') {
	    $domains[$dname]['size'] = $obj->FLEN;
        }

        if (isset($obj->CHARID)) {
            $domains[$dname]['charset'] = $s_charsets[$obj->CHARID]['name'];
        }
  
        $domains[$dname]['collate'] = (isset($obj->COLLID)  &&  $obj->COLLID != 0) 
            ? $s_charsets[$obj->CHARID]['collations'][$obj->COLLID] 
            : NULL;

        if ($domains[$dname]['type'] == 'DECIMAL' || $domains[$dname]['type'] == 'NUMERIC') {
            $domains[$dname]['prec']   = $obj->FPREC;
            $domains[$dname]['scale']  = -($obj->FSCALE);
        }

        if ($domains[$dname]['type'] == 'BLOB') {
            $domains[$dname]['segsize'] = $obj->SEGLEN;
        }

        $domains[$dname]['notnull'] = (isset($obj->NFLAG)  &&  !empty($obj->NFLAG)) ? TRUE : FALSE;

        $domains[$dname]['default'] = (isset($obj->DSOURCE)  &&  !empty($obj->DSOURCE))
            ? get_domain_default($dname)
            : '';
            
        $domains[$dname]['check'] = (isset($obj->VSOURCE)  &&  !empty($obj->VSOURCE))
            ? get_domain_check($dname)
            : '';
        
        $domains[$dname]['status'] = (isset($olddomains[$dname])) ? $olddomains[$dname]['status'] : 'close';
    }
    ibase_free_result($res);

    return $domains;
}

function get_domain_default($dname) {

    return substr(get_blob_content('SELECT RDB$DEFAULT_SOURCE'
                                   .' FROM RDB$FIELDS'
                                   ." WHERE RDB\$FIELD_NAME='".$dname."'"), 8);
}

function get_domain_check($dname) {

    return substr(get_blob_content('SELECT RDB$VALIDATION_SOURCE'
                                   .' FROM RDB$FIELDS'
                                   ." WHERE RDB\$FIELD_NAME='".$dname."'"), 6);
}


//
// create a domain from the values in $domdefs
// 
function create_domain($domdefs) {
    global $dbhandle, $lsql, $db_error;

    $check_str =  '';
    if (!empty($domdefs['check'])) {
        $check_str = stristr($domdefs['check'], 'VALUE') === FALSE  &&  stristr($domdefs['check'], 'NOT') === FALSE
            ? ' CHECK (VALUE ' . $domdefs['check'] . ')'
            : ' CHECK (' . $domdefs['check'] . ')';
    }

    $lsql = 'CREATE DOMAIN ' . $domdefs['name'] . ' AS ' . build_datatype($domdefs, 'domain')
          . (!empty($domdefs['default']) ? ' DEFAULT ' . $domdefs['default'] : '')
          . ($domdefs['notnull'] == 'yes' ? ' NOT NULL' : '')
          . $check_str
          . (!empty($domdefs['collate']) ? ' COLLATE ' . $domdefs['collate'] : '');    
    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
    if (!@ibase_query($dbhandle, $lsql)) {
        $db_error = $errorstring;
        return FALSE;
    }
    
    return TRUE;
}


//
// drop the domain $name off the database
//
function drop_domain($name) {
    global $s_domains, $dbhandle, $db_error;

    $lsql = 'DROP DOMAIN '.$name;
    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
    if (!@ibase_query($dbhandle, $lsql)) {
        $db_error = $errorstring;
    }
    else {
        unset($s_domains[$name]);
    }
}


//
// execute sql to modify a domain
//
function modify_domain($olddef, $domdef) {
    global $dbhandle, $db_error;

    $lsql = array();

    if ($domdef['name'] != $olddef['name']) {
        $lsql[] = 'ALTER DOMAIN ' . $olddef['name'] . ' TO ' . $domdef['name'];
    }

    if (datatype_is_modified($olddef, $domdef)) {
        $lsql[] = 'ALTER DOMAIN ' . $domdef['name'] . ' TYPE ' . build_datatype($domdef);
    }

    if (isset($olddef['default'])  &&  empty($domdef['default'])) {
        $lsql[] = 'ALTER DOMAIN ' . $domdef['name'] . ' DROP DEFAULT';
    }

    if (isset($olddef['default'])  
    &&  !empty($domdef['default'])  &&  $olddef['default'] != $domdef['default']) {
        $lsql[] = 'ALTER DOMAIN ' . $domdef['name'] . ' SET DEFAULT ' . $domdef['default'];
    }

    if ((isset($olddef['check'])  && !empty($olddef['check']))
    &&  (empty($domdef['check'])  ||  $olddef['check'] != $domdef['check'])) {
        $lsql[] = 'ALTER DOMAIN ' . $domdef['name'] . ' DROP CONSTRAINT';
    }

    if (isset($olddef['check'])  &&  $olddef['check'] != $domdef['check']) {
        $lsql[] = 'ALTER DOMAIN ' . $domdef['name'] . ' ADD CHECK ' . $domdef['check'];
    }

    foreach ($lsql as $sql) {
        if (!@ibase_query($dbhandle, $sql)) {
            $db_error = $errorstring . "<br>\n>";

            return FALSE;
        }
    }

    return TRUE;
}


//
// return the html displaying the domain details in a table
//
function &get_domain_table(&$domains) {
    global $HTTP_SERVER_VARS, $acc_strings, $tb_strings;

    $html = "<table cellpadding=\"0\" cellspacing=\"0\" border>\n"
           ."  <tr align=\"left\">\n"
           .'    <th class="detail">'.$acc_strings['Name']."</th>\n"
           .'    <th class="detail">'.$acc_strings['Type']."</a></th>\n"
           .'    <th class="detail">'.$acc_strings['Charset']."</th>\n"
           .'    <th class="detail">'.$acc_strings['Collate']."</th>\n"
           .'    <th class="detail">'.$acc_strings['Subtype']."</th>\n"
           .'    <th class="detail">'.$acc_strings['SegSiShort']."</th>\n"
           .'    <th class="detail">'.$tb_strings['NotNull']."</th>\n"
           .'    <th class="detail">'.$tb_strings['Default']."</th>\n"
           .'    <th class="detail">'.$tb_strings['Check']."</th>\n"
           ."  </tr>\n";

    foreach($domains as $dname => $domain) {
        $type_str = get_type_string($domain);
        $char_str  = (isset($domain['charset'])  &&  !empty($domain['charset']))  ? $domain['charset']  : '&nbsp;';
        $coll_str  = (isset($domain['collate'])  &&  !empty($domain['collate']))  ? $domain['collate']  : '&nbsp;';
        $stype_str = (isset($domain['stype'])  &&  !empty($domain['stype']))    ? $domain['stype']    : '&nbsp;';
        $segs_str  = (isset($domain['segsize'])  &&  !empty($domain['segsize']))  ? $domain['segsize']  : '&nbsp;';
        $null_str  = ($domain['notnull'] === TRUE) ? $acc_strings['Yes'] : '&nbsp;';
        $def_str   = (!empty($domain['default'])) ? $domain['default']  : '&nbsp;';
        $chk_str   = (!empty($domain['check']))   ? $domain['check']    : '&nbsp;';

        $html .= "  <tr>\n"
                .'    <td class="detail">'.$dname."</td>\n"
                .'    <td class="detail">'.$type_str."</td>\n"
                .'    <td class="detail">'.$char_str."</td>\n"
                .'    <td class="detail">'.$coll_str."</td>\n"
                .'    <td class="detail" align="right">'.$stype_str."</td>\n"
                .'    <td class="detail" align="right">'.$segs_str."</td>\n"
                .'    <td class="detail" align="center">'.$null_str."</td>\n"
                .'    <td class="detail">'.$def_str."</td>\n"
                .'    <td class="detail">'.$chk_str."</td>\n"
                ."  </tr>\n";
    }

    $html .= "</table>\n";

    return $html;
}


//
// return the html for the constraint elements of a domain definition form
//
function get_domain_constraint($domdefs, $notnull=TRUE) {
    global $tb_strings;

    $nn_checkbox = $notnull == TRUE
        ?  '      <b>'.$tb_strings['NotNull']."</b><br>\n"
          .'      <input type="checkbox" name="cd_def_notnull" value ="yes"'.($domdefs['notnull'] == 'yes' ? ' checked' : '').'>'."\n"
        :  '      &nbsp;';

    return "  <tr>\n"
          ."    <td colspan=\"3\">\n"
          .'      <b>'.$tb_strings['Default']."</b><br>\n"
          .'      <input type="text" size="45" maxlength="256" name="cd_def_default" value ="'.$domdefs['default']."\">\n"
          ."    </td>\n"
          ."    <td colspan=\"5\">\n"
          .'      <b>'.$tb_strings['Check']."</b><br>\n"
          .'      <input type="text" size="55" maxlength="256" name="cd_def_check" value ="'.$domdefs['check']."\">\n"
          ."    </td>\n"
          ."    <td align=\"center\">\n"
          . $nn_checkbox
          ."    </td>\n"
          ."  </tr>\n";
}

?>
