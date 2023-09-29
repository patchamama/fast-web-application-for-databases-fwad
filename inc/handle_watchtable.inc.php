<?php
// File           inc/handle_watchtable.inc.php / ibWebAdmin
// Purpose        provides the watch table handling for sql.php and data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <01/02/13 22:31:31 lb>
//
// $Id: handle_watchtable.inc.php,v 1.45.2.2 2004/11/17 20:53:51 lbrueckner Exp $


// initialize $s_tables[] and $s_fields[] if necessary
$idx = get_panel_index($s_sql_panels, 'tb_watch');
if ($s_sql_panels[$idx][2] == 'open'  &&  $db_connected == TRUE  &&  $s_tables_valid == FALSE) {

    include('./inc/get_tables.inc.php');
    if (get_tables()){
        $s_tables_valid = TRUE;
    }
}


// define the wathtable to use in this scripts routines and functions
if ($db_connected == TRUE) {
    if (WATCHTABLE_METHOD == WT_BEST_GUESS) {
        define('WT_METHOD', guess_watchtable_method(SERVER_FAMILY, SERVER_VERSION));
    }
    else {
        define('WT_METHOD', WATCHTABLE_METHOD);
    }
}


set_watch_table_title($s_watch_table);


//
// the Config link on the Watch Table panel was clicked
//
if (isset($HTTP_GET_VARS['wcfg'])) {
    $tb_watch_cfg_flag = TRUE;
}


//
// the Select button on the Watch Table panel was clicked
//
if (isset($HTTP_POST_VARS['tb_watch_select'])
&&  $HTTP_POST_VARS['tb_watch_table'] != ''
&&  $HTTP_POST_VARS['tb_watch_table'] != $s_watch_table) {
    $s_watch_table = $HTTP_POST_VARS['tb_watch_table'];
    set_watch_table_title($s_watch_table);
    $s_watch_columns = set_watch_all();
    $s_watch_blinks  = set_watch_blinks();
    $s_watch_blobas  = set_watch_blobas();
    $s_watch_start = 1;
    $s_watch_sort = '';
    $s_watch_direction = 'ASC';
    $s_watch_del  = ($s_login['user'] == 'SYSDBA'  ||  in_array('R', $s_tables[$s_watch_table]['privileges'])) ? 'YES' : 'NO';
    $s_watch_edit = ($s_login['user'] == 'SYSDBA'  ||  in_array('U', $s_tables[$s_watch_table]['privileges'])) ? 'YES' : 'NO';
    $s_watch_condition = '';
    $wt_changed = TRUE;
}

//
// the Done button on Config Watch Table panel was clicked
//
if (isset($HTTP_POST_VARS['tb_watch_cfg_doit'])) {
    if (isset($HTTP_POST_VARS['columns'])  &&  count($HTTP_POST_VARS['columns']) > 0) {
        $s_watch_columns = $HTTP_POST_VARS['columns'];
    }
    else {
        $s_watch_columns = set_watch_all();
    }

    if (isset($HTTP_POST_VARS['bloblinks'])) {
        $s_watch_blinks = $HTTP_POST_VARS['bloblinks'];
    }
    else {
        $s_watch_blinks = array();
    }

    if (isset($HTTP_POST_VARS['blobas'])) {
        $s_watch_blobas = $HTTP_POST_VARS['blobas'];
    }
    else {
        $s_watch_blobas = array();
    }

    if ((int) $HTTP_POST_VARS['tb_watch_rows'] != 0) {
        $s_watch_rows = abs($HTTP_POST_VARS['tb_watch_rows']);
    }
    if ((int) $HTTP_POST_VARS['tb_watch_start'] != 0) {
        $s_watch_start = abs($HTTP_POST_VARS['tb_watch_start']);
    }
    if (!empty($HTTP_POST_VARS['radiobox'])) {
        $s_watch_sort = $HTTP_POST_VARS['radiobox'];
    }
    else {
        $s_watch_sort = '';
    }
    $s_watch_direction = ($HTTP_POST_VARS['tb_watch_direction'] == $sql_strings['Asc']) ? 'ASC' : 'DESC';
    $s_watch_del = strtoupper($HTTP_POST_VARS['tb_watch_del']);
    $s_watch_edit = strtoupper($HTTP_POST_VARS['tb_watch_edit']);

    $s_watch_tblob_inline = strtoupper($HTTP_POST_VARS['tb_watch_tblob_inline']);
    $s_watch_tblob_chars = (int)$HTTP_POST_VARS['tb_watch_tblob_chars'];

    if (isset($HTTP_POST_VARS['tb_watch_condition'])) {
        $s_watch_condition = get_magic_quotes_gpc() ? stripslashes($HTTP_POST_VARS['tb_watch_condition']) : $HTTP_POST_VARS['tb_watch_condition'];
    }
    set_watch_table_title($s_watch_table);
    $wt_changed = TRUE;
}


if (isset($wt_changed)  &&  $db_connected == TRUE) {

    // editing/deleting from views is not supported now
    if ($s_tables[$s_watch_table]['is_view']) {
        $s_watch_edit = 'NO';
        $s_watch_del = 'NO';
        $message = $MESSAGES['NO_VIEW_SUPPORT'];
    }

    // disable the 'del' and 'edit' links if the user have no remove/update permissions
    // for the selected table
    if ($s_watch_del == 'YES'  &&  $s_login['user'] != 'SYSDBA'  &&  !in_array('R', $s_tables[$s_watch_table]['privileges'])) {
        $warning = sprintf($WARNINGS['DEL_NO_PERMISSON'], $s_watch_table);
        $s_watch_del = 'NO';
    }
    if ($s_watch_edit == 'YES'  &&  $s_login['user'] != 'SYSDBA'  &&  !in_array('U', $s_tables[$s_watch_table]['privileges'])) {
        $warning .= sprintf($WARNINGS['EDIT_NO_PERMISSON'], $s_watch_table);
        $s_watch_edit = 'NO';
    }

    if ($warning == '') {
        // for editing or deleting the table must have a primary key
        $have_primary = FALSE;
        if ($s_watch_edit == 'YES'  ||  $s_watch_del == 'YES') {
            foreach ($s_fields as $field) {
                if ($field['table'] == $s_watch_table
                &&  (isset($field['primary']) &&  !empty($field['primary']))) {
                    $have_primary = TRUE;
                    break;
                }
            }
        }

        // avoid editing of tables without a primary key
        if (!$have_primary  &&  $s_watch_edit == 'YES') {
            $s_watch_edit = 'NO';
            $warning .= $WARNINGS['CAN_NOT_EDIT_TABLE'];
        }

        // avoid deleting of tables without a primary key
        if (!$have_primary  &&  $s_watch_del == 'YES') {
            $s_watch_del = 'NO';
            $warning .= $WARNINGS['CAN_NOT_DEL_TABLE'];
        }
    }

    // for editing make sure that $s_watch_columns[] contains the primary key fields
    if ($s_watch_edit == 'YES') {
        $add_primary = FALSE;
        foreach ($s_fields as $field) {
            if ($field['table'] == $s_watch_table
            &&  (isset($field['primary']) &&  $field['primary'] == 'Yes')
            &&  (!in_array($field['name'], $s_watch_columns))) {
                $s_watch_columns[] = $field['name'];
                $add_primary = TRUE;
            }
        }
        if ($add_primary) {
            $message .= $MESSAGES['EDIT_ADD_PRIMARY'];
        }               
    }

    // get foreign key definititions
    if ($s_use_jsrs == TRUE) {
        $s_watch_fks = get_foreignkeys($s_watch_table);
    }
    

    // cleanup the watchtable output buffer
    $s_watch_buffer = '';

    if (WT_METHOD == WT_STORED_PROCEDURE) {

        include ('./inc/stored_procedures.inc.php');

        if (sp_limit_create($s_watch_table,
                            $s_watch_columns,
                            $s_watch_sort,
                            $s_watch_direction,
                            $s_watch_condition,
                            $s_watch_start,
                            $s_watch_rows)) {

            ibase_close($dbhandle);
            $dbhandle = db_connect()
                or db_error();

            $s_procedures_valid = FALSE;

            // for a reason I don't know the stored procedure
            // is not available before a reload
            globalize_session_vars();
            $script = (!empty($HTTP_SERVER_VARS['PHP_SELF'])) ? $HTTP_SERVER_VARS['PHP_SELF'] : $HTTP_SERVER_VARS['SCRIPT_NAME'];
            $url = $Confs["PROTOCOL"].'://'.$HTTP_SERVER_VARS['SERVER_NAME'].url_session($script);

            redirect($url);
        }
    }
}


