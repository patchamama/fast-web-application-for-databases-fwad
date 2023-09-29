<?php
// File           inc/handle_editdata.inc.php / ibWebAdmin
// Purpose        provides the handling of the dt_edit-panel for sql.php and data.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <01/04/16 16:19:48 lb>
//
// $Id: handle_editdata.inc.php,v 1.19.2.2 2004/11/17 20:53:51 lbrueckner Exp $


//
// check if and which 'done' or 'cancel' button on which dt_edit panel was clicked
//

foreach ($HTTP_POST_VARS as $name => $value) {
    if (preg_match('/dt_edit_(cancel|save)([0-9]+)/', $name, $matches)) {
        // index for array $s_edit_where[]
        $instance = $matches[2];
        $table = $s_edit_where[$instance]['table'];
        $job = $matches[1];
        $success = FALSE;

        if ($job == 'save') {

            // the origin types of domain-based columns are needed
            if (!$s_domains_valid) {

                include_once('./inc/domains.inc.php');

                $s_domains = get_domain_definitions($s_domains);
                $s_domains_valid = TRUE;
            }

            $quote = identifier_quote($s_login['dialect']);

            $s_edit_values[$instance] = $bindargs = array();
            $sql = 'UPDATE ' . $quote . $table . $quote . ' SET ';
            $k = 1;
            foreach($s_fields as $field) {
                if ($field['table'] != $table  ||  isset($field['comp'])) {
                    continue;
                }

                if (isset($HTTP_POST_FILES['dt_edit_file_'.$instance.'_'.$k])  &&
                    !empty($HTTP_POST_FILES['dt_edit_file_'.$instance.'_'.$k]['name'])) {

                    $value = $HTTP_POST_FILES['dt_edit_file_'.$instance.'_'.$k];
                } else {
                    $value = get_request_data('dt_edit_field_'.$instance.'_'.$k);
                }

                // type of the field or the origin type of a domain-based field
                $type = !isset($field['domain']) ? $field['type'] : $s_domains[$field['type']]['type'];

                switch($type) {
                    case 'CHARACTER' :
                    case 'VARCHAR'   :
                    case 'DATE'      :
                    case 'TIME'      :
                    case 'TIMESTAMP' :
                        $bindargs[] = empty($field['notnull'])  &&  empty($value) ? NULL : $value;
                        $sql .= $quote . $field['name'] . $quote . '=?, ';
                        break;
                    case 'BLOB' :
                        // blob from file-upload
                        if (is_array($value)  &&  strlen(trim($value['name'])) > 0) {
                            $bfname = $value['tmp_name'];
                            $bfhandle = fopen($bfname, 'r') or die('cannot open file '.$bfname);
                            $bstr = ibase_blob_import($dbhandle, $bfhandle);
                            fclose($bfhandle);
                            $sql .= $quote . $field['name'] . $quote . '=?, ';

                            $bindargs[] = $bstr;
                        }
                        // drop blob checkbox
                        elseif (isset($HTTP_POST_VARS['dt_drop_blob_'.$instance.'_'.$k])
                                && empty($field['notnull'])) {
                            $sql .= $quote . $field['name'] . $quote . '=?, ';

                            $bindargs[] = NULL;
                        }
                        // blob from textarea
                        elseif (!empty($value)) {
                            $bhandle = ibase_blob_create($dbhandle) or die('cannot create blob: '.__FILE__.', '.__LINE__);
                            ibase_blob_add($bhandle, $value);
                            $bstr = ibase_blob_close($bhandle);
                            $sql .= $quote . $field['name'] . $quote . '=?, ';

                            $bindargs[] = $bstr;
                        }
                        break;
                    default:
                        if ($value == '') {
                            $value = NULL;
                        }
                        $sql .= $quote . $field['name'] . $quote . '=?, ';

                        $bindargs[] = empty($field['notnull'])  &&  empty($value) ? NULL : $value;
                }
                $k++;
            }

            $sql = substr($sql, 0, -2);
            $sql .= ' '.$s_edit_where[$instance]['where'];

            if (count($bindargs) > 0) {
                if (DEBUG) add_debug('$sql: '.$sql, __FILE__, __LINE__);

                $query = ibase_prepare($dbhandle, $sql) or db_error(__FILE__, __LINE__, $sql);
                call_user_func_array('ibase_execute', array_merge(array($query), $bindargs))
                    or $db_error = $errorstring;

                if (empty($db_error)) {
                    $success = TRUE;
                    $s_enter_values = array();
                    $s_watch_buffer = '';

                    // cleanup the watchtable output buffer
                    $s_watch_buffer = '';
                }
            }
        }

        $panels_arrayname = get_panel_array($HTTP_SERVER_VARS['SCRIPT_NAME']);

        if ($success  ||  $job == 'cancel') {
            // remove the dt_edit panel
            $name = 'dt_edit'.$instance;
            $idx = get_panel_index($$panels_arrayname, $name);
            array_splice($$panels_arrayname, $idx, 1); 
            unset($s_edit_where[$instance]);
            unset($s_edit_values[$instance]);
            if (count($s_edit_where) == 0) {
                $s_edit_idx = 0;
            }
        }

        // save the values from other edit forms
        $notthis = $success  ||  $job == 'cancel' ? $instance : NULL;
        $s_edit_values = save_editform_values($notthis, $$panels_arrayname);
    }
}



