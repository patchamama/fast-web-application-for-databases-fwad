<?php
// File           info.php / ibWebAdmin
// Purpose        html sequence for the info panel
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/09/09 19:43:36 lb>
//
// $Id: info.php,v 1.18.2.1 2004/11/28 14:32:51 lbrueckner Exp $


echo "<table>\n";

if (!empty($dbhandle->_errorMsg))
	{
	$db_error = $dbhandle->_errorMsg;
	$dbhandle->_errorMsg = "";
	error_handler(0, $db_error, "", 0, "");
	}

if  (isset($binary_output)  &&  count($binary_output) > 0  &&  $active != 'SQL'
&&   strstr('Use CONNECT or CREATE DATABASE to specify a database', $binary_output[0]) === FALSE) {
    echo '<tr><td colspan="2"><b>'.$info_strings['ExtResult'].":</b><br />\n";
    foreach ($binary_output as $line) {
        echo $line."<br />\n";
    }
    echo "</td>\n</tr>\n";
}

if ($db_error != '') {
    echo '<tr><td class="err">'.$info_strings['IBError'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo $db_error;
    echo "</td></tr>\n";
}

if (isset($binary_error)  && $binary_error != '') {
    echo '<tr><td class="err">'.$info_strings['ExtError'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo nl2br($binary_error);
    echo "</td>\n</tr>\n";
}

if ($error != '') {
    echo '<tr><td class="err">'.$info_strings['Error'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo $error;
    echo "</td>\n</tr>\n";
}

if ($php_error != '') {
    echo '<tr><td class="err">'.$info_strings['PHPError'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo $php_error;
    echo "</td>\n</tr>\n";
}

if ($warning != '') {
    echo '<tr><td class="err">'.$info_strings['Warning'].":</td></tr>\n";
    echo "<tr><td>\n";
    echo $warning;
    echo "</td>\n</tr>\n";
}

if ($message != '') {
    echo '<tr><td><b>'.$info_strings['Message'].":</b></td></tr>\n";
    echo "<tr><td>\n";
    echo $message;
    echo "</td>\n</tr>\n";
}

if ($externcmd != '') {
    echo '<tr><td><b>'.$info_strings['ComCall'].":</b></td></tr>\n";
    echo "<tr><td>\n";
    echo $externcmd;
    echo "</td>\n</tr>\n";
}

if ($Confs["DEBUG"]  &&  count($debug) > 0) {
    echo '<tr><td><b>'.$info_strings['Debug'].":</b></td>\n</tr>\n";
    echo "<tr><td>\n";
    foreach($debug as $str) {
        echo $str;
    }
    echo "</td>\n</tr>\n";
}


echo "</table>\n";

echo "<pre>";
print_r($dbhandle);
echo "</pre>";


?>