// deleting of a row is confirmed
if (isset($HTTP_POST_VARS['confirm_yes'])) {
    if (preg_match('/row([0-9]+)/', $HTTP_POST_VARS['confirm_subject'], $matches)) {
        $instance = $matches[1];
        $sql = $s_confirmations['row'][$instance]['sql'];
        @ibase_query($dbhandle, $sql)
            or $db_error = $errorstring;
        remove_confirm($instance);

        // cleanup the watchtable output buffer
        $s_watch_buffer = '';
    }
}

// deleting a subject is canceled
if (isset($HTTP_POST_VARS['confirm_no'])) {
    if (preg_match('/row([0-9]+)/', $HTTP_POST_VARS['confirm_subject'], $matches)) {
        $instance = $matches[1];
        remove_confirm($matches[1]);
    }
}

if (!empty($s_watch_table)) {

    if (!empty($s_watch_fks)  &&  $s_use_jsrs == TRUE) {
        $js_stack .= js_jsrs_client()
                    .js_jsrs_fk();
    }

    $js_stack .= js_selectableelements()
                .js_selectabletablerows_listener('watchtable');
}


// remove the confirm panel
function remove_confirm($instance) {
    global $HTTP_SERVER_VARS;
    global $s_confirmations, $s_delete_idx;

    $panels_arrayname = get_panel_array($HTTP_SERVER_VARS['SCRIPT_NAME']);
    $name = 'dt_delete'.$instance;
    $idx = get_panel_index($GLOBALS[$panels_arrayname], $name);
    array_splice($GLOBALS[$panels_arrayname], $idx, 1); 
    unset($s_confirmations['row'][$instance]);
            
    if (count($s_confirmations['row']) == 0) {
        unset($s_confirmations['row']);
        $s_delete_idx = 0;
    }
}


//
// preselect all fields from $s_watch_table 
//
function set_watch_all() {
    global $s_fields, $s_watch_table;

    $columns = array();
    foreach($s_fields as $idx => $field) {
        if ($field['table'] == $s_watch_table) {
            $columns[] = $field['name'];
        }
    }
    return $columns;
}


//
// preselect 'Blob As Link' for all blob fields
//
function set_watch_blinks() {
    global $s_fields, $s_watch_table, $s_tables;

    $blinks = array();
    foreach ($s_fields as $idx => $field) {
        if ($field['table'] == $s_watch_table  &&  !$s_tables[$field['table']]['is_view']  &&  $field['type'] == 'BLOB') {
            $blinks[] = $field['name'];
        }
    }

    return $blinks;
}


//
// preselect blob type 'text' if subtype is 1, 'hex' for all other blob fields
//
function set_watch_blobas() {
    global $s_fields, $s_watch_table;

    $blobas = array();
    foreach ($s_fields as $idx => $field) {
        if ($field['table'] == $s_watch_table  &&  $field['type'] == 'BLOB') {
            $blobas[$field['name']] = $field['stype'] == 1 ? 'text' : 'hex';
        }
    }

    return $blobas;
}

