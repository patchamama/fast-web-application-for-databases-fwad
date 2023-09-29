<?php
// File           udfs.inc.php / ibWebAdmin
// Purpose        functions working with user defined functions, included from accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <03/12/21 18:27:30 lb>
//
// $Id: udfs.inc.php,v 1.3 2004/07/28 09:11:31 lbrueckner Exp $


//
// return an array with the properties of the user defined functions
//
function get_udfs($order=1, $dir='ASC') {
    global $dbhandle;

    $sql = 'SELECT F.RDB$FUNCTION_NAME AS FNAME,'
                .' F.RDB$MODULE_NAME AS MODULE,'
                .' F.RDB$ENTRYPOINT AS EPOINT,'
                .' F.RDB$RETURN_ARGUMENT AS RPOS,'
                .' A.RDB$ARGUMENT_POSITION AS APOS,'
                .' A.RDB$FIELD_TYPE AS FTYPE,'
                .' A.RDB$FIELD_SUB_TYPE AS STYPE,'
                .' A.RDB$FIELD_SCALE AS SCALE,'
                .' A.RDB$FIELD_LENGTH AS FLENGTH,'
                .' A.RDB$FIELD_PRECISION AS PREC'
           .' FROM RDB$FUNCTIONS F'
     .' INNER JOIN RDB$FUNCTION_ARGUMENTS A'
             .' ON F.RDB$FUNCTION_NAME=A.RDB$FUNCTION_NAME'
          .' ORDER BY '.$order.' '.$dir;
    $res = ibase_query($dbhandle, $sql) or db_error($sql);

    $udfs = array();
    while ($obj = ibase_fetch_object($res)) {
        $fname = trim($obj->FNAME);
        $udfs[$fname]['module'] = trim($obj->MODULE);
        $udfs[$fname]['entrypoint'] = trim($obj->EPOINT);
        if ($obj->APOS == $obj->RPOS) {
            $udfs[$fname]['returns'] = get_datatype($obj->FTYPE, $obj->STYPE) . get_datatye_size_string($obj->FTYPE, $obj->FLENGTH, $obj->PREC, $obj->SCALE);
        }
        else {
            $udfs[$fname]['params'][$obj->APOS] = get_datatype($obj->FTYPE, $obj->STYPE) . get_datatye_size_string($obj->FTYPE, $obj->FLENGTH, $obj->PREC, $obj->SCALE);
        }
    }

    return $udfs;
}


//
// return the size string for an udf parameter
//
function get_datatye_size_string($type, $length, $prec, $scale) {

    $str = '';
    switch ($type) {
    case 16:
        $str = '('.$prec.','.abs($scale).')';
        break;
    case 37:
    case 14:
        $str = '('.$length.')';
        break;
    }

    return $str;
}


//
// return the html displaying the user defined functions in a table
//
function get_udf_table($udfs, $order, $dir) {
    global $HTTP_SERVER_VARS, $acc_strings;

    $heads = array('Name', 'Module', 'EPoint', 'IParams', 'Returns');

    $html = "<table cellpadding=\"0\" cellspacing=\"0\" border>\n"
           ."  <tr align=\"left\">\n";

    foreach ($heads as $idx => $head) {
        if ($idx > 2) {
            $html .= '    <th class="detail">'.$acc_strings[$head]."</th>\n";
            continue;
        }
        $url  = url_session($HTTP_SERVER_VARS['PHP_SELF'].'?udforder=1&order='.($idx +1));
        $title = $acc_strings[$head];
        if ($order == $idx +1) {
            $title = $dir == 'ASC' ? '*&nbsp;'.$title : $title.'&nbsp;*';
        }

        $html .= '    <th class="detail"><a href="'.$url.'">'.$title."</a></th>\n";
    }

    $html .= "  </tr>\n";

    foreach ($udfs as $uname => $udf) {
        $parameters = isset($udf['params']) ? implode(', ', $udf['params']) : '';
        $html .= "  <tr>\n"
                .'    <td class="detail">'.$uname."</td>\n"
                .'    <td class="detail">'.$udf['module']."</td>\n"
                .'    <td class="detail">'.$udf['entrypoint']."</td>\n"
                .'    <td class="detail">' . (!empty($parameters) ? $parameters : '&nbsp;') . "</td>\n"
                .'    <td class="detail">'.$udf['returns']."</td>\n"
                ."  </tr>\n";
    }

    $html .= "</table>\n";

    return $html;
}


//
// return the html for a udf selectlist
//
function get_udf_select($name, $sel=NULL, $empty=TRUE, $tags=array()) {
    global $s_udfs;

    $unames = array_keys($s_udfs);
    sort($unames);
    $unames['-=ALL_DEFINED_UDFS=-'] = '-=ALL_DEFINED_UDFS=-';

    return get_selectlist($name, $unames, $sel, $empty, $tags);
}


//
// drop the user defined function $name off the database
//
function drop_udf($name) {
    global $s_udfs, $dbhandle;
    global $db_error, $lsql;

    $lsql = 'DROP EXTERNAL FUNCTION '.$name;
    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
    if (!@ibase_query($dbhandle, $lsql)) {
        $db_error = $errorstring;
        return FALSE;
    }
    else {
         unset($s_udfs[$name]);
        return TRUE;
    }
}


//
// drop all user defined functions
//
function drop_all_udfs($udfs) {

    foreach (array_keys($udfs) as $udf_name) {
        drop_udf($udf_name);
    }
}
    
?>
