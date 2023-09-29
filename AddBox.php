<?php
// File           GiveMeLst.php / Phyllacanthus
// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//
require('./inc/script_start.inc.php');

echo html_head('Add_Box');
echo html_body();

?>

<script LANGUAGE="JavaScript">
<!--  
sortitems = 1; 

function move(fbox,tbox) {
	for(var i=0; i<fbox.options.length; i++) {
		if(fbox.options[i].selected && fbox.options[i].value != "") {
			var no = new Option();
			no.value = fbox.options[i].value;
			no.text = fbox.options[i].text;
			tbox.options[tbox.options.length] = no;
			fbox.options[i].value = "";
			fbox.options[i].text = "";
	   }
	}
	BumpUp(fbox);
	if (sortitems) SortD(tbox);
}

function BumpUp(box)  {
	for(var i=0; i<box.options.length; i++) {
		if(box.options[i].value == "")  {
			for(var j=i; j<box.options.length-1; j++)  {
				box.options[j].value = box.options[j+1].value;
				box.options[j].text = box.options[j+1].text;
			}
		
			var ln = i;
			break;
	   }
	}
	if(ln < box.options.length)  {
		box.options.length -= 1;
		BumpUp(box);
	   }
}

function SortD(box)  {
	var temp_opts = new Array();
	var temp = new Object();
	for(var i=0; i<box.options.length; i++)  {
		temp_opts[i] = box.options[i];
	}
	for(var x=0; x<temp_opts.length-1; x++)  {
		for(var y=(x+1); y<temp_opts.length; y++)  {
			if(temp_opts[x].text > temp_opts[y].text)  {
				temp = temp_opts[x].text;
				temp_opts[x].text = temp_opts[y].text;
				temp_opts[y].text = temp;
				temp = temp_opts[x].value;
				temp_opts[x].value = temp_opts[y].value;
				temp_opts[y].value = temp;
				}
		   }
	}
	for(var i=0; i<box.options.length; i++)  {
		box.options[i].value = temp_opts[i].value;
		box.options[i].text = temp_opts[i].text;
	   }
}

function Accept(fbox) {
	var str = "";
	for(var i=0; i<fbox.options.length; i++) {
		if(str == "")  {
			str = fbox.options[i].value;
			}
		else {
			str += "<?php echo substr($HTTP_GET_VARS['delimitedchar'],0,-1); ?>"+fbox.options[i].value;
			}
	   }
	window.opener.document.<?php echo $HTTP_GET_VARS['Opener']; ?>.<?php echo $HTTP_GET_VARS['field'];   ?>.value= str;
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
	$vDelimitedChar = substr($HTTP_GET_VARS['delimitedchar'], 0, -1);
	$vFilter = split($vDelimitedChar, $vFilter);
	$vElements = array();
	
	foreach ($vFilter as $tt)  {
		$vElements[] = trim($tt);
		}
	//Query...
	$res = &$dbhandle->Execute($vSql);
	if ($res === FALSE) {
		$db_error = ibase_errmsg();
		}												

?>

<form ACTION METHOD="POST">
<table border="0">
	<tr>
		<td>Opciones</td>
		<td></td>
		<td>Seleccionado</td>
	</tr>
	<tr>
		<td><select multiple size="9" name="list1">
<?php
	//fill the select html option...
	$entro = 0;
	$vHTML = '';
	$vSelected = array();
	while (!$res->EOF) {
		$entro = 1;
		if (in_array (trim($res->fields[$vDesc]), $vElements))  {
	   		$vHTML .=  '<option value="'.$res->fields[$vDesc].'" >'.$res->fields[$vDesc].'</option>';
	   		$vSelected[] = trim($res->fields[$vDesc]);
			}	   
		else	{
			echo '<option value="'.$res->fields[$vDesc].'" >'.$res->fields[$vDesc].'</option>';
			}
		$res->MoveNext();
		}
	foreach ($vElements as $tt) {
		if ((!in_array ($tt, $vSelected)) and (!empty($tt)))  {
			$vHTML .=  '<option value="'.$tt.'" >'.$tt.'</option>';
			}
		}
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

?>		
		</select></td>
		<td>
			<input type="button" value="   &gt;&gt;   " onclick="move(this.form.list1,this.form.list2)" name="B1"><br>
			<input type="button" value="   &lt;&lt;   " onclick="move(this.form.list2,this.form.list1)" name="B2"> </td>
		<td>
			<select multiple size="9" name="list2">
			<?php
				echo $vHTML; 
			?> 
			</select></td>
    </tr>
  </table>
  <br>
 <input name="Submit" type="submit" onClick="javascript:return Accept(this.form.list2)" value="Aceptar" >
 <input name="Submit" type="submit" onClick="window.close()" value="Cancelar" >
</form>

<?php 
	require('./inc/script_end.inc.php');
?>