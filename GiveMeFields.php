<?php
	// File           GiveMeLst.php / Phyllacanthus
	// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
	// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
	// Version        Jun 1, 2005
	//
	require('./inc/script_start.inc.php');

	//echo html_head('Give_Me_Fields');
	//echo html_body();

	//Query...
	//GiveMeFields.php?opener=Form&field='+vfieldSt.name+'&tablecheck='+vfield.value
	$vTable = $HTTP_GET_VARS['tablecheck'];
	$vt = $syntax['table'];
	$vt = ereg_replace('#1',$vTable, $vt);
	$vSql = 'SELECT * FROM '.$vt;
	$rec = $dbhandle->SelectLimit($vSql,1);
	if ($rec === FALSE) {
		$db_error = ibase_errmsg();
		}												

	$vScriptGlobal 	=  '	fieldsArray = new Array();'."\n";
	$vScriptGlobal 	.=  '	fieldsArrayCount = 0;'."\n";
	$vScriptGlobal 	.=  "\n";
	$vScriptGlobal 	.=  '	fieldsArray[0] = new Option("", "");'."\n";

	for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++) 
		{
		$fld = $rec->FetchField($i);
		//$type = $rec->MetaType($fld->type);
		$vScriptGlobal 	.=  '	fieldsArray['.($i+1).'] = new Option("'.$fld->name.'", "'.$fld->name.'");'."\n";
		}
	$vScriptGlobal 	.=  '	fieldsArrayCount = '.($i+1).";\n";					
	



?>
<script LANGUAGE="JavaScript">
<!--  

function JS_OnLoad()
{
	UpdateList(window.opener.document.<?php echo $HTTP_GET_VARS['opener']; ?>.<?php echo $HTTP_GET_VARS['field']; ?>);
}


function UpdateList(vfield)
{
<?php echo $vScriptGlobal; ?>
	if (vfield.type=='text')
		{
		for(var s=0; s<fieldsArrayCount; s++) 
			{
			document.form_lst.Lst.options[s]= fieldsArray[s];	
			}
		document.form_lst.Lst.selectedIndex = 0;		
		}
	else	{
		cant = vfield.options.length;
		for(var i=0; i<cant; i++) 
			{
			vfield.options[i]=null;	
			}
		for(var s=0; s<fieldsArrayCount; s++) 
			{
			vfield.options[s]= fieldsArray[s];	
			}
		vfield.selectedIndex = 0;
		window.close();
		}
	
	
}

function Accept(form)
{
	window.opener.document.<?php echo $HTTP_GET_VARS['opener']; ?>.<?php echo $HTTP_GET_VARS['field'];   ?>.value= form_lst.Lst.value;
	window.close();
}
//-->
</script>
<?php

	print "<BODY ONLOAD='JS_OnLoad()'>\n";
	
?>
	<form name="form_lst" method="post" action="">
	Fields 
	<select name="Lst" size="1">
   		<option value=""></option>
	</select>
	<br>
	<br>
	<input name="Submit" type="submit" onClick="javascript:return Accept(this)" value="Aceptar" >
	<input name="Submit" type="submit" onClick="javascript:window.close()" value="Cancelar" >
</form>

<?php 	
	require('./inc/script_end.inc.php');
?>