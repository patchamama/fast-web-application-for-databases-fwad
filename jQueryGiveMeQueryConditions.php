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
	
	$vlevel = (int)$HTTP_GET_VARS['level'];
	$vlevel = $vlevel + 0;
	
	$vpos = (int)$HTTP_GET_VARS['element'];
	$vpos = $vpos + 0;
	

	
	$vcantrows= (int)$HTTP_GET_VARS['cantrows'];
	$vcantrows = $vcantrows + 0;
	
	$velem_selected= (int)$HTTP_GET_VARS['elem_selected'];
	$velem_selected = $velem_selected + 0;
	
	$viscondition = ($HTTP_GET_VARS['iscondition']=='1');
	
	$s_xml_conf['showqueryfields'] = isset($s_xml_conf['showqueryfields']) ? $s_xml_conf['showqueryfields']: array();
	
	//$vtype = $s_xml_conf['elements'][$vpos]['type'];
	
	$vHTML = "";
	$vScriptGlobal 	= "";
	//$vHTML .=  "\n".'<br><select name="query_values" size="'.$vrows.'" onClick="javascript:SelectQueryConditions();">'."\n";
	//$vHTML .=  "\n".'	<option value="" >--- '.$query_strings["notFieldSelected"].' ---</option>';
	//$vHTML .=  "\n".'	</select>'."\n";	
	
	if ($viscondition)
		{
		$vIsThereConditions = (isset($s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName][0]));
		$vSql = '';
		$vid = '';
		$vdesc = '';
				
		if (!$vIsThereConditions)
			{
			$vHTML 	.=  '	<option value="" >--- '.stripcslashes($query_strings["insertCondition"]).' ---</option>'."\n";
			//$vScriptGlobal 	=  '	fieldsArray = new Array();'."\n";
			//$vScriptGlobal 	.=  '	fieldsArrayCount = 1;'."\n";
			//$vScriptGlobal 	.=  '	fieldsArray[0] = new Option("--- '.$query_strings["insertCondition"].' ---", "");'."\n";
			}
		else
			{
			//$vScriptGlobal 	=  '	fieldsArray = new Array();'."\n";
			//$vScriptGlobal 	.=  '	fieldsArrayCount = 0;'."\n";
			//$vScriptGlobal 	.=  "\n";
			$vc = -1;
			for ($i=0, $max=count($s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName]); $i < $max; $i++) 
				{
				if ($s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName][$i]['level']==$vlevel)
					{
					$vc++;
					$vstselected = ($velem_selected==$i) ? 'selected': '';
					if (array_key_exists($s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName][$i]['value'], $unaryCOP))
						{
						$vv = ereg_replace("'",'"', ($s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName][$i]['text']));
						$vHTML 	.=  '	<option value="'.($i).'" '.$vstselected.'>'.stripcslashes($s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName][$i]['text']).'</option>'."\n";
						//$vScriptGlobal 	.=  '	fieldsArray['.($vc).'] = new Option("'.$s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName][$i]['text'].'", "'.($i).'");'."\n";
						}
					else	{
						$vcomparison = $s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName][$i]['comparison'];
						$vv = ereg_replace("'",'"', ("[".$query_strings[$vcomparison]."] ".$s_xml_conf['elements'][$vpos]['q_conditions'][$vinModName][$i]['text']));
						$vHTML 	.=  '	<option value="'.($i).'" '.$vstselected.'>'.stripcslashes($vv).'</option>'."\n";
						//$vScriptGlobal 	.=  "	fieldsArray[".($vc)."] = new Option('".$vv."', '".($i)."');"."\n";
						}
					}

				}
			$vc++;
			$vHTML 	.=  '	<option value="" >--- '.stripcslashes($query_strings["insertCondition"]).' ---</option>'."\n";
			//$vScriptGlobal 	.=  '	fieldsArray['.($vc).'] = new Option("--- '.$query_strings["insertCondition"].' ---", "");'."\n";
			//$vScriptGlobal 	.=  '	fieldsArrayCount = '.($vc+1).";\n";					
			}
		}
	else	//show fields...
		{	
		
		$vIsThereQFields = (isset($s_xml_conf['showqueryfields'][0]));
		
		//$vScriptGlobal 	=  '	fieldsArray = new Array();'."\n";
		//$vScriptGlobal 	.=  '	fieldsArrayCount = 0;'."\n";
		//$vScriptGlobal 	.=  "\n";
		$vc = -1;
		$max = 0;
		$verror = 0;
		
		if ($vIsThereQFields)
			{
			for ($i=0, $max=count($s_xml_conf['showqueryfields']); $i < $max; $i++) 
				{
				$vc++;
				$vstselected = ($velem_selected==$i) ? 'selected': '';
				$vv = $s_xml_conf['showqueryfields'][$i]['name'];
				$vvposi = $s_xml_conf['showqueryfields'][$i]['position'];
				$vHTML 	.=  '	<option value="'.($vvposi).'" '.$vstselected.'>'.stripcslashes($vv).'</option>'."\n";
				//$vScriptGlobal 	.=  "	fieldsArray[".($vc)."] = new Option('".$vv."', '".($vvposi)."');"."\n";
				}
			}
					
		if ( ($vpos>-1) && ($s_xml_conf['elements'][$vpos]['tagname'] != "section") )
			{
			if (isset($s_xml_conf['elements'][$vpos]['alias']))
				{
				$vv = ereg_replace("'",'"', ($s_xml_conf['elements'][$vpos]['querylabel']));
				}
			else	{
				$vv = ereg_replace("'",'"', ($s_xml_conf['elements'][$vpos]['content']));
				}
				
			
			$verror = 0;
			for ($i=0, $max=count($s_xml_conf['showqueryfields']); $i < $max; $i++) 
				{
				if ($s_xml_conf['showqueryfields'][$i]['name']==$vv)
					{
					//js_alert($message_strings['DuplicateField']);
					$i = $max;
					$verror = 1;
					}
				}		
			if ($verror==0)
				{
				$vc++;
				$vHTML 	.=  '	<option value="'.($vpos).'" >'.stripcslashes($vv).'</option>'."\n";
				//$vScriptGlobal 	.=  "	fieldsArray[".($vc)."] = new Option('".$vv."', '".($vpos)."');"."\n";
				$s_xml_conf['showqueryfields'][$max]['name'] = $vv;
				$s_xml_conf['showqueryfields'][$max]['position'] = $vpos;
				}
			}
			
		if ((!isset($s_xml_conf['showqueryfields'])) or
		    (count($s_xml_conf['showqueryfields'])==0) )
			{
			$vc++;
			$vv = $query_strings["notFieldSelected"];
			$vvposi = -1;
			$vHTML 	.=  '	<option value="'.($vvposi).'" >'.'--- '.stripcslashes($vv).' ---'.'</option>'."\n";
			//$vScriptGlobal 	.=  "	fieldsArray[".($vc)."] = new Option('--- ".$vv." ---', '".($vvposi)."');"."\n";
			}

		//$vScriptGlobal 	.=  '	fieldsArrayCount = '.($vc+1).";\n";			
			
		}



	echo "\n".'<br><select name="query_values" size="'.$vcantrows.'" onClick="javascript:SelectQueryConditions();">'."\n";
	echo $vHTML;
	echo "\n".'</select>'."\n";
	//require('./inc/script_end.inc.php');
	//is necesary to save the session because the new values of the fields selected are saved/inserted here
	//globalize_session_vars( $vModName );;
?>



