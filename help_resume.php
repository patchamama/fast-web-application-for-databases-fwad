<?php

// File           run.php / Phyllacanthus
// Purpose        Edit any structure defined in a xml file, is posible actions as save, insert, search
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        6.10
//

$vscript_start_ini = false;
$vHTML_Out = "";

if (isset($_GET['mod']))
	{
	require('./inc/script_start.inc.php');
	$vscript_start_ini = true;
	}
elseif (isset($HTTP_POST_VARS['mod']))
	{
	require('./inc/script_start.inc.php');
	$vscript_start_ini = true;
	}
else	{
	$HTTP_PARAM_VARS['mod']="collman/itf.xml";
	require('./inc/configuration.inc.php');
	require('./inc/lang/es.inc.php');
	require('./inc/session.inc.php');
	require('./inc/functions.inc.php');
	include('./inc/debug_funcs.inc.php');
	set_error_handler('error_handler');
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>CollMan: Manejo de Colecciones Biológicas</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1">
	<meta name="Description" content="Gestión de información biológica, ecológica, etnobotánica, taxonómica, literatura, Sistemas de informacion geográfica (SIG).">
	<meta name="Keywords" content="CollMan: Manejo de Colecciones Biológicas, bioinformática, ecología, itf, hispid, colecciones biológicas, jardín botánico, herbario, fast web application, fwa, SIG, GIS">
	<meta name="author" content="Red de Jardínes Botánicos Cubanos">
	<meta name="owner" content="JBPR/JBN Licencia GPL.">
	<meta name="robots" content="all" />



	<script type="text/javascript" src="lib/js/jquery.js"></script>
	<script type="text/javascript" src="lib/js/thickbox.js"></script>
	<script type="text/javascript" src="lib/js/thickbox_wait.js"></script>

	<style type="text/css" media="all">

		@import "styles/thickbox.css";
		@import "styles/default.css";
	</style>

	<!-- Preload de image images/loadingAnimation.gif -->
	<SCRIPT LANGUAGE="JavaScript">
	image1 = new Image();
	image1.src = "images/loadingAnimation.gif";
	</script>

<?php





$vHelpOpenNewWindow = true;
$vShowButtons = true;
$vNewButtons = "";
$vHelpChar = '?';  //Char that will be showed as a link to show the help...
$vActionLog = "";

echo "</head>\n";
echo "<BODY ONLOAD='JS_OnLoad()'>\n";
echo '<form name="Form">';

if ( isset($_GET['updates']) )
	{
	$directory = dir(getcwd()."/updates");
	//print_r($directory);
	//echo getcwd()."/updates";
	  while ( $entry = $directory->read())
	  	{

   		if ( eregi("\.txt$", $entry) )
			{
			$vVarName = ereg_replace('\.',"_",$entry);
			echo "<br />Archivo: ".$vVarName;

			$vFile = getcwd()."/updates/".$entry;
			if ( (isset($_GET["save_".$vVarName])) && isset($_GET["text_".$vVarName]) )
				{
				$vActionLog .= "<br/ > El archivo ".$entry." fue grabado...";
				$fd = fopen ($vFile, "w");
				$vTxt = $_GET["text_".$vVarName];
				$vTxt  = stripcslashes($vTxt );
				fwrite($fd, $vTxt);
				fclose ($fd);
				}

			?>
		        <table border="1">
			        <tr>
			            <td width="850">
			                <p>
			                <?php
			                $buffer = "";
			                if (!isset($_GET["text_".$vVarName]))
			                	{
			                	$fd = fopen ($vFile, "r");
								while (!feof($fd))
									{
									$buffer .= fgets($fd);
									//echo  $buffer."<br>";
									}
								fclose ($fd);
								}
							else
								{
								$buffer = urldecode(stripcslashes($_GET["text_".$vVarName]));
								}
							$buffer = stripcslashes($buffer);
							
					echo '<textarea name="text_'.$vVarName.'" cols="120" rows="25">'.htmlentities($buffer).'</textarea>';

					
			                ?>
			                </p>
			            </td>
			        </tr>
    			</table>
    			<?php
    			echo '       <input name="save_'.$vVarName.'" type="submit" value="Grabar">';
				?>
    			<br />
    			<hr />
    			<?php

			}
		}
	echo '       <input name="updates" type="hidden" value="">';
	$directory->close();

	}
else if ($vscript_start_ini)
	{

	$vPrefix = "itf";

	//http://localhost/fwa/_genera.php?mod=collman/itf.xml

	$vvHelpIndexInfo = array();
	$vvHelpIndexInfoChecked = array();
	$vvHelpIndexEmptyCant = -1;
	$vvHelpIndexEmpty = array();

	for ($a=0; $a < count($s_xml_conf['elements']); $a++)
		{
		$vContent = get_value($s_xml_conf['elements'][$a],'content');
		$vQuerylabel = get_value($s_xml_conf['elements'][$a],'querylabel');
		$vInfoShow = (!empty($vQuerylabel)) ? $vQuerylabel : $vContent;
		$vHelp = get_value($s_xml_conf['elements'][$a],'help');
		$vtable = get_value($s_xml_conf['elements'][$a],'table');
		$vfield = get_value($s_xml_conf['elements'][$a],'field');
		$vtype = get_value($s_xml_conf['elements'][$a],'type');
		if (!empty($vHelp))
			{
			$vvHelpIndexInfo[$vHelp]["content"] = $vInfoShow;
			$vvHelpIndexInfo[$vHelp]["table"] = $vtable;
			$vvHelpIndexInfo[$vHelp]["field"] = $vfield;
			$vvHelpIndexInfo[$vHelp]["type"] = $vtype;
			$vvHelpIndexInfo[$vHelp]["elementid"] = $a;

			}
		else
			{
			if ( (!empty($vtable)) && (!empty($vfield)) && ($vtype!="hidden"))
				{
				$vvHelpIndexEmptyCant++;
				$vvHelpIndexEmpty[$vvHelpIndexEmptyCant]["table"] = $vtable;
				$vvHelpIndexEmpty[$vvHelpIndexEmptyCant]["field"] = $vfield;
				$vvHelpIndexEmpty[$vvHelpIndexEmptyCant]["content"] = $vInfoShow;
				$vvHelpIndexEmpty[$vvHelpIndexEmptyCant]["type"] = $vtype;
				$vvHelpIndexEmpty[$vvHelpIndexEmptyCant]["elementid"] = $a;
				}
			}
		}
	$vHelpPath = $pathMod."help/".$s_xml_conf['lang']."/";



	if (strpos($HTTP_PARAM_VARS['mod'],'/itf.xml'))  //módulo de accesiones vivas (ITF.xml)
		{
		$vHTML_Out .=  "<a href='".$vHelpPath.$vPrefix."_help.htm"."'>".$vHelpPath.$vPrefix."_help.htm</a>";
		if (file_exists($vHelpPath.$vPrefix."_help.htm"))
			{
			$vvlineas = "";
			$vvfile = "";
			$vvlastLetter = "";
			$vvIndex = "";
			$vvlinks =
				array('A' => $vPrefix."_A_head.php",
				      'B' => $vPrefix."_B_Accesion.php",
				      'C' => $vPrefix."_C_Name.php",
				      'D' => $vPrefix."_D_Verif.php",
				      'E' => $vPrefix."_E_Source.php",
				      'F' => $vPrefix."_F_Origin.php",
				      'G' => $vPrefix."_G_Extras.php"
				      );
			$vvdesc =
					array('A' => "A. Datos de identificación de archivos",
					      'B' => "B. Datos de accesión",
					      'C' => "C. NOMBRE DE LA PLANTA",
					      'D' => "D. DATOS de  VERIFICACIÓN",
					      'E' => "E. FUENTE DE LOS DATOS",
					      'F' => "F. LUGAR DE ORIGEN",
					      'G' => "G. DATOS ADICIONALES"
				      );

			$fd = fopen ($vHelpPath.$vPrefix."_help.htm", "r");
			while (!feof($fd))
				{
				$buffer = fgets($fd);
				$match = "";

				$vvvposi = (stripos($buffer,'p class="Descr1"'))+0;
				if ( ($vvvposi) )
					{
					//echo ($vvvposi).htmlentities($buffer);
					$vHTML_Out .=  "<hr />";
					if (!empty($vvfile))
						{
						//grabamos...
						$fd1 = fopen ($vHelpPath.$vPrefix."_".$vvfile.".php", "w");
						$vvlineas = "<html><head><title>".$vvfile."</title></head><body>".$vvlineas."</body></html>";
						fwrite($fd1,$vvlineas);
						fclose ($fd1);

						$vvIndexShow = "";
						if (isset($vvHelpIndexInfo[$vPrefix."_".$vvfile.".php"]["content"]))
							{
							$vvIndexShow = " - ".$vvHelpIndexInfo[$vPrefix."_".$vvfile.".php"]["content"];
							$vvHelpIndexInfoChecked[$vPrefix."_".$vvfile.".php"] = "";
							}
						$vvIndex .= "\n"."<a href='"."<"."?"."php".' echo (isset($_GET["helpath"])) ? $_GET["helpath"] : ""; '."?".">".$vPrefix."_".$vvfile.".php"."'>".htmlentities($vvfile)."</a> $vvIndexShow <br />";
						$vvlineas = "";
						}

					}


				if (@preg_match('|.*<a name="(\w+)">.*|im', $buffer, $match))
					{
					$vvfile = $match[1];
					$vHTML_Out .=  "<h1>".$match[1]."</h1>";
					}
				if (@preg_match('|.*lang="ES-TRAD">([A-G])\..*<.*|im', $buffer, $match))
					{
					//$vvfile = $match[1];
					$vHTML_Out .=  "<h3>[".$match[1]."]</h3>";
					$vlet = $match[1];

					$vHTML_Out .=  "<a href='"."<"."?"."php".' echo (isset($_GET["helpath"])) ? $_GET["helpath"] : ""; '."?".">".$vvlinks[$vlet]."'>".htmlentities($vvdesc[$vlet])."</a> <hr />";
					$vvlineas = $vvlineas."\n"."<h3><a href='"."<"."?"."php".' echo (isset($_GET["helpath"])) ? $_GET["helpath"] : ""; '."?".">".$vvlinks[$vlet]."'>".htmlentities($vvdesc[$vlet])."</a></h3> <hr />";
					if ($vvlastLetter!=$vlet)
						{
						$vvIndex .= "\n"."<h3><a href='"."<"."?"."php".' echo (isset($_GET["helpath"])) ? $_GET["helpath"] : ""; '."?".">".$vPrefix."_".$vvlinks[$vlet]."'>".htmlentities($vvdesc[$vlet])."</a></h3><hr />";
						$vvlastLetter=$vlet;
						}

					}
				$vvlineas = $vvlineas."\n".$buffer;
				$vHTML_Out .=  $buffer;
				}

			if (!empty($vvfile))
				{
				//grabamos...
				$fd1 = fopen ($vHelpPath.$vPrefix."_".$vvfile.".php", "w");
				$vvlineas = "<html><head><title>".$vvfile."</title></head><body>".$vvlineas."</body></html>";
				fwrite($fd1,$vvlineas);
				fclose ($fd1);

				$vvIndexShow = "";
				if (isset($vvHelpIndexInfo[$vPrefix."_".$vvfile.".php"]["content"]))
					{
					$vvIndexShow = " - ".$vvHelpIndexInfo[$vPrefix."_".$vvfile.".php"]["content"];
					$vvHelpIndexInfoChecked[$vPrefix."_".$vvfile.".php"] = "";
					}
				$vvIndex .= "\n"."<a href='"."<"."?"."php".' echo (isset($_GET["helpath"])) ? $_GET["helpath"] : ""; '."?".">".$vPrefix."_".$vvfile.".php"."'>".htmlentities($vvfile)."</a> $vvIndexShow <br />";
				$vvlineas = "";
				}

			fclose ($fd);

			$fd1 = fopen ($vHelpPath.$vPrefix."_index.php", "w");
			$vvIndex = "<html><head><title>Ayuda</title></head><body>".$vvIndex."</body></html>";
			fwrite($fd1,$vvIndex);
			fclose ($fd1);

			//salida de archivo rastreado
			echo  "Archivo de salida temporal generado: <a href='".$vHelpPath."_TMP".$vPrefix."_help_out.php"."'>".$vHelpPath."_TMP".$vPrefix."_help_out.php</a>";
			$fd1 = fopen ($vHelpPath."_TMP".$vPrefix."_help_out.php", "w");
			fwrite($fd1,$vHTML_Out);
			fclose ($fd1);
			//echo '<textarea name="html_out" cols="100" rows="20" onChange="javascript: CheckSqlChange();">'.htmlentities($vHTML_Out)."</textarea>";

			}
		else
			{
			echo "...El archivo no existe...";
			}
		}
	//crear ayuda online
	//ver ayuda


	//$vvHelpIndexInfo[$vHelp]["content"] = $vInfoShow;
	//$vvHelpIndexInfo[$vHelp]["table"] = $vtable;
	//$vvHelpIndexInfo[$vHelp]["field"] = $vfield;

	echo "<br /><a name='resumen' /><h1>Tabla resumen</h1><hr />";
	echo "<table border=1>";
	echo "<tr><td>Config.</td><td><b>Archivo ayuda</b></td><td><b>Contenido</b></td><td><b>Generado</b></td><td><b>Tabla</b></td><td><b>Campo</b></td><td><b>Visualiza archivo</b></td><td>Tipo</td></tr>";




	foreach ($vvHelpIndexInfo as $vkey => $vvalue)
		{
		$vvChecked = (isset($vvHelpIndexInfoChecked[$vkey])) ? "<img src='images/ball.gray.gif' border='0'>" : "<img src='images/ball.red.gif' border='0'>";

		$vvvFileName = (strpos($vkey,'#')) ? substr($vkey, 0,(strpos($vkey,'#'))) : $vkey;
		$vvFileExist = (file_exists($vHelpPath.$vvvFileName)) ? '<a href="'.$vHelpPath.$vkey.'?keepThis=true&TB_iframe=true&height=250&width=700" title="'.$message_strings['Help'].'" class="thickbox"><img src="images/index.gif"border="0"></a>' : "<img src='images/alert.red.gif' border='0'>";

		echo "<tr><td><a href='"."edit.php?mod=".$HTTP_PARAM_VARS['mod']."&element=".$vvHelpIndexInfo[$vkey]["elementid"]."'><img src='images/patch.gif' border='0'></a></td><td>$vkey</td><td>".$vvHelpIndexInfo[$vkey]["content"]."</td><td>$vvChecked</td><td>".$vvHelpIndexInfo[$vkey]["table"]."</td><td>".$vvHelpIndexInfo[$vkey]["field"]."</td><td>$vvFileExist</td><td>".$vvHelpIndexInfo[$vkey]["type"]."</td></tr>";
		}

	//$vvHelpIndexEmptyCant++;
	//$vvHelpIndexEmpty[$vvHelpIndexEmptyCant]["table"] = $vtable;
	//$vvHelpIndexEmpty[$vvHelpIndexEmptyCant]["field"] = $vfield;
	//$vvHelpIndexEmpty[$vvHelpIndexEmptyCant]["content"] = $vInfoShow;
	for ($a=0; $a <=$vvHelpIndexEmptyCant; $a++)
		{

		echo "<tr><td><a href='"."edit.php?mod=".$HTTP_PARAM_VARS['mod']."&element=".$vvHelpIndexEmpty[$a]["elementid"]."'><img src='images/patch.gif' border='0'></a><td><img src='images/alert.red.gif' border='0'></td><td>".$vvHelpIndexEmpty[$a]["content"]."</td><td><img src='images/ball.red.gif' border='0'></td><td>".$vvHelpIndexEmpty[$a]["table"]."</td><td>".$vvHelpIndexEmpty[$a]["field"]."</td><td><img src='images/alert.red.gif' border='0'></td><td>".$vvHelpIndexEmpty[$a]["type"]."</td></tr>";
		}

	echo "</table>";
	//print_r($vvHelpIndexInfo)

	echo "<hr />";
	echo "S&iacute;mbolos usados";
	echo "<table>";
	echo "<tr><td><img src='images/patch.gif' border='0'></td><td>Editar</td></tr>";
	echo '<tr><td><img src="images/index.gif"border="0"></td><td>Visualizar</td></tr>';
	echo "<tr><td><img src='images/alert.red.gif' border='0'></td><td>Error! Si es posible, posee un vinculo para arreglarlo...</td></tr>";
	echo "<tr><td><img src='images/ball.gray.gif' border='0'></td><td>Si/hecho</td></tr>";
	echo "<tr><td><img src='images/ball.red.gif' border='0'></td><td>No/imposible</td></tr>";



	echo "</table>";

	echo "<hr />";
	require('./inc/script_end.inc.php');

		if (file_exists ($xmlConfDirectory."foot.php"))
				{
				require($xmlConfDirectory."foot.php");
				}
	}

	echo $vHTML_Out;
	echo '</form>';
	echo $vActionLog;
//	echo "<pre>";
//	print_r($_GET);
//	echo "</pre>";
?>

</body>
</html>





