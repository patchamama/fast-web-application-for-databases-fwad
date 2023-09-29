<?php
	// File           SaveQueryConditions.php.php / Phyllacanthus
	// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
	// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
	// Version        Jun 1, 2005
	//
	// params	element		Contain the number of the element in the session
	//		level		Level of the conditions
	//		opener		Form that will be update
	//		field		List that will be update
	//		condition	Position of the condition
	//		all		Delete all the conditions?
	//		mark		Is the value string used to mark a field that contain conditions

	$HTTP_PARAM_VARS['cantrows'] = (isset($HTTP_PARAM_VARS['cantrows'])) ? $HTTP_PARAM_VARS['cantrows'] : 15;
	$vcantrows= (int)$HTTP_PARAM_VARS['cantrows'];
	$vcantrows = $vcantrows + 0;

	$vGoFast = true; //To dont access/download the database and xml conf file...
	require('./inc/script_start.inc.php');

	//echo html_head('Save_q_conditions');
	//echo html_body();

	$vinModName = (isset($HTTP_GET_VARS['in_mod'])) ? $HTTP_GET_VARS['in_mod'] : $vModName;
	
	$vlevel = (int)$HTTP_GET_VARS['level'];
	$vlevel = $vlevel + 0;

	$velement = (int)$HTTP_GET_VARS['element'];
	$velement = $velement + 0;

	$vcondition = (int)$HTTP_GET_VARS['condition'];
	$vcondition = $vcondition + 0;
	$vqueryfield = $vcondition;

	$viscondition = ($HTTP_GET_VARS['iscondition']=='1');

	$vall = (int)$HTTP_GET_VARS['all']+ 0;

	$vUnMark = 0;

	if ($viscondition)
		{
		if (!isset($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName]))
			{
			$s_xml_conf['elements'][$velement]['q_conditions'][$vinModName] = array();
			}
		else	{
			$vPosCondLevel = -1;
			for ($i=0, $max=count($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName]); $i < $max; $i++)
				{
				if ($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$i]['level']==$vlevel)
					{
					$vPosCondLevel++;
					if ($vPosCondLevel==$vcondition)
						{
						$vcondition = $i;
						$i = $max;
						}
					}
				}
			}

		$vUnMark = 0;
		if ($vall==1)
			{
			unset($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName]);
			$vUnMark = 1;
			echo "\n".'<br><select name="query_values" size="'.$vcantrows.'" onClick="javascript:SelectQueryConditions();">'."\n";
			echo '	<option value="" >--- '.stripcslashes($query_strings["insertCondition"]).' ---</option>'."\n";
			echo "\n".'</select>'."\n";
			}
		elseif (($velement>-1) and ($vcondition>-1))
			{
			$a = 0;

			for ($a=$vcondition; $a<count($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName])-1; $a++)
				{
				$s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$a] = $s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$a+1];

				}
			//$a = count($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName]);
			if ($a>=0)
				{
				unset($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$a]);
				}
			//if (count($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName])==0)
			//	{
			//	unset($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName]);
			//	}
			$vUnMark = (count($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName])==0)? 1: 0;
			}
		}
	else	{
		$vIsThereQFields = (isset($s_xml_conf['showqueryfields'][0]));
		if ($vIsThereQFields)
			{
//			js_alert('entro');
			$vdelpos = -1;
			if ($vall==1)
				{
				unset($s_xml_conf['showqueryfields']);
				echo "\n".'<br><select name="query_values" size="'.$vcantrows.'" onClick="javascript:SelectQueryConditions();">'."\n";
				echo '	<option value="" >--- '.stripcslashes($query_strings["insertCondition"]).' ---</option>'."\n";
				echo "\n".'</select>'."\n";
				}
			else
				{
	
				for ($i=0, $max=count($s_xml_conf['showqueryfields']); $i < $max; $i++)
					{
					if ($vqueryfield==$s_xml_conf['showqueryfields'][$i]['position'])
						{
						$vdelpos = $i;
						$i = $max;
						}
					}
				if ($vdelpos>-1)
					{
					//unset($s_xml_conf['showqueryfields'][$vdelpos]);
					for ($i=$vdelpos; $i < $max-1; $i++)
						{
						$s_xml_conf['showqueryfields'][$i] = $s_xml_conf['showqueryfields'][$i+1];
						}
					if ($i>=0)
						{
						unset($s_xml_conf['showqueryfields'][$i]);
						}
					}
				}

			}
		}
	//require('./inc/script_end.inc.php');
	//globalize_session_vars( $vModName );;
	globalize_conditions( $vModName );

?>

