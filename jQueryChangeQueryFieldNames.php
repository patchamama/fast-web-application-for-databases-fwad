<?php
	// File           SaveQueryConditions.php.php / Phyllacanthus
	// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
	// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
	// Version        Jun 1, 2005
	//
	// params	position
	//		name

	$vGoFast = true; //To dont access/download the database and xml conf file...
	require('./inc/script_start.inc.php');

	//echo html_head('ChangeQueryFieldNames');
	//echo html_body();
	
	$vposition1 = (int)$HTTP_GET_VARS['position1'];
	$vposition1 = $vposition1 + 0;
	
	$vposition2 = (int)$HTTP_GET_VARS['position2'];
	$vposition2 = $vposition2 + 0;
	
	$temp = $s_xml_conf['showqueryfields'][$vposition1];
	$s_xml_conf['showqueryfields'][$vposition1] = $s_xml_conf['showqueryfields'][$vposition2];
	$s_xml_conf['showqueryfields'][$vposition2] = $temp;
	
	//require('./inc/script_end.inc.php');
	//globalize_session_vars( $vModName );;
	globalize_conditions( $vModName );
?>
