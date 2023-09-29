<?php
	$vVar = ($_REQUEST["All"]);

if (isset($_REQUEST['stclear']))
	{
	$vVar = stripslashes($vVar);
	}

$vfunctions = array("All","urlencode","urldecode","utf8_encode","utf8_decode","htmlentities","htmlspecialchars","strtolower",
					"rawurlencode","addslashes","stripslashes","stripcslashes","quotemeta","htmlspecialchars","nl2br",
					"base64_decode","base64_encode", "md5","sha1","crc32","bin2hex","convert_uuencode",
					"soundex","strip_tags");

//Download Text					
if ( (isset($_POST["functaction"])) && (isset($_POST["Download"])) )
	{
		$vfunc = @($_POST["functaction"]);
		$vcode = ($vfunc($vVar));
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-type: application/force-download");
		//header("Content-Disposition: attachment; filename=\"".basename($file)."\";" );
		header("Content-Disposition: attachment; filename=\"".rand().".txt\";" );
		header("Content-Transfer-Encoding: binary");
		//header("Content-Length: ".filesize($file));
		header("Content-Length: ".strlen($vcode));
		//readfile("$file");
		echo $vcode;
		exit();
		//htaccess with apache
		//	<Files *.jpeg>
		//	ForceType application/octet-stream
		//	Header set Content-Disposition attachment
		//	</Files>	
	}

//Show content directly				
if ( (isset($_POST["functaction"])) && (isset($_POST["showpage"])) )
	{
		$vfunc = @($_POST["functaction"]);
		$vcode = ($vfunc($vVar));
		echo $vcode;
		exit();
		//htaccess with apache
		//	<Files *.jpeg>
		//	ForceType application/octet-stream
		//	Header set Content-Disposition attachment
		//	</Files>	
	}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>String-coding</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link href="images/dweb.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br><table border='1'><tr><td>
<form name="mainform" method="post" action="string_encode.php">
	Text:<br />	
	<textarea name='All' rows='15' cols='120'><?php echo htmlentities($vVar);?></textarea>
	<br />
	<select name="Order">
		<?php foreach ($vfunctions as $vfunct) {
			echo '<option value="'.$vfunct.'" '.(( (isset($_REQUEST["Order"])) && ($_REQUEST["Order"]==$vfunct)) ? "selected" : "").'>'.$vfunct.'</option>'; }
		?>
	</select>
	<input value="Submit" type="submit" />
	<br>Delete the slasches characters that sometimes are inserted? <input name="stclear" type="checkbox" value="1" <?php echo (isset($_REQUEST['stclear']))? 'checked' : ''; ?>> 
	</p>
</td></tr></table>

<?php

echo "Parameters:";
echo "<br><table border='1'>";
foreach($_POST as $vvar => $vval)
	{
	if ($vvar!="All")
		print "<tr><td ALIGN='right'>".htmlentities(stripcslashes($vvar))."</td><td>".htmlentities(stripcslashes($vval))."</td></tr>";
	}
	
echo "</table>";


echo "<br><table border='1'>";
echo "<tr><td></td><td ALIGN='right'>Function</td><td>Value</td></tr>";
if (isset($_POST["Order"]))
	{
	echo "<tr><td></td><td ALIGN='right'>String: </td><td>".htmlentities($vVar)."</td></tr>";
	foreach ($vfunctions as $vfunct) {
		if ( ( ($_POST["Order"]=="All") || ($_POST["Order"]==$vfunct) ) && ($vfunct != "All") )
			{
			$vfunc = @($vfunct);
			$vval = ($vfunc($vVar));
			echo "<tr><td><input name='functaction' type='radio' value='$vfunct' onclick='javascript: document.submit(); ' /></td><td ALIGN='right'><a href='javascript: void(0);' title='http://www.php.net/$vfunct' onclick='javascript: window.open(\"http://www.php.net/$vfunct\")'>$vfunct:</a> </td>
				  <td>".htmlentities($vval)."</td></tr>";
			}
		}	
	if ($_POST["Order"]=="All") 
		{
		print "<tr><td></td><td ALIGN='right'>html_entity_decode: </td><td>".htmlentities(html_entity_decode($vVar))."</td></tr>";
		print "<tr><td></td><td ALIGN='right'>html_entity_decode ISO-8859-1: </td><td>".htmlentities(html_entity_decode($vVar,ENT_NOQUOTES,'ISO-8859-1'))."</td></tr>";
		print "<tr><td></td><td ALIGN='right'>html_entity_decode ISO-8859-15: </td><td>".htmlentities(html_entity_decode($vVar,ENT_NOQUOTES,'ISO-8859-15'))."</td></tr>";
		print "<tr><td></td><td ALIGN='right'>html_entity_decode UTF-8: </td><td>".htmlentities(html_entity_decode($vVar,ENT_NOQUOTES,'UTF-8'))."</td></tr>";
		}
	}
echo "<tr><td></td><td></td><td><input type='submit' name='Download' value='Download' /><input type='submit' name='showpage' value='Show Page' /></td></tr>";	
echo "</table>";

//phpinfo();
?>
</form>
</body>
</html>