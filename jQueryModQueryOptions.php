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
	
	$vcantrows= (int)$HTTP_GET_VARS['cantrows'];
	$vcantrows = $vcantrows + 0;	
	$vQueryMark = '*';
	$vQueryCount = -1;
	
	$vHTML = "";
	

	
	if (!isset($s_xml_conf['queryOptions'])) //We create in the session the values of the queryOptions
		{
		$vsection = "";
		for ($a=0; $a < count($s_xml_conf['elements']); $a++)
			{
			$vType = get_value($s_xml_conf['elements'][$a],'type');   //type of edition of the field...
			$vTable = get_value($s_xml_conf['elements'][$a],'table');  //table name
			$vField = get_value($s_xml_conf['elements'][$a],'field');	//Field to save of read from the db
			$vquerylabel = (get_value($s_xml_conf['elements'][$a],'querylabel'));
			//$vQuerySection = (get_value($s_xml_conf['elements'][$a],'tagname')=='section') ? (get_value($s_xml_conf['elements'][$a],'content')) : "";
			$vContent = get_value($s_xml_conf['elements'][$a],'content');
			
			if (get_value($s_xml_conf['elements'][$a],'tagname')=='section')
				{  //if is a Section tag (title of the group of fields)...
				$vsection = get_value($s_xml_conf['elements'][$a],'content');
				$vQuerySection = $vsection;
				}
									
			if (($vType!='hidden') && (!empty($vField)) && (get_value($s_xml_conf['elements'][$a],'tagname')=='element') )
				{
				if (!empty($vquerylabel))
					{
					if (!empty($vQuerySection))
						{
						$vQueryCount++;
						$s_xml_conf['queryOptions'][$vQueryCount]['mod'] = $vinModName;
						$s_xml_conf['queryOptions'][$vQueryCount]['value'] = -1;
						$s_xml_conf['queryOptions'][$vQueryCount]['text'] = htmlentities('-----'.strtoupper($vsection)."-----");
						$vQuerySection = '';
						}

					$vQueryCount++;
					$s_xml_conf['queryOptions'][$vQueryCount]['mod'] = $vinModName;
					$s_xml_conf['queryOptions'][$vQueryCount]['value'] = $a;
					$s_xml_conf['queryOptions'][$vQueryCount]['text'] = htmlentities($vquerylabel);

					}
				elseif (!empty($vContent))
					{
					if (!empty($vQuerySection))
						{
						$vQueryCount++;
						$s_xml_conf['queryOptions'][$vQueryCount]['mod'] = $vinModName;
						$s_xml_conf['queryOptions'][$vQueryCount]['value'] = -1;
						$s_xml_conf['queryOptions'][$vQueryCount]['text'] = htmlentities('-----'.strtoupper($vsection)."-----");
						$vQuerySection = '';
						}

					$vQueryCount++;
					$s_xml_conf['queryOptions'][$vQueryCount]['mod'] = $vinModName;
					$s_xml_conf['queryOptions'][$vQueryCount]['value'] = $a;
					$s_xml_conf['queryOptions'][$vQueryCount]['text'] = htmlentities($vContent);

					}

				}
			}

		}

		
	$vHTML .=  "\n".'	<select name="query_fields" size="'.($vcantrows).'" onClick="javascript: openWindowGiveMeQueryConditions(document.Form.query_fields.value, 1, document.Form.query_values,0);">'."\n";
	
	$vQueryCount = count($s_xml_conf['queryOptions']);
	for ($i=0; $i < $vQueryCount; $i++)
		{
		$vvvpos = ($s_xml_conf['queryOptions'][$i]['value']);
		if (isset($s_xml_conf['elements'][$vvvpos]['q_conditions'][$vinModName]))
			{
			$vqm = (count($s_xml_conf['elements'][$vvvpos]['q_conditions'][$vinModName])>0)? $vQueryMark	: '';
			}
		else	{
			$vqm = '';
			}
		$vHTML .= '	<option value="'.($s_xml_conf['queryOptions'][$i]['value']).'" >'.$vqm.$s_xml_conf['queryOptions'][$i]['text']."</option>\n";
		}	
			

	$vHTML .=  "\n".'	</select>'."\n";
	
//echo htmlentities($vHTML);
	echo $vHTML;
	globalize_session_vars( $vModName );;

	//require('./inc/script_end.inc.php');

?>