// save the values from all edit forms beside the completed one
// into an array
function save_editform_values($notthis, $parray) {
    global $s_fields, $s_edit_where;
    global $HTTP_POST_FILES, $HTTP_POST_VARS;

    $values = array();
    foreach ($parray as $panel) {
        if (preg_match('/^dt_edit([0-9]+)/', $panel[0], $matches)  &&
            $matches[1] != $notthis) {

            $idx = $matches[1];
            $table = $s_edit_where[$idx]['table'];
            $k = 1;
            foreach($s_fields as $field) {
                if ($field['table'] == $table) {
                    if (isset($HTTP_POST_FILES['dt_edit_field_'.$idx.'_'.$k])) {
                        $values[$idx][] = $HTTP_POST_FILES['dt_edit_field_'.$idx.'_'.$k]['name'];
                    }
                    else {
                        $values[$idx][] = get_request_data('dt_edit_field_'.$idx.'_'.$k);
                    }
                    $k++;
                }
            }
        }
    }

    return $values;
}


//
// output the form elements for editing a dataset
//
function data_edit($idx) {
    global $dbhandle, $s_login, $s_edit_where, $s_fields, $s_watch_blobas;
    global $s_edit_values;

    $table = $s_edit_where[$idx]['table'];
    $where = $s_edit_where[$idx]['where'];

    // $s_edit_values[] is filled in handle_editdata.php with the values
    // from the posted edit forms
    if (!isset($s_edit_values[$idx])) {
        $quote = identifier_quote($s_login['dialect']);
        $sql = 'SELECT * FROM ' . $quote. $table .$quote . ' ' . $where;
        $res = ibase_query($dbhandle, $sql) or db_error();
        if ($obj = ibase_fetch_object($res, IBASE_TEXT)) {
            $arr = get_object_vars($obj);
        }
        else {
            $arr = array();
            $GLOBALS['db_error'] = "Query didn't return a result: ".$sql;
        }
        ibase_free_result($res);
    }

    $k = 1;
    foreach($s_fields as $field) {
        if ($field['table'] == $table) {
            if (isset($field['comp'])) {
                continue;
            }

            $value = '';
            if (isset($arr[$field['name']])) {
                $value = trim($arr[$field['name']]);
            }
            elseif (isset($s_edit_values[$idx][$k-1])){
                $value = $s_edit_values[$idx][$k-1];
            }
            $value = htmlspecialchars($value);

            $name = 'dt_edit_field_'.$idx.'_'.$k;

            $maxlen = (isset($field['size'])) ? $field['size'] : 20;
            if (!isset($field['size'])) {
                $size = 20;
            }
            else {
                $size = ($field['size'] + 1  > DATA_MAXWIDTH) ? DATA_MAXWIDTH : $field['size'] + 1;
            }

            echo '<tr><td valign="top">'.$field['name']."</td>\n";

            if ($field['type'] != 'BLOB') {
                echo '<td><input type="text" size="'.$size.'" maxlength="'.$maxlen.'" name="'.$name.'" value="'.$value."\">\n</td>\n";
            }

            else {  // $field['type'] == 'BLOB'
                $blob_str = $drop_str = $textarea_str = '';
                if (isset($arr[$field['name']])) {
                    $url = url_session('showblob.php?where='.urlencode($where)."&table=$table&col=".$field['name']);

                    $blob_str = '<i><a href="'.$url.'" target="_blank"><b>BLOB</b></a>&nbsp;</i>';
                    $drop_str = '&nbsp;|&nbsp;<input type="checkbox" name="dt_drop_blob_'.$idx.'_'.$k.'">&nbsp;drop&nbsp;';
                    $size = 38;
                }
                else {
                    $size = 50;
                }
                if ($field['stype'] == 1  ||  $s_watch_blobas[$field['name']] == 'text') {
                    $textarea_str = "<br>\n"
                        . '<textarea name="'.$name.'" cols="42" rows="3">'.$value."</textarea>\n";
                }
                echo "<td>\n"
                   . $blob_str.$drop_str.'<input type="file" size="'.$size.'" name="dt_edit_file_'.$idx.'_'.$k."\" value=\"upload\">\n"
                   . $textarea_str
                   . "</td>\n";
            }
            echo "</tr>\n";
            $k++;
        }
    }
}

?>
