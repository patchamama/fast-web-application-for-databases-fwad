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

	//echo html_head('SaveQueryFieldName');
	//echo html_body();
	
	$vposition = (int)$HTTP_GET_VARS['position'];
	$vposition = $vposition + 0;
	
	$vname = $HTTP_GET_VARS['name'];
	
	for ($i=0, $max=count($s_xml_conf['showqueryfields']); $i < $max; $i++) 
		{
		if ($s_xml_conf['showqueryfields'][$i]['position']==$vposition)
			{
			$s_xml_conf['showqueryfields'][$i]['name'] = $vname;
			//$s_xml_conf['showqueryfields'][$i]['position'] = $vposition;
			$i = $max;
			}
		}	

	//require('./inc/script_end.inc.php');
	//globalize_session_vars( $vModName );;
	globalize_conditions( $vModName );

?>

