<?php
// File           inc/display_variable.php / ibWebAdmin
// Purpose        print variable content for debugging purpose
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001,2002 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <02/10/14 17:47:02 lb>
//
// $Id: display_variable.php,v 1.1 2002/10/15 14:18:45 lbrueckner Exp $

//require('configuration.inc.php');
require('configuration.inc.php');

session_name($HTTP_GET_VARS['s_name']);
session_id($HTTP_GET_VARS['s_id']);
session_start();

switch ($HTTP_GET_VARS['var']) {
    case 'SESSION':
        $display = $HTTP_SESSION_VARS;
        break;
    case 'POST':
    case 'GET':
        $display = $HTTP_SESSION_VARS[$HTTP_GET_VARS['s_section']]['s_'.$HTTP_GET_VARS['var']];
        break;
    case 'SQL':
        $display = $HTTP_SESSION_VARS[$HTTP_GET_VARS['s_section']]['s_sql_log'];
        break;
    default:
        $display = NULL;
}

echo '<pre>';
print "Session: ".session_name()."=".session_id()."\n";
print_r($display);
echo '</pre>';

?>
