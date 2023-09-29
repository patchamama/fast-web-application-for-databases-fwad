<?php

$vResultFile = "de.inc.php";

$vContentFile = chr(60).chr(63)."php\n\n";

// Esta herramienta lee dos archivos de mensajes en lenguajes diferentes y los combina en uno 
// tomando como base el primero y sustituyéndolo por el segundo...

require('en.inc.php');

	$info_strings1 = $info_strings;
	$MESSAGES1 = $MESSAGES;
	$message_strings1 = $message_strings;
	$button_strings1 = $button_strings;
	$OutputType_strings1 = $OutputType_strings;
	$query_strings1 = $query_strings;
	$_REPORT1 = (isset($_REPORT)) ? $_REPORT: array();


require($vResultFile);

	$info_strings2 = $info_strings;
	$MESSAGES2 = $MESSAGES;
	$message_strings2 = $message_strings;
	$button_strings2 = $button_strings;
	$OutputType_strings2 = $OutputType_strings;
	$query_strings2 = $query_strings;
	$_REPORT2 = (isset($_REPORT)) ? $_REPORT: array();

$_REPORT = "";

// charset encoding  for html output
$vContentFile .= '$charset = "iso-8859-1";'."\n";

//=======================================================================
	$vContentFile .= "\n";
	foreach ($info_strings2 as $vvar => $vval)
		{
		$vval = ereg_replace("\'",'\"', $vval);
		$vContentFile .= '$info_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
		}
	//buscamos los valores que están en el primer array (base) y no se han agregado en el segundo
	$vValuesFound = false;
	foreach ($info_strings1 as $vvar => $vval)
		{
		if (!array_key_exists($vvar, $info_strings2))
			{
			$vContentFile .= (!$vValuesFound)? "//Values not translated...(Doit!)...\n" : "";
			$vValuesFound = true;
			$_REPORT .= '//$info_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			$vContentFile .= '$info_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			}
		}

//=======================================================================
	$vContentFile .= "\n";
	foreach ($MESSAGES2 as $vvar => $vval)
		{
		$vval = ereg_replace("\'",'\"', $vval);
		$vContentFile .= '$MESSAGES["'.$vvar.'"] '."= \t\t\t'$vval';\n";
		}
	//buscamos los valores que están en el primer array (base) y no se han agregado en el segundo
	$vValuesFound = false;
	foreach ($MESSAGES1 as $vvar => $vval)
		{
		if (!array_key_exists($vvar, $MESSAGES2))
			{
			$vContentFile .= (!$vValuesFound)? "//Values not translated...(Doit!)...\n" : "";
			$vValuesFound = true;
			$_REPORT .= '//$MESSAGES["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			$vContentFile .= '$MESSAGES["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			}
		}

//=======================================================================
	$vContentFile .= "\n";
	foreach ($message_strings2 as $vvar => $vval)
		{
		$vval = ereg_replace("\'",'\"', $vval);
		$vContentFile .= '$message_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
		}
	//buscamos los valores que están en el primer array (base) y no se han agregado en el segundo
	$vValuesFound = false;
	foreach ($message_strings1 as $vvar => $vval)
		{
		if (!array_key_exists($vvar, $message_strings2))
			{
			$vContentFile .= (!$vValuesFound)? "//Values not translated...(Doit!)...\n" : "";
			$vValuesFound = true;
			$_REPORT .= '//$message_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			$vContentFile .= '$message_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			}
		}

//=======================================================================
	$vContentFile .= "\n";
	foreach ($button_strings2 as $vvar => $vval)
		{
		$vval = ereg_replace("\'",'\"', $vval);
		$vContentFile .= '$button_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
		}
	//buscamos los valores que están en el primer array (base) y no se han agregado en el segundo
	$vValuesFound = false;
	foreach ($button_strings1 as $vvar => $vval)
		{
		if (!array_key_exists($vvar, $button_strings2))
			{
			$vContentFile .= (!$vValuesFound)? "//Values not translated...(Doit!)...\n" : "";
			$vValuesFound = true;
			$_REPORT .= '//$button_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			$vContentFile .= '$button_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			}
		}

//=======================================================================
	$vContentFile .= "\n";
	foreach ($OutputType_strings2 as $vvar => $vval)
		{
		$vval = ereg_replace("\'",'\"', $vval);
		$vContentFile .= '$OutputType_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
		}
	//buscamos los valores que están en el primer array (base) y no se han agregado en el segundo
	$vValuesFound = false;
	foreach ($OutputType_strings1 as $vvar => $vval)
		{
		if (!array_key_exists($vvar, $OutputType_strings2))
			{
			$vContentFile .= (!$vValuesFound)? "//Values not translated...(Doit!)...\n" : "";
			$vValuesFound = true;
			$_REPORT .= '//$OutputType_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			$vContentFile .= '$OutputType_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			}
		}

//=======================================================================
	$vContentFile .= "\n";
	foreach ($query_strings2 as $vvar => $vval)
		{
		$vval = ereg_replace("\'",'\"', $vval);
		$vContentFile .= '$query_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
		}
	//buscamos los valores que están en el primer array (base) y no se han agregado en el segundo
	$vValuesFound = false;
	foreach ($query_strings1 as $vvar => $vval)
		{
		if (!array_key_exists($vvar, $query_strings2))
			{
			$vContentFile .= (!$vValuesFound)? "//Values not translated...(Doit!)...\n" : "";
			$vValuesFound = true;
			$_REPORT .= '//$query_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			$vContentFile .= '$query_strings["'.$vvar.'"] '."= \t\t\t'$vval';\n";
			}
		}

//=======================================================================
		{
		$vContentFile .= "\n\n //Report for this conversion \n";
		$vContentFile .= $_REPORT;
		}


$vContentFile .= chr(63).chr(62);


echo "Salida a archivo: '$vResultFile'<br />";
echo nl2br($vContentFile);

$fh = fopen($vResultFile, 'w') or die("Error creando archivo !!!");
fwrite($fh, $vContentFile);
fclose($fh);


?>