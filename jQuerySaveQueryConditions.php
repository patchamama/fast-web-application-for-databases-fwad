<?php
	// File           SaveQueryConditions.php.php / Phyllacanthus
	// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
	// Author         Armando Urquiola Cabrera (urquiolaf@jbpr.org), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
	// Version        Jun 1, 2005
	//
	// params	element		Contain the number of the element in the session
	//		level		Level of the conditions 
	//		condition	Position of the condition
	//		comparison
	//		value
	//		text
	//		uselink

	
	$vGoFast = true; //To dont access/download the database and xml conf file...
	require('./inc/script_start.inc.php');

	//echo html_head('Save_q_conditions');
	//echo html_body();
	
	$vinModName = (isset($HTTP_GET_VARS['in_mod'])) ? $HTTP_GET_VARS['in_mod'] : $vModName;
	
	$vlevel = (int)$HTTP_GET_VARS['level'];
	$vlevel = $vlevel + 0;
	
	$velement = (int)$HTTP_GET_VARS['element'];
	$velement = $velement + 0;
	
	//vcondition already have the position of the condition in the $s_xml_conf sesion var accord to any level declared....
	$vcondition = (int)$HTTP_GET_VARS['condition'];
	$vcondition = $vcondition + 0;	
	
	$vcomparison = $HTTP_GET_VARS['comparison'];
	$vvalue = $HTTP_GET_VARS['value'];
	$vtext = $HTTP_GET_VARS['text'];
	$vuselink = $HTTP_GET_VARS['uselink'];

	$vcondition = ($vcondition==-1)? 0 : $vcondition;

	if ($velement>-1) 
		{
		$s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['value'] = $vvalue;
		$s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['text'] = $vtext;
		$s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['level'] = $vlevel;
		$s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['comparison'] = $vcomparison;
		$s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['uselink'] = $vuselink;
		}

	//require('./inc/script_end.inc.php');
	globalize_conditions( $vModName );


?>
