<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>UrlEncode</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link href="images/dweb.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">




<?php
$vVar = "";
if (!empty($_REQUEST["urlencode"]))
	$vVar = ($_REQUEST["urlencode"]);
if (!empty($_REQUEST["urldecode"]))
	$vVar = ($_REQUEST["urldecode"]);
if (!empty($_REQUEST["utfencode"]))
	$vVar = ($_REQUEST["utfencode"]);
if (!empty($_REQUEST["utfdecode"]))
	$vVar = ($_REQUEST["utfdecode"]);
if (!empty($_REQUEST["htmlencode"]))
	$vVar = ($_REQUEST["htmlencode"]);
if (!empty($_REQUEST["rawurlencode"]))
	$vVar = ($_REQUEST["rawurlencode"]);
if (!empty($_REQUEST["todos"]))
	$vVar = ($_REQUEST["todos"]);
	
if (isset($HTTP_GET_VARS['stclear']))
	{
	$vVar = stripcslashes($vVar);
	}

?>
<form method="get" action="urlencode.php">
<p><input name="urlencode" value="<?php echo htmlentities($vVar);?>" type="text" />
<input value="urlencode" type="submit" /></p>
</form>
<form method="get" action="urlencode.php">
<p><input name="urldecode" value="<?php echo htmlentities($vVar);?>" type="text" />
<input value="urldecode" type="submit" /></p>
</form>
<form method="get" action="urlencode.php">
<p><input name="utfencode" value="<?php echo htmlentities($vVar);?>" type="text" />
<input value="UTF-8 encode" type="submit" /></p>
</form>
<form method="get" action="urlencode.php">
<p><input name="utfdecode" value="<?php echo htmlentities($vVar);?>" type="text" />
<input value="UTF-8 decode" type="submit" /></p>
</form>
<form method="get" action="urlencode.php">
<p><input name="htmlencode" value="<?php echo htmlentities($vVar);?>" type="text" />
<input value="htmlencode" type="submit" /></p>
</form>

<br><table border='1'><tr><td>
<form method="get" action="urlencode.php">
<p><input name="todos" value="<?php echo htmlentities($vVar);?>" type="text" />
<input value="todos" type="submit" />
<br>Quitamos los slaches que se insertan a veces de más? <input name="stclear" type="checkbox" value="1" <?php echo (isset($HTTP_GET_VARS['stclear']))? 'checked' : ''; ?>> 
</p>
</form>
</td></tr></table>

<?php

echo "Parámetros:";
echo "<br><table border='1'>";
foreach($HTTP_GET_VARS as $vvar => $vval)
	{
	print "<tr><td ALIGN='right'>".htmlentities(stripcslashes($vvar))."</td><td>".htmlentities(stripcslashes($vval))."</td></tr>";
	}
echo "</table>";


echo "<br><table border='1'>";
print "<tr><td ALIGN='right'>Resultado: </td><td>";
if (!empty($_REQUEST["urlencode"]))
	print htmlentities(urlencode($vVar));
if (!empty($_REQUEST["urldecode"]))
	print htmlentities(urldecode($vVar));
if (!empty($_REQUEST["utfencode"]))
	print htmlentities(utf8_encode($vVar));
if (!empty($_REQUEST["utfdecode"]))
	print htmlentities(utf8_decode($vVar));
if (!empty($_REQUEST["htmlencode"]))
	print htmlentities(htmlentities($vVar));
if (!empty($_REQUEST["rawurlencode"]))
	print htmlentities(rawurlencode($vVar));
echo "</td></tr>";	

if (!empty($_REQUEST["todos"]))
	{
	print "<tr><td ALIGN='right'>texto: </td><td>".htmlentities($vVar)."</td></tr>";
	print "<tr><td ALIGN='right'>urlencode: </td><td>".htmlentities(urlencode($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>urldecode: </td><td>".htmlentities(urldecode($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>utf8_encode: </td><td>".htmlentities(utf8_encode($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>utf8_decode: </td><td>".htmlentities(utf8_decode($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>htmlentities: </td><td>".htmlentities(htmlentities($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>rawurlencode: </td><td>".htmlentities(rawurlencode($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>addslashes: </td><td>".htmlentities(addslashes($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>stripslashes: </td><td>".htmlentities(stripslashes($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>stripcslashes: </td><td>".htmlentities(stripcslashes($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>quotemeta: </td><td>".htmlentities(quotemeta($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>htmlspecialchars: </td><td>".htmlentities(htmlspecialchars($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>nl2br: </td><td>".htmlentities(nl2br($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>html_entity_decode: </td><td>".htmlentities(html_entity_decode($vVar))."</td></tr>";
	print "<tr><td ALIGN='right'>html_entity_decode ISO-8859-1: </td><td>".htmlentities(html_entity_decode($vVar,ENT_NOQUOTES,'ISO-8859-1'))."</td></tr>";
	print "<tr><td ALIGN='right'>html_entity_decode ISO-8859-15: </td><td>".htmlentities(html_entity_decode($vVar,ENT_NOQUOTES,'ISO-8859-15'))."</td></tr>";
	print "<tr><td ALIGN='right'>html_entity_decode UTF-8: </td><td>".htmlentities(html_entity_decode($vVar,ENT_NOQUOTES,'UTF-8'))."</td></tr>";

	
	}
echo "</table>";

//phpinfo();
?>
<br><a href="http://templo/tools/regextool.php">Expresiones regulares</a> <br>
</body>
</html>