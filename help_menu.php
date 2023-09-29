<?php

$vLinksOk = array(
		  "XAMPP-local" => "/xampp/",
		  "PhpMyAdmin-local" => "/phpmyadmin/",
		  //"PhpPgAdmin" => "/phppgadmin/",
		  "Ejecutar SQL (MySql) " => "TableView.php?pos=0&mod=empty-MySQL.xml",
		  "Ejecutar SQL (PgSql) " => "TableView.php?pos=0&mod=empty-PgSQL.xml",
		  "Información de los módulos" => "conf_info.php?mod=collman/itf.xml",
  		  "Codificar string" => "string_encode.php",
		  "Codificar texto/archivo" => "tools/convert_collation.php",
  		  "Actualizar versión de FWA" => "inc/configuration_update.php?version'",

  		  "------" => "",
		  "Alemán (frames) " => "configuration/alemanmysql/aleman.php",
		  "CollMan" => "run.php?mod=collman/itf.xml",
		  "Catálogo de sitios web" => "run.php?mod=web-links/sites.xml",

		  "------" => "",
		  "Página del Jardín (jbpr.org)" => "http://jbpr.org",
		  "CollMan en Internet (jbpr.org)" => "http://collman.jbpr.org",
		  "Jardín Botánico de PR en wikipedia" => "http://es.wikipedia.org/wiki/Jard%C3%ADn_Bot%C3%A1nico_de_Pinar_del_R%C3%ADo",
		  "XAMPP (Internet)" => "http://www.apachefriends.org/en/xampp.html",
		  "AnnualCheckList local" => "/annualchecklist",
		  "Annual CheckList (internet)" => "http://www.catalogueoflife.org/annual-checklist/",
		  "Dynamic CheckList (internet)" => "http://www.catalogueoflife.org/dynamic-checklist/search.php",

		  "------" => "",
		  "Expresiones regulares" => "http://www.rexv.org/",
		  "PhpFunctions" => "http://phpfunctions.nfshost.com/",
		  "jQuery Base/Expression/XPath" => "http://jquery.com/docs/Base/Expression/XPath/",
		  "jQuery Demos » AJAX Plugin" => "http://jquery.com/demo/ajax/",
		  "jQuery Visual Documentation***" => "http://www.visualjquery.com/index.xml"

	//        "" => "",
		);


// File           run.php / Phyllacanthus
// Purpose        Edit any structure defined in a xml file, is posible actions as save, insert, search
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        6.10
//

$vscript_start_ini = false;

if (isset($HTTP_GET_VARS['mod']))
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



echo "</head>\n";
echo "<BODY ONLOAD='JS_OnLoad()'>\n";
//echo html_body();


$vHelpOpenNewWindow = true;
$vShowButtons = true;
$vNewButtons = "";
$vHelpChar = '?';  //Char that will be showed as a link to show the help...
$vScriptIni = "";
$vScript = "";

set_time_limit(0);

if  (!empty($HTTP_PARAM_VARS['mod']))
	{
	$pathMod = pathinfo($HTTP_PARAM_VARS['mod']);
	$xmlDirectory = getcwd().'/configuration/'.$pathMod["dirname"].'/';
	$xmlFile = $pathMod['basename'];
	if ($vscript_start_ini)
		{
		if (!empty($HTTP_PARAM_VARS['ConfAction']))
			{
			$vScriptIni .= "\n if (parent.frames.length > 0) {";
			$vScriptIni .= "\n 	parent.bottomFrame.location.href='edit.php?mod=".$HTTP_PARAM_VARS['mod']."';";
			$vScriptIni .= "\n 	}";
			}
		else if (!empty($HTTP_PARAM_VARS['RunAction']))
			{
			$vScriptIni .= "\n if (parent.frames.length > 0) {";
			$vScriptIni .= "\n 	parent.bottomFrame.location.href='run.php?mod=".$HTTP_PARAM_VARS['mod']."';";
			$vScriptIni .= "\n 	}";
			}
		else	{
			$vScriptIni .= "\n if (parent.frames.length > 0) {";
			$vScriptIni .= "\n 	parent.bottomFrame.location.href='help_resume.php?mod=".$HTTP_PARAM_VARS['mod']."#resumen';";
			$vScriptIni .= "\n 	}";
			}
		}
	}
else
	{
	if (isset($HTTP_PARAM_VARS['folder']))
		{
		$pathMod["dirname"] = $HTTP_PARAM_VARS['folder'];
		$xmlDirectory = getcwd().'/configuration/'.$pathMod["dirname"].'/';
		}
	else
		{
		$pathMod["dirname"] = '';
		$xmlDirectory = getcwd().'/configuration/';
		}
	$xmlFile = '';
	}


