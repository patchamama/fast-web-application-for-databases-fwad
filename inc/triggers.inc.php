<?php
// File           triggers.inc.php / ibWebAdmin
// Purpose        functions working with triggerss, included from accessories.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 , 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <03/01/19 15:52:24 lb>
//
// $Id: triggers.inc.php,v 1.16.2.1 2005/01/17 21:20:55 lbrueckner Exp $


//
// get the properties for all defined triggers 
//
function &get_triggers(&$oldtriggers) {
    global $dbhandle, $HTTP_POST_VARS;

    $lsql = 'SELECT RDB$TRIGGER_NAME AS NAME,'
                 .' RDB$RELATION_NAME AS TNAME,'
                 .' RDB$TRIGGER_SEQUENCE AS POS,'
                 .' RDB$TRIGGER_TYPE AS TTYPE,'
                 .' RDB$TRIGGER_SOURCE AS TSOURCE,'
                 .' RDB$TRIGGER_INACTIVE AS STATUS'
            .' FROM RDB$TRIGGERS'
           .' WHERE (RDB$SYSTEM_FLAG IS NULL  OR  RDB$SYSTEM_FLAG=0)'
             .' AND RDB$TRIGGER_NAME NOT IN (SELECT RDB$TRIGGER_NAME FROM RDB$CHECK_CONSTRAINTS)'
           .' ORDER BY RDB$TRIGGER_NAME';
    $res = ibase_query($dbhandle, $lsql) or db_error(__FILE__, __LINE__, $lsql);

    $triggers = array();
    while ($obj = ibase_fetch_object($res)) {
        $name = trim($obj->NAME);
        $display = (isset($oldtriggers[$name])) ? $oldtriggers[$name]['display'] : 'close';

        // get the source code for the open triggers 
        $tsource = '';
        if ((isset($oldtriggers[$name]) &&  $display == 'open')
        ||  isset($HTTP_POST_VARS['acc_trigger_mod'])) {

            $bid = ibase_blob_open($obj->TSOURCE);
            $arr = ibase_blob_info($obj->TSOURCE);

            // $arr[0] holds the blob length
            $tsource = ibase_blob_get($bid, $arr[0]);
            ibase_blob_close($bid);

            // discard the 'AS ' from the source-string
            $tsource = substr(trim($tsource), 3);
        }

        $triggers[$name] = array('table'   => trim($obj->TNAME),
                                 'type'    => get_trigger_type($obj->TTYPE),
                                 'pos'     => $obj->POS,
                                 'status'  => get_trigger_status($obj->STATUS),
                                 'source'  => $tsource,
                                 'display' => $display);
    }

    return $triggers;
}


//
// create trigger from the definitions in $triggerdefs
//
function create_trigger($triggerdefs) {
    global $s_login, $isql, $binary_output, $binary_error;

    $isql = trigger_create_source($triggerdefs);

    if (DEBUG) add_debug('isql', __FILE__, __LINE__);

    // this must be done by isql because 'create trigger' is not supported from within php
    list($binary_output, $binary_error) = isql_execute($isql, $s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);

    return ($binary_error != ''  ||  count($binary_output) > 0) ? FALSE : TRUE;
}


function trigger_create_source($triggerdefs) {

    $isql  = "SET TERM !! ;\n"
            .'CREATE TRIGGER '.$triggerdefs['name'].' FOR '.$triggerdefs['table']
            .' '.$triggerdefs['status'].' '.$triggerdefs['type'];
    if ($triggerdefs['pos'] != 0) {
        $isql .= ' POSITION '.$triggerdefs['pos'];
    }
    
    $isql .= " AS\n".$triggerdefs['source']."\n"
            ."SET TERM ; !!\n";

    return $isql;
}


function modify_trigger($name, $triggerdefs) {
    global $s_login, $isql, $binary_output, $binary_error;

    $isql = 'DROP TRIGGER '.$name.";\n"
            .trigger_create_source($triggerdefs);

    if (DEBUG) add_debug('isql', __FILE__, __LINE__);

    list($binary_output, $binary_error) = isql_execute($isql, $s_login['user'], $s_login['password'], $s_login['database'], $s_login['host']);

    return ($binary_error != ''  ||  count($binary_output) > 0) ? FALSE : TRUE;
}


//
// drop the trigger $name off the database
//
function drop_trigger($name) {
    global $s_triggers, $dbhandle, $db_error;
    global $lsql;

    $lsql = 'DROP TRIGGER '.$name;
    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
    if (!@ibase_query($dbhandle, $lsql)) {
        $db_error = $errorstring;
    }
    else {
        unset($s_triggers[$name]);
    }
}


//
// deliver the html for an opened view on the views panel
//
function &get_opened_trigger($name, &$trigger, $url) {
    global $dbhandle, $red_triangle_icon, $acc_strings, $ptitle_strings;

    $html = <<<EOT
          <a href="$url" class="dtitle"><img src="$red_triangle_icon" alt="${ptitle_strings['Close']}" title="${ptitle_strings['Close']}" border="0" hspace="7">$name</a>
        <table cellpadding="0" cellspacing="0">
          <tr>
            <td width="26">
            </td>
            <td>
              <table border cellpadding="3" cellspacing="0">
                <tr>
                  <th>${acc_strings['Table']}</th>
                  <th>${acc_strings['Type']}</th>
                  <th>${acc_strings['Pos']}</th>
                  <th>${acc_strings['Status']}</th>
                  <th>${acc_strings['Source']}</th>
                </tr>
                <tr>
	          <td valign="top">&nbsp;${trigger['table']}</td>
	          <td valign="top">&nbsp;${trigger['type']}</td>
	          <td valign="top">&nbsp;${trigger['pos']}</td>
	          <td valign="top">&nbsp;${trigger['status']}</td>
	          <td valign="top"><pre>${trigger['source']}</pre></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </nobr>

EOT;

    return $html;
}


