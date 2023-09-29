<?php
	// File           SaveQueryConditions.php.php / Phyllacanthus
	// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
	// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
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

	$vHTML = '';
	
	$vcond = (int)$HTTP_GET_VARS['conditions'];
	$vcond = $vcond + 0;
	
	$vquery_fields_rows = (int)$HTTP_GET_VARS['vquery_fields_rows'];
	$vquery_fields_rows = $vquery_fields_rows + 0;
	
	
	
//$vHTML .=  '<div id="query_values">';
	//-------------------------------------------------------
	$vHTML .=  "\n".'	<input name="query_changevalue" type="button" onClick="javascript:ChangeSelectedQueryConditions();" value="'.$button_strings['Change'].'">'."\n";
	$vHTML .=  "\n".'	<input name="query_delvalue" type="button" onClick="javascript:DeleteSelectedQueryConditions();" value="'.$button_strings['Delete'].'">'."\n";
	$vHTML .=  "\n".'	<input name="query_delvalues" type="button" onClick="javascript:DeleteAllQueryConditions();" value="'.$button_strings['DeleteAll'].'">'."\n";
	$vHTML .=  "\n".'<br>';

	if ($vcond==1)
		{
		$vHTML .=  "\n".'	Nivel: <input name="level_dec" type="button" onClick="alert();" value="-">'."\n";
		$vHTML .=  "\n".'	<input name="cant_levelmax" type="hidden" value="1">'."\n";
		$vHTML .=  "\n".'	<input name="level_value" type="text" readonly="true" size="1" value="1">'."\n";
		$vHTML .=  "\n".'	<input name="level_inc" type="button" onClick="" value="+">'."\n";
		}
	else	{
		$vHTML .=  "\n".'	<input name="goUp" type="button" onClick="javascript:QueryConditionsUp(document.Form.query_values);" value="'.$button_strings['Up'].'">'."\n";
		$vHTML .=  "\n".'	<input name="goDown" type="button" onClick="javascript:QueryConditionsDown(document.Form.query_values);" value="'.$button_strings['Down'].'">'."\n";
		}
	//-------------------------------------------------------	
	$vHTML .=  '<div id="query_selectvalues">';
		$vHTML .=  "\n".'<br><select name="query_values" size="'.$vquery_fields_rows.'" onClick="javascript:SelectQueryConditions();">'."\n";
		$vHTML .=  "\n".'	<option value="" >--- '.$query_strings["notFieldSelected"].' ---</option>';
		$vHTML .=  "\n".'	</select>'."\n";
	$vHTML .=  '</div>';
//$vHTML .=  '</div>';

	echo $vHTML;

?>

