<?php
// File           roles.inc.php / ibWebAdmin
// Purpose        functions working with roles, included from user.php
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/05/26 11:29:40 lb>
//
// $Id: roles.inc.php,v 1.5 2004/02/04 17:43:16 lbrueckner Exp $


//
// create a role called $name
//
function create_role($name) {
    global $dbhandle, $roles, $s_login;
    global $db_error, $lsql;

    $name = strtoupper($name);

    $lsql = 'CREATE ROLE '.$name;

    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);

    if (!@ibase_query($dbhandle, $lsql)) {
        $db_error = $errorstring;
    }

    if (empty($db_error)) {

        $roles[$name]['owner'] = $s_login['user'];
        $roles[$name]['members'] = array();

        return TRUE;
    }
    else {

        return FALSE;
    }
}


//
// drop the role $name off the database
//
function drop_role($name) {
    global $roles, $dbhandle;
    global $db_error, $lsql;

    $lsql = 'DROP ROLE '.$name;
    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);
    if (!@ibase_query($dbhandle, $lsql)) {
        $db_error = $errorstring;
        return FALSE;
    }
    else {
        unset($roles[$name]);
        return TRUE;
    }
}


//
// grant a role to an user
//
function grant_role_to_user($role, $user) {
    global $dbhandle, $roles;
    global $db_error, $lsql;

    $user = strtoupper($user);

    $lsql = 'GRANT '.$role.' TO '.$user;

    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);

    if (!@ibase_query($dbhandle, $lsql)) {
        $db_error = $errorstring;
    }

    if (empty($db_error)) {

        $roles[$role]['members'][] = $user;
        return TRUE;
    }
    else {

        return FALSE;
    }
}


//
// revoke a role from an user
//
function revoke_role_from_user($role, $user) {
    global $dbhandle, $roles;
    global $db_error, $lsql;

    $user = strtoupper($user);

    $lsql = 'REVOKE '.$role.' FROM '.$user;

    if (DEBUG) add_debug('lsql', __FILE__, __LINE__);

    if (!@ibase_query($dbhandle, $lsql)) {
        $db_error = $errorstring;
    }

    if (empty($db_error)  &&
        ($idx = array_search($user, $roles[$role]['members'])) !== FALSE) {
        unset($roles[$role]['members'][$idx]);
        return TRUE;
    }
    else {

        return FALSE;
    }
}



//
// return an array with the properties of the defined indeces 
//
function get_roles() {
    global $dbhandle;

    $sql = 'SELECT R.RDB$ROLE_NAME AS NAME,'
                .' R.RDB$OWNER_NAME AS OWNER,'
                .' P.RDB$USER AS MEMBER'
           .' FROM RDB$ROLES R'
      .' LEFT JOIN RDB$USER_PRIVILEGES P'
             .' ON R.RDB$ROLE_NAME=P.RDB$RELATION_NAME'
            ." AND P.RDB\$PRIVILEGE='M'"
           .'ORDER BY R.RDB$ROLE_NAME';
    $res = ibase_query($dbhandle, $sql) or db_error();

    $roles = array();
    $lastone = '';
    while ($obj = ibase_fetch_object($res)) {
        $rname  = trim($obj->NAME);
        $member = (isset($obj->MEMBER)) ? trim($obj->MEMBER) : '';

        if ($rname == $lastone) {
            $roles[$rname]['members'][]   = $member;
            continue;
        }

        $roles[$rname]['owner'] = trim($obj->OWNER);
        $roles[$rname]['members'] = (!empty($member)) ? array($member) : array();
        $lastone = $rname;
    }

    return $roles;
}


//
// output the options for the role selectlist
//
function build_roles_options(&$roles, $selected) {
    global $s_login;

    echo "<option>\n";
    foreach($roles as $name => $role) {
        if ($role['owner'] != $s_login['user']  &&  $s_login['user'] != 'SYSDBA') {
            continue;
        }
        if ($name == $selected) {
            echo '<option selected> '.$name."\n";
        } else
            echo '<option> '.$name."\n";
    }
}

?>