//
// return the definition sourcecode for a trigger
//
function get_trigger_source($name) {
    global $dbhandle;

    $tsource = '';
    $lsql = 'SELECT RDB$TRIGGER_SOURCE AS TSOURCE'
            .' FROM RDB$TRIGGERS'
           ." WHERE RDB\$TRIGGER_NAME='".$name."'";
    $res = ibase_query($dbhandle, $lsql) or db_error(__FILE__, __LINE__, $lsql);
    $obj = @ibase_fetch_object($res);

    if (is_object($obj)) {
        $bid = ibase_blob_open($obj->TSOURCE);
        $arr = ibase_blob_info($obj->TSOURCE);

        // $arr[0] holds the blob length
        $tsource = trim(ibase_blob_get($bid, $arr[0]));
        ibase_blob_close($bid);

        // discard the 'AS ' from the source-string
        $tsource = substr($tsource, 3);
    }
    ibase_free_result($res);

    return $tsource;
}


//
// return the string equivalent for the trigger-status $int
//
function get_trigger_status($int) {

    if ($int == 0) {
        return 'Active';
    } elseif ($int == 1) {
        return 'Inactive';
    }
    die('Error: get_trigger_status() bad parameter');
}


//
// return the string equivalent for the trigger-type $int
//
function get_trigger_type($int) {
    global $trigger_types;

    return $trigger_types[$int];
}


//
// outputs a html-table with a form to define/modify a trigger 
//
// Variables:    $title     headline-string for the table
function trigger_definition($title) {
    global $s_triggerdefs, $trigger_types, $acc_strings, $s_cust;

    $trigger_source = htmlspecialchars($s_triggerdefs['source']);

?>
<table border cellpadding="3" cellspacing="0">
  <tr>
    <th colspan="5" align="left"><?php echo $title; ?></th>
  </tr>
  <tr>
    <td><b><?php echo $acc_strings['Name']; ?></b><br>
        <input type="text" size="20" maxlength="31" name="def_trigger_name" value="<?php if (isset($s_triggerdefs['name']))  echo $s_triggerdefs['name']; ?>">
    </td>
    <td><b><?php echo $acc_strings['Table']; ?></b><br>
      <?php echo get_table_selectlist('def_trigger_table', array('owner'), $s_triggerdefs['table'], TRUE); ?>
    </td>
    <td><b><?php echo $acc_strings['Type']; ?></b><br>
      <?php echo get_selectlist('def_trigger_type', $trigger_types, $s_triggerdefs['type'], FALSE); ?>
    </td>
    <td align="center"><b><?php echo $acc_strings['Position']; ?></b><br>
        <input type="text" size="2" maxlength="2" name="def_trigger_pos" value="<?php if (isset($s_triggerdefs['pos']))  echo $s_triggerdefs['pos']; ?>">
    </td>
    <td><b><?php echo $acc_strings['Status']; ?></b><br>
      <select name="def_trigger_status">
         <option<?php if ($s_triggerdefs['status'] == 'Active') echo ' selected'; ?>> Active
         <option<?php if ($s_triggerdefs['status'] == 'Inactive') echo ' selected'; ?>> Inactive
      </select>
    </td>
  </tr>
  <tr>
    <td colspan="5">
      <b><?php echo $acc_strings['Source']; ?></b><br>
      <textarea name="def_trigger_source" rows="<?php echo $s_cust['textarea']['rows']; ?>" cols="<?php echo $s_cust['textarea']['cols']; ?>" wrap="virtual"><?php echo $trigger_source; ?></textarea>
    </td>
  </tr>
</table>
<?php

}


//
// save the form vars we got from trigger_definition()
//
function save_triggerdefs() {
    global $s_triggerdefs, $HTTP_POST_VARS;

    $s_triggerdefs['name']   = strtoupper(get_request_data('def_trigger_name'));
    $s_triggerdefs['table']  = $HTTP_POST_VARS['def_trigger_table'];
    $s_triggerdefs['type']   = $HTTP_POST_VARS['def_trigger_type'];
    $s_triggerdefs['pos']    = $HTTP_POST_VARS['def_trigger_pos'];
    $s_triggerdefs['status'] = $HTTP_POST_VARS['def_trigger_status'];
    $s_triggerdefs['source'] = get_request_data('def_trigger_source');
}


//
// mark all triggers as opened or closed in $s_triggers
//
function &toggle_all_triggers(&$triggers, $status) {

    foreach (array_keys($triggers) as $name) {
        $triggers[$name]['display'] = $status;

        if ($status == 'open'  &&  empty($triggers[$name]['source'])) {
            $triggers[$name]['source'] = get_trigger_source($name);
        }
    }

    return $triggers;
}

?>
