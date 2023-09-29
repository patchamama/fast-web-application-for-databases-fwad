<?php
// File           users.inc.php / ibWebAdmin
// Purpose        functions working with users, included from user.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/06/13 20:44:12 lb>
//
// $Id: users.inc.php,v 1.9 2004/02/04 17:43:21 lbrueckner Exp $


//
// get an array with the users information from the security database
//
function get_user() {
    global $dbhandle, $s_login;

    $users = array();

    if ($s_login['database'] != SECURITY_DB) {
        $db_str = !empty($s_login['host']) ? $s_login['host'].':'.SECURITY_DB : SECURITY_DB;

        if (($udbh = @ibase_connect($db_str, $s_login['user'], $s_login['password'])) == FALSE) {

            $GLOBALS['db_error'] = $errorstring;

            return FALSE;
        }
    }


    $sql = 'SELECT USER_NAME, UID, GID, '
                 .'FIRST_NAME, MIDDLE_NAME, LAST_NAME '
            .'FROM USERS ';
    $dbh = (isset($udbh)) ? $udbh : $dbhandle;
    $res = ibase_query($dbh, $sql) or db_error();

    while ($obj = ibase_fetch_object($res)) {
        $users[$obj->USER_NAME] = array('UID' => (isset($obj->UID)) ? $obj->UID : NULL,
                                        'GID' => (isset($obj->GID)) ? $obj->GID : NULL,
                                        'FIRST_NAME'  => (isset($obj->FIRST_NAME)) ? $obj->FIRST_NAME : NULL,
                                        'MIDDLE_NAME' => (isset($obj->MIDDLE_NAME)) ? $obj->MIDDLE_NAME : NULL,
                                        'LAST_NAME'   => (isset($obj->LAST_NAME)) ? $obj->LAST_NAME : NULL
                                        );
    }
    ibase_free_result($res);

    if (is_resource($udbh)) {
        ibase_close($udbh);
    }

    return $users;
}


//
// create the user from the values posted by the user-form
//
function create_user() {
    global $HTTP_POST_VARS, $s_sysdba_pw, $users, $s_login;
    global $WARNINGS, $warning, $binary_output, $binary_error;

    if (empty($HTTP_POST_VARS['def_user_name'])) {
        $warning = $WARNINGS['UN_REQUIRED'];
        return FALSE;
    }

    if (empty($HTTP_POST_VARS['def_user_pw'])) {
        $warning = $WARNINGS['PW_REQUIRED'];
        return FALSE;
    }

    if (empty($HTTP_POST_VARS['def_user_pwa'])
    || $HTTP_POST_VARS['def_user_pwa'] != $HTTP_POST_VARS['def_user_pw']) {
        $warning = $WARNINGS['PW_WRONG_REPEAT'];
        return FALSE;
    }

    $db_str = !empty($s_login['host']) ? $s_login['host'].':'.SECURITY_DB : SECURITY_DB;

    $parameters =  ' -user SYSDBA -password '.ibwa_escapeshellarg($s_sysdba_pw).' -database '.ibwa_escapeshellarg($db_str)
                  .' -add '.ibwa_escapeshellarg($HTTP_POST_VARS['def_user_name'])
                  .' -pw '.ibwa_escapeshellarg($HTTP_POST_VARS['def_user_pw']);
    $parameters .= (isset($HTTP_POST_VARS['def_user_fname'])) ? ' -fname '.ibwa_escapeshellarg($HTTP_POST_VARS['def_user_fname']) : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_mname'])) ? ' -mname '.ibwa_escapeshellarg($HTTP_POST_VARS['def_user_mname']) : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_lname'])) ? ' -lname '.ibwa_escapeshellarg($HTTP_POST_VARS['def_user_lname']) : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_uid']))   ? ' -uid '.(int)$HTTP_POST_VARS['def_user_uid'] : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_gid']))   ? ' -gid '.(int)$HTTP_POST_VARS['def_user_gid'] : '';

    list($binary_output, $binary_error) = exec_command('gsec', $parameters, $stderr=TRUE);

    if (!empty($binary_error)  ||  (count($binary_output) > 0)) {

        return FALSE;
    }

    $users[strtoupper($HTTP_POST_VARS['def_user_name'])] =
         array('UID' => (int)$HTTP_POST_VARS['def_user_uid'],
               'GID' => (int)$HTTP_POST_VARS['def_user_gid'],
               'FIRST_NAME'  => $HTTP_POST_VARS['def_user_fname'],
               'MIDDLE_NAME' => $HTTP_POST_VARS['def_user_mname'],
               'LAST_NAME'   => $HTTP_POST_VARS['def_user_lname']
               );

    return TRUE;
}


