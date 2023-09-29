<?php
	// File           SelectQueryConditions.php / Phyllacanthus
	// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
	// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
	// Version        Jun 1, 2005
	//
	// params	element		Contain the number of the element in the session
	//		level		Level of the conditions 
	//		opener		Form that will be update
	//		field		List that will be update
	//		condition	Position of the condition
	//		mark		Is the value string used to mark a field that contain conditions
	
	
	//'<input name="query_isnull" type="checkbox" '.$vDisabled.' onClick="" value="'.$vaddSelected.'">';
	
	require('./inc/script_start.inc.php');

	//echo html_head('Select_q_conditions');
	//echo html_body();
	
	$vinModName = (isset($HTTP_GET_VARS['in_mod'])) ? $HTTP_GET_VARS['in_mod'] : $vModName;
	
	
	$vlevel = (int)$HTTP_GET_VARS['level'];
	$vlevel = $vlevel + 0;
	
	$velement = (int)$HTTP_GET_VARS['element'];
	$velement = $velement + 0;
	
	$vcondition = (int)$HTTP_GET_VARS['condition'];
	$vcondition = $vcondition + 0;	
	
	$viscondition = ($HTTP_GET_VARS['iscondition']=='1');


	
	$vSql = '';
	$vid = '';
	$vdesc = '';
	$vMark = 0;
	$vUseLink = 0;
	//$vScript = '';
	//$vScriptIni = '';
	//$vScriptGlobal = '';
	$vHTML =  '';
	//$vHTML .=  '<form name="Form" method="post" action="">';	
	
	if ($viscondition)
		{
		
		$vtype = $s_xml_conf['elements'][$velement]['type'];


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

		$vcondition = ($vcondition==-1) ? count($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName]) :$vcondition;

		if ($velement==-1)
			{
			//there is not value selected
			}
		elseif ( ($vtype=='combobox') or
		     ($vtype=='checkbox_multi') or
		     ($vtype=='radio') or
		     ($vtype=='listbox_listbox') or
		     ($vtype=='listbox_combobox') )
			{
			//comparisons: isnull, notisnull, equal, notequal
			$vSql = $s_xml_conf['elements'][$velement]['sql'];
			$vId = $s_xml_conf['elements'][$velement]['id'];
			$vDesc = $s_xml_conf['elements'][$velement]['desc'];
			if (!isset($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['value']))
				{
				$vSelected = '';
				$vComparison = '';
				$vMark = 1;
				}
			else	{
				$vSelected = $s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['value'];
				$vComparison = $s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['comparison'];
				}

			$rec = $dbhandle->Execute($vSql);
			if ($rec === FALSE) 
				{
				$db_error .= $dbhandle->ErrorMsg();
				}
			else	
				{

				//$vHTML .=  '<table border="1" cellpadding="4" cellspacing="0" class="TableFieldValue">';
				//$vHTML  .=  '<tr><td class="TableField">';
				//if ( (isset($s_xml_conf['elements'][$velement]['querylabel'])) and
				//	(!empty($s_xml_conf['elements'][$velement]['querylabel'])) )
				//	{
				//	$vHTML .=  $s_xml_conf['elements'][$velement]['querylabel'];	
				//	}
				//else	{
				//	$vHTML .=  $s_xml_conf['elements'][$velement]['content'];
				//	}
				//$vHTML .=  '</td><td class="TableFieldValue">';



				//array('isnull'=>"IS NULL", 'isnotnull'=>"IS NOT NULL");
				$vDisabled = '';
				$vAlreadyActive = false;
				if ((array_key_exists("isnull", $unaryCOP)) and (array_key_exists("isnotnull", $unaryCOP)))
					{

					if ($vSelected=="isnull")
						{
						$vchecked = 'checked';
						$vAlreadyActive = true;
						}
					else	{
						$vchecked = '';
						}
					$vHTML .=  '<input type="radio" name="radio_check" value="1" onClick="radioUpdate()" '.$vchecked.'>';
					$vHTML .=  '	'.stripcslashes($query_strings["isnull"]);

					if ($vSelected=="isnotnull")
						{
						$vAlreadyActive = true;
						$vchecked = 'checked';
						}
					else	{
						$vchecked = '';
						}
					$vHTML .=  '<br><input type="radio" name="radio_check" value="2" onClick="radioUpdate()" '.$vchecked.'>';
					$vHTML .=  '	'.stripcslashes($query_strings["isnotnull"]);
					}

				if ($vAlreadyActive)
					{
					$vDisabled = 'disabled="true"';
					$vchecked = '';
					}
				else	{
					$vchecked = 'checked';
					$vDisabled = '';
					}

				$vHTML .=  '<br><input type="radio" name="radio_check" value="3" onClick="radioUpdate()" '.$vchecked.'>';
				$vHTML .=  '<select name="query_compare" '.$vDisabled.'>'."\n";
				if (array_key_exists("equals", $binaryCOP))
					{
					if (($vComparison=="equals") or (strlen($vComparison)==0))
						{
						$vHTML .=  '<option value="equals" selected>'.stripcslashes($query_strings['equals'])."</option>\n";
						}
					else	{
						$vHTML .=  '<option value="equals" >'.stripcslashes($query_strings['equals'])."</option>\n";
						}
					}
				if (array_key_exists("notequals", $binaryCOP))
					{
					if (($vComparison=="notequals") and (strlen($vComparison)>0))
						{
						$vHTML .=  '<option value="notequals" selected>'.stripcslashes($query_strings['notequals'])."</option>\n";
						}
					else	{
						$vHTML .=  '<option value="notequals" >'.stripcslashes($query_strings['notequals'])."</option>\n";
						}
					}	
				$vHTML .=  '</select>'."\n";

				$vHTML .=  '<select name="query_value" '.$vDisabled.'>'."\n";
				$vDescTemp = '';
				while (!$rec->EOF)
					{
					//if the show values is equal to the field value, select this...
					if (($vSelected==$rec->fields[$vId])  and (strlen($vSelected)>0))
						{
						$vDescTemp = $rec->fields[$vDesc];
						$vHTML .=  '<option value="'.$rec->fields[$vId].'" selected>'.stripcslashes($rec->fields[$vDesc])."</option>\n";
						}
					else	{
						$vHTML .=  '<option value="'.$rec->fields[$vId].'" >'.stripcslashes($rec->fields[$vDesc])."</option>\n";
						}
					$rec->MoveNext();
					}
				$vHTML .=  '</select>'."\n";

				//$vHTML .=  '<input name="query_textboxvalue" type="text" disabled="true">';
				//$vHTML .=  '</td></tr></table>';
				$vHTML .=  '<input name="checklink" type="hidden" value="disabled" > ';

				//$vScript = 'AcceptValue();';

				}



			}
		elseif (($vtype=='date') && false )
			{
			//all the comparisons
			$vDateFrmtEnter =  get_value($s_xml_conf['elements'][$velement],'datefrmenter');  //Used in the type date to define the format of the field date in the form
			$vDateFrmtEnter  = (IsEmpty($vDateFrmtEnter))? 'ymd': $vDateFrmtEnter;
			$vDateFrmtSave  =  get_value($s_xml_conf['elements'][$velement],'datefrmsave');  //Used in  the type date to define the format of the field date  when will stored in the database
			$vDateFrmtSave  = (IsEmpty($vDateFrmtSave))? 'ymd': $vDateFrmtSave;
			$vHTML .=  '<input name="checklink" type="hidden" value="disabled" > ';
			}
		else 
			//( ($vtype=='textbox_list') or
			//($vtype=='textbox') or
			//($vtype=='textbox_add') or
			//($vtype=='memo') or
			//(strpos($vType,'textbox_combobox')===0) )
			{
			
			//all the comparisons
			//$vSql = $s_xml_conf['elements'][$velement]['sql'];
			//echo $vModName."...".$vinModName."....".($velement)."---".($vcondition);
			$vCheckLink = get_value($s_xml_conf['elements'][$velement],'fieldst');
			$vCheckLink = ( ($vtype=='textbox_list') and (!empty($vCheckLink)) );
			if (!isset($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['value']))
				{
				$vSelected = '';
				//$vComparison = "equals";
				$vComparison = "like";
				$vMark = 1;
				$vUseLink = 0;
				}
			else	
				{
				$vSelected = $s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['value'];
				$vComparison = $s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['comparison'];
				$vUseLink = get_value($s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition],'uselink');
				}

			//$vHTML .=  '<table border="1" cellpadding="4" cellspacing="0" class="TableFieldValue">';
			//$vHTML  .=  '<tr><td class="TableField">';

			//if ( (isset($s_xml_conf['elements'][$velement]['querylabel'])) and
			//	(!empty($s_xml_conf['elements'][$velement]['querylabel'])) )
			//	{
			//	$vHTML .=  $s_xml_conf['elements'][$velement]['querylabel'];	
			//	}
			//else	{
			//	$vHTML .=  $s_xml_conf['elements'][$velement]['content'];
			//	}
			//$vHTML .=  '</td><td class="TableFieldValue">';

			//array('isnull'=>"IS NULL", 'isnotnull'=>"IS NOT NULL");
			$vDisabled = '';
			$vAlreadyActive = false;
			if ((array_key_exists("isnull", $unaryCOP)) and (array_key_exists("isnotnull", $unaryCOP)))
				{

				if ($vSelected=="isnull")
					{
					$vchecked = 'checked';
					$vAlreadyActive = true;
					}
				else	{
					$vchecked = '';
					}
				$vHTML .=  '<input type="radio" name="radio_check" value="1" onClick="radioUpdate()" '.$vchecked.'>';
				$vHTML .=  '	'.$query_strings["isnull"];

				if ($vSelected=="isnotnull")
					{
					$vAlreadyActive = true;
					$vchecked = 'checked';
					}
				else	{
					$vchecked = '';
					}
				$vHTML .=  '<br><input type="radio" name="radio_check" value="2" onClick="radioUpdate()" '.$vchecked.'>';
				$vHTML .=  '	'.$query_strings["isnotnull"];
				}


			if ($vAlreadyActive)
				{
				$vDisabled = 'disabled="true"';
				$vchecked = '';
				}
			else	{
				$vchecked = 'checked';
				$vDisabled = '';
				}

			$vHTML .=  '<br><input type="radio" name="radio_check" value="3" onClick="radioUpdate()" '.$vchecked.' />';
			
			if ( ($vtype=='comboboxYN') or
				($vtype=='checkbox') )
				{
				$vHTML .=  '<select name="query_compare" >'."\n";
				
				$vHTML .=  '<option value="equals" '.(($vComparison=="equals") ? "selected" : "").'>'.stripcslashes($query_strings["equals"])."</option>\n";
				$vHTML .=  '<option value="notequals" '.(($vComparison=="notequals") ? "selected" : "").'>'.stripcslashes($query_strings["notequals"])."</option>\n";
				$vHTML .=  '</select>'."\n";
				
				//comparisons: isnull, notisnull, equal, notequal
				$vChecked = get_value($s_xml_conf['elements'][$velement],'checked');  //value when is checked (if is not defined as default asigned 1...
				$vUnChecked = get_value($s_xml_conf['elements'][$velement],'unchecked');  //value when is checked (if is not defined as default asigned 0...
				$vChecked = (IsEmpty($vChecked))? 1: $vChecked;
				$vUnChecked = (IsEmpty($vUnChecked))? 0: $vUnChecked;
					
				$vSelected = stripcslashes($vSelected);
				$vHTML .=  '<select name="query_value" size="1" '.$vDisabled.' >'."\n";
				if ($vSelected==$vChecked)  {
					$vHTML .=  '<option value="'.$vChecked.'" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="'.$vUnChecked.'">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($vSelected==$vUnChecked)  {
					$vHTML .=  '<option value="'.$vChecked.'">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="'.$vUnChecked.'" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="'.$vChecked.'">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="'.$vUnChecked.'">'.$button_strings['No'].'</option>'."\n";
					}
				$vHTML .=  "</select>\n";
				}			
			else
				{
				$vHTML .=  '<select name="query_compare" '.$vDisabled.'>'."\n";
				foreach ($binaryCOP as $vc => $vv)
					{
					if ($vComparison==$vc)
						{
						$vHTML .=  '<option value="'.$vc.'" selected>'.stripcslashes($query_strings[$vc])."</option>\n";
						}
					else	{
						$vHTML .=  '<option value="'.$vc.'">'.stripcslashes($query_strings[$vc])."</option>\n";
						}
					}
				$vHTML .=  '</select>'."\n";
			
				$vHTML .=  '<input type="text" name="query_value" value="'.stripcslashes($vSelected).'" '.$vDisabled.'>'."\n";
				}
			
			if ($vCheckLink)
				{
				$vHTML .=  '<br> <input name="checklink" type="checkbox" value="'.$vUseLink.'" '.(($vUseLink)? 'checked' : '').'> '.$query_strings["UseLink"];
				}
			else	
				{
				$vHTML .=  '<input name="checklink" type="hidden" value="disabled" > ';
				}

			//$vHTML .=  '</td></tr></table>';

			//$vScript = 'AcceptValue();';

			}		

		//$vScriptGlobal 	=  '	fieldsArray = new Array();'."\n";
		//$vScriptGlobal 	.=  '	fieldsArrayCount = 0;'."\n";
		//$vScriptGlobal 	.=  "\n";
		//$vScriptGlobal 	.=  '	fieldsArray[0] = new Option("'.$query_strings["isnull"].'", "isnull");'."\n";
		//$vScriptGlobal 	.=  '	fieldsArray[1] = new Option("'.$query_strings["isnotnull"].'", "isnotnull");'."\n";
		//$vScriptGlobal 	.=  '	fieldsArray[0] = new Option("--- '.$query_strings["insertCondition"].' ---", "");'."\n";
		//$vScriptGlobal 	.=  '	fieldsArrayCount = 1;'."\n";

		//for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++) 
		//	{
		//	$fld = $rec->FetchField($i);
		//	//$type = $rec->MetaType($fld->type);
		//	$vScriptGlobal 	.=  '	fieldsArray['.($i+1).'] = new Option("'.$fld->name.'", "'.$fld->name.'");'."\n";
		//	}
		//$vScriptGlobal 	.=  '	fieldsArrayCount = '.($i+1).";\n";					
		}
	else	
		{
		$vIsThereQFields = (isset($s_xml_conf['showqueryfields'][0]));
		
		if ($vIsThereQFields)
			{
			//
			//$vHTML .=  '<table border="1" cellpadding="4" cellspacing="0" class="TableFieldValue">';
			//$vHTML  .=  '<tr><td class="TableField">';
			
			//$vHTML .=  '</td><td class="TableFieldValue">';
			$vHTML .=  '<input type="text" size="100" name="vvvnew_name" value="'.$s_xml_conf['showqueryfields'][$vcondition]['name'].'">'."\n";	
			$vHTML .=  '<input type="hidden" name="vvvposition" value="'.$s_xml_conf['showqueryfields'][$vcondition]['position'].'">'."\n";
			//$vHTML .=  '</td></tr></table>';

			//$vScriptIni .= '  vselected = document.'.$HTTP_GET_VARS['opener'].'.'.$HTTP_GET_VARS['field'].'.selectedIndex; '."\n";
			//$vScriptIni .= '  document.Form.new_name.value = document.'.$HTTP_GET_VARS['opener'].'.'.$HTTP_GET_VARS['field'].'.options[vselected].text; '."\n";
			//$vScriptIni .= '  document.Form.position.value = document.'.$HTTP_GET_VARS['opener'].'.'.$HTTP_GET_VARS['field'].'.options[vselected].value; '."\n";

			//$vScript = 'AcceptNewFieldName();';
			}
		}


	//print "<BODY ONLOAD='JS_OnLoad()'>\n";
		
	echo "<fieldset><legend>";
	if ($viscondition)
		{
		if ( (isset($s_xml_conf['elements'][$velement]['querylabel'])) and
			(!empty($s_xml_conf['elements'][$velement]['querylabel'])) )
			{
			echo $s_xml_conf['elements'][$velement]['querylabel'];	
			}
		else	{
			echo $s_xml_conf['elements'][$velement]['content'];
			}
		}
	else	{
		echo stripcslashes($button_strings['FieldName']);
		}
	echo "</legend>".$vHTML;



	
	
	//<form name="Form" method="post" action="">
	//Fields 
	//<select name="Lst" size="1">
	//	<option value=""></option>
	//</select>
	
	echo "<br>";
	echo "<br>";
	
	if ($viscondition)
		{
		?>
		<input name="Accept" type="button" onClick="AcceptValueSelectQueryCond(document.Form.<?php echo $HTTP_GET_VARS['field'].','.$vUseLink.','.$vcondition.','.$velement; ?>);" value="Aceptar" >
		<?php 
		}
	else	{
		?>
		<input name="Accept" type="button" onClick="AcceptNewFieldName(document.Form.<?php echo $HTTP_GET_VARS['field']; ?>,document.Form.vvvnew_name.value,document.Form.vvvposition.value);" value="Aceptar" >
		<?php 
		}	
?>
	<input name="Cancel" type="button" onClick='$("div#query_values").show(); $("div#query_editval").hide();' value="Cancelar" >

</fieldset>
<?php 	

	//require('./inc/script_end.inc.php');
	//globalize_session_vars( $vModName );;
?>