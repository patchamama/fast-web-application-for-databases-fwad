<?php
// File           GiveMeLst.php / Phyllacanthus
// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//
require('./inc/script_start.inc.php');

echo html_body();

//JavaScript to update the value of the paramaters in the father window....
?>
<script language="JavaScript" type="text/JavaScript">
<!--  
function Accept(form)
{
	var limit = 0;
	var str = form_lst.Lst.value;
	for (i = 0; i < str.length; i++)
		{
		  if (str.substring(i,i+1) == '_')
			{
			  limit = i;
			  i = str.length;
			}
		 }
	window.opener.document.<?php echo $HTTP_GET_VARS['Opener']; ?>.<?php echo $HTTP_GET_VARS['fieldst']; ?>.value= str.substring(limit+1,str.length); 
	window.opener.document.<?php echo $HTTP_GET_VARS['Opener']; ?>.<?php echo $HTTP_GET_VARS['field'];   ?>.value= str.substring(0,limit);
	window.opener.document.<?php echo $HTTP_GET_VARS['Opener']; ?>.<?php echo $HTTP_GET_VARS['field']; ?>_Button.value='<?php echo $HTTP_GET_VARS['linked']; ?>';		
	window.close();
}
//-->
</script>

<?php
	//fields need to work in the sql query and combobox...
	$vSql = urldecode(stripcslashes($HTTP_GET_VARS['sql']));  
	$vId = $HTTP_GET_VARS['id'];
	$vDesc = $HTTP_GET_VARS['desc'];
	//Update the values of the sql read in xml conf. file by the values of the form in father windows...
	$vFilter = $HTTP_GET_VARS['filter'];
	$match = array();
	//if ( eregi('select (.+) from (.+) where (.+)', $vSql, $match) )
	if (@preg_match('|select (.+) from (.+) where (.+)|ixms', $vSql, $match))
		{
		if ( (strpos($match[3],$syntax['wildcard'])>0) and (strpos($HTTP_GET_VARS['filter'],$syntax['wildcard'])>0) )
			{
			$match[3] = ereg_replace($syntax['wildcard'], '', $match[3]);
			$vSql = 'select '.$match[1].' from '.$match[2].' where '.$match[3];
			}
		}
	
	$vSql = ereg_replace("__text__", $vFilter, $vSql);
	if (strpos($vSql,'__textUp__')>0)
		{
		$vSql = ereg_replace("__textUp__", strtoupper($vFilter), $vSql);
		$vSql = ereg_replace("Ñ", "ñ", $vSql);
		$vSql = ereg_replace("Á", "á", $vSql);
		$vSql = ereg_replace("É", "é", $vSql);
		$vSql = ereg_replace("Í", "í", $vSql);
		$vSql = ereg_replace("Ó", "ó", $vSql);
		$vSql = ereg_replace("Ú", "ú", $vSql);
		}
	//working with the form...
	$vHTML = '<form name="form_lst" method="post" action="">';
	//Query...
	$res = &$dbhandle->Execute($vSql);
	if ($res === FALSE) {
		$ib_error = ibase_errmsg();
		}												
	//fill the select html option...
	$vHTML .=  '<table border="1" cellpadding="4" cellspacing="0" class="TableFieldValue">';
	$vHTML  .=  '<tr><td class="TableField">';
	$vHTML  .=  htmlentities(urldecode($HTTP_GET_VARS['FieldName']));
	$vHTML .=  '</td><td class="TableFieldValue">';
	$vHTML .=  '<select name="Lst" size="1">';
	$vHTML .= 'Seleccione un dato <br>';
	$entro = 0;
	while (!$res->EOF) {
	   	$entro = 1;
	   	$vHTML .=  '<option value="'.$res->fields[$vId].'_'.$res->fields[$vDesc].'" >'.$res->fields[$vDesc].'</option>';			   
	   	$res->MoveNext();
		}	   
	$vHTML .=  '</select>';
	$vHTML .=  '</td></tr></table>';
	//if was not found any data, show a message...
	if ($entro == 0)
		{
?>		
		<script language="JavaScript" type="text/JavaScript">
		<!-- 
		alert('<?php echo $MESSAGES['DATA_DONT_FOUND']?>');
		window.close();
		//-->
		</script>
<?php 
		}
	echo $vHTML;			

?>
	<br>
	<input name="Submit" type="submit" onClick="javascript:return Accept(this)" value="Aceptar" >
	<input name="Submit" type="submit" onClick="window.close()" value="Cancelar" >
</form>

<?php 
	require('./inc/script_end.inc.php');
?>