//
// set the title for the Watch Table panel regarding $s_watch_table
//
function set_watch_table_title($table) {
    global $ptitle_strings;

    $title = (!isset($table) or $table == '') ? $ptitle_strings['tb_watch'] : $ptitle_strings['tb_watch'].': '.$table;
    set_panel_title('tb_watch', $title);
}



//
// print the watch table
//
function display_table($table, $cols, $start = 0, $rows = 0, $direction = 'ASC'){
    global $HTTP_SERVER_VARS, $dbhandle, $sql_strings;
    global $s_watch_sort, $s_watch_direction, $s_watch_edit, $s_watch_del;
    global $s_watch_condition, $s_watch_buffer, $s_cust, $s_login;

    if ($table == ''  or  !is_array($cols)) {
        return;
    }

    // if the buffer is filled, just display its content
    if (!empty($s_watch_buffer)) {
        echo $s_watch_buffer;
        echo '('.$sql_strings['DisplBuf'].')';
        return;
    }
    $quote = identifier_quote($s_login['dialect']);
    $sql = 'SELECT COUNT(*) FROM ' . $quote . $table . $quote;
    $sql .= ($s_watch_condition != '') ? ' WHERE '.$s_watch_condition : '';

    if (!($res = @ibase_query($dbhandle, $sql))) {
        echo '<br><b>Error: '.$errorstring.'</b><br>';
        return FALSE;
    }
    $row = ibase_fetch_row($res);
    $rowcount = $row[0];
    if ($rowcount < $start) {
        $start = $GLOBALS['s_watch_start'] = $rowcount;
    }

    ob_start();

    // navigation
    echo "<table>\n<tr>\n";
    if ($start > 1) {
        $url = url_session('watchtable.php?go=start'); 
        echo '<td><a href="'.$url.'"><b>&lt;&lt; '.$sql_strings['Start']."</b></a></td>\n";
        $url = url_session('watchtable.php?go=prev'); 
        echo '<td><a href="'.$url.'"><b>&lt; '.$sql_strings['Prev']."</b></a></td>\n";
    }
    $end = (($start + $rows >= $rowcount)) ? $rowcount : $start + $rows - 1; 
    $cinfo = sprintf('<b>%d - %d (%d %s)</b>', $start, $end, $rowcount, $sql_strings['Total']);
    echo '<td>&nbsp;</td><td>'.$cinfo."</td><td>&nbsp;</td>\n";
    if ($rowcount >= $start + $rows) {
        $url = url_session('watchtable.php?go=next'); 
        echo '<td><a href="'.$url.'"><b>'.$sql_strings['Next']." &gt;</b></a></td>\n";
        $laststart = floor(($rowcount-1)/$rows)*$rows + 1;
        $url = url_session('watchtable.php?go='.$laststart); 
        echo '<td><a href="'.$url.'"><b>'.$sql_strings['End']." &gt;&gt;</b></a></td>\n";
    }
    echo "</tr>\n</table>\n";

    // table head
    echo "<table id=\"watchtable\" cellpadding=\"2\" border=\"0\" onselectstart=\"return false\" style=\"-moz-user-select: none\">\n<tr>\n";
    foreach ($cols as $col) {
        $url = url_session('watchtable.php?order='.$col); 
        if ($col == $s_watch_sort) {
            $col = ($s_watch_direction == 'ASC') ? '*&nbsp;'.$col : $col.'&nbsp;*';
        }
        echo '<th><a href="'.$url.'">'.$col."</a></th>\n";
    }
    if ($s_watch_edit == 'YES') {
        echo '<th style="background-color: '.$s_cust['color']['area']."\">&nbsp;</th>\n"; 
    }
    if ($s_watch_del == 'YES') {
        echo '<th style="background-color: '.$s_cust['color']['area']."\">&nbsp;</th>\n"; 
    }
    echo "</tr>\n";

    // rows
    if ($rowcount > 0) {
        if (WT_METHOD == WT_STORED_PROCEDURE) {
            print_rows_sp(); 
        }
        else {
            print_rows_nosp($table, $cols, $start, $rows, $direction); 
        }
    }
    echo "</table>\n";

    // save the resulting table in the session
    $s_watch_buffer = ob_get_contents();
    ob_end_flush();
}


