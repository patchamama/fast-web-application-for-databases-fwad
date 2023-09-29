<?php
// File           script_end.inc.php / ibWebAdmin
// Purpose        output the whole html source for the page
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/18 09:12:24 lb>
//
// $Id: script_end.inc.php,v 1.22 2004/05/30 16:43:24 lbrueckner Exp $


//require('./inc/show_menu.inc.php');

$s_connection['user_type'] = 'administrator';

if (($Confs["DEBUG"] === TRUE) && ($s_connection['user_type'] == 'administrator'))
	{
	echo "[<a href=\"javascript:void(0)\" onclick=\"$('div#debug').show();\">Show debug info</a>] <div id=\"debug\" align=\"left\">\n";
	
	//$vs_name = strtolower(basename($HTTP_PARAM_VARS['mod']));
	$vs_name = strtolower(($HTTP_PARAM_VARS['mod']));
	if (strpos($vs_name,'.')>0)
		{
		$vs_name = substr($vs_name, 0, strpos($vs_name,'.'));
		}

	// display links to display the session, post or get variables
	$session_url = url_session('./inc/display_variable.php?var=SESSION&s_name='.$vSessionName.'&s_id='.$HTTP_PARAM_VARS['s_id']);
	echo '<a href="'.$session_url.'" target="_blank">[ Session ]</a>'."\n";

	$post_url = url_session('./inc/display_variable.php?var=POST&s_section='.$vModName.'&s_name='.$vSessionName.'&s_id='.$HTTP_PARAM_VARS['s_id']);
	echo '<a href="'.$post_url.'" target="_blank">[ POST ]</a>'."\n";

	$get_url = url_session('./inc/display_variable.php?var=GET&s_section='.$vModName.'&s_name='.$vSessionName.'&s_id='.$HTTP_PARAM_VARS['s_id']);
	echo '<a href="'.$get_url.'" target="_blank">[ GET ]</a>'."\n";

	echo '<a href="./inc/phpinfo.php" target="_blank">[ phpinfo ]</a>'."\n";

	$kill_url = url_session('./inc/kill_session.php?s_name='.$vSessionName.'&s_id='.$HTTP_PARAM_VARS['s_id']);
	echo '<a href="'.$kill_url.'">[ kill session ]</a>'."\n";

	// display the SQL sentences executed....
	$session_url = url_session('./inc/display_variable.php?var=SQL&s_section='.$vModName.'&s_name='.$vSessionName.'&s_id='.$HTTP_PARAM_VARS['s_id']);
	echo '<a href="'.$session_url.'" target="_blank">[ SQL ]</a>'."\n"; 

	// display the SQL sentences executed....
	echo '<a href="javascript:void(0)"  onClick="javascript: ShowComponents();" target="_blank">[ Components ]</a>'."\n"; 

	// Go to de Edit tools...
	echo '<a href="edit.php?sid='.($HTTP_PARAM_VARS['s_id']).'&amp;mod='.($HTTP_PARAM_VARS['mod']).'" target="_blank">[ Edit Tools ]</a>'."\n"; 

	echo "<br />Session name: ".$vSessionName."=".$HTTP_PARAM_VARS['s_id']."<br />";;
	show_time_consumption($start_time, microtime());

	// see http://xdebug.derickrethans.nl/
	if (function_exists('xdebug_memory_usage')) {
	echo 'memory usage: '.xdebug_memory_usage()."<br />\n";
	}
	
	// Inhalt von $_POST und $_GET in der Session hinterlegen
	$s_POST = $HTTP_POST_VARS;
	$s_GET  = $HTTP_GET_VARS;

	require('./inc/info.php');
	
	echo "</div>\n";
?>		
		<script language="JavaScript" type="text/JavaScript">
		<!-- 
		$("div#debug").hide();
		//-->
		</script>
<?php 	
	}

if (file_exists ('support/errorlog.htm'))
	{
	echo '[<a href="support/support.php?show_errorlog">Reporte de errores</a>]';
	}

if ($Confs["DEBUG_HTML"]) {
    $fname = $Confs["TMPPATH"].substr_replace(basename($HTTP_SERVER_VARS['PHP_SELF']), 'html', -3);
    write_output_buffer($fname);

//     if (in_array('tidy', get_loaded_extensions())) {
//         $tidy = tidy_parse_file($fname);
//         debug_var(tidy_get_error_buffer($tidy));
//     }
}


//echo html_bottom();

//the sesion was initializada one time...
$s_firstTime = 0;

globalize_session_vars( $vModName );


//
// check the global error-variables
//
//function critical_error() {
//
//    return !empty($GLOBALS['error'])  ||
//           !empty($GLOBALS['db_error'])  ||
//           !empty($GLOBALS['php_error'])  ||
//           !empty($GLOBALS['externcmd']);
//}



?>
