<?php
// File           get_tables.inc.php / ibWebAdmin
// Purpose        function that gets the table properties for all tables in the database
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/06 08:34:43 lb>
//
// $Id: get_tables.inc.php,v 1.26.2.1 2004/11/28 14:50:03 lbrueckner Exp $


//
// set the session variables $s_tables[], $s_fields[]
// for the database $dbhandle
//
function get_tables() {
    global $dbhandle, $db_error, $s_tables, $s_fields, $s_foreigns, $s_primaries, $s_uniques, $s_login;
    global $s_charsets, $s_tables_counts, $s_views_counts;

    $previous = $s_tables;
    $s_tables = array();
    $s_fields = array();

    // returns the tablenames, owner and the privileges for the current user
    $sql  = 'SELECT R.RDB$RELATION_NAME AS RNAME,'
                  .' R.RDB$VIEW_BLR AS VBLR,'
                  .' R.RDB$OWNER_NAME AS OWNER,'
                  .' P.RDB$PRIVILEGE AS PRIV'
            .' FROM RDB$RELATIONS R'
       .' LEFT JOIN RDB$USER_PRIVILEGES P'
              .' ON R.RDB$RELATION_NAME=P.RDB$RELATION_NAME'
             ." AND (P.RDB\$USER='".$s_login['user']."' OR P.RDB\$USER='PUBLIC')"
            .'WHERE R.RDB$SYSTEM_FLAG=0'
        .' ORDER BY R.RDB$RELATION_NAME';
    $res = @ibase_query($dbhandle, $sql) or db_error(__FILE__, __LINE__, $sql);
    if (!is_resource($res)) {
       return FALSE;
    }

    // initialize $s_tables[]
    $lastone = '';
    while ($row = ibase_fetch_object($res)) {

        $tablename = trim($row->RNAME);

        if (isset($row->PRIV)  &&  trim($row->PRIV) != '') {
            $s_tables[$tablename]['privileges'][] =  trim($row->PRIV);
        }
        else {
            $s_tables[$tablename]['privileges'] = array();
        }

        // collect all privileges above, but do the rest only once
        if ($tablename == $lastone) {
            continue;
        }

        $s_tables[$tablename]['status'] = (isset($previous[$tablename])) ? $previous[$tablename]['status'] : 'close';
        $s_tables[$tablename]['is_view'] = (isset($row->VBLR)  &&  $row->VBLR !== NULL) ? TRUE : FALSE;
        $s_tables[$tablename]['owner'] = trim($row->OWNER);
        $lastone = $tablename;
    }
    ibase_free_result($res);
    unset($previous);

    // find the check, not null, unique, pk and fk and  constraints
    $sql ='SELECT RC.RDB$RELATION_NAME TNAME,'
               .' RC.RDB$CONSTRAINT_TYPE RTYPE,'
               .' RC.RDB$CONSTRAINT_NAME CNAME,'
               .' RC.RDB$INDEX_NAME INAME,'
               .' CC.RDB$TRIGGER_NAME TRIGNAME,'
               .' SE.RDB$FIELD_NAME SENAME,'
               .' SE.RDB$FIELD_POSITION POS,'
               .' DP.RDB$FIELD_NAME DPNAME'
          .' FROM RDB$RELATION_CONSTRAINTS RC'
     .' LEFT JOIN RDB$CHECK_CONSTRAINTS CC'
            .' ON RC.RDB$CONSTRAINT_NAME=CC.RDB$CONSTRAINT_NAME'
           ." AND RC.RDB\$CONSTRAINT_TYPE='CHECK'"
     .' LEFT JOIN RDB$INDEX_SEGMENTS SE'
            .' ON RC.RDB$INDEX_NAME=SE.RDB$INDEX_NAME'
     .' LEFT JOIN RDB$DEPENDENCIES DP'
            .' ON CC.RDB$TRIGGER_NAME=DP.RDB$DEPENDENT_NAME'
         .' ORDER BY RC.RDB$RELATION_NAME';
    $res = @ibase_query($dbhandle, $sql) or db_error(__FILE__, __LINE__, $sql);

    // reset the index infos
    $s_foreigns  = array();
    $s_primaries = array();
    $s_uniques   = array();

    $constraints = array();
    while ($row = ibase_fetch_object($res)) {
        $cname = trim($row->CNAME);
        switch (trim($row->RTYPE)) {
            case 'CHECK':
                $constraints[trim($row->TNAME)][trim($row->DPNAME)]['check'] = $cname;
                break;
            case 'UNIQUE':
                $constraints[trim($row->TNAME)][trim($row->SENAME)]['unique'] = $cname;
                $s_uniques[$cname]['index'] = trim($row->INAME);
                $s_uniques[$cname]['cols']  = isset($s_uniques[$cname]['cols']) ? $s_uniques[$cname]['cols']++ : 1;
                break;
            case 'FOREIGN KEY':
                $constraints[trim($row->TNAME)][trim($row->SENAME)]['foreign'] = $cname;
                $s_foreigns[$cname]['index'] = trim($row->INAME);
                $s_foreigns[$cname]['cols']  = isset($s_foreigns[$cname]['cols']) ? $s_foreigns[$cname]['cols']++ : 1;
                break;
            case 'PRIMARY KEY':
                $constraints[trim($row->TNAME)][trim($row->SENAME)]['primary'] = $cname;
                $s_primaries[$cname]['index'] = trim($row->INAME);
                $s_primaries[$cname]['cols']  = isset($s_primaries[$cname]['cols']) ? $s_primaries[$cname]['cols']++ : 1;
                break;
        }
    }
    ibase_free_result($res);
    
//     debug_var($sql);
//     debug_var($constraints);
//     debug_var($s_foreigns);
//     debug_var($s_primaries);

    // find the field properties for all non-system tables
    $sql  = 'SELECT DISTINCT R.RDB$FIELD_NAME AS FNAME,'
                 .' R.RDB$NULL_FLAG AS NFLAG,'
                 .' R.RDB$DEFAULT_SOURCE AS DSOURCE,'
                 .' R.RDB$FIELD_POSITION,'
                 .' R.RDB$RELATION_NAME AS TNAME,'
                 .' R.RDB$COLLATION_ID AS COLLID,'
                 .' F.RDB$FIELD_NAME AS DNAME,'
                 .' F.RDB$FIELD_TYPE AS FTYPE,'
                 .' F.RDB$FIELD_SUB_TYPE AS STYPE,'
                 .' F.RDB$FIELD_LENGTH AS FLEN,'
                 .' F.RDB$COMPUTED_SOURCE AS CSOURCE,'
                 .' F.RDB$FIELD_PRECISION AS FPREC,'
                 .' F.RDB$FIELD_SCALE AS FSCALE,'
                 .' F.RDB$SEGMENT_LENGTH AS SEGLEN,'
                 .' F.RDB$CHARACTER_SET_ID AS CHARID,'
                 .' D.RDB$LOWER_BOUND AS LBOUND,'
                 .' D.RDB$UPPER_BOUND AS UBOUND'
            .' FROM RDB$RELATION_FIELDS R '
            .' JOIN RDB$FIELDS F ON R.RDB$FIELD_SOURCE=F.RDB$FIELD_NAME'
       .' LEFT JOIN RDB$FIELD_DIMENSIONS D ON R.RDB$FIELD_SOURCE=D.RDB$FIELD_NAME'
           .' WHERE F.RDB$SYSTEM_FLAG=0'
       . ' ORDER BY R.RDB$FIELD_POSITION';
    $res = @ibase_query($dbhandle, $sql) or db_error(__FILE__, __LINE__, $sql);

    //initialize $s_fields[]
    $i = 0;
    while ($row = ibase_fetch_object($res)) {
        $field = $s_fields[$i]['name']  = trim($row->FNAME);
        $table = $s_fields[$i]['table'] = trim($row->TNAME);
        if (strpos($row->DNAME, 'RDB$') !== 0){
            $s_fields[$i]['domain'] = 'Yes';
            $s_fields[$i]['type'] = trim($row->DNAME);
        } else {
            $s_fields[$i]['stype'] = (isset($row->STYPE)) ? $row->STYPE : NULL; 
            $s_fields[$i]['type']  = get_datatype($row->FTYPE, $s_fields[$i]['stype']);
        }
	if ($s_fields[$i]['type'] == 'VARCHAR' || $s_fields[$i]['type'] == 'CHARACTER') {
	    $s_fields[$i]['size']    = $row->FLEN;
	}

        // field is defined as NOT NULL
        if (!empty($row->NFLAG)) {
            $s_fields[$i]['notnull'] = 'YES';
        }

        // this field is computed
	if (isset($row->CSOURCE)) {
            $s_fields[$i]['comp']   = 'Yes';
            $s_fields[$i]['csource'] = FALSE;
        }

        // this field has a default value
	if (isset($row->DSOURCE)) {
            $s_fields[$i]['default']= 'Yes';
            $s_fields[$i]['dsource'] = FALSE;
        }

    	if (($s_fields[$i]['type'] == 'DECIMAL')  or  ($s_fields[$i]['type'] == 'NUMERIC')) {
	    $s_fields[$i]['prec']   = $row->FPREC;
	    $s_fields[$i]['scale']  = -($row->FSCALE);
	}

	if ($s_fields[$i]['type'] == 'BLOB') {
            $s_fields[$i]['segsize'] = $row->SEGLEN;
        }

	$s_fields[$i]['charset'] = isset($row->CHARID) ? $s_charsets[$row->CHARID]['name'] : NULL;
        $s_fields[$i]['collate'] = (isset($row->COLLID)  &&  $row->COLLID != 0  &&  isset($s_charsets[$row->CHARID]['collations'][$row->COLLID]))
                                 ? $s_charsets[$row->CHARID]['collations'][$row->COLLID] 
                                 : NULL;

        // optional array dimensions
        if (isset($row->LBOUND)) {
            $s_fields[$i]['lower_bound'] = $row->LBOUND;
            $s_fields[$i]['upper_bound'] = $row->UBOUND;
        }

        // column constraints
        foreach (array('check', 'unique', 'foreign', 'primary') as $ctype) {
            if (isset($constraints[$table][$field][$ctype])) {
                $s_fields[$i][$ctype] = $constraints[$table][$field][$ctype];
            }
        }

	$i++;
    }
//     debug_var($s_fields);

    foreach ($s_tables as $name => $properties) {
        if (($properties['is_view'] == FALSE  &&  $s_tables_counts == 'yes')
        ||  ($properties['is_view'] == TRUE   &&  $s_views_counts  == 'yes')) { 

            $sql = 'SELECT COUNT(*) AS CNT FROM '.$name;
            $res = ibase_query($dbhandle, $sql)
                or $db_error .= $errorstring."<br>\n";
            if (is_resource($res)) {
                $row = ibase_fetch_object($res);
                $s_tables[$name]['count'] = $row->CNT;
                ibase_free_result($res);
            }
        }
    }

    return TRUE;
}

?>