//
// output the table rows, use the stored procedure generated by sp_limit_create()
//
function print_rows_sp() {
    global $dbhandle, $db_error, $s_watch_columns, $s_watch_rows; 
    global $s_watch_edit, $s_watch_del;

    $cols = $s_watch_columns;
    $types = get_column_types($cols);
    $rows = $s_watch_rows;
    $class = 'wttr2';
    $sql = 'SELECT * FROM '.SP_LIMIT_NAME;
    $res = ibase_query($dbhandle, $sql) 
        or $db_error = $errorstring;

    while ($row = ibase_fetch_row($res)) {
        unset($obj);
        foreach ($cols as $idx => $colname) {
            $obj[$colname] = (isset($row[$idx])) ? $row[$idx] : '';
        }
        settype($obj, 'object');

        $class = ($class == 'wttr1') ? 'wttr2' : 'wttr1';
        echo '<tr class="'.$class.'">';
        for ($k = 0; $k < count($cols); $k++) {
            if (!isset($row[$k])) {
                print_value(NULL, NULL);
            }
            else {
                print_value($row[$k], $types[$cols[$k]], $cols[$k], $obj);
            }
        }

        // get parameter for the edit and/or del link
        if ($s_watch_edit == 'YES'  ||  $s_watch_del == 'YES') {
            build_editdel_links($obj);
            echo "</tr>\n";
        }            
    }
    ibase_free_result($res);
}


//
// output the table rows, skip all rows<$start and rows>$start+$cols
//
function print_rows_nosp(&$table, &$cols, $start = 1, $rows = 0, $direction = 'ASC') {
    global $dbhandle, $HTTP_SERVER_VARS, $s_login; 
    global $s_watch_sort, $s_watch_edit, $s_watch_del, $s_fields, $s_watch_condition; 

    $types = get_column_types($cols);
    $class = 'wttr2';

    $quote = identifier_quote($s_login['dialect']);

    $sql = (WT_METHOD == WT_FIREBIRD_SKIP) ? 'SELECT FIRST '.$rows.' SKIP '.($start > 0 ? $start -1 : 0).' ' : 'SELECT ';
    $sql .= $quote . implode($quote . ',' . $quote , $cols) . $quote . ' FROM ' . $quote . $table . $quote;
    $sql .= ($s_watch_condition != '') ? ' WHERE ' . $s_watch_condition : '';

    if(!empty($s_watch_sort)) {
         $sql .= ' ORDER BY '.$s_watch_sort.' '.$direction;
    }

    if (WT_METHOD == WT_IB65_ROWS) {
        $sql .= ' ROWS '.$start.' TO '.($start + $rows -1);
    }

    $res = @ibase_query($dbhandle, $sql) or db_error(__FILE__, __LINE__, $sql);

    // skip the rows until $start
    if (WT_METHOD == WT_SKIP_ROWS) {
        for ($i = 1; $i < $start; $i++) {
            ibase_fetch_row($res);
        }
    }

    for ($i = 0; $i < $rows; $i++) {
        $obj = @ibase_fetch_object($res);
        // stop, if there are no more rows
        if (!is_object($obj)) { 
             break;
        }

        $class = ($class == 'wttr1') ? 'wttr2' : 'wttr1';
        echo '<tr class="'.$class.'">';
        $arr = get_object_vars($obj);
        for ($k = 0; $k < count($cols); $k++) {
            if (!isset($arr[$cols[$k]])) {
                print_value(NULL, NULL);
            }
            else {
                print_value($arr[$cols[$k]], $types[$cols[$k]], $cols[$k], $obj);
            }
        }

        // get parameter for the edit and/or del link
        if ($s_watch_edit == 'YES'  ||  $s_watch_del == 'YES') {
            build_editdel_links($obj);
            echo "</tr>\n";
        }            
    }
    ibase_free_result($res);
}


