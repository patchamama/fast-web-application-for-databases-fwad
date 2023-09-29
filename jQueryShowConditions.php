<?php
	// File           GiveMeQueryConditions.php / Phyllacanthus
	// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
	// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
	// Version        Jun 1, 2005
	//
	// params	element		Contain the number of the element in the session
	//		level		Level of the conditions 
	//		opener		Form that will be update
	//		field		List that will be update
	// 		cantrows	Number of lines in select...
			
	$vGoFast = true; //To dont access/download the database and xml conf file...
	require('./inc/script_start.inc.php');

	//echo html_body();
	
	$vinModName = (isset($HTTP_GET_VARS['in_mod'])) ? $HTTP_GET_VARS['in_mod'] : $vModName;
	
	
	
	
	//$vtype = $s_xml_conf['elements'][$vpos]['type'];
	
	$vHTML = "<br />";
	$vScriptGlobal 	= "";
	$vfield = "";
	$vlevelreg = -1;

	if (isset($s_POST["query_run"]))
		{
		$vHTML .= "Status: The query is executing with destiny to [".$s_POST["query_dest"]."]";
		}
	else if ( (isset($s_POST["searchquick"])) && (!empty($s_POST["searchquick"])) && (isset($s_POST["quickaction"])) )
		{
		$vHTML .= "Status: The quickquery is executing to search [".$s_POST["searchquick"]."]...";
		}
	else
		{
		$vHTML .= "Status: The query is not executing...";
		}
	
	
	
	$vHTML .= "<table border='1'>";
		for ($vlevel=1; $vlevel < 5; $vlevel++)
			for ($i=0, $max=count($s_xml_conf['elements']); $i < $max; $i++) 
				{
				if (isset($s_xml_conf['elements'][$i]['q_conditions'][$vinModName]))
					{
					for ($vvv=0, $vvvmax=count($s_xml_conf['elements'][$i]['q_conditions'][$vinModName]); $vvv < $vvvmax; $vvv++) 
						if ($s_xml_conf['elements'][$i]['q_conditions'][$vinModName][$vvv]['level'] == $vlevel)
							{
							if ($vlevelreg != $vlevel)
								{
								$vlevelreg = $vlevel;
								$vHTML .= "<tr><td><b>".$button_strings["level"]." $vlevel</b></td></tr>";
								}
							
							
							$vtemp = $s_xml_conf['elements'][$i]['q_conditions'][$vinModName][$vvv]['comparison'];
							$vcomp = $query_strings[$vtemp];
							if ($vfield != $s_xml_conf['elements'][$i]["content"])
								{
								//
								$vfield = $s_xml_conf['elements'][$i]["content"];
								$vHTML .= "<tr><td><a href='javascript: void(0);' onclick='javascript: selectqueryvalue(".($i)."); openWindowGiveMeQueryConditions(\"".($i)."\", 1, document.Form.query_values,0);'>".$vfield."</a></td>";
								}
							else
								$vHTML .= "<tr><td></td>";
							$vtextshow = $s_xml_conf['elements'][$i]['q_conditions'][$vinModName][$vvv]['text'];
							if (strlen($vtextshow)>70)
								$vtextshow = substr($vtextshow,0,70)."...";
							$vHTML .= "<td>$vcomp</td>
									   <td>".htmlentities(stripcslashes($vtextshow))."</td>
									   <td><a href='javascript: void(0);' onclick='javascript: selectqueryvalue(".($i)."); openWindowGiveMeQueryConditions(\"".($i)."\", 1, document.Form.query_values,0);'>".$Confs["EditIco"]."</a>
									   <a href='javascript: void(0);' onclick='javascript: DeleteCondition(".($i).",".($vvv)."); '>".$Confs["DelIco"]."</a>".
									  "</td></tr>";
							}
					}//
				}
	
	if ($vlevelreg == -1)
		{
		$vHTML .=  "<tr><td>".$message_strings["NotConditionsDefined"]."</td></tr>";
		}
	$vHTML .= "</table>";
	
	echo $vHTML;
	
	//require('./inc/script_end.inc.php');
	//is necesary to save the session because the new values of the fields selected are saved/inserted here
	//globalize_session_vars( $vModName );;
?>