if ( (isset($HTTP_PARAM_VARS['FolderFile'])) and
	(isset($HTTP_PARAM_VARS['folder'])) and
	(!empty($HTTP_PARAM_VARS['folder'])) and
	($HTTP_PARAM_VARS['folder']!=$pathMod["dirname"]) )
	{
	if (is_dir(getcwd().'/configuration/'.$HTTP_PARAM_VARS['folder']) )
		{
		$pathMod["dirname"] = $HTTP_PARAM_VARS['folder'];
		$xmlDirectory = getcwd().'/configuration/'.$HTTP_PARAM_VARS['folder'].'/';
		$xmlFile = '';
		}
	}



	//| <a href="javascript: open_url_buttom('support/Errores_Collman_20061123.pdf'">reporte 200061123</a>
	//| <a href="javascript: open_url_buttom('');"></a>
?>

	<a href="javascript: open_url_buttom('install/index.html');">Instalaci&oacute;n</a>
	| <a href="javascript: open_url_buttom('help/index.htm');">General</a>
	| <a href="javascript: open_url_buttom('help/fwad.htm');">Sobre FWAD</a>
	| <a href="javascript: open_url_buttom('support/support.php');">Errores</a>
	| <a href="javascript: open_url_buttom('tools/phpinfo.php');">phpinfo()</a>
	| <a href="javascript: open_url_buttom('tools/portcheck.php?port=ftp_Default_Port:21,smtp_Mail_Default_Port:25,http_Apache_Default_Port:80,pop3_Default_Port:110,imap_Default_Port:143,https_Apache_SSL_Port:443,mysql_Default_Port:3306,FileZillaFTP_AdminPort:14147,Tomcat_AJP/1.3_Port:8009,HTTP_Alternate_DWEBPRO/Tomcat_Default_Port:8080,firebird_Default_Port:3050,postgreSql_Default_Port:5432');">Chequea Puertos</a>
	| <a href="javascript: open_url_buttom('run.php?version');">Versi&oacute;n</a>
	| <a href="javascript: open_url_buttom('help_resume.php?updates');">Tareas/changelogs</a>
	| <a href="mailto: support@collman.jbpr.org">Cont&aacute;ctenos</a>

	<hr />
		<form action="help_menu.php#markGo" method="POST" enctype="multipart/form-data" name="Form">

			Carpeta: <input name="folder" type="text" value="<?php echo $pathMod["dirname"];?>">
			<input name="modNew" type="hidden" value="">
			Archivo de configuraci&oacute;n:
			<select name="mod">
			<option value=""></option>
			<?php

			  // Create an object to access the current directory.
			  $directory = dir($xmlDirectory);

			  // Run through all files of the current directory.
			  while ( $entry = $directory->read())
				{
				    // Check whether it's an XML file.
				    if ( eregi("\.xml$", $entry) )
					{
					      // Skip files that end .new.xml
					      if (eregi("\.new\.xml$", $entry)) continue;

					      // Add an entry for this file.
					      echo "<option value=\"".$pathMod["dirname"]."/$entry\"";

					      // Check whether this file is selected.
					      if ( strtolower($entry) == strtolower($xmlFile) )
						{
						// Select the entry.
						echo " selected";
						}

					      // Close the option tag.
					      echo ">$entry</option>\n";
					}
				}

			  // Close the directory.
			  $directory->close();

			?>
			</select>

			<input name="XmlFile" type="submit" value="Ayuda de campos" />
			<input name="ConfAction" type="submit" value="Configurar" />
			<input name="RunAction" type="submit" value="Editar/correr" />

			<hr />
			<select name="links">
			<?php
			foreach ($vLinksOk as $vkey => $vval)
				{
				echo "<option value='$vval'>".htmlentities($vkey)."</option>";
				}
			?>
			</select><input name="doit" type="button" value="<?php echo ">" ?>" onClick="javascript: open_url_buttom(document.Form.links.value);" />


		</form>


	<script language="JavaScript" type="text/JavaScript">
	<!--

	<?php echo $vScript; ?>

	function JS_OnLoad()
	{
		<?php echo $vScriptIni; ?>
		CheckSqlChange();

	}


	function open_url_buttom(vurl)
	{
		if (vurl == "")
			{
			}
		else
			{
			if (parent.frames.length > 0) {
				parent.bottomFrame.location.href= vurl;
				}
			}
	}

	//-->
	</script>

<?php



//require('./inc/script_end.inc.php');


?>

</body>
</html>