function print_value($val, $type, $colname=NULL, $obj=NULL) {
    global $s_watch_blinks, $s_watch_table, $s_watch_fks, $s_watch_tblob_inline, $s_watch_tblob_chars, $s_watch_blobas, $s_use_jsrs;
    
    if ($val === NULL) {
        $data= '<i>NULL</i>';
        $align_str = ' align="center"';
    }        
    elseif (strlen(trim($val)) == 0) {
        $data = '&nbsp;';
        $align_str = '';
    }
    elseif (in_array($type, array('CHARACTER', 'VARCHAR'))) {
        $data = htmlspecialchars(trim($val));
        $align_str = ' align="left"';
    }        
    elseif ($type != 'BLOB') { 
        $data = trim($val);
        $align_str = ' align="right"';
    }
    else {
        $inline_flag = FALSE;
        $data = '';
        if ($s_watch_tblob_inline =='YES'  &&  $s_watch_blobas[$colname] == 'text') {
            $blob_handle = ibase_blob_open($val);
            $blob_info   = ibase_blob_info($val);
            $blob_length = $blob_info[0];
            $data = htmlspecialchars(ibase_blob_get($blob_handle, $s_watch_tblob_chars));
            ibase_blob_close($blob_handle);
            if ($blob_length > $s_watch_tblob_chars) {
                $data .= ' ...&nbsp;';
            }
            else {
                $inline_flag = TRUE;
            }
        }
        if (in_array($colname, $s_watch_blinks)  && !$inline_flag) {
            $url  = 'showblob.php?where='.get_where_str($obj);
            $url .= '&table='.$s_watch_table.'&col='.$colname;
            $url = url_session($url);
            $data .= '<i><a href="'.$url.'" target="_blank">BLOB</a></i>';
        }

        if ($data == '') {
            $data= '<i>BLOB</i>';
        }

        $align_str = $inline_flag == TRUE ? ' align="left"' : ' align="center"';
    }

    if (isset($s_watch_fks[$colname])  &&  $s_use_jsrs == TRUE) {
        $link = sprintf("javascript:requestFKValues('%s', '%s', '%s')",
                        $s_watch_fks[$colname]['table'],
                        $s_watch_fks[$colname]['column'],
                        $data);
        $data = '<a href="'.$link.'">'.$data.'</a>' ;
    }

    echo '<td'.$align_str.'>'.$data.'</td>';
}


function build_editdel_links($obj) {
    global $s_watch_edit, $s_watch_del, $sql_strings; 

    $where = get_where_str($obj);
    // build the Edit-Link
    if ($s_watch_edit == 'YES') {
        $url = url_session('watchtable.php?edit='.$where);
        echo '<td><a href="'.$url.'"><b>&nbsp;'.$sql_strings['Edit'].'&nbsp;</b></a></td>';
    }
    // build the Del-link
    if ($s_watch_del == 'YES') {
        $url = url_session('watchtable.php?del='.$where);
        echo '<td><a href="'.$url.'"><b>&nbsp;'.$sql_strings['Delete'].'&nbsp;</b></a></td>';   
   }
}


function get_where_str($obj) {
    global $s_fields, $s_watch_table, $s_login;

    static $quote;
    if (!isset($quote)) {
        $quote = identifier_quote($s_login['dialect']);
    }

    $where = 'WHERE ';
    foreach ($s_fields as $field) {
        if ($field['table'] == $s_watch_table  
        &&  (isset($field['primary']) &&  !empty($field['primary']))) {
            $where .= $quote . $field['name'] . $quote . '=';
            $where .= (is_number($field)) 
                    ? $obj->{$field['name']} 
                    : "'".str_replace("'", "''", $obj->{$field['name']})."'";
            $where .= ' AND ';
        }
    } 
    $where = substr($where, 0, -5);
    $where = urlencode($where);

    return $where;
}