//
// modify the user $uname according the values posted by the user-form
//
function modify_user($uname) {
    global $HTTP_POST_VARS, $s_sysdba_pw, $users, $s_login;
    global $WARNINGS, $warning, $binary_output, $binary_error;

    if (!empty($HTTP_POST_VARS['def_user_pw'])) {
        if (empty($HTTP_POST_VARS['def_user_pwa'])
        || $HTTP_POST_VARS['def_user_pwa'] != $HTTP_POST_VARS['def_user_pw']) {
            $warning = $WARNINGS['PW_WRONG_REPEAT'];

            return FALSE;
        }
        else {
            $change_pw = TRUE;
        }
    }

    $db_str = !empty($s_login['host']) ? $s_login['host'].':'.SECURITY_DB : SECURITY_DB;

    $parameters  =  ' -user SYSDBA -password '.ibwa_escapeshellarg($s_sysdba_pw).' -database '.ibwa_escapeshellarg($db_str) .' -modify '.ibwa_escapeshellarg($uname);
    $parameters .= (isset($change_pw)) ? ' -pw '.$HTTP_POST_VARS['def_user_pw'] : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_fname'])) ? ' -fname '.ibwa_escapeshellarg($HTTP_POST_VARS['def_user_fname']) : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_mname'])) ? ' -mname '.ibwa_escapeshellarg($HTTP_POST_VARS['def_user_mname']) : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_lname'])) ? ' -lname '.ibwa_escapeshellarg($HTTP_POST_VARS['def_user_lname']) : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_uid'])) ? ' -uid '.(int)$HTTP_POST_VARS['def_user_uid'] : '';
    $parameters .= (isset($HTTP_POST_VARS['def_user_gid'])) ? ' -gid '.(int)$HTTP_POST_VARS['def_user_gid'] : '';

    list($binary_output, $binary_error) = exec_command('gsec', $parameters, $stderr=TRUE);

    if (!empty($binary_error)  ||  (count($binary_output) > 0)) {
        return FALSE;
    }

    $users[$uname] =
         array('UID' => (int)$HTTP_POST_VARS['def_user_uid'],
               'GID' => (int)$HTTP_POST_VARS['def_user_gid'],
               'FIRST_NAME'  => $HTTP_POST_VARS['def_user_fname'],
               'MIDDLE_NAME' => $HTTP_POST_VARS['def_user_mname'],
               'LAST_NAME'   => $HTTP_POST_VARS['def_user_lname']
               );

    return TRUE;
}


//
// remove the user $uname
//
function drop_user($uname) {
    global $HTTP_POST_VARS, $s_sysdba_pw, $users, $s_login;
    global $WARNINGS, $warning, $binary_output, $binary_error;

    $db_str = !empty($s_login['host']) ? $s_login['host'].':'.SECURITY_DB : SECURITY_DB;

    $parameters =  ' -user SYSDBA -password '.ibwa_escapeshellarg($s_sysdba_pw).' -database '.ibwa_escapeshellarg($db_str) .' -delete ' . ibwa_escapeshellarg($uname);

    list($binary_output, $binary_error) = exec_command('gsec', $parameters, $stderr=TRUE);

    if (!empty($binary_error)  ||  (count($binary_output) > 0)) {
        return FALSE;
    }

    unset($users[$uname]);

    return TRUE;
}


//
// output a html-table with a form to define/modify an user 
//
// Variables:  $uname  name of the user to modify or NULL to create a new one
//             $title  headline-string for the table
function user_definition($uname, $title) {
    global $users, $HTTP_POST_VARS, $usr_strings;

    if ($uname != NULL  &&  !isset($HTTP_POST_VARS['usr_user_mod_doit'])) {
        $name  = $uname;
        $fname = $users[$uname]['FIRST_NAME'];
        $mname = $users[$uname]['MIDDLE_NAME'];
        $lname = $users[$uname]['LAST_NAME'];
        $uid   = $users[$uname]['UID'];
        $gid   = $users[$uname]['GID'];
    }
    else {
        $name  = (isset($HTTP_POST_VARS['def_user_name']))  ? trim($HTTP_POST_VARS['def_user_name'])  : '';
        $fname = (isset($HTTP_POST_VARS['def_user_fname'])) ? trim($HTTP_POST_VARS['def_user_fname']) : '';
        $mname = (isset($HTTP_POST_VARS['def_user_mname'])) ? trim($HTTP_POST_VARS['def_user_mname']) : '';
        $lname = (isset($HTTP_POST_VARS['def_user_lname'])) ? trim($HTTP_POST_VARS['def_user_lname']) : '';
        $uid   = (isset($HTTP_POST_VARS['def_user_uid']))   ? trim($HTTP_POST_VARS['def_user_uid'])   : '';
        $gid   = (isset($HTTP_POST_VARS['def_user_gid']))   ? trim($HTTP_POST_VARS['def_user_gid'])   : '';
    }
?>
<table border cellpadding="3" cellspacing="0">
  <tr>
    <th colspan="3" align="left"><?php echo $title; ?></th>
  </tr>
  <tr>
    <td><b><?php echo $usr_strings['UName']; ?></b><br>
        <input type="text" size="20" maxlength="128" name="def_user_name" value="<?php echo $name; ?>" <?php if ($uname != NULL) echo 'readonly'; ?>>
    </td>
    <td><b><?php echo $usr_strings['Password']; ?></b><br>
        <input type="password" size="20" maxlength="31" name="def_user_pw" value="">
    </td>
    <td><b><?php echo $usr_strings['RepeatPW']; ?></b><br>
        <input type="password" size="20" maxlength="31" name="def_user_pwa" value="">
    </td>
  <tr>
    <td><b><?php echo $usr_strings['FName']; ?></b><br>
        <input type="text" size="20" maxlength="128" name="def_user_fname" value="<?php echo $fname; ?>">
    </td>
    <td><b><?php echo $usr_strings['MName']; ?></b><br>
        <input type="text" size="20" maxlength="128" name="def_user_mname" value="<?php echo $mname; ?>">
    </td>
    <td><b><?php echo $usr_strings['LName']; ?></b><br>
        <input type="text" size="20" maxlength="128" name="def_user_lname" value="<?php echo $lname; ?>">
    </td>
  </tr>
  <tr>
    <td><b><?php echo $usr_strings['UserID']; ?></b><br>
        <input type="text" size="10" maxlength="10" name="def_user_uid" value="<?php echo $uid; ?>">
    </td>
    <td><b><?php echo $usr_strings['GroupID']; ?></b><br>
        <input type="text" size="10" maxlength="10" name="def_user_gid" value="<?php echo $gid; ?>">
    </td>
    <td>&nbsp;</td>
</tr>
</table>
<?php

}

?>