function get_column_types($cols) {
    global $s_fields;

    $types = array();
    foreach ($s_fields as $farr) {
        if (in_array($farr['name'], $cols)) {
            $types[$farr['name']] = $farr['type'];
        }
    }

    return $types;
}


//
// display a table with the elements to configure the watchtable for $table
//
function watchtable_column_options($table, $show_cols, $sort_col, $bloblinks, $blobas) {
    global $s_fields, $blob_types, $sql_strings;

    echo "<table border>\n";
    echo '<tr><th>'.$sql_strings['Column']
            .'</th><th>'.$sql_strings['Show'].'</th>'
            .'<th>'.$sql_strings['Sort'].'</th>'
            .'<th>'.$sql_strings['BlobLink'].'</th>'
            .'<th>'.$sql_strings['BlobType'].'</th>'
        ."</tr>\n";

    foreach($s_fields as $field) {
	if ($field['table'] == $table) {
            echo "<tr>\n";

            // column names
            echo '<td>'.$field['name']."</td>\n";

            // 'Show' checkboxes
            echo '<td align="center"><input type="checkbox" name="columns[]" value="'.$field['name'].'"';
            if (in_array($field['name'], $show_cols)) {
                echo ' checked';
            }
            echo "></td>\n";

            // 'Sort' radioboxes
            echo '<td align="center"><input type="radio" name="radiobox" value="'.$field['name'].'"';
            if ($field['name'] == $sort_col) {
                echo ' checked';
            }
            echo "></td>\n";

            // 'Blob as Link' checkboxes
            echo '<td align="center">';
            if ($field['type'] == 'BLOB') {
                echo '<input type="checkbox"  name="bloblinks[]" value="'.$field['name'].'"';
                if (in_array($field['name'], $bloblinks)) {
                    echo ' checked';
                }
                echo '>';
            }
            else {
                echo '&nbsp;';
            }
            echo "</td>\n";

            // 'Blob Type' select lists
            echo '<td align="center">';
            if ($field['type'] == 'BLOB') {
                $sel = (isset($blobas[$field['name']])) ? $blobas[$field['name']] : NULL;
                echo get_selectlist('blobas['.$field['name'].']', $blob_types, $sel, TRUE);
            }
            else {
                echo '&nbsp;';
            }
            echo "</td>\n";
            echo "</tr>\n";
        }
    }
    echo "</table>\n";
}


//
// find the foreign keys defined for $table,
// only foreign keys over single columns are taken into consideration
//
function get_foreignkeys($table) {
    global $dbhandle;

    $sql = 'SELECT I2.RDB$RELATION_NAME FKTABLE,'
                .' IS1.RDB$FIELD_NAME FKFIELD,'
                .' IS2.RDB$FIELD_NAME TFIELD'
           .' FROM RDB$RELATION_CONSTRAINTS RC'
     .' INNER JOIN RDB$INDICES I1 ON RC.RDB$INDEX_NAME=I1.RDB$INDEX_NAME'
     .' INNER JOIN RDB$INDICES I2 ON I1.RDB$FOREIGN_KEY=I2.RDB$INDEX_NAME'
     .' INNER JOIN RDB$INDEX_SEGMENTS IS1 ON I2.RDB$INDEX_NAME=IS1.RDB$INDEX_NAME'
     .' INNER JOIN RDB$INDEX_SEGMENTS IS2 ON I1.RDB$INDEX_NAME=IS2.RDB$INDEX_NAME'
          ." WHERE RC.RDB\$RELATION_NAME='".$table."'"
            ." AND RC.RDB\$CONSTRAINT_TYPE='FOREIGN KEY'"
            .' AND I1.RDB$SEGMENT_COUNT=1';

    $res = @ibase_query($dbhandle, $sql) or db_error(__FILE__, __LINE__, $sql);

    $fk = array();
    while ($row = ibase_fetch_object($res)) {
        $fk[trim($row->TFIELD)] = array('table'  => trim($row->FKTABLE),
                                        'column' => trim($row->FKFIELD));
    }
    ibase_free_result($res);

    return $fk;
}

?>