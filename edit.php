<?php
//lín 1972
// línea 1097

// File           run.php / Phyllacanthus
// Purpose        Edit any structure defined in a xml file, is posible actions as save, insert, search
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//

require('./inc/script_start.inc.php');
//require('./lib/adodb/adodb-datadict.inc.php');

echo $_POST["mod"];
//Check if the configuration folder exist and if not exist, create it...
if ((isset($_POST["FolderFile"])) && (isset($_POST["folder"])))
	{
	$xmlDirectory = getcwd().'/'.$Confs["configuration-path"];
	echo $xmlDirectory.$_POST["folder"];
	if (!is_dir($xmlDirectory.$_POST["folder"]))
		{
		mkdir($xmlDirectory.$_POST["folder"]);
		if (file_exists($xmlDirectory.$_POST["mod"]))
			{
			if (!copy($xmlDirectory.$_POST["mod"], $xmlDirectory.$_POST["folder"].$_POST["mod"])) {
				echo "failed to copy $file...\n";
				}
			}
		}
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

set_time_limit(0);

$vScriptIni = '';
$vScript = '';
$vCheckTableChange = '';
$vCheckTypeChange = '';
$vCheckSqlChange = '';
$vScriptGlobal = '';
$vHelpChar = '?';
$vSection = 'elements';
$vHead = '';
$vHTML = '';
$vMenu = '';
$vButtons = '';
$vJsDynamicOptionList = false;
$vSectionName = array(
			'connection' => 'Connection',
			'tables' => 'Tables',
			'Searchs' => 'Search',
			'elements' => 'Elements',
			'Links' => 'Links',
			'configuration' => 'Configuration',
			'Queries' => 'Queries/tools',
			'BioCase' => 'BioCase Tool',			
			'ViewXml' => 'View xml'
			);

$vTypesDb = array(
		'access'=>'Microsoft Access/Jet. You need to create an ODBC DSN (B-Windows only)',
		'ibase'=>'Interbase 6 or earlier (B-Unix and Windows)',
		'firebird'=>'Firebird version of interbase (C-Unix and Windows)',
		'mysql'=>'MySQL without transaction support (Unix and Windows)',
		'mysqlt'=>'MySQL with transaction support (Unix and Windows)',
		'postgres'=>'Generic PostgreSQL driver. Currently identical to postgres7 driver (A-Unix and Windows)',
		'postgres64'=>'For PostgreSQL 6.4 and earlier which does not support LIMIT internally (A-Unix and Windows)',
		'postgres7'=>'PostgreSQL which supports LIMIT and other version 7 functionality (A-Unix and Windows)',
		'postgres8'=>'PostgreSQL which supports version 8 functionality (A-Unix and Windows)'
		);


foreach($HTTP_PARAM_VARS as $vvar => $vval)
	{
	//if (strpos($vvar,'__')>0)
		{
		$HTTP_PARAM_VARS[$vvar] = stripcslashes($vval);
		}
	}

//Percent of the cols in the form
$vLeftPercent = 20;  //field name percent
$vRightPercent = 100-$vLeftPercent; //value percent...
$vFormPercent = 100;
//$HTTP_PARAM_VARS['element'] = (isset($HTTP_PARAM_VARS['element'])) ? $HTTP_PARAM_VARS['element'] : '0';
//$HTTP_PARAM_VARS['LastElement'] = (isset($HTTP_PARAM_VARS['LastElement'])) ? $HTTP_PARAM_VARS['element'] : '0';

$HTTP_PARAM_VARS['data'] = (isset($HTTP_PARAM_VARS['data'])) ? $HTTP_PARAM_VARS['data'] : 'elements';
if ((isset($HTTP_PARAM_VARS['data'])) and
   (!IsEmpty($HTTP_PARAM_VARS['data'])))
	{
	$vSection = $HTTP_PARAM_VARS['data'];
	}

if ((isset($HTTP_PARAM_VARS['UrlGo'])) and
   (!IsEmpty($HTTP_PARAM_VARS['UrlGo'])))
	{
	$vScriptIni .= "\n location.href=".'"'.$HTTP_PARAM_VARS['UrlGo'].'";';
	}

$vHead .= '<br /><br /><input name="UrlGo" type="hidden" value="">';
$vHead .= '<input name="data" type="hidden" value="'.$vSection.'">';
$vHTML .=  '<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableForm">';

if (!empty($HTTP_PARAM_VARS['mod']))
	{
	$pathMod = pathinfo($HTTP_PARAM_VARS['mod']);
	$xmlDirectory = getcwd().'/'.$Confs["configuration-path"].$pathMod["dirname"].'/';
	$xmlFile = $pathMod['basename'];
	}
else
	{
	if (isset($HTTP_PARAM_VARS['folder']))
		{
		$pathMod["dirname"] = $HTTP_PARAM_VARS['folder'];
		$xmlDirectory = getcwd().'/'.$Confs["configuration-path"].$pathMod["dirname"].'/';
		}
	else
		{
		$pathMod["dirname"] = '';
		$xmlDirectory = getcwd().'/'.$Confs["configuration-path"];
		}
	$xmlFile = '';
	}


if ( (isset($HTTP_PARAM_VARS['FolderFile'])) and
	(isset($HTTP_PARAM_VARS['folder'])) and
	(!empty($HTTP_PARAM_VARS['folder'])) and
	($HTTP_PARAM_VARS['folder']!=$pathMod["dirname"]) )
	{
	if (is_dir(getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['folder']) )
		{
		$pathMod["dirname"] = $HTTP_PARAM_VARS['folder'];
		$xmlDirectory = getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['folder'].'/';
		$xmlFile = '';
		}
	}

if ( (isset($HTTP_PARAM_VARS['modNew'])) and
	(!empty($HTTP_PARAM_VARS['modNew'])) and
	(!file_exists(getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['modNew'])))
	{
	$vpath = pathinfo($HTTP_PARAM_VARS['modNew']);

	if ( (!isset($vpath["extension"])) or ((isset($vpath["extension"])) and (strtolower($vpath["extension"])!='xml')) )
		{
		$HTTP_PARAM_VARS['modNew'] = $HTTP_PARAM_VARS['modNew'].'.xml';
		}
	if ( ($fp = fopen(getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['modNew'], 'w')) )
		{
		fclose ($fp);
		$HTTP_PARAM_VARS['mod'] = $HTTP_PARAM_VARS['modNew'];

		$pathMod = pathinfo($HTTP_PARAM_VARS['mod']);
		$xmlDirectory = getcwd().'/'.$Confs["configuration-path"].$pathMod["dirname"].'/';
		$xmlFile = $pathMod['basename'];
		$vScriptIni .= "\n document.Form.submit(); ";
		}
	else	{
		$error .= '<br />Was not possible to create the file '.getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['modNew'];
		}
	}


		?>
		
		<form action="edit.php#markGo" method="POST" enctype="multipart/form-data" name="Form">
		Configuration Folder: <input name="folder" type="text" value="<?php echo $pathMod["dirname"];?>">
		<input name="modNew" type="hidden" value="">
		<input name="FolderFile" type="submit" value="<?php echo $button_strings['Select']."/".$button_strings["Create"]; ?>" onClick="">
		Configuration file: <select name="mod">
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
		<input name="XmlFile" type="button" value="<?php echo $button_strings['Select']?>" onClick="javascript: ChangeMod(); ">

		<?php

if  ((isset($s_xml_conf)) && (count($s_xml_conf['configuration'])>0))
	{
	$vExist = false;
	for ($i = 0; $i < count($s_xml_conf['configuration']); $i++)
		{
		if (get_value($s_xml_conf['configuration'][$i],'tagname')=='lang')
			{
			if (!$vExist)
				{
				echo  '	'.$button_strings['Language'].'   <select name="Language" size="1" >'."\n";
				$vExist = true;
				}
			if (get_value($s_xml_conf['configuration'][$i],'value')==$ADODB_LANG)
				{
				echo  '<option value="'.get_value($s_xml_conf['configuration'][$i],'value').'" selected>'.get_value($s_xml_conf['configuration'][$i],'content')."</option>\n";
				}
			else	{
				echo  '<option value="'.get_value($s_xml_conf['configuration'][$i],'value').'" >'.get_value($s_xml_conf['configuration'][$i],'content')."</option>\n";
				}
			}
		}
	if ($vExist)
		{
		echo  "</select>\n";
		}
	}

if (!empty($xmlFile))
	{

	//Insert a element
	if (isset($HTTP_PARAM_VARS['Insert']))
		{
		if (($HTTP_PARAM_VARS['data']=='elements') and
			(isset($HTTP_PARAM_VARS['LastElement'])))
			{
			//insert a element blank at the end....
			$vpos = count($s_xml_conf['elements']);

			$vpos = (int)$HTTP_PARAM_VARS['LastElement'] + 0;
			$vpos = ($HTTP_PARAM_VARS['ActionInsert']=='before') ? $vpos : $vpos+1;

			for ($t = count($s_xml_conf['elements']); $t>$vpos; $t--)
				{
				$s_xml_conf['elements'][$t] = $s_xml_conf['elements'][$t-1];
				}
			unset($s_xml_conf['elements'][$vpos]);
			$s_xml_conf['elements'][$vpos]['tagname'] = 'element';
			$s_xml_conf['elements'][$vpos]['type'] = 'textbox';
			$HTTP_PARAM_VARS['LastElement'] = $vpos;
			}

		if (($HTTP_PARAM_VARS['data']=='tables') and
			(isset($HTTP_PARAM_VARS['LastTable'])))
			{
			//insert a element blank at the end....
			$vpos = count($s_xml_conf['tables']);

			$vpos = (int)$HTTP_PARAM_VARS['LastTable'] + 0;
			$vpos = ($HTTP_PARAM_VARS['ActionInsert']=='before') ? $vpos : $vpos+1;

			for ($t = count($s_xml_conf['tables']); $t>$vpos; $t--)
				{
				$s_xml_conf['tables'][$t] = $s_xml_conf['tables'][$t-1];
				}
			unset($s_xml_conf['tables'][$vpos]);
			$s_xml_conf['tables'][$vpos]['tagname'] = 'table';
			$HTTP_PARAM_VARS['LastTable'] = $vpos;
			}
		}

	//Delete a element in edition
	if ((isset($HTTP_PARAM_VARS['Delete'])) and
		($HTTP_PARAM_VARS['Delete']=='true'))
		{
		if (($HTTP_PARAM_VARS['data']=='elements') and
			(isset($HTTP_PARAM_VARS['LastElement'])))
			{
			$vpos = (int)$HTTP_PARAM_VARS['LastElement'] + 0;
			if ($vpos==count($s_xml_conf['elements'])-1)
				{
				$HTTP_PARAM_VARS['LastElement'] = $vpos-1;
				$HTTP_PARAM_VARS['element'] = $vpos-1;
				}
			for ($t = $vpos; $t < count($s_xml_conf['elements'])-1; $t++)
				{
				$s_xml_conf['elements'][$t] = $s_xml_conf['elements'][$t+1];
				}
			$vpos = count($s_xml_conf['elements']);
			unset($s_xml_conf['elements'][$vpos-1]);
			}

		if (($HTTP_PARAM_VARS['data']=='tables') and
			(isset($HTTP_PARAM_VARS['LastTable'])))
			{
			$vpos = (int)$HTTP_PARAM_VARS['LastTable'] + 0;
			if ($vpos==count($s_xml_conf['tables'])-1)
				{
				$HTTP_PARAM_VARS['LastTable'] = $vpos-1;
				$HTTP_PARAM_VARS['table'] = $vpos-1;
				}
			for ($t = $vpos; $t < count($s_xml_conf['tables'])-1; $t++)
				{
				$s_xml_conf['tables'][$t] = $s_xml_conf['tables'][$t+1];
				}
			$vpos = count($s_xml_conf['tables']);
			unset($s_xml_conf['tables'][$vpos-1]);
			}
		}
	//_________________________________________________________________________________________________________
	if ($HTTP_PARAM_VARS['data']=='Queries')
		{


		//---------------------------------
			if  (isset($HTTP_PARAM_VARS['use_default']))
				{
				$HTTP_PARAM_VARS['is_connected'] = 'false';
				$s_xml_conf['connection'][1]['hostname'] = $s_connection['hostname'];
				$s_xml_conf['connection'][1]['database'] = $s_connection['database'];
				$s_xml_conf['connection'][1]['user'] = $s_connection['user'];
				$s_xml_conf['connection'][1]['pswd'] = $s_connection['password'];
				$s_xml_conf['connection'][1]['role'] = $s_connection['role'];
				$s_xml_conf['connection'][1]['locale'] = $s_connection['locale'];
				$s_xml_conf['connection'][1]['type'] = (isset($dbhandle->databaseType)) ? $dbhandle->databaseType : ' access';
				//$HTTP_PARAM_VARS['connect'] = true;
				}

		if (!isset($s_xml_conf['connection'][1]['type']))
			{
			  $s_xml_conf['connection'][1]['type'] = (isset($dbhandle->databaseType)) ? $dbhandle->databaseType : ' access';
			}
		if ((isset($HTTP_PARAM_VARS['type_db'])) && (isset($HTTP_PARAM_VARS['updatetype'])) )
			 {
			 $vtype_db = $HTTP_PARAM_VARS['type_db'];
			 $s_xml_conf['connection'][1]['type'] =  $HTTP_PARAM_VARS['type_db'];
			}
		else
			{
			$vtype_db =  $s_xml_conf['connection'][1]['type'];
			}

		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>type: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<select name="type_db" onChange="javascript: CheckTypeChange();">'."\n";
		$vHTML .=  '<option value="" ></option>'."\n";
		foreach ($vTypesDb as $vdrive => $vdescrip)
			{
			if ($vtype_db==$vdrive)
				{
				$vHTML .=  '<option value="'.$vdrive.'" selected>'.$vdrive.' - '.$vdescrip.'</option>'."\n";
				}
			else
				{
				$vHTML .=  '<option value="'.$vdrive.'">'.$vdrive.' - '.$vdescrip.'</option>'."\n";
				}
			}


		$vHTML .=  "</select>\n";
		$vHTML .=  '<input type="submit" name="updatetype" value="Update">'."\n";
		$vHTML .='</td></tr>';

		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<input type="submit" name="butt_createDB" value="Create database:">'."\n";
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';


		$vHTML .=  '<input name="name_createDB" type="text" size="100" value="">'."\n";

		$vHTML .='</td></tr>';


			if (isset($HTTP_PARAM_VARS['updatetype']))
				{
				 $HTTP_PARAM_VARS['is_connected'] = 'false';
				}
			$HTTP_PARAM_VARS['is_connected'] = (isset($HTTP_PARAM_VARS['is_connected']))? $HTTP_PARAM_VARS['is_connected']: 'false';
			if ( (isset($HTTP_PARAM_VARS['connect'])) || ($HTTP_PARAM_VARS['is_connected']=='true') )
				{
				$vHTML .=  '<input type="hidden" name="is_connected" value="true">'."\n";
				$HTTP_PARAM_VARS['is_connected'] = 'true';
				}
			else
				{
				 $vHTML .=  '<input type="hidden" name="is_connected" value="false">'."\n";
				}



		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		if ($HTTP_PARAM_VARS['is_connected'] == 'false')
			{
			$vHTML .=  '<input type="submit" name="connect" value="Connect->">'."\n";
			}

		$vHTML .=  '<br /><input type="submit" name="use_default" value="Use default values->">'."\n";
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .='<table>';

				//Attr: hostname
			//if (isset($s_xml_conf['connection_attrs'][0]['hostname_attr']))
				{
				$s_xml_conf['connection'][0]['hostname'] = (isset($s_xml_conf['connection'][0]['hostname'])) ? $s_xml_conf['connection'][0]['hostname'] : '127.0.0.1';
				$s_xml_conf['connection'][1]['hostname'] = (isset($s_xml_conf['connection'][1]['hostname'])) ? $s_xml_conf['connection'][1]['hostname'] : $s_xml_conf['connection'][0]['hostname'];
				if ((isset($HTTP_PARAM_VARS['hostname'])) && (isset($HTTP_PARAM_VARS['connect'])) )
					 {
					 $s_xml_conf['connection'][1]['hostname'] = $HTTP_PARAM_VARS['hostname'];
					}
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>hostname: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="hostname" type="text" size="100" value="'.$s_xml_conf['connection'][1]['hostname'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: database
			//if (isset($s_xml_conf['connection_attrs'][0]['database_attr']))
				{
				$s_xml_conf['connection'][0]['database'] = (isset($s_xml_conf['connection'][0]['database'])) ? $s_xml_conf['connection'][0]['database'] : '';
				$s_xml_conf['connection'][1]['database'] = (isset($s_xml_conf['connection'][1]['database'])) ? $s_xml_conf['connection'][1]['database'] : $s_xml_conf['connection'][0]['database'];
				if ((isset($HTTP_PARAM_VARS['database']))  && (isset($HTTP_PARAM_VARS['connect'])) )
					 {
					 $s_xml_conf['connection'][1]['database'] = $HTTP_PARAM_VARS['database'];
					}
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>database: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="database" type="text" size="100" value="'.$s_xml_conf['connection'][1]['database'].'">'."\n";
				$vHTML .='</td></tr>';
				}


			//Attr: user
			//if (isset($s_xml_conf['connection_attrs'][0]['user_attr']))
				{
				$s_xml_conf['connection'][0]['user'] = (isset($s_xml_conf['connection'][0]['user'])) ? $s_xml_conf['connection'][0]['user'] : '';
				$s_xml_conf['connection'][1]['user'] = (isset($s_xml_conf['connection'][1]['user'])) ? $s_xml_conf['connection'][1]['user'] : $s_xml_conf['connection'][0]['user'];
				if ( (isset($HTTP_PARAM_VARS['user'])) && (isset($HTTP_PARAM_VARS['connect'])) )
					 {
					 $s_xml_conf['connection'][1]['user'] = $HTTP_PARAM_VARS['user'];
					}
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'user: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="user" type="text" size="100" value="'.$s_xml_conf['connection'][1]['user'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: pswd
			//if (isset($s_xml_conf['connection_attrs'][0]['pswd_attr']))
				{
				$s_xml_conf['connection'][0]['pswd'] = (isset($s_xml_conf['connection'][0]['pswd'])) ? $s_xml_conf['connection'][0]['pswd'] : '';
				$s_xml_conf['connection'][1]['pswd'] = (isset($s_xml_conf['connection'][1]['pswd'])) ? $s_xml_conf['connection'][1]['pswd'] : $s_xml_conf['connection'][0]['pswd'];
				if ((isset($HTTP_PARAM_VARS['pswd'])) && (isset($HTTP_PARAM_VARS['connect'])) )
					 {
					 $s_xml_conf['connection'][1]['pswd'] = $HTTP_PARAM_VARS['pswd'];
					}
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'pswd: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="pswd" type="text" size="100" value="'.$s_xml_conf['connection'][1]['pswd'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: role
			//if (isset($s_xml_conf['connection_attrs'][0]['role_attr']))
				{
				$s_xml_conf['connection'][0]['role'] = (isset($s_xml_conf['connection'][0]['role'])) ? $s_xml_conf['connection'][0]['role'] : '';
				$s_xml_conf['connection'][1]['role'] = (isset($s_xml_conf['connection'][1]['role'])) ? $s_xml_conf['connection'][1]['role'] : $s_xml_conf['connection'][0]['role'];
				if ((isset($HTTP_PARAM_VARS['role']))  && (isset($HTTP_PARAM_VARS['connect'])) )
					 {
					 $s_xml_conf['connection'][1]['role'] = $HTTP_PARAM_VARS['role'];
					}
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'role: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="role" type="text" size="100" value="'.$s_xml_conf['connection'][1]['role'].'">'."\n";
				$vHTML .='</td></tr>';
				}


			//Attr: locale
			//if (isset($s_xml_conf['connection_attrs'][0]['locale_attr']))
				{
				$s_xml_conf['connection'][0]['locale'] = (isset($s_xml_conf['connection'][0]['locale'])) ? $s_xml_conf['connection'][0]['locale'] : '';
				$s_xml_conf['connection'][1]['locale'] = (isset($s_xml_conf['connection'][1]['locale'])) ? $s_xml_conf['connection'][1]['locale'] : $s_xml_conf['connection'][0]['locale'];
				if ((isset($HTTP_PARAM_VARS['locale']))  && (isset($HTTP_PARAM_VARS['connect'])) )
					 {
					 $s_xml_conf['connection'][1]['locale'] = $HTTP_PARAM_VARS['locale'];
					}
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'locale: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="locale" type="text" size="100" value="'.$s_xml_conf['connection'][1]['locale'].'">'."\n";
				$vHTML .='</td></tr>';
				}
		$vHTML .='</table>';
		$vHTML .='</td></tr>';

		//$dbhandle = false;
		$s_xml_conf['connection'][1]['type'] = $vtype_db;
		if ($vtype_db)
			{
			$s_connection['conected'] = $dbhandle = &ADONewConnection($vtype_db);   //create de connection
			}
		if (($s_connection['conected']) && ($HTTP_PARAM_VARS['is_connected']=='true') )
			{
			  if($vtype_db == "odbc")
					{
					if(PERSISTANT_CONNECTIONS)
						{
						$s_connection['conected'] = $dbhandle->PConnect($s_xml_conf['connection'][1]['database'], $s_xml_conf['connection'][1]['user'],$s_xml_conf['connection'][1]['pswd'], $s_xml_conf['connection'][1]['locale']);
						}
					else 	$s_connection['conected'] = $dbhandle->Connect($s_xml_conf['connection'][1]['database'], $s_xml_conf['connection'][1]['user'],$s_xml_conf['connection'][1]['pswd'], $s_xml_conf['connection'][1]['locale']);
					}
				if($vtype_db == "access")
					{

					if(PERSISTANT_CONNECTIONS)
						{
						//$dbhandle->PConnect($s_connection['database'], $s_connection['user'],$s_connection['pswd'], $s_connection['locale']);
						$s_connection['conected'] = $dbhandle->PConnect("Driver={Microsoft Access Driver (*.mdb)};Dbq=".$s_xml_conf['connection'][1]['database'].";Uid=".$s_xml_conf['connection'][1]['user'].";Pwd=".$s_xml_conf['connection'][1]['pswd'].";");
						}
					else
						{
						//$dbhandle->Connect($s_connection['database'], $s_connection['user'],$s_connection['pswd'], $s_connection['locale']);
						$s_connection['conected'] = $dbhandle->Connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=".$s_xml_conf['connection'][1]['database'].";Uid=".$s_xml_conf['connection'][1]['user'].";Pwd=".$s_xml_conf['connection'][1]['pswd'].";");
						}
					}
				else if (($vtype_db == "ibase") or ($vtype_db == "firebird"))
					{
					if(PERSISTANT_CONNECTIONS)
						{
						$s_connection['conected'] = $dbhandle->PConnect($s_xml_conf['connection'][1]['hostname'].":".$s_xml_conf['connection'][1]['database'],$s_xml_conf['connection'][1]['user'],$s_xml_conf['connection'][1]['pswd']);
						}
					else 	{
						$s_connection['conected'] = $dbhandle->Connect($s_xml_conf['connection'][1]['hostname'].":".$s_xml_conf['connection'][1]['database'],$s_xml_conf['connection'][1]['user'],$s_xml_conf['connection'][1]['pswd']);
						}
					}
				else 	{
					if(PERSISTANT_CONNECTIONS)
						{
						$s_connection['conected'] = $dbhandle->PConnect($s_xml_conf['connection'][1]['hostname'],$s_xml_conf['connection'][1]['user'],$s_xml_conf['connection'][1]['pswd'], $s_xml_conf['connection'][1]['database'],$s_xml_conf['connection'][1]['locale']);
						}
					else $s_connection['conected'] = $dbhandle->Connect($s_xml_conf['connection'][1]['hostname'],$s_xml_conf['connection'][1]['user'],$s_xml_conf['connection'][1]['pswd'],$s_xml_conf['connection'][1]['database'],$s_xml_conf['connection'][1]['locale']);
					}
			}

		if ($s_connection['conected'])
			{
			
			$dict = NewDataDictionary($dbhandle);
			if (($vtype_db) && (isset($HTTP_PARAM_VARS['butt_createDB'])) && (isset($HTTP_PARAM_VARS['name_createDB'])))
				{
				  if ($HTTP_PARAM_VARS['name_createDB'])
				  	{
					  $vname_createDB = $HTTP_PARAM_VARS['name_createDB'];
					  $sqlarray=$dict->CreateDatabase($vname_createDB);
					  $dict->ExecuteSQLArray($sqlarray);
					  print_r($sqlarray);
					}
				}
			if (($vtype_db) && (isset($HTTP_PARAM_VARS['butt_createTable'])) && (isset($HTTP_PARAM_VARS['name_createTable'])))
				{
				  if ($HTTP_PARAM_VARS['name_createTable'])
				  	{
					  $vname_create = $HTTP_PARAM_VARS['name_createTable'];
					  $vfields_create = $HTTP_PARAM_VARS['def_createTable'];
					  $sqlarray=$dict->CreateTableSQL($vname_create,$vfields_create);
					  //$dict->ExecuteSQLArray($sqlarray);

					}
				}
			}

		if ($HTTP_PARAM_VARS['is_connected'] == 'true')
			{
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			$vHTML .=  '<input type="submit" name="butt_createTable" value="Create table:">'."\n";
			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
			$vHTML .=  '<strong>table name:</strong><input name="name_createTable" type="text" size="40" value="">'."\n";

			$vHTML .=  '<hr><strong>field name:</strong><input name="field_createTable" type="text" size="10" value="">'."\n";
			$vHTML .=  '<strong>type:</strong><select name="type_createTable">'."\n";
			  $vHTML .=  '<option value="C" >Varchar, capped to 255 characters</option>'."\n";
			  $vHTML .=  '<option value="X" >Larger varchar, capped to 4000 characters (to be compatible with Oracle)</option>'."\n";
			  $vHTML .=  '<option value="XL" >For Oracle, returns CLOB, otherwise the largest varchar size</option>'."\n";
			  $vHTML .=  '<option value="C2" >Multibyte varchar</option>'."\n";
			  $vHTML .=  '<option value="X2" >Multibyte varchar (largest size)</option>'."\n";
			  $vHTML .=  '<option value="B" >BLOB (binary large object)</option>'."\n";
			  $vHTML .=  '<option value="D" >Date (some databases do not support this, and we return a datetime type)</option>'."\n";
			  $vHTML .=  '<option value="T" >Datetime or Timestamp</option>'."\n";
			  $vHTML .=  '<option value="L" >Integer field suitable for storing booleans (0 or 1)</option>'."\n";
			  $vHTML .=  '<option value="I" >Integer (mapped to I4)</option>'."\n";
			  $vHTML .=  '<option value="I1" >1-byte integer</option>'."\n";
			  $vHTML .=  '<option value="I2" >2-byte integer</option>'."\n";
			  $vHTML .=  '<option value="I4" >4-byte integer</option>'."\n";
			  $vHTML .=  '<option value="I8" >8-byte integer</option>'."\n";
			  $vHTML .=  '<option value="F" >Floating point number</option>'."\n";
			  $vHTML .=  '<option value="N" >Numeric or decimal number</option>'."\n";
			$vHTML .=  '</select>';
			$vHTML .=  'size:<input name="size_createTable" type="text" size="5" value="">'."\n";
			$vHTML .=  ' <br />Autoincrement<input name="fieldAUTOINCREMENT" type="checkbox" value="1">'."\n";
			$vHTML .=  ' Primary key<input name="fieldPRIMARYKEY" type="checkbox" value="1">'."\n";
			$vHTML .=  ' Not null<input name="fieldNOTNULL" type="checkbox" value="1">'."\n";
			$vHTML .=  ' DEFDATE<input name="fieldDEFDATE" type="checkbox" value="1">'."\n";
			$vHTML .=  ' DEFTIMESTAMP<input name="fieldDEFTIMESTAMP" type="checkbox" value="1">'."\n";
			$vHTML .=  ' NOQUOTE<input name="fieldNOQUOTE" type="checkbox" value="1">'."\n";
		    	$vHTML .=  ' <br />Default value:<input name="fieldDEFAULT" type="text" value="">'."\n";
			$vHTML .=  ' Constraints:<input name="fieldCONSTRAINTS" type="text" value="">'."\n";
			$vHTML .=  '<input name="addfield_createTable" type="button" value="Add field" onClick="javascript: Updatefieldsdef();">'."\n";
			$vHTML .=  '<hr>';
			$vHTML .=  'Example:<br />';
			$vHTML .=  'COLNAME DECIMAL(8.4) DEFAULT 0 NOTNULL,<br />';
			$vHTML .=  'id I AUTO,<br />';
			$vHTML .=  '`MY DATE` D DEFDATE,<br />';
			$vHTML .=  "NAME C(32) CONSTRAINTS 'FOREIGN KEY REFERENCES reftable'<br />";
			$vHTML .=  '<hr>';

			$vHTML .=  '<textarea name="def_createTable" cols="100" rows="4">'."</textarea>\n";
			$vHTML .=  '</td></tr>';

			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			$vHTML .=  '<input type="button" name="butt_deleteTable" value="Delete table:">'."\n";
			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
			if ($s_connection['conected'])
				{
				$vHTML .=  '<select name="name_deleteTable" size="1">'."\n";
					$vHTML .=  '<option value="" ></option>'."\n";

						$rec = $dbhandle->MetaTables();
						if ($rec)
							{
							$tableCount = sizeof($rec);
							for ($i=0; $i < $tableCount; $i++)
								{
								$vtemp['tables_list'][$rec[$i]] = '';
								}
							}
						else	{
							$vtemp['tables_list'][''] = '';
							}
						ksort ($vtemp['tables_list']);



					foreach ($vtemp['tables_list'] as $vvar => $vval)
						{
						$vt = $syntax['table'];
						$vt = ereg_replace('#1',$vvar, $vt);
						$vHTML .=  '<option value="'.$vt.'">'.$vvar."</option>\n";
						}
					$vHTML .=  "</select>\n";
				}
			else
				{

				}
			$vHTML .=  '</td></tr>';

			//--sql---------------
			$vsql_q = (isset($HTTP_PARAM_VARS['sql_q']))? $HTTP_PARAM_VARS['sql_q'] : '';
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			$vHTML .=  'sql: ';
			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  'Tables: <select name="sql_table_list" size="1" onChange="javascript: openWindowGiveMeFields(true,this, document.Form.sql_field_list);">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";

				foreach ($vtemp['tables_list'] as $vvar => $vval)
					{
					$vt = $syntax['table'];
					$vt = ereg_replace('#1',$vvar, $vt);
					$vHTML .=  '<option value="'.$vt.'">'.$vvar."</option>\n";
					}
				$vHTML .=  "</select>\n";
				$vHTML .=  '<input type="button" name="Add_sql_table" onClick="javascript: AddSqlAttribute(sql_table_list, sql); " value="Add table">       '."\n";

				$vHTML .=  ' Fields: <select name="sql_field_list" size="1" >'."\n";
				$vHTML .=  '	<option value=""></option>'."\n";
				$vHTML .=  "	</select>\n";
				$vHTML .=  '<input type="button" name="Add_sql_field" onClick="javascript: AddSqlAttribute(sql_field_list, sql); " value="Add field">       '."\n";
				//$vHTML .=  '<input type="button" name="sql_field_list_button" onClick="javascript: openWindowGiveMeFields(false,document.Form.sql_table_list, document.Form.sql_field_list);" value="Update field">       '."\n";


				$vHTML .=  '<br />Variables: <select name="sql_variables_list" size="1" >'."\n";
				$vHTML .=  '	<option value=""></option>'."\n";

				for ($t = 0; $t < count($s_xml_conf['elements']); $t++)
					{
					if (	(isset($s_xml_conf['elements'][$t]['table'])) and
						(isset($s_xml_conf['elements'][$t]['field'])) )
						{
						$vt = $s_xml_conf['elements'][$t]['table'].'.'.$s_xml_conf['elements'][$t]['field'];
						$vHTML .=  '	<option value="__'.$vt.'__">'.$vt.'</option>'."\n";
						}

					}
				$vHTML .=  "	</select>\n";
				$vHTML .=  '<input type="button" name="Add_sql_variables" onClick="javascript: AddSqlAttribute(sql_variables_list, sql); " value="Add variable">       '."\n";
				$vHTML .=  "<br />\n";
				$vHTML .=  '<textarea name="sql" cols="100" rows="8" onChange="javascript: CheckSqlChange();">'.htmlentities($vsql_q)."</textarea>\n";
				$vHTML .=  '<br /><input type="button" name="sql_run_button" onClick="javascript: openWindowSql(document.Form.sql, 1);" value="Run">       '."\n";
				$vHTML .='</td></tr>';
			}

		$vHTML .=  '<hr>';
		}

	//_________________________________________________________________________________________________________
	if ($HTTP_PARAM_VARS['data']=='BioCase')
		{
		$xPath =& new XPath();
		$xPath->setSkipWhiteSpaces(TRUE);
		if ($xPath->importFromFile(getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['mod']))
			{
			$vHead .=  '    <a href="javascript: Redirect('."'".'run.php?mod='.$HTTP_PARAM_VARS['mod']."'".')">Run</a>';
			$vHTML .=  '<a href="javascript:void(0)" onClick="javascript: window.open('."'".$Confs["configuration-path"].$HTTP_PARAM_VARS['mod']."','','');".'">'.$HTTP_PARAM_VARS['mod'].'</a><hr>';
			$vHTML .= urldecode($xPath->exportAsHtml())."<hr>";
			}		
		}
		
	//_________________________________________________________________________________________________________
	if ($HTTP_PARAM_VARS['data']=='ViewXml')
		{
		$xPath =& new XPath();
		$xPath->setSkipWhiteSpaces(TRUE);
		if ($xPath->importFromFile(getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['mod']))
			{
			$vHead .=  '    <a href="javascript: Redirect('."'".'run.php?mod='.$HTTP_PARAM_VARS['mod']."'".')">Run</a>';
			$vHTML .=  '<a href="javascript:void(0)" onClick="javascript: window.open('."'".$Confs["configuration-path"].$HTTP_PARAM_VARS['mod']."','','');".'">'.$HTTP_PARAM_VARS['mod'].'</a><hr>';
			$vHTML .= urldecode($xPath->exportAsHtml())."<hr>";
			}
		}
	//_________________________________________________________________________________________________________
	if ($HTTP_PARAM_VARS['data']=='connection')
		{
		if ((!isset($s_xml_conf['connection'])) or
			(count($s_xml_conf['connection'])==0))
			{
			$s_xml_conf['connection'][0]['tagname'] = 'db';
			}

		//Register in the section vars the values of the post/get if those exist
		if (	((isset($HTTP_PARAM_VARS['Save']))) or
			(isset($HTTP_PARAM_VARS['SaveXML'])))
			{

			//foreach($HTTP_PARAM_VARS as $vvar => $vval)
				{
				//$pp = $HTTP_PARAM_VARS['Lastconnection'] + 0;
				//if (strpos($s_xml_conf['connection_attrs'][0]['attrs'].';', ':'.$vvar.';')> -1)
					{
					if (isset($HTTP_PARAM_VARS['tagname']))
						{
						//$s_xml_conf['connection'][0]['tagname'] = stripcslashes($HTTP_PARAM_VARS['tagname']);
						$s_xml_conf['connection'][0]['tagname'] = ($HTTP_PARAM_VARS['tagname']);
						}
					if ($s_xml_conf['connection'][0]['tagname'] == 'db')
						{
						$s_xml_conf['connection'][0]['hostname'] = (isset($HTTP_PARAM_VARS['hostname'])) ? ($HTTP_PARAM_VARS['hostname']): '';
						$s_xml_conf['connection'][0]['database'] = (isset($HTTP_PARAM_VARS['database'])) ? ($HTTP_PARAM_VARS['database']): '';
						$s_xml_conf['connection'][0]['type'] = (isset($HTTP_PARAM_VARS['type'])) ? ($HTTP_PARAM_VARS['type']): '';
						$s_xml_conf['connection'][0]['user'] = (isset($HTTP_PARAM_VARS['user'])) ? ($HTTP_PARAM_VARS['user']): '';
						$s_xml_conf['connection'][0]['pswd'] = (isset($HTTP_PARAM_VARS['pswd'])) ? ($HTTP_PARAM_VARS['pswd']): '';
						$s_xml_conf['connection'][0]['locale'] = (isset($HTTP_PARAM_VARS['locale'])) ? ($HTTP_PARAM_VARS['locale']): '';
						$s_xml_conf['db'][0]['tagname'] = 'register';
						$s_xml_conf['db'][0]['content'] = (isset($HTTP_PARAM_VARS['register'])) ? (urldecode($HTTP_PARAM_VARS['register'])): '';
						}
					else	//inc....
						{
						$s_xml_conf['connection'][0]['content'] = (isset($HTTP_PARAM_VARS['content'])) ? (urldecode($HTTP_PARAM_VARS['content'])): '';
						$s_xml_conf['db'] = array();
						}
					}
				}
			//$vScriptIni .= ' document.Form.Refresh.click(); ';
			}


		$vHead .=  '    <a href="javascript: Redirect('."'".'run.php?mod='.$HTTP_PARAM_VARS['mod']."'".')">Run</a>';

		//Attr: tagname
		//$s_xml_conf['connection'][0]['tagname'] = (isset($s_xml_conf['connection'][0]['tagname'])) ? $s_xml_conf['connection'][0]['tagname'] : 'db';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>Tagname: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<select name="tagname" onChange="javascript: CheckTypeChange();">'."\n";
		$vHTML .=  '<option value="" ></option>'."\n";
		if ($s_xml_conf['connection'][0]['tagname']=='db')
			{
			$vHTML .=  '<option value="db" selected>db</option>'."\n";
			$vHTML .=  '<option value="inc">inc</option>'."\n";
			}
		else 	{
			$vHTML .=  '<option value="db">db</option>'."\n";
			$vHTML .=  '<option value="inc" selected>inc</option>'."\n";
			}

		$vHTML .=  "</select>\n";
		//$vCheckTypeChange .= ' if (document.Form.tagname.value!= "'.$s_xml_conf['connection'][0]['tagname'].'") {'."\n";
		//$vCheckTypeChange .= ' 		alert("We go to update the value of the fields related with this type..."); '."\n";
		$vCheckTypeChange .= ' 		document.Form.Save.click(); '."\n";

		if ($s_xml_conf['connection'][0]['tagname'] == 'db')
			{
			//Attr: hostname
			//if (isset($s_xml_conf['connection_attrs'][0]['hostname_attr']))
				{
				if ($s_connection['conected'])
					{
					$vDatabasesInf = $dbhandle->MetaDatabases();
					//echo "<pre> "; print_r($vDatabasesInf); echo "</pre>";
					//echo "<pre> veamos"; print_r($dbhandle); echo "</pre>";
					}
				$s_xml_conf['connection'][0]['hostname'] = (isset($s_xml_conf['connection'][0]['hostname'])) ? $s_xml_conf['connection'][0]['hostname'] : '127.0.0.1';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>hostname: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="hostname" type="text" size="100" value="'.$s_xml_conf['connection'][0]['hostname'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: database
			//if (isset($s_xml_conf['connection_attrs'][0]['database_attr']))
				{
				$s_xml_conf['connection'][0]['database'] = (isset($s_xml_conf['connection'][0]['database'])) ? $s_xml_conf['connection'][0]['database'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>database: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  'To use a relativ path you can use the variable: __pathApp__ that contain the path where is found the web aplication, ej: c:\collman<br />';
				$vHTML .=  '<input name="database" type="text" size="100" value="'.$s_xml_conf['connection'][0]['database'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: type
			//if (isset($s_xml_conf['connection_attrs'][0]['type_attr']))
				{

				$s_xml_conf['connection'][0]['type'] = (isset($s_xml_conf['connection'][0]['type'])) ? $s_xml_conf['connection'][0]['type'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>type: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<select name="type" onChange="javascript: CheckTypeChange();">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				foreach ($vTypesDb as $vdrive => $vdescrip)
					{
					if ($s_xml_conf['connection'][0]['type']==$vdrive)
						{
						$vHTML .=  '<option value="'.$vdrive.'" selected>'.$vdrive.' - '.$vdescrip.'</option>'."\n";
						}
					else
						{
						$vHTML .=  '<option value="'.$vdrive.'">'.$vdrive.' - '.$vdescrip.'</option>'."\n";
						}
					}
				$vHTML .=  "</select>\n";
				//$vHTML .=  '<input name="vtype" type="text" size="100" value="'.$s_xml_conf['connection'][0]['type'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: user
			//if (isset($s_xml_conf['connection_attrs'][0]['user_attr']))
				{
				$s_xml_conf['connection'][0]['user'] = (isset($s_xml_conf['connection'][0]['user'])) ? $s_xml_conf['connection'][0]['user'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'user: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="user" type="text" size="100" value="'.$s_xml_conf['connection'][0]['user'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: pswd
			//if (isset($s_xml_conf['connection_attrs'][0]['pswd_attr']))
				{          
				
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'pswd: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="pswd" type="text" size="100" value="'.$s_xml_conf['connection'][0]['pswd'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: role
			//if (isset($s_xml_conf['connection_attrs'][0]['role_attr']))
				{
				$s_xml_conf['connection'][0]['role'] = (isset($s_xml_conf['connection'][0]['role'])) ? $s_xml_conf['connection'][0]['role'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'role: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="role" type="text" size="100" value="'.$s_xml_conf['connection'][0]['role'].'">'."\n";
				$vHTML .='</td></tr>';
				}


			//Attr: locale
			//if (isset($s_xml_conf['connection_attrs'][0]['locale_attr']))
				{
				$s_xml_conf['connection'][0]['locale'] = (isset($s_xml_conf['connection'][0]['locale'])) ? $s_xml_conf['connection'][0]['locale'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'locale: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="locale" type="text" size="100" value="'.$s_xml_conf['connection'][0]['locale'].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: locale
			//if (isset($s_xml_conf['db'][0]))
				{
				$s_xml_conf['db'][0]['tagname'] = 'register';
				$s_xml_conf['db'][0]['content'] = (isset($s_xml_conf['db'][0]['content'])) ? $s_xml_conf['db'][0]['content'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'register: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  'You must use the internal variables __user__ and __pswd__ in the sql query to specify them in the where section...';
				$vHTML .=  '<textarea name="register" cols="100" rows="8" >'.htmlentities($s_xml_conf['db'][0]['content'])."</textarea>\n";
				//$vHTML .=  '<input name="register" type="text" size="100" value="'.htmlentities($s_xml_conf['db'][0]['content']).'">'."\n";
				$vHTML .='</td></tr>';
				}

			}
		else	//inc....
			{
			//Attr: content
			//if (isset($s_xml_conf['connection_attrs'][0]['content_attr']))
				{
				$s_xml_conf['connection'][0]['content'] = (isset($s_xml_conf['connection'][0]['content'])) ? $s_xml_conf['connection'][0]['content'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>xml file: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<select name="content" >'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";

				  if (!empty($s_xml_conf['connection'][0]['content']))
					{
					$pathMod = pathinfo($s_xml_conf['connection'][0]['content']);
					$xmlDirectory = getcwd().'/'.$Confs["configuration-path"].$pathMod["dirname"].'/';
					$xmlFile = $pathMod['basename'];
					}
				  else	{
					$pathMod = pathinfo($HTTP_PARAM_VARS['mod']);
					$xmlDirectory = getcwd().'/'.$Confs["configuration-path"].$pathMod["dirname"].'/';
					$xmlFile = '';
					}

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
						     $vHTML .= "<option value=\"".$pathMod["dirname"]."/$entry\"";

						      // Check whether this file is selected.
						      if ( trim($entry) == trim($xmlFile) )
							{
							// Select the entry.
							$vHTML .= " selected";
							}

						      // Close the option tag.
						      $vHTML .= ">$entry</option>\n";
						}
					}

				  // Close the directory.
				  $directory->close();

				$vHTML .=  "</select>\n";

				//$vHTML .=  '<input name="content" type="text" size="100" value="'.$s_xml_conf['connection'][0]['content'].'">'."\n";
				$vHTML .=  '<input name="goXML" type="button" value="'.$button_strings['Select'].'" onClick="javascript: goMod(content.value);">'."\n";
				$vHTML .='</td></tr>';
				}

			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
			$vHTML .=  '</td></tr>';
			//Attr: hostname
			if (isset($s_connection['hostname']))
				{
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>hostname: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  $s_connection['hostname'];
				$vHTML .='</td></tr>';
				}

			//Attr: database
			if (isset($s_connection['database']))
				{
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>database: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  $s_connection['database'];
				$vHTML .='</td></tr>';
				}

			//Attr: type
			if (isset($s_connection['type']))
				{
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  '<strong>type: </strong>';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  $s_connection['type'];
				$vHTML .='</td></tr>';
				}

			//Attr: user
			if (isset($s_connection['user']))
				{
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'user: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  $s_connection['user'];
				$vHTML .='</td></tr>';
				}

			//Attr: pswd
			if (isset($s_connection['password']))
				{
				 
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'pswd: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  $s_connection['password'];
				$vHTML .='</td></tr>';
				}

			//Attr: role
			if (isset($s_connection['role']))
				{
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'role: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  $s_connection['role'];
				$vHTML .='</td></tr>';
				}


			//Attr: locale
			if (isset($s_connection['locale']))
				{
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'locale: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  $s_connection['locale'];
				$vHTML .='</td></tr>';
				}

			//Attr: locale
			if (isset($s_connection['register']))
				{
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'register: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  $s_connection['register'];
				$vHTML .='</td></tr>';
				}


			}

		if ( (isset($s_connection['register'])) && (!empty($s_connection['register'])) )
			{

			$vnametableUser = "users";
			$vnamefieldUser = "user";
			$vnamefieldRole = "role";
			$vnamefieldPswd = "pswd";

			if ( isset($HTTP_PARAM_VARS['insert_new_user']) )  //insert a new user
				{
				$vkey = ereg_replace('#1',$vnamefieldUser, $syntax['field']);
				$vval = ereg_replace('#1',$HTTP_PARAM_VARS['new_value_user'], $syntax['string']);

				$vkey = $vkey.', '.ereg_replace('#1',$vnamefieldRole, $syntax['field']);
				$vval = $vval.', '.ereg_replace('#1',$HTTP_PARAM_VARS['new_role_user'], $syntax['string']);

				$vkey = $vkey.', '.ereg_replace('#1',$vnamefieldPswd, $syntax['field']);
				$vval = $vval.', '.ereg_replace('#1',md5(trim($HTTP_PARAM_VARS['new_pswd_user'])), $syntax['string']);

				$vSql = "insert into ".ereg_replace('#1',$vnametableUser, $syntax['table'])." (".$vkey.") values (".$vval.") ";
				if (!$dbhandle->Execute($vSql))
					{
					$db_error .= $dbhandle->ErrorMsg();
					$db_error .= "</br>Error with the query: ".$vSql;
					}
				else	{
					$warning .= '<br />'.$vSql;
					}
				}

			$vSql = $s_connection['register'];
			if (strpos(strtoupper($vSql), "WHERE")>0)
				{
				$vSql = substr($vSql, 0, strpos(strtoupper($vSql), "WHERE"));
				}

			$rec = $dbhandle->Execute(urldecode($vSql));

			if ($rec === FALSE)
				{
				$db_error .= $dbhandle->ErrorMsg();
				}
			else	{

					$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
					$vHTML .=  'Users: ';
					$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';


					$vHTML .='<table border="1">';
					while (!$rec->EOF )
						{

						$vRecRole = $rec->fields[$vnamefieldRole];

						$vvtemp = 'change_values_'.$rec->fields[$vnamefieldUser];
						if ( isset($HTTP_PARAM_VARS[$vvtemp]) )  //save the values of the user
							{
							$vvtemp = 'edit_role_'.$rec->fields[$vnamefieldUser];
							$vval = ereg_replace('#1',$vnamefieldRole, $syntax['field']);
							$vval = $vval.' = '.ereg_replace('#1',$HTTP_PARAM_VARS[$vvtemp], $syntax['string']);

							$vvtemp = 'edit_pswd_'.$rec->fields[$vnamefieldUser];
							$vval = $vval.', '.ereg_replace('#1',$vnamefieldPswd, $syntax['field']);;
							$vval = $vval.' = '.ereg_replace('#1',md5(trim($HTTP_PARAM_VARS[$vvtemp])), $syntax['string']);

							$vSql = "update ".ereg_replace('#1',$vnametableUser, $syntax['table'])." set ".$vval. " where ".ereg_replace('#1',$vnamefieldUser, $syntax['field']).' = '.ereg_replace('#1',$rec->fields[$vnamefieldUser], $syntax['string']);
							if (!$dbhandle->Execute($vSql))
								{
								$db_error .= $dbhandle->ErrorMsg();
								$db_error .= "</br>Error with the query: ".$vSql;
								}
							else 	{
								$vvtemp = 'edit_role_'.$rec->fields[$vnamefieldUser];
								$vRecRole = $HTTP_PARAM_VARS[$vvtemp];
								$warning .= '<br />'.$vSql;
								}
							}


						$vvtemp = 'delete_value_'.$rec->fields[$vnamefieldUser];
						if ( isset($HTTP_PARAM_VARS[$vvtemp]) )  //delete the user
							{
							$vvtemp = 'edit_role_'.$rec->fields[$vnamefieldUser];
							$vval = ereg_replace('#1',$vnamefieldRole, $syntax['field']);

							$vSql = "delete from ".ereg_replace('#1',$vnametableUser, $syntax['table'])." where ".ereg_replace('#1',$vnamefieldUser, $syntax['field']).' = '.ereg_replace('#1',$rec->fields[$vnamefieldUser], $syntax['string']);
							if (!$dbhandle->Execute($vSql))
								{
								$db_error .= $dbhandle->ErrorMsg();
								$db_error .= "</br>Error with the query: ".$vSql;
								}
							else	{
								$warning .= '<br />'.$vSql;
								}
							}
						else	{  //this user exist...


							$vHTML .='<tr class="TableFieldValue">';
							$vHTML .= '<td>'.$rec->fields[$vnamefieldUser].'</td>';
							$vHTML .= '<td>';
							$vHTML .=  '<select name="edit_role_'.$rec->fields[$vnamefieldUser].'" size="1" >';

							foreach ($role_strings as $vkey => $vval)
								{
								$vHTML .=  '<option value="'.$vkey.'" ';
								if ($vRecRole== $vkey)
									{
									$vHTML .=  ' selected';
									}
								$vHTML .=  ' >'.$vval.'</option>';
								}
							$vHTML .=  '</select>';

							$vHTML .= '</td>';
							$vHTML .= '<td><input name="edit_pswd_'.$rec->fields[$vnamefieldUser].'" type="password" value=""></td>';
							$vHTML .= '<td><input name="change_values_'.$rec->fields[$vnamefieldUser].'" type="submit" value="'.$button_strings["Modify"].'"></td>';
							$vHTML .= '<td><input name="delete_value_'.$rec->fields[$vnamefieldUser].'" type="submit" value="'.$button_strings["Delete"].'"></td>';

							$vHTML .='</tr>';
							}


						$rec->MoveNext();
						}

					$vHTML .='<tr class="TableFieldValue">';
					$vHTML .= '<td><input name="new_value_user" type="text" value=""></td>';
					$vHTML .= '<td>';
					$vHTML .=  '<select name="new_role_user" size="1" >';

					foreach ($role_strings as $vkey => $vval)
						{
						$vHTML .=  '<option value="'.$vkey.'" ';
						$vHTML .=  ' >'.$vval.'</option>';
						}
					$vHTML .=  '</select>';

					$vHTML .= '</td>';
					$vHTML .= '<td><input name="new_pswd_user" type="password" value=""></td>';
					$vHTML .= '<td><input name="insert_new_user" type="submit" value="'.$button_strings["Add"].'"></td>';
					$vHTML .='</tr>';

					$vHTML .='</table>';

					$vHTML .='</td></tr>';



				}



			}



		}

	//_________________________________________________________________________________________________________
	if (	($HTTP_PARAM_VARS['data']=='Searchs') )
		{
		if ((!isset($s_xml_conf['searchs'])) or
			(count($s_xml_conf['searchs'])==0))
			{
			$s_xml_conf['searchs'][0]['tagname'] = 'sql';
			}

		//Register in the section vars the values of the post/get if those exist
		if (	((isset($HTTP_PARAM_VARS['Save']))) or
			(isset($HTTP_PARAM_VARS['SaveXML'])))
			{

			//foreach($HTTP_PARAM_VARS as $vvar => $vval)
				{
				//$pp = $HTTP_PARAM_VARS['Lastconnection'] + 0;
				//if (strpos($s_xml_conf['connection_attrs'][0]['attrs'].';', ':'.$vvar.';')> -1)
					{
					if (isset($HTTP_PARAM_VARS['tagname']))
						{
						$s_xml_conf['searchs'][0]['tagname'] = ($HTTP_PARAM_VARS['tagname']);
						}
					if ($s_xml_conf['searchs'][0]['tagname'] == 'sql')
						{
						$s_xml_conf['searchs'][0]['content'] = (isset($HTTP_PARAM_VARS['content'])) ? (urldecode($HTTP_PARAM_VARS['content'])): '';
						$s_xml_conf['searchs'][0]['tableid'] = (isset($HTTP_PARAM_VARS['tableid'])) ? ($HTTP_PARAM_VARS['tableid']): '';
						$s_xml_conf['searchs'][0]['fieldid'] = (isset($HTTP_PARAM_VARS['fieldid'])) ? ($HTTP_PARAM_VARS['fieldid']): '';
						$s_xml_conf['searchs'][0]['msg'] = (isset($HTTP_PARAM_VARS['msg'])) ? (urldecode($HTTP_PARAM_VARS['msg'])): '';
						$s_xml_conf['searchs'][0]['id'] = (isset($HTTP_PARAM_VARS['id'])) ? ($HTTP_PARAM_VARS['id']): '';
						$s_xml_conf['searchs'][0]['desc'] = (isset($HTTP_PARAM_VARS['desc'])) ? ($HTTP_PARAM_VARS['desc']): '';
						$s_xml_conf['searchs'][0]['orderby'] = (isset($HTTP_PARAM_VARS['orderby'])) ? ($HTTP_PARAM_VARS['orderby']): '';
						$s_xml_conf['searchs'][0]['quickquery'] = (isset($HTTP_PARAM_VARS['quickquery'])) ? ($HTTP_PARAM_VARS['quickquery']): '';
						$s_xml_conf['searchs'][0]['helpquickquery'] = (isset($HTTP_PARAM_VARS['helpquickquery'])) ? ($HTTP_PARAM_VARS['helpquickquery']): '';
						$s_xml_conf['searchs'][0]['wherequickquery'] = (isset($HTTP_PARAM_VARS['wherequickquery'])) ? ($HTTP_PARAM_VARS['wherequickquery']): '';
						
						}

					}
				}
			//$vScriptIni .= ' document.Form.Refresh.click(); ';
			}


		$vHead .=  '    <a href="javascript: Redirect('."'".'run.php?mod='.$HTTP_PARAM_VARS['mod']."'".')">Run</a>';



		{
		$s_xml_conf['searchs'][0]['msg'] = (isset($s_xml_conf['searchs'][0]['msg'])) ? ($s_xml_conf['searchs'][0]['msg']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>msg: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<input name="msg" type="text" size="100" value="'.htmlentities($s_xml_conf['searchs'][0]['msg']).'">'."\n";
		$vHTML .='</td></tr>';
		}

		{

		//for ($dbhandle as $vvar => $vval)
			{
			//echo "<br />$vvar...$vval";
			}
		$s_xml_conf['searchs'][0]['content'] = (isset($s_xml_conf['searchs'][0]['content'])) ? $s_xml_conf['searchs'][0]['content'] : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>sql: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';



		if ( (!isset($s_xml_conf['tables_list'])) && ($s_connection['conected']) )
			{
			$vHTML .=  'Tables: <select name="sql_table_list" size="1" onChange="javascript: openWindowGiveMeFields(true,this, document.Form.sql_field_list);">'."\n";
			$vHTML .=  '<option value="" ></option>'."\n";

			$rec = $dbhandle->MetaTables();
			if ($rec)
				{
				$tableCount = sizeof($rec);
				for ($i=0; $i < $tableCount; $i++)
					{
					$s_xml_conf['tables_list'][$rec[$i]] = '';
					}
				}
			else	{
				$s_xml_conf['tables_list'][''] = '';
				}
			ksort ($s_xml_conf['tables_list']);

			foreach ($s_xml_conf['tables_list'] as $vvar => $vval)
				{
				$vt = $syntax['table'];
				$vt = ereg_replace('#1',$vvar, $vt);
				$vHTML .=  '<option value="'.$vt.'">'.$vvar."</option>\n";
				}
			$vHTML .=  "</select>\n";
			$vHTML .=  '<input type="button" name="Add_sql_table" onClick="javascript: AddSqlAttribute(sql_table_list, content); " value="Add table">       '."\n";

			$vHTML .=  '	<select name="sql_field_list" size="1" >'."\n";
			$vHTML .=  '	<option value=""></option>'."\n";
			$vHTML .=  "	</select>\n";
			$vHTML .=  '<input type="button" name="Add_sql_field" onClick="javascript: AddSqlAttribute(sql_field_list, content); " value="Add field">       '."\n";
			//$vHTML .=  '<input type="button" name="sql_field_list_button" onClick="javascript: openWindowGiveMeFields(false,document.Form.sql_table_list, document.Form.sql_field_list);" value="Update field">       '."\n";

			$vHTML .=  "<br />\n";
			}


		$vHTML .=  '<textarea name="content" cols="100" rows="8" >'.htmlentities($s_xml_conf['searchs'][0]['content'])."</textarea>\n";
		$vHTML .=  '<br /><input type="button" name="sql_run_button" onClick="javascript: openWindowSql(document.Form.content,0);" value="Run">       '."\n";
		$vHTML .=  '<input name="sql_edit_button" type="button" onClick="javascript: window.open('."'".'TableView.php?mod='.$HTTP_PARAM_VARS['mod']."'".');" value="Edit query">     ';
		$vHTML .='</td></tr>';
		}

		{
		$s_xml_conf['searchs'][0]['tableid'] = (isset($s_xml_conf['searchs'][0]['tableid'])) ? $s_xml_conf['searchs'][0]['tableid'] : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>tableid: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

		$vHTML .=  '	<select name="tableid" size="1" onChange="javascript: openWindowGiveMeFields(true,this, document.Form.fieldid);">'."\n";
		//$vHTML .=  '	<option value=""></option>'."\n";

		for ($a = 0; $a < count($s_xml_conf['tables']); $a++)
			{
			if ($s_xml_conf['tables'][$a]['name']==$s_xml_conf['searchs'][0]['tableid'])
				{
				$vHTML .=  '	<option value="'.$s_xml_conf['tables'][$a]['name'].'" selected>'.$s_xml_conf['tables'][$a]['name']."</option>\n";
				}
			else	{
				$vHTML .=  '	<option value="'.$s_xml_conf['tables'][$a]['name'].'">'.$s_xml_conf['tables'][$a]['name']."</option>\n";
				}
			}
		$vHTML .=  "	</select>\n";

		//$vHTML .=  '<input name="tableid" type="text" size="100" value="'.$s_xml_conf['searchs'][0]['tableid'].'">'."\n";
		$vHTML .='</td></tr>';
		}


		{
		$s_xml_conf['searchs'][0]['fieldid'] = (isset($s_xml_conf['searchs'][0]['fieldid'])) ? $s_xml_conf['searchs'][0]['fieldid'] : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>fieldid: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		if ($s_connection['conected'])
			{
			$vHTML .=  '	<select name="fieldid" size="1" >'."\n";
			$vHTML .=  '	<option value=""></option>'."\n";
			if (!empty($s_xml_conf['searchs'][0]['tableid']))
				{
				$vTable = $s_xml_conf['searchs'][0]['tableid'];
				$vt = $syntax['table'];
				$vt = ereg_replace('#1',$vTable, $vt);
				$vSql = 'SELECT * FROM '.$vt;
				$rec = $dbhandle->SelectLimit($vSql,1);
				for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++)
					{
					$fld = $rec->FetchField($i);
					//$type = $rec->MetaType($fld->type);
					if ($fld->name==$s_xml_conf['searchs'][0]['fieldid'])
						{
						$vHTML .=  '<option value="'.$fld->name.'" selected>'.$fld->name."</option>\n";
						}
					else	{
						$vHTML .=  '<option value="'.$fld->name.'">'.$fld->name."</option>\n";
						}
					}
				$vHTML .=  "	</select>\n";
				}


			$vHTML .=  '<input type="button" name="fieldid_button" onClick="javascript: openWindowGiveMeFields(false,document.Form.tableid, document.Form.fieldid);" value="Select field">       '."\n";
			}


		$vHTML .='</td></tr>';
		}

		{
		$s_xml_conf['searchs'][0]['id'] = (isset($s_xml_conf['searchs'][0]['id'])) ? ($s_xml_conf['searchs'][0]['id']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>id: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<input name="id" type="text" size="100" value="'.htmlentities($s_xml_conf['searchs'][0]['id']).'">'."\n";
		$vHTML .='</td></tr>';
		}

		{
		$s_xml_conf['searchs'][0]['desc'] = (isset($s_xml_conf['searchs'][0]['desc'])) ? ($s_xml_conf['searchs'][0]['desc']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>desc: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<input name="desc" type="text" size="100" value="'.htmlentities($s_xml_conf['searchs'][0]['desc']).'">'."\n";
		$vHTML .='</td></tr>';
		}


		{
		$s_xml_conf['searchs'][0]['orderby'] = (isset($s_xml_conf['searchs'][0]['orderby'])) ? ($s_xml_conf['searchs'][0]['orderby']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>orderby: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<input name="orderby" type="text" size="100" value="'.htmlentities($s_xml_conf['searchs'][0]['orderby']).'">'."\n";
		$vHTML .='</td></tr>';
		}

		{
		$s_xml_conf['searchs'][0]['quickquery'] = (isset($s_xml_conf['searchs'][0]['quickquery'])) ? ($s_xml_conf['searchs'][0]['quickquery']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"></td>';
		$vHTML .=  '<td class="TableFieldValue" width="'.$vRightPercent.'%"><hr /></td></tr>';
		
		$s_xml_conf['searchs'][0]['quickquery'] = (isset($s_xml_conf['searchs'][0]['quickquery'])) ? ($s_xml_conf['searchs'][0]['quickquery']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  'Quick Search:';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<textarea name="quickquery" cols="100" rows="8" >'.htmlentities($s_xml_conf['searchs'][0]['quickquery'])."</textarea>\n";
		$vHTML .=  '<br /><input type="button" name="sql_run_button1" onClick="javascript: openWindowSql(document.Form.quickquery,0);" value="Run" /> '."\n";
		$vHTML .='</td></tr>';

		$s_xml_conf['searchs'][0]['wherequickquery'] = (isset($s_xml_conf['searchs'][0]['wherequickquery'])) ? ($s_xml_conf['searchs'][0]['wherequickquery']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  'Where quick Search:';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<textarea name="wherequickquery" cols="100" rows="8" >'.htmlentities($s_xml_conf['searchs'][0]['wherequickquery'])."</textarea>\n";
		$vHTML .='</td></tr>';		
		
		$s_xml_conf['searchs'][0]['helpquickquery'] = (isset($s_xml_conf['searchs'][0]['helpquickquery'])) ? ($s_xml_conf['searchs'][0]['helpquickquery']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  'Help quick Search:';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<textarea name="helpquickquery" cols="100" rows="8" >'.htmlentities($s_xml_conf['searchs'][0]['helpquickquery'])."</textarea>\n";
		$vHTML .='</td></tr>';		
		}




		}

//_________________________________________________________________________________________________________
	if (	($HTTP_PARAM_VARS['data']=='configuration') )
		{
		if ((!isset($s_xml_conf['configuration'])) or
			(count($s_xml_conf['configuration'])==0))
			{
			$s_xml_conf['configuration'][0]['tagname'] = 'info';
			}

		//Register in the section vars the values of the post/get if those exist
		if (	((isset($HTTP_PARAM_VARS['Save']))) or
			(isset($HTTP_PARAM_VARS['SaveXML'])))
			{
			$s_xml_conf['configuration'][0]['tagname'] = 'info';
			$s_xml_conf['configuration'][0]['content'] = (isset($HTTP_PARAM_VARS['content'])) ? (urldecode($HTTP_PARAM_VARS['content'])): '';
			}


		$vHead .=  '    <a href="javascript: Redirect('."'".'run.php?mod='.$HTTP_PARAM_VARS['mod']."'".')">Run</a>';



		{
		$s_xml_conf['configuration'][0]['content'] = (isset($s_xml_conf['configuration'][0]['content'])) ? ($s_xml_conf['configuration'][0]['content']) : '';
		$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
		$vHTML .=  '<strong>content: </strong>';
		$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
		$vHTML .=  '<textarea name="content" cols="100" rows="8" >'.htmlentities($s_xml_conf['configuration'][0]['content'])."</textarea>\n";
		$vHTML .='</td></tr>';
		}


		}

	//_________________________________________________________________________________________________________
	if ($HTTP_PARAM_VARS['data']=='Links')
		{
		if ((!isset($s_xml_conf['links'])) or
			(count($s_xml_conf['links'])==0))
			{
			$s_xml_conf['links'][0]['tagname'] = 'link';
			$vpos = 0;
			}
		else	{
			$HTTP_PARAM_VARS['link_id'] = (isset($HTTP_PARAM_VARS['link_id'])) ? $HTTP_PARAM_VARS['link_id'] : 0;
			if (isset($HTTP_PARAM_VARS['LastLink']))
				{
				if (isset($HTTP_PARAM_VARS['Search']))
					{
					$vpos = $HTTP_PARAM_VARS['link_id'] + 0;
					}
				else	{
					$vpos = $HTTP_PARAM_VARS['LastLink'] + 0;
					}
				}
			else	{
				$vpos = $HTTP_PARAM_VARS['link_id'] + 0;
				}
			}
		$vpos = (count($s_xml_conf['links'])<$vpos) ? 0 : $vpos;

		$vAttrUsed = array();


		if (!isset($s_xml_conf['links_attrs']))
			{
			$vpath = (parse_url($_SERVER["REQUEST_URI"]));
			$vv = (dirname($vpath['path'])=="\\")? '': dirname($vpath['path']);
			if ($_SERVER["SERVER_SOFTWARE"]=='DWebPro')
				{
				$vpath = 'http://127.0.0.1:8080'.$vv.'/';
				}
			else	{
				$vpath = 'http://'.$_SERVER["HTTP_HOST"].$vv.'/';
				}


				
				//$vpath = $vpath.'edit.xml';
				$vpath = getcwd()."/".'edit.xml';
				$dom =& new XPath();
				$dom->setSkipWhiteSpaces(TRUE);
				if (!$dom->importFromFile($vpath))
					{
					echo "Don´t exist the xml file ".$vpath;
					exit;
					}
				else	
					{
					process_xml_path($dom, '/main', 'links_attrs');
					for ($a = 0; $a < count($s_xml_conf['links_attrs']); $a++)
						{
						$va = split(';', $s_xml_conf['links_attrs'][$a]['attrs']);
						foreach ($va as $vaval)
							{
							if (strpos($vaval,':')==0)
								{
								$s_xml_conf['links_attrs'][$a][$vaval.'attr'] = 'o';
								}
							else	{
								$s_xml_conf['links_attrs'][$a][substr ($vaval, strpos($vaval,':')+1).'_attr'] = substr($vaval, 0,strpos($vaval,':'));
								}
							}

						}
					}
					
			}

			//Searching the link_attr
			if ($s_xml_conf['links'][$vpos]['tagname']=='link')
				{//section link
				$vAttrUsed[] = 'link';  //'section'
				$vtypPos = -1;
				for ($t = 0; $t < count($s_xml_conf['links_attrs']); $t++)
					{
					if ($s_xml_conf['links_attrs'][$t]['name']==$s_xml_conf['links'][$vpos]['tagname'])
						{
						$vtypPos = $t;
						$t = count($s_xml_conf['links_attrs']);
						}
					}
				if ($vtypPos==-1)
					{
					$warning .= "<br /> Don´t exist defined the tagname ".$s_xml_conf['links'][$vpos]['tagname']. " in the xml configuration file...";
					}
				}


		//Register in the section vars the values of the post/get if those exist
		if ((isset($HTTP_PARAM_VARS['LastLink'])) and
			((isset($HTTP_PARAM_VARS['Save']))) or
			(isset($HTTP_PARAM_VARS['SaveXML'])))
			{

			foreach($HTTP_PARAM_VARS as $vvar => $vval)
				{
				$pp = $HTTP_PARAM_VARS['LastLink'] + 0;
				if (strpos($s_xml_conf['links_attrs'][0]['attrs'].';', ':'.$vvar.';')> -1)
					{
					//$warning .= "<br />".$vvar."   AldValue->".$s_xml_conf['links'][$pp][$vvar];
					$s_xml_conf['links'][$pp][$vvar] = stripcslashes($vval);
					//$warning .= '.......NewValue->'.stripcslashes($vval);
					}
				}
			//$vScriptIni .= ' document.Form.Refresh.click(); ';
			}


		$vHead .=  'Links: <select name="link_id" size="1" >'."\n";
		$vHead .=  '<option value="" ></option>'."\n";
		$vLinkLast = '';
		$vLinkNext = '';
		$vSelected = -1;
		$vLast = -1;
		for ($t = 0; $t < count($s_xml_conf['links']); $t++)
			{
			$st = $s_xml_conf['links'][$t]['tagname'];

			$st .= (!empty($s_xml_conf['links'][$t]['content'])) ? ' "'.$s_xml_conf['links'][$t]['content'].'"': '';

			if (!empty($s_xml_conf['links'][$t]['content']))
				{
				if ($t==$vpos)
					{
					if ($vLast>-1)
						{
						$vLinkLast = 'edit.php?mod='.$HTTP_PARAM_VARS['mod'].'&data=Links&link_id='.sprintf ("%s",$vLast) ;
						}
					$vHead .=  '<option value="'.($t).'" selected>'.$st."</option>\n";
					$vSelected = $t;
					}
				else	{
					if ($vSelected>-1)
						{
						$vLinkNext = 'edit.php?mod='.$HTTP_PARAM_VARS['mod'].'&data=Links&link_id='.sprintf ("%s",$t);
						$vSelected = -1;
						}
					$vHead .=  '<option value="'.($t).'">'.$st."</option>\n";
					}
				$vLast = $t;
				}
			}


		$vHead .=  "</select>\n";
		$vHead .=  '<input name="Search" type="submit" value="'.$button_strings['Select'].'">       ';
		$vHead .=  '<input name="LastLink" type="hidden" value="'.$vpos.'">       ';
		$vHead .=  '<br /><br />';
		if ((!IsEmpty($vLinkLast)) or (!IsEmpty($vLinkNext)))
			{  //Show the links Previous and Next?
			if (!IsEmpty($vLinkLast))
				{
				$vHead .=  '    <a href="javascript: Redirect('."'".$vLinkLast."'".')">'.$button_strings['Prev'].'</a>';
				}
			else	{
				$vHead .=  '    '.$button_strings['Prev'];
				}
			if (!IsEmpty($vLinkNext))
				{
				$vHead .=  '    <a href="javascript: Redirect('."'".$vLinkNext."'".')">'.$button_strings['Next'].'</a>';
				}
			else	{
				$vHead .=  '    '.$button_strings['Next'];
				}
			}

		$vHead .=  '    <a href="javascript: Redirect('."'".'run.php?mod='.$HTTP_PARAM_VARS['mod']."'".')">Run</a>';

		//Attr: content
		if (isset($s_xml_conf['links_attrs'][$vtypPos]['content_attr']))
			{
			$vAttrUsed[] = 'content';
			$s_xml_conf['links'][$vpos]['content'] = (isset($s_xml_conf['links'][$vpos]['content'])) ? $s_xml_conf['links'][$vpos]['content'] : '';
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			if ($s_xml_conf['links_attrs'][$vtypPos]['content_attr']=='m')
				{
				$vHTML .=  '<strong>content: </strong>';
				}
			else	{
				$vHTML .=  'content: ';
				}
			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
			$vHTML .=  '<input name="content" type="text" size="100" value="'.$s_xml_conf['links'][$vpos]['content'].'">'."\n";
			$vHTML .='</td></tr>';
			}
		//Attr: href
		if (isset($s_xml_conf['links_attrs'][$vtypPos]['href_attr']))
			{
			$vAttrUsed[] = 'href';
			$s_xml_conf['links'][$vpos]['href'] = (isset($s_xml_conf['links'][$vpos]['href'])) ? $s_xml_conf['links'][$vpos]['href'] : '';
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			if ($s_xml_conf['links_attrs'][$vtypPos]['href_attr']=='m')
				{
				$vHTML .=  '<strong>href: </strong>';
				}
			else	{
				$vHTML .=  'href: ';
				}
			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
			$vHTML .=  '<input name="href" type="text" size="100" value="'.$s_xml_conf['links'][$vpos]['href'].'">'."\n";
			$vHTML .=  '<input type="button" name="help_button" onClick="javascript: window.open(document.Form.href.value);" value=" '.$button_strings['Go'].' ">       '."\n";
			$vHTML .='</td></tr>';
			}
		}



	//_________________________________________________________________________________________________________
	if ( 	($HTTP_PARAM_VARS['data']=='tables') )
		{
		if ((!isset($s_xml_conf['tables'])) or
			(count($s_xml_conf['tables'])==0))
			{
			$s_xml_conf['tables'][0]['tagname'] = 'table';
			$vpos = 0;
			}
		else	{
			$HTTP_PARAM_VARS['table_id'] = (isset($HTTP_PARAM_VARS['table_id'])) ? $HTTP_PARAM_VARS['table_id'] : 0;
			if (isset($HTTP_PARAM_VARS['LastTable']))
				{
				if (isset($HTTP_PARAM_VARS['Search']))
					{
					$vpos = $HTTP_PARAM_VARS['table_id'] + 0;
					}
				else	{
					$vpos = $HTTP_PARAM_VARS['LastTable'] + 0;
					}
				}
			else	{
				$vpos = $HTTP_PARAM_VARS['table_id'] + 0;
				}
			}

		$vAttrUsed = array();
		$vpos = (count($s_xml_conf['tables'])<$vpos) ? 0 : $vpos;

		if (!isset($s_xml_conf['tables_attrs']))
			{
			$vpath = (parse_url($_SERVER["REQUEST_URI"]));
			$vv = (dirname($vpath['path'])=="\\")? '': dirname($vpath['path']);
			if ($_SERVER["SERVER_SOFTWARE"]=='DWebPro')
				{
				$vpath = 'http://127.0.0.1:8080'.$vv.'/';
				}
			else	{
				$vpath = 'http://'.$_SERVER["HTTP_HOST"].$vv.'/';
				}
				
				//$vpath = $vpath.'edit.xml';
				$vpath = getcwd()."/".'edit.xml';
				$dom =& new XPath();
				$dom->setSkipWhiteSpaces(TRUE);
				if (!$dom->importFromFile($vpath))
					{
					echo "Don´t exist the xml file ".$vpath;
					exit;
					}
				else	
					{
					process_xml_path($dom, '/main', 'tables_attrs');
					for ($a = 0; $a < count($s_xml_conf['tables_attrs']); $a++)
						{
						$va = split(';', $s_xml_conf['tables_attrs'][$a]['attrs']);
						foreach ($va as $vaval)
							{
							if (strpos($vaval,':')==0)
								{
								$s_xml_conf['tables_attrs'][$a][$vaval.'attr'] = 'o';
								}
							else	{
								$s_xml_conf['tables_attrs'][$a][substr ($vaval, strpos($vaval,':')+1).'_attr'] = substr($vaval, 0,strpos($vaval,':'));
								}
							}

						}
					}
				
				
				
				
			}

			//Searching the table_attr
			if ($s_xml_conf['tables'][$vpos]['tagname']=='table')
				{//section table
				$vAttrUsed[] = 'table';  //'section'
				$vtypPos = -1;
				for ($t = 0; $t < count($s_xml_conf['tables_attrs']); $t++)
					{
					if ($s_xml_conf['tables_attrs'][$t]['name']==$s_xml_conf['tables'][$vpos]['tagname'])
						{
						$vtypPos = $t;
						$t = count($s_xml_conf['tables_attrs']);
						}
					}
				if ($vtypPos==-1)
					{
					$warning .= "<br /> Don´t exist defined the tagname ".$s_xml_conf['tables'][$vpos]['tagname']. " in the xml configuration file...";
					}
				}


		//Register in the section vars the values of the post/get if those exist
		if ((isset($HTTP_PARAM_VARS['LastTable'])) and
			((isset($HTTP_PARAM_VARS['Save']))) or
			(isset($HTTP_PARAM_VARS['SaveXML'])))
			{

			foreach($HTTP_PARAM_VARS as $vvar => $vval)
				{
				$pp = $HTTP_PARAM_VARS['LastTable'] + 0;
				if (strpos($s_xml_conf['tables_attrs'][0]['attrs'].';', ':'.$vvar.';')> -1)
					{
					//$warning .= "<br />".$vvar."   AldValue->".$s_xml_conf['tables'][$pp][$vvar];
					$s_xml_conf['tables'][$pp][$vvar] = stripcslashes($vval);
					//$warning .= '.......NewValue->'.stripcslashes($vval);
					}
				}
			//$vScriptIni .= ' document.Form.Refresh.click(); ';
			}


		$vHead .=  'Tables: <select name="table_id" size="1" >'."\n";
		$vHead .=  '<option value="" ></option>'."\n";
		$vLinkLast = '';
		$vLinkNext = '';
		$vSelected = -1;
		$vLast = -1;
		for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
			{
			$st = $s_xml_conf['tables'][$t]['tagname'];

			$st .= (!empty($s_xml_conf['tables'][$t]['name'])) ? ' "'.$s_xml_conf['tables'][$t]['name'].'"': '';

			if (!empty($st))
				{
				if ($t==$vpos)
					{
					if ($vLast>-1)
						{
						$vLinkLast = 'edit.php?mod='.$HTTP_PARAM_VARS['mod'].'&data=tables&table_id='.sprintf ("%s",$vLast) ;
						}
					$vHead .=  '<option value="'.($t).'" selected>'.$st."</option>\n";
					$vSelected = $t;
					}
				else	{
					if ($vSelected>-1)
						{
						$vLinkNext = 'edit.php?mod='.$HTTP_PARAM_VARS['mod'].'&data=tables&table_id='.sprintf ("%s",$t);
						$vSelected = -1;
						}
					$vHead .=  '<option value="'.($t).'">'.$st."</option>\n";
					}
				$vLast = $t;
				}
			}


		$vHead .=  "</select>\n";
		$vHead .=  '<input name="Search" type="submit" value="'.$button_strings['Select'].'">       ';
		$vHead .=  '<input name="LastTable" type="hidden" value="'.$vpos.'">       ';
		$vHead .=  '<br /><br />';

		if ((!IsEmpty($vLinkLast)) or (!IsEmpty($vLinkNext)))
			{  //Show the links Previous and Next?
			if (!IsEmpty($vLinkLast))
				{
				$vHead .=  '    <a href="javascript: Redirect('."'".$vLinkLast."'".')">'.$button_strings['Prev'].'</a>';
				}
			else	{
				$vHead .=  '    '.$button_strings['Prev'];
				}
			if (!IsEmpty($vLinkNext))
				{
				$vHead .=  '    <a href="javascript: Redirect('."'".$vLinkNext."'".')">'.$button_strings['Next'].'</a>';
				}
			else	{
				$vHead .=  '    '.$button_strings['Next'];
				}
			}

		$vHead .=  '    <a href="javascript: Redirect('."'".'run.php?mod='.$HTTP_PARAM_VARS['mod']."'".')">Run</a>';

		//Attr: name
		if (isset($s_xml_conf['tables_attrs'][$vtypPos]['name_attr']))
			{
			$vAttrUsed[] = 'name';
			$s_xml_conf['tables'][$vpos]['name'] = (isset($s_xml_conf['tables'][$vpos]['name'])) ? $s_xml_conf['tables'][$vpos]['name'] : '';
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			if ($s_xml_conf['tables_attrs'][$vtypPos]['name_attr']=='m')
				{
				$vHTML .=  '<strong>Table: </strong>';
				}
			else	{
				$vHTML .=  'Table: ';
				}
			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
			$vHTML .=  '<select name="name" size="1" onChange="javascript: CheckTableChange(document.Form.name.value);">'."\n";
			$vHTML .=  '<option value="" ></option>'."\n";
			//if ( (!isset($s_xml_conf['tables_list'])) && ($s_connection['conected']) )
			if ($s_connection['conected']) 
				{
				$rec = $dbhandle->MetaTables();

				if ($rec)
					{
					$tableCount = sizeof($rec);
					for ($i=0; $i < $tableCount; $i++)
						{
						$s_xml_conf['tables_list'][$rec[$i]] = '';
						}
					}
				else	{
					$s_xml_conf['tables_list'][''] = '';
					}
				//ksort ($s_xml_conf['tables_list']);

				$vTable = $s_xml_conf['tables'][$vpos]['name'];
				foreach ($s_xml_conf['tables_list'] as $vvar => $vval)
					{
					if ($vvar==$s_xml_conf['tables'][$vpos]['name'])
						{
						$vHTML .=  '<option value="'.$vvar.'" selected>'.$vvar."</option>\n";

						if ((!isset($s_xml_conf['tables'][$vpos]['field'])) and (!empty($vTable)))
							{
							//we go to know what fields exist in the table
							$vt = $syntax['table'];
							$vt = ereg_replace('#1',$vTable, $vt);
							$vSql = 'SELECT * FROM '.$vt;
							$rec = $dbhandle->SelectLimit($vSql,1);
							for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++)
								{
								$fld = $rec->FetchField($i);
								$type = $rec->MetaType($fld->type);
								$s_xml_conf['tables'][$vpos]['field'][$fld->name] = $type;
								}
							}
						}
					else	
						{
						$vHTML .=  '<option value="'.$vvar.'">'.$vvar."</option>\n";
						}
					}


				$vHTML .=  "</select>\n";
				}



			$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#table'."'".')">'.$vHelpChar.'</a>';
			$vHTML .=  '<input type="button" disabled="disabled" name="table_add_button" onClick="javascript: window.open(document.Form.href.value);" value="Add table">       '."\n";
			$vHTML .='</td></tr>';
			}

		//Attr: pk
		if (isset($s_xml_conf['tables_attrs'][$vtypPos]['pk_attr']))
			{
			$vvtemp = array();
			$vAttrUsed[] = 'pk';
			$s_xml_conf['tables'][$vpos]['pk'] = (isset($s_xml_conf['tables'][$vpos]['pk'])) ? $s_xml_conf['tables'][$vpos]['pk'] : '';
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			if ($s_xml_conf['tables_attrs'][$vtypPos]['pk_attr']=='m')
				{
				$vHTML .=  '<strong>pk: </strong>';
				}
			else	{
				$vHTML .=  'pk: ';
				}


			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

			$vDelimitedChar = ';';
			$vrows = '9';

			$vHTML .=  "<table border='0'>\n";
			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td>Campos</td>\n";
			$vHTML .=  "	<td></td>\n";
			$vHTML .=  "	<td>Seleccionados</td>\n";
			$vHTML .=  "</tr>\n";

			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td>\n";
			$vHTML .=  '	<select name="pk_combobox" size="'.$vrows.'" >'."\n";

			if (isset($s_xml_conf['tables'][$vpos]['field']))
				{
				foreach ($s_xml_conf['tables'][$vpos]['field'] as $vfnam => $vfval)
					{
					$vHTML .=  '<option value="'.$vfnam.'" >'.$vfnam."</option>\n";
					}
				}

			$vHTML .=  "	</select>\n";
			$vHTML .=  "	</td>\n";
			$vHTML .=  "	<td>\n";
			//Button add
			$vHTML .=  "		<table border='0'>\n";
			$vHTML .=  '		<tr><td><input name="pk_ButtonAdd" type="button"  onClick="listboxAdd(document.Form.pk_combobox, document.Form.pk_listbox, document.Form.pk, document.Form.pk_selected, '."'".$vDelimitedChar."'".')"';
			$vHTML .=  ' value=" > "></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="pk_ButtonDel" type="button"  onClick="listboxDel(document.Form.pk_listbox, document.Form.pk, document.Form.pk_selected, '."'".$vDelimitedChar."'".')" value=" < "></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="pk_ButtonDel" type="button"  onClick="listboxClear(document.Form.pk_listbox, document.Form.pk, document.Form.pk_selected, '."'".$vDelimitedChar."'".')" value="'.$button_strings['Start'].'"></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="pk_ButtonUp" type="button"  onClick="listboxUp(document.Form.pk_listbox, document.Form.pk, document.Form.pk_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Up'].'"></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="pk_ButtonUp" type="button"  onClick="listboxDown(document.Form.pk_listbox, document.Form.pk, document.Form.pk_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Down'].'"></td></tr>'."\n";
			$vHTML .=  "		</table>\n";
			$vHTML .=  "	</td>\n";
			$vHTML .=  "	<td>\n";
			$vHTML .=  '	<select size="'.$vrows.'" multiple name="pk_listbox" onChange="listboxUpdate(document.Form.pk_listbox, document.Form.pk, document.Form.pk_selected, '."'".$vDelimitedChar."'".')">'."\n";

			$vvtemp1 = split($vDelimitedChar,$s_xml_conf['tables'][$vpos]['pk']);
			foreach ($vvtemp1 as $tt)  {
				$vvtemp[$tt] = '';
				}

			foreach ($vvtemp as $tt=>$vv)
				{
				if ($tt)
					{
					$vHTML .=  '	<option value="'.$tt.'" >'.$tt."</option>\n";
					}
				}
			$vHTML .=  "	</select>\n";
			$vHTML .=  "	</td>\n";
			$vHTML .=  "</tr>\n";
			$vHTML .=  "</table>\n";
			$vHTML .= '<input name="pk" type="hidden" value="'.$s_xml_conf['tables'][$vpos]['pk'].'">';
			$vHTML .= '<input name="pk_selected" type="hidden" value="">';

			$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#pk'."'".')">'.$vHelpChar.'</a>';
			$vHTML .=  '<input type="button" disabled="disabled" name="field_add_button" onClick="javascript: window.open(document.Form.href.value);" value="Add Field">       '."\n";
			$vHTML .='</td></tr>';
			}

		//Attr: autonumeric
		if (isset($s_xml_conf['tables_attrs'][$vtypPos]['autonumeric_attr']))
			{
			$vvtemp = array();
			$vAttrUsed[] = 'autonumeric';
			$s_xml_conf['tables'][$vpos]['autonumeric'] = (isset($s_xml_conf['tables'][$vpos]['autonumeric'])) ? $s_xml_conf['tables'][$vpos]['autonumeric'] : '';
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			if ($s_xml_conf['tables_attrs'][$vtypPos]['autonumeric_attr']=='m')
				{
				$vHTML .=  '<strong>autonumeric: </strong>';
				}
			else	{
				$vHTML .=  'autonumeric: ';
				}


			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

			$vDelimitedChar = ';';
			$vrows = '9';

			$vHTML .=  "<table border='0'>\n";
			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td>Campo</td>\n";
			$vHTML .=  "	<td></td>\n";
			$vHTML .=  "	<td>Generador</td>\n";
			$vHTML .=  "</tr>\n";

			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td>\n";
			$vHTML .=  '	<select name="autonumeric_combobox" size="1" >'."\n";
			$vHTML .=  '	<option value=""></option>'."\n";
			if (isset($s_xml_conf['tables'][$vpos]['field']))
				{
				foreach ($s_xml_conf['tables'][$vpos]['field'] as $vfnam => $vfval)
					{
					$vHTML .=  '<option value="'.$vfnam.'" >'.$vfnam."</option>\n";
					}
				}

			$vHTML .=  "	</select>\n";
			$vHTML .=  "	<td>:</td>\n";
			$vHTML .=  "	<td>\n";
			$vHTML .=  "	<input name='generator' type='text' value=''>\n";
			$vHTML .=  "	</td>\n";
			$vHTML .=  "</tr>\n";
			$vHTML .=  "</table>\n";

			$vHTML .=  "<table border='0'>\n";
			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td></td>\n";
			$vHTML .=  "	<td>Seleccionados</td>\n";
			$vHTML .=  "</tr>\n";
			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td>\n";
			//Button add
			$vHTML .=  "		<table border='0'>\n";
			$vHTML .=  '		<tr><td><input name="autonumeric_ButtonAdd" type="button"  onClick="listboxAddGen(document.Form.autonumeric_combobox, document.Form.generator, document.Form.autonumeric_listbox, document.Form.autonumeric, document.Form.autonumeric_selected, '."'".$vDelimitedChar."'".')"';
			$vHTML .=  ' value=" > "></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="autonumeric_ButtonDel" type="button"  onClick="listboxDel(document.Form.autonumeric_listbox, document.Form.autonumeric, document.Form.autonumeric_selected, '."'".$vDelimitedChar."'".')" value=" < "></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="autonumeric_ButtonDel" type="button"  onClick="listboxClear(document.Form.autonumeric_listbox, document.Form.autonumeric, document.Form.autonumeric_selected, '."'".$vDelimitedChar."'".')" value="'.$button_strings['Start'].'"></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="autonumeric_ButtonUp" type="button"  onClick="listboxUp(document.Form.autonumeric_listbox, document.Form.autonumeric, document.Form.autonumeric_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Up'].'"></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="autonumeric_ButtonUp" type="button"  onClick="listboxDown(document.Form.autonumeric_listbox, document.Form.autonumeric, document.Form.autonumeric_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Down'].'"></td></tr>'."\n";
			$vHTML .=  "		</table>\n";
			$vHTML .=  "	</td>\n";
			$vHTML .=  "	<td>\n";

			$vHTML .=  '	<select size="'.$vrows.'" multiple name="autonumeric_listbox" onChange="listboxUpdate(document.Form.autonumeric_listbox, document.Form.autonumeric, document.Form.autonumeric_selected, '."'".$vDelimitedChar."'".')">'."\n";
			$vvtemp1 = split($vDelimitedChar,$s_xml_conf['tables'][$vpos]['autonumeric']);
			foreach ($vvtemp1 as $tt)  {
				$vvtemp[$tt] = '';
				}

			foreach ($vvtemp as $tt=>$vv)
				{
				if ($tt)
					{
					$vHTML .=  '	<option value="'.$tt.'" >'.$tt."</option>\n";
					}
				}
			$vHTML .=  "	</select>\n";
			$vHTML .=  "	</td>\n";
			$vHTML .=  "</tr>\n";
			$vHTML .=  "</table>\n";
			$vHTML .= '<input name="autonumeric" type="hidden" value="'.$s_xml_conf['tables'][$vpos]['autonumeric'].'">';
			$vHTML .= '<input name="autonumeric_selected" type="hidden" value="">';

			$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#autonumeric'."'".')">'.$vHelpChar.'</a>';
			$vHTML .=  '<input type="button" disabled="disabled" name="field_add_button" onClick="javascript: window.open(document.Form.href.value);" value="Add Field">       '."\n";
			$vHTML .='</td></tr>';
			}

		//Attr: fk
		if (isset($s_xml_conf['tables_attrs'][$vtypPos]['fk_attr']))
			{
			$vvtemp = array();
			$vAttrUsed[] = 'fk';
			$s_xml_conf['tables'][$vpos]['fk'] = (isset($s_xml_conf['tables'][$vpos]['fk'])) ? $s_xml_conf['tables'][$vpos]['fk'] : '';
			$s_xml_conf['tables'][$vpos]['fknull'] = (isset($s_xml_conf['tables'][$vpos]['fknull'])) ? $s_xml_conf['tables'][$vpos]['fknull'] : '';
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			if ($s_xml_conf['tables_attrs'][$vtypPos]['fk_attr']=='m')
				{
				$vHTML .=  '<strong>fk: </strong>';
				}
			else	{
				$vHTML .=  'fk: ';
				}


			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

			$vDelimitedChar = ';';
			$vrows = '9';

			$vHTML .=  "<table border='0'>\n";
			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td>Campo</td>\n";
			$vHTML .=  "	<td></td>\n";
			$vHTML .=  "	<td>Tabla</td>\n";
			$vHTML .=  "	<td></td>\n";
			$vHTML .=  "	<td>Campo</td>\n";
			$vHTML .=  "</tr>\n";

			$vHTML .=  "<tr>\n";

			$vHTML .=  "	<td>\n";
			$vHTML .=  '	<select name="fk_combobox" size="1" >'."\n";
			$vHTML .=  '	<option value=""></option>'."\n";
			if (isset($s_xml_conf['tables'][$vpos]['field']))
				{
				foreach ($s_xml_conf['tables'][$vpos]['field'] as $vfnam => $vfval)
					{
					$vHTML .=  '<option value="'.$vfnam.'" >'.$vfnam."</option>\n";
					}
				}
			$vHTML .=  "	</select>\n";
			$vHTML .=  "	</td>\n";

			$vHTML .=  "	<td>=</td>\n";

			$vHTML .=  "	<td>\n";
			$vHTML .=  '	<select name="fk_table" size="1" onChange="javascript: openWindowGiveMeFields(true,this, document.Form.fk_field);" >'."\n";
			$vHTML .=  '	<option value=""></option>'."\n";
			for ($a = 0; $a < count($s_xml_conf['tables']); $a++)
				{
				$vHTML .=  '	<option value="'.$s_xml_conf['tables'][$a]['name'].'">'.$s_xml_conf['tables'][$a]['name']."</option>\n";
				}
			$vHTML .=  "	</select>\n";
			$vHTML .=  "	</td>\n";

			$vHTML .=  "	<td>.</td>\n";

			$vHTML .=  "	<td>\n";

			$vHTML .=  '	<select name="fk_field" size="1" >'."\n";
			$vHTML .=  '	<option value=""></option>'."\n";
			$vHTML .=  "	</select>\n";
			$vHTML .=  "	</td>\n";


			$vHTML .=  "</tr>\n";
			$vHTML .=  "</table>\n";

			$vHTML .=  "<table border='0'>\n";
			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td></td>\n";
			$vHTML .=  "	<td>Seleccionados</td>\n";
			$vHTML .=  "	<td></td>\n";
			$vHTML .=  "	<td>Campos nulos permitidos al grabar</td>\n";
			$vHTML .=  "</tr>\n";
			$vHTML .=  "<tr>\n";
			$vHTML .=  "	<td>\n";
			//Button add
			$vHTML .=  "		<table border='0'>\n";
			$vHTML .=  '		<tr><td><input name="fk_ButtonAdd" type="button"  onClick="listboxAddFk(document.Form.fk_combobox, document.Form.fk_table, document.Form.fk_field, document.Form.fk_listbox, document.Form.fk, document.Form.fk_selected, '."'".$vDelimitedChar."'".')"';
			$vHTML .=  ' value=" > "></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="fk_ButtonDel" type="button"  onClick="listboxDelFk(document.Form.fk_listbox, document.Form.fk, document.Form.fk_selected, '."'".$vDelimitedChar."'".')" value=" < "></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="fk_ButtonDel" type="button"  onClick="listboxClear(document.Form.fk_listbox, document.Form.fk, document.Form.fk_selected, '."'".$vDelimitedChar."'".')" value="'.$button_strings['Start'].'"></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="fk_ButtonUp" type="button"  onClick="listboxUp(document.Form.fk_listbox, document.Form.fk, document.Form.fk_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Up'].'"></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="fk_ButtonUp" type="button"  onClick="listboxDown(document.Form.fk_listbox, document.Form.fk, document.Form.fk_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Down'].'"></td></tr>'."\n";
			$vHTML .=  "		</table>\n";
			$vHTML .=  "	</td>\n";
			$vHTML .=  "	<td>\n";
			//Seleccionados
			$vHTML .=  '	<select size="'.$vrows.'" multiple name="fk_listbox" onChange="listboxUpdate(document.Form.fk_listbox, document.Form.fk, document.Form.fk_selected, '."'".$vDelimitedChar."'".')">'."\n";

			$vvtemp1 = split($vDelimitedChar,$s_xml_conf['tables'][$vpos]['fk']);
			foreach ($vvtemp1 as $tt)  {
				$vvtemp[$tt] = '';
				}

			foreach ($vvtemp as $tt=>$vv)
				{
				if ($tt)
					{
					$vHTML .=  '	<option value="'.$tt.'" >'.$tt."</option>\n";
					}
				}
			$vHTML .=  "	</select>\n";
			$vHTML .=  "	</td>\n";

			//
			$vHTML .=  "	<td>\n";
			$vHTML .=  "		<table border='0'>\n";
			$vHTML .=  '		<tr><td><input name="fk_ButtonAdd" type="button"  onClick="listboxAddfknull(document.Form.fk_listbox, document.Form.fknull_listbox, document.Form.fknull, document.Form.fknull_selected, '."'".$vDelimitedChar."'".')"';
			$vHTML .=  ' value=" > "></td></tr>'."\n";
			$vHTML .=  '		<tr><td><input name="fk_ButtonDel" type="button"  onClick="listboxDel(document.Form.fknull_listbox, document.Form.fknull, document.Form.fknull_selected, '."'".$vDelimitedChar."'".')" value=" < "></td></tr>'."\n";
			$vHTML .=  "		</table>\n";
			$vHTML .=  "	</td>\n";

			//campos nulos permitidos al grabar
			$vHTML .=  "	<td>\n";
			$vHTML .=  '	<select size="'.$vrows.'" multiple name="fknull_listbox" onChange="listboxUpdate(document.Form.fk_listbox, document.Form.fk, document.Form.fk_selected, '."'".$vDelimitedChar."'".')">'."\n";

			$vvtemp = array();
			if (isset($s_xml_conf['tables'][$vpos]['fknull']))
				{
				$vvtemp1 = split($vDelimitedChar,$s_xml_conf['tables'][$vpos]['fknull']);
				}
			else	{
				$vvtemp1 = array();
				}
			foreach ($vvtemp1 as $tt)  {
				$vvtemp[$tt] = '';
				}

			foreach ($vvtemp as $tt=>$vv)
				{
				if ($tt)
					{
					$vHTML .=  '	<option value="'.$tt.'" >'.$tt."</option>\n";
					}
				}
			$vHTML .=  "	</select>\n";
			$vHTML .=  "	</td>\n";


			$vHTML .=  "</tr>\n";
			$vHTML .=  "</table>\n";
			$vHTML .= '<input name="fk" type="hidden" value="'.$s_xml_conf['tables'][$vpos]['fk'].'">';
			$vHTML .= '<input name="fk_selected" type="hidden" value="">';
			$vHTML .= '<input name="fknull_selected" type="hidden" value="">';
			$vHTML .= '<input name="fknull" type="hidden" value="'.$s_xml_conf['tables'][$vpos]['fknull'].'">';

			$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#fk'."'".')">'.$vHelpChar.'</a>';
			$vHTML .=  '<input type="button" disabled="disabled" name="field_add_button" onClick="javascript: window.open(document.Form.href.value);" value="Add Field">       '."\n";
			$vHTML .='</td></tr>';
			}

		}



	//_________________________________________________________________________________________________________
	if (	($HTTP_PARAM_VARS['data']=='elements') )
		{
		if (isset($HTTP_PARAM_VARS['searchelement']))
			{
			for ($t = 0; $t < count($s_xml_conf['elements']); $t++)
				{
				if ( 	(!empty($s_xml_conf['elements'][$t]['table'])) &&
					(!empty($s_xml_conf['elements'][$t]['field'])) &&
					($s_xml_conf['elements'][$t]['table'].".".$s_xml_conf['elements'][$t]['field']==$HTTP_PARAM_VARS['searchelement'])
				   )
				   {
				   $HTTP_PARAM_VARS['LastElement'] = $t;
				   $t = 999;
				   }
				 }
			}

		if ((!isset($s_xml_conf['elements'])) or
			(count($s_xml_conf['elements'])==0))
			{
			$s_xml_conf['elements'][0]['tagname'] = 'element';
			$s_xml_conf['elements'][0]['type'] = 'textbox';
			$vpos = 0;
			}
		else	{
			$HTTP_PARAM_VARS['element'] = (isset($HTTP_PARAM_VARS['element'])) ? $HTTP_PARAM_VARS['element'] : 0;
			if (isset($HTTP_PARAM_VARS['LastElement']))
				{
				if (isset($HTTP_PARAM_VARS['Search']))
					{
					$vpos = $HTTP_PARAM_VARS['element'] + 0;
					}
				else	{
					$vpos = $HTTP_PARAM_VARS['LastElement'] + 0;
					}
				}
			else	{
				$vpos = $HTTP_PARAM_VARS['element'] + 0;
				}
			}
		$vpos = (count($s_xml_conf['elements'])<$vpos) ? 0: $vpos;

			{
			$vAttrUsed = array();

			if (!isset($s_xml_conf['elements_attrs']))
				{

				$vpath = (parse_url($_SERVER["REQUEST_URI"]));
				$vv = (dirname($vpath['path'])=="\\")? '': dirname($vpath['path']);
				if ($_SERVER["SERVER_SOFTWARE"]=='DWebPro')
					{
					$vpath = 'http://127.0.0.1:8080'.$vv.'/';
					}
				else	
					{
					$vpath = 'http://'.$_SERVER["HTTP_HOST"].$vv.'/';
					}
					
				//$vpath = $vpath.'edit.xml';
				$vpath = getcwd()."/".'edit.xml';
				$dom =& new XPath();
				$dom->setSkipWhiteSpaces(TRUE);
				if (!$dom->importFromFile($vpath))
					{
					echo "Don´t exist the xml file ".$vpath;
					exit;
					}
				else	
					{
					
					process_xml_path($dom, '/main', 'elements_attrs');
					for ($a = 0; $a < count($s_xml_conf['elements_attrs']); $a++)
						{
						$va = split(';', $s_xml_conf['elements_attrs'][$a]['attrs']);
						foreach ($va as $vaval)
							{
							if (strpos($vaval,':')==0)
								{
								$s_xml_conf['elements_attrs'][$a][$vaval.'attr'] = 'o';
								}
							else	{
								$s_xml_conf['elements_attrs'][$a][substr ($vaval, strpos($vaval,':')+1).'_attr'] = substr($vaval, 0,strpos($vaval,':'));
								}
							}
						}						
					}
				}

			//Some especification to tagname='element' or 'section'
			if ($s_xml_conf['elements'][$vpos]['tagname']=='section')
				{//section tagname
				$vAttrUsed[] = 'tagname';  //'section'
				$vtypPos = -1;
				for ($t = 0; $t < count($s_xml_conf['elements_attrs']); $t++)
					{
					if ($s_xml_conf['elements_attrs'][$t]['name']==$s_xml_conf['elements'][$vpos]['tagname'])
						{
						$vtypPos = $t;
						$t = count($s_xml_conf['elements_attrs']);
						}
					}
				if ($vtypPos==-1)
					{
					$warning .= "<br /> Don´t exist defined the tagname ".$s_xml_conf['elements'][$vpos]['tagname']. " in the xml configuration file...";
					}
				}
			elseif ($s_xml_conf['elements'][$vpos]['tagname']=='element')
				{//init of element tagname
				$vAttrUsed[] = 'tagname';  //'element'

				$vtypPos = -1;
				$s_xml_conf['elements'][$vpos]['type'] = (isset($s_xml_conf['elements'][$vpos]['type'])) ? $s_xml_conf['elements'][$vpos]['type'] : 'textbox';
				for ($t = 0; $t < count($s_xml_conf['elements_attrs']); $t++)
					{
					if ($s_xml_conf['elements_attrs'][$t]['name']==$s_xml_conf['elements'][$vpos]['tagname'])
						{
						if ((isset($s_xml_conf['elements'][$vpos]['type'])) and
						   (!empty($s_xml_conf['elements'][$vpos]['type'])))
							{
							//case of element tagname -> there is a type defined
							if ($s_xml_conf['elements_attrs'][$t]['content']==$s_xml_conf['elements'][$vpos]['type'])
								{
								$vtypPos = $t;
								$t = count($s_xml_conf['elements_attrs']);
								}
							}
						elseif ($s_xml_conf['elements_attrs'][$t]['content']=='__empty__')
							{
							$vtypPos = $t;
							$t = count($s_xml_conf['elements_attrs']);
							}
						}
					}
				if ($vtypPos==-1)
					{
					$vtypPos=0;
					//$warning .= "<br /> Don´t exist defined the type ".$s_xml_conf['elements'][$vpos]['type']. " in the xml configuration file...";
					}
				}

			//Register in the section vars the values of the post/get if those exist
			if ((isset($HTTP_PARAM_VARS['LastElement'])) and
				((isset($HTTP_PARAM_VARS['Save']))) or
				(isset($HTTP_PARAM_VARS['SaveXML'])))
				{
				foreach($HTTP_PARAM_VARS as $vvar => $vval)
					{
					if (array_key_exists($vvar.'_attr', $s_xml_conf['elements_attrs'][$vtypPos]))
						{
						$pp = $HTTP_PARAM_VARS['LastElement'] + 0;
						//$warning .= "<br />".$vvar."   AldValue->".$s_xml_conf['elements'][$HTTP_PARAM_VARS['LastElement']][$vvar];
						$s_xml_conf['elements'][$pp][$vvar] = ($vval);
						//$s_xml_conf['elements'][$pp][$vvar] = stripcslashes($vval);
						//$warning .= '.......NewValue->'.stripcslashes($vval);
						}
					}
				$vScriptIni .= ' document.Form.Refresh.click(); ';
				}



			$vHead .=  'Elements: <select name="element" size="1" >'."\n";
			$vHead .=  '<option value="" ></option>'."\n";
			$vLinkLast = '';
			$vLinkNext = '';
			$vSelected = -1;
			$vLast = -1;
			for ($t = 0; $t < count($s_xml_conf['elements']); $t++)
				{
				if ($s_xml_conf['elements'][$t]['tagname']=='element')
					{
					$s_xml_conf['elements'][$t]['type'] = (isset($s_xml_conf['elements'][$t]['type'])) ? $s_xml_conf['elements'][$t]['type'] : 'textbox';
					$st = $s_xml_conf['elements'][$t]['type'];
					}
				else	{
					$st = $s_xml_conf['elements'][$t]['tagname'];
					}
				$st .= (!empty($s_xml_conf['elements'][$t]['content'])) ? ' "'.$s_xml_conf['elements'][$t]['content'].'"': '';
				$st .= (!empty($s_xml_conf['elements'][$t]['table'])) ? ' ('.$s_xml_conf['elements'][$t]['table']: '';
				$st .= (!empty($s_xml_conf['elements'][$t]['field'])) ? '.'.$s_xml_conf['elements'][$t]['field'].')': '';
				if (!empty($st))
					{
					if ($t==$vpos)
						{
						if ($vLast>-1)
							{
							$vLinkLast = 'edit.php?mod='.$HTTP_PARAM_VARS['mod'].'&data=elements&element='.sprintf ("%s",$vLast) ;
							}
						$vHead .=  '<option value="'.($t).'" selected>'.$st."</option>\n";
						$vSelected = $t;
						}
					else	{
						if ($vSelected>-1)
							{
							$vLinkNext = 'edit.php?mod='.$HTTP_PARAM_VARS['mod'].'&data=elements&element='.sprintf ("%s",$t);
							$vSelected = -1;
							}
						$vHead .=  '<option value="'.($t).'">'.$st."</option>\n";
						}
					$vLast = $t;
					}
				}

			$vHead .=  "</select>\n";
			$vHead .=  '<input name="Search" type="submit" value="'.$button_strings['Select'].'">       ';
			$vHead .=  '<input name="LastElement" type="hidden" value="'.$vpos.'">       ';
			$vHead .=  '<br /><br />';
			if ((!IsEmpty($vLinkLast)) or (!IsEmpty($vLinkNext)))
				{  //Show the links Previous and Next?
				if (!IsEmpty($vLinkLast))
					{
					$vHead .=  '    <a href="javascript: Redirect('."'".$vLinkLast."'".')">'.$button_strings['Prev'].'</a>';
					}
				else	{
					$vHead .=  '    '.$button_strings['Prev'];
					}
				if (!IsEmpty($vLinkNext))
					{
					$vHead .=  '    <a href="javascript: Redirect('."'".$vLinkNext."'".')">'.$button_strings['Next'].'</a>';
					}
				else	{
					$vHead .=  '    '.$button_strings['Next'];
					}
				}
			$vHead .=  '    <a href="javascript: Redirect('."'".'run.php?mod='.$HTTP_PARAM_VARS['mod']."'".')">Run</a>';

			//----------------------------------------------------------------------------------

			//Attr: tagname
			$s_xml_conf['elements'][$vpos]['tagname'] = (isset($s_xml_conf['elements'][$vpos]['tagname'])) ? $s_xml_conf['elements'][$vpos]['tagname'] : 'element';
			$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
			$vHTML .=  '<strong>Tagname: </strong>';
			$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
			$vHTML .=  '<select name="tagname">'."\n";
			$vHTML .=  '<option value="" ></option>'."\n";
			if ($s_xml_conf['elements'][$vpos]['tagname']=='element')
				{
				$vHTML .=  '<option value="element" selected>element</option>'."\n";
				$vHTML .=  '<option value="section">section</option>'."\n";
				}
			else 	{
				$vHTML .=  '<option value="element" >element</option>'."\n";
				$vHTML .=  '<option value="section" selected>section</option>'."\n";			}

			$vHTML .=  "</select>\n";

			$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#tagname'."'".')">'.$vHelpChar.'</a>';
			$vHTML .='</td></tr>';


			//Attr: content
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['content_attr']))
				{
				$vAttrUsed[] = 'content';
				$s_xml_conf['elements'][$vpos]['content'] = (isset($s_xml_conf['elements'][$vpos]['content'])) ? $s_xml_conf['elements'][$vpos]['content'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['content_attr']=='m')
					{
					$vHTML .=  '<strong>content: </strong>';
					}
				else	{
					$vHTML .=  'content: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="content" type="text" size="100" value="'.$s_xml_conf['elements'][$vpos]['content'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#content'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: querylabel
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['querylabel_attr']))
				{
				$vAttrUsed[] = 'querylabel';
				$s_xml_conf['elements'][$vpos]['querylabel'] = (isset($s_xml_conf['elements'][$vpos]['querylabel'])) ? $s_xml_conf['elements'][$vpos]['querylabel'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['querylabel_attr']=='m')
					{
					$vHTML .=  '<strong>querylabel: </strong>';
					}
				else	{
					$vHTML .=  'querylabel: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="querylabel" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['querylabel'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#querylabel'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: type
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['type_attr']))
				{
				$vAttrUsed[] = 'type';
				$s_xml_conf['elements'][$vpos]['type'] = (isset($s_xml_conf['elements'][$vpos]['type'])) ? $s_xml_conf['elements'][$vpos]['type'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['type_attr']=='m')
					{
					$vHTML .=  '<strong>Type: </strong>';
					}
				else	{
					$vHTML .=  'Type: ';
					}
				$vHTML .=  '</div></td><td class="TabletypeValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<select name="type" size="1" onChange="javascript: CheckTypeChange();">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				for ($t = 0; $t < count($s_xml_conf['elements_attrs']); $t++)
					if (!empty($s_xml_conf['elements_attrs'][$t]['content']))
					{
					if ($s_xml_conf['elements'][$vpos]['type']==$s_xml_conf['elements_attrs'][$t]['content'])
						{
						$vHTML .=  '<option value="'.$s_xml_conf['elements_attrs'][$t]['content'].'" selected>'.$s_xml_conf['elements_attrs'][$t]['content']."</option>\n";
						}
					else	{
						$vHTML .=  '<option value="'.$s_xml_conf['elements_attrs'][$t]['content'].'">'.$s_xml_conf['elements_attrs'][$t]['content']."</option>\n";
						}
					}
				$vHTML .=  "</select>\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#'."'+document.Form.type.value".')">'.$vHelpChar.'</a>';

				//show the equivalents components if is defined the attribute equivalent
				if (isset($s_xml_conf['elements_attrs'][$vtypPos]['equivalent']))
					{
					$vHTML .=  " [".$s_xml_conf['elements_attrs'][$vtypPos]['equivalent']."] is a component equivalent of [".$s_xml_conf['elements_attrs'][$vtypPos]['content']."]";
					}

				$vHTML .='</td></tr>';
				$vCheckTypeChange .= ' if (document.Form.type.value!= "'.$s_xml_conf['elements'][$vpos]['type'].'") {'."\n";
				$vCheckTypeChange .= ' 		alert("We go to update the value of the fields related with this type..."); '."\n";
				$vCheckTypeChange .= ' 		document.Form.Save.click(); '."\n";
				//$vCheckTypeChange .= ' for (var i = 0; i < document.Form.elements.length; i++)'."\n";
				//$vCheckTypeChange .= ' 		{ '."\n";
				//$vCheckTypeChange .= ' 		if ( (document.Form.elements[i].name!="type") && !(document.Form.elements[i].type == "submit") && !(document.Form.elements[i].type == "button") )'."\n";
				//$vCheckTypeChange .= ' 			{'."\n";
				//$vCheckTypeChange .= ' 			document.Form.elements[i].disabled = "disabled";'."\n";
				//$vCheckTypeChange .= ' 			}'."\n";
				//$vCheckTypeChange .= ' 		}}'."\n";
				//$vCheckTypeChange .= ' else	{'."\n";
				//$vCheckTypeChange .= ' 		for (var i = 0; i < document.Form.elements.length; i++)'."\n";
				//$vCheckTypeChange .= ' 			{'."\n";
				//$vCheckTypeChange .= ' 			document.Form.elements[i].disabled = "";'."\n";
				//$vCheckTypeChange .= ' 			}'."\n";
				$vCheckTypeChange .= ' 		}'."\n";
				}

			//Attr: filename
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['filename_attr']))
				{
				$vAttrUsed[] = 'filename';
				$s_xml_conf['elements'][$vpos]['filename'] = (isset($s_xml_conf['elements'][$vpos]['filename'])) ? $s_xml_conf['elements'][$vpos]['filename'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['filename_attr']=='m')
					{
					$vHTML .=  '<strong>filename: </strong>';
					}
				else	{
					$vHTML .=  'filename: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="filename" type="text" size="100" value="'.$s_xml_conf['elements'][$vpos]['filename'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#filename'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: var
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['var_attr']))
				{
				$vAttrUsed[] = 'var';
				$s_xml_conf['elements'][$vpos]['var'] = (isset($s_xml_conf['elements'][$vpos]['var'])) ? $s_xml_conf['elements'][$vpos]['var'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['var_attr']=='m')
					{
					$vHTML .=  '<strong>var: </strong>';
					}
				else	{
					$vHTML .=  'var: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="var" type="text" size="30" value="'.$s_xml_conf['elements'][$vpos]['var'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#var'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: Table
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['table_attr']))
				{
				$vAttrUsed[] = 'table';
				$s_xml_conf['elements'][$vpos]['table'] = (isset($s_xml_conf['elements'][$vpos]['table'])) ? $s_xml_conf['elements'][$vpos]['table'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['table_attr']=='m')
					{
					$vHTML .=  '<strong>Table: </strong>';
					}
				else	{
					$vHTML .=  'Table: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<select name="table" size="1" onChange="javascript: CheckTableChange(document.Form.table.value);">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";

				$vScriptGlobal 	.=  '	fieldsArray = new Array();'."\n";
				$vScriptGlobal 	.=  '	fieldsArrayCount = new Array();'."\n";

				for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
					{
					$vTable = $s_xml_conf['tables'][$t]['name'];
					if (!empty($vTable))
						{
						if ($vTable==$s_xml_conf['elements'][$vpos]['table'])
							{
							$vHTML .=  '<option value="'.$vTable.'" selected>'.$vTable."</option>\n";
							}
						else	{
							$vHTML .=  '<option value="'.$vTable.'">'.$vTable."</option>\n";
							}
						if ($s_connection['conected'])
							{
							if ((!isset($s_xml_conf['tables_checked'])) or
								($s_xml_conf['tables_checked']<>'true'))
								{
								//we go to know what fields exist in the table
								$vt = $syntax['table'];
								$vt = ereg_replace('#1',$vTable, $vt);
								$vSql = 'SELECT * FROM '.$vt;
								if ($rec = $dbhandle->SelectLimit($vSql,1))
									{
									$vScriptGlobal 	.=  "\n";
									$vScriptGlobal 	.=  '	fieldsArray["'.$vTable.'0"] = new Option("", "");'."\n";
									for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++)
										{
										$fld = $rec->FetchField($i);
										$type = $rec->MetaType($fld->type);
										$s_xml_conf['tables'][$t]['field'][$fld->name] = $type;

										$vScriptGlobal 	.=  '	fieldsArray["'.$vTable.($i+1).'"] = new Option("'.$fld->name.'", "'.$fld->name.'");'."\n";
										}
									$vScriptGlobal 	.=  '	fieldsArrayCount["'.$vTable.'"] = '.($i+1).";\n";
									}
								}
							else	{  //we go to store the value of the fields to every table in javascript array without run a sql sentence...
								$vScriptGlobal 	.=  "\n";
								$vScriptGlobal 	.=  '	fieldsArray["'.$vTable.'0"] = new Option("", "");'."\n";
								$i = 0;
								foreach ($s_xml_conf['tables'][$t]['field'] as $vvar => $vval)
									{
									$vScriptGlobal 	.=  '	fieldsArray["'.$vTable.($i+1).'"] = new Option("'.$vvar.'", "'.$vvar.'");'."\n";
									$i++;
									}
								$vScriptGlobal 	.=  '	fieldsArrayCount["'.$vTable.'"] = '.($i+1).";\n";
								}
							}
						}
					}
				$s_xml_conf['tables_checked'] = 'true';

				$vHTML .=  "</select>\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#table'."'".')">'.$vHelpChar.'</a>';
				$vHTML .=  '<input type="button" disabled="disabled" name="table_add_button" onClick="javascript: window.open(document.Form.href.value);" value="Add table">       '."\n";
				$vHTML .='</td></tr>';
				}

			//Attr: Field
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['field_attr']))
				{
				$vAttrUsed[] = 'field';
				$s_xml_conf['elements'][$vpos]['field'] = (isset($s_xml_conf['elements'][$vpos]['field'])) ? $s_xml_conf['elements'][$vpos]['field'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['field_attr']=='m')
					{
					$vHTML .=  '<strong>Field: </strong>';
					}
				else	{
					$vHTML .=  'Field: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';


				if ($s_connection['conected'])
					{
					$vHTML .=  '<select name="field" size="1" >'."\n";
					$vHTML .=  '<option value="" ></option>'."\n";
					for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
					   if ( ($s_xml_conf['tables'][$t]['name']==$s_xml_conf['elements'][$vpos]['table']) and
					      (!empty($s_xml_conf['tables'][$t]['name'])) )
						{
						foreach ($s_xml_conf['tables'][$t]['field'] as $vfnam => $vfval)
							{
							if ($vfnam==$s_xml_conf['elements'][$vpos]['field'])
								{
								$vHTML .=  '<option value="'.$vfnam.'" selected>'.$vfnam."</option>\n";
								}
							else	{
								$vHTML .=  '<option value="'.$vfnam.'">'.$vfnam."</option>\n";
								}

							}
						}
					$vHTML .=  "</select>\n";
					}
				else
					{
					$vHTML .=  '<input name="field" size="30" type="text"  value="'.$s_xml_conf['elements'][$vpos]['field'].'">'."\n";
					}


				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#field'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				$vCheckTableChange .= ' ChangeTable(vtableName, document.Form.field); '."\n";
				}

			//Attr: FieldSt
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['fieldst_attr']))
				{
				$vAttrUsed[] = 'fieldst';
				$s_xml_conf['elements'][$vpos]['fieldst'] = (isset($s_xml_conf['elements'][$vpos]['fieldst'])) ? $s_xml_conf['elements'][$vpos]['fieldst'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['fieldst_attr']=='m')
					{
					$vHTML .=  '<strong>FieldSt: </strong>';
					}
				else	{
					$vHTML .=  'FieldSt: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				if ($s_connection['conected'])
					{
					$vHTML .=  '<select name="fieldst" size="1" >'."\n";
					$vHTML .=  '<option value="" ></option>'."\n";
					for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
					   if ($s_xml_conf['tables'][$t]['name']==$s_xml_conf['elements'][$vpos]['table'])
						{
						foreach ($s_xml_conf['tables'][$t]['field'] as $vfnam => $vfval)
							{
							if ($vfnam==$s_xml_conf['elements'][$vpos]['fieldst'])
								{
								$vHTML .=  '<option value="'.$vfnam.'" selected>'.$vfnam."</option>\n";
								}
							else	{
								$vHTML .=  '<option value="'.$vfnam.'">'.$vfnam."</option>\n";
								}

							}
						}
					$vHTML .=  "</select>\n";
					}
				else	{
					$vHTML .=  '<input name="fieldst" size="30" type="text"  value="'.$s_xml_conf['elements'][$vpos]['fieldst'].'">'."\n";
					}

				$vHTML .='</td></tr>';
				$vCheckTableChange .= ' ChangeTable(vtableName, document.Form.fieldst); '."\n";
				}
			}

			//Attr: huckfield
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['huckfield_attr']))
				{
				$vAttrUsed[] = 'huckfield';
				$s_xml_conf['elements'][$vpos]['huckfield'] = (isset($s_xml_conf['elements'][$vpos]['huckfield'])) ? $s_xml_conf['elements'][$vpos]['huckfield'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['huckfield_attr']=='m')
					{
					$vHTML .=  '<strong>huckfield: </strong>';
					}
				else	{
					$vHTML .=  'huckfield: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

			        if ($s_connection['conected'])
					{
					$vHTML .=  '<select name="huckfield" size="1" >'."\n";
					$vHTML .=  '<option value="" ></option>'."\n";
					for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
					   if ($s_xml_conf['tables'][$t]['name']==$s_xml_conf['elements'][$vpos]['table'])
						{
						foreach ($s_xml_conf['tables'][$t]['field'] as $vfnam => $vfval)
							{
							if ($vfnam==$s_xml_conf['elements'][$vpos]['huckfield'])
								{
								$vHTML .=  '<option value="'.$vfnam.'" selected>'.$vfnam."</option>\n";
								}
							else	{
								$vHTML .=  '<option value="'.$vfnam.'">'.$vfnam."</option>\n";
								}

							}
						}
					$vHTML .=  "</select>\n";
					}
				else	{
					$vHTML .=  '<input name="huckfield" size="30" type="text"  value="'.$s_xml_conf['elements'][$vpos]['huckfield'].'">'."\n";
					}

				$vHTML .='</td></tr>';
				$vCheckTableChange .= ' ChangeTable(vtableName, document.Form.huckfield); '."\n";
				}


			//Attr: orderfield
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['orderfield_attr']))
				{
				$vAttrUsed[] = 'orderfield';
				$s_xml_conf['elements'][$vpos]['orderfield'] = (isset($s_xml_conf['elements'][$vpos]['orderfield'])) ? $s_xml_conf['elements'][$vpos]['orderfield'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['orderfield_attr']=='m')
					{
					$vHTML .=  '<strong>orderfield: </strong>';
					}
				else	{
					$vHTML .=  'orderfield: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				if ($s_connection['conected'])
					{
					$vHTML .=  '<select name="orderfield" size="1" >'."\n";
					$vHTML .=  '<option value="" ></option>'."\n";
					for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
					   if ($s_xml_conf['tables'][$t]['name']==$s_xml_conf['elements'][$vpos]['table'])
						{
						foreach ($s_xml_conf['tables'][$t]['field'] as $vfnam => $vfval)
							{
							if ($vfnam==$s_xml_conf['elements'][$vpos]['orderfield'])
								{
								$vHTML .=  '<option value="'.$vfnam.'" selected>'.$vfnam."</option>\n";
								}
							else	{
								$vHTML .=  '<option value="'.$vfnam.'">'.$vfnam."</option>\n";
								}

							}
						}
					$vHTML .=  "</select>\n";
					}
				else	{
					$vHTML .=  '<input name="orderfield" size="30" type="text"  value="'.$s_xml_conf['elements'][$vpos]['orderfield'].'">'."\n";
					}

				$vHTML .='</td></tr>';
				$vCheckTableChange .= ' ChangeTable(vtableName, document.Form.orderfield); '."\n";
				}


			//Attr: sql
			$sqlFields = array();
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['sql_attr']))
				{
				$vAttrUsed[] = 'sql';
				$s_xml_conf['elements'][$vpos]['sql'] = (isset($s_xml_conf['elements'][$vpos]['sql'])) ? $s_xml_conf['elements'][$vpos]['sql'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['sql_attr']=='m')
					{
					$vHTML .=  '<strong>sql: </strong>';
					}
				else	{
					$vHTML .=  'sql: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';


				if ($s_connection['conected'])
						{
						$vHTML .=  'Tables: <select name="sql_table_list" size="1" onChange="javascript: openWindowGiveMeFields(true,this, document.Form.sql_field_list);">'."\n";
						$vHTML .=  '<option value="" ></option>'."\n";

						$rec = $dbhandle->MetaTables();
						if ($rec)
							{
							$tableCount = sizeof($rec);
							for ($i=0; $i < $tableCount; $i++)
								{
								$s_xml_conf['tables_list'][$rec[$i]] = '';
								}
							}
						else	{
							$s_xml_conf['tables_list'][''] = '';
							}
						ksort ($s_xml_conf['tables_list']);

						foreach ($s_xml_conf['tables_list'] as $vvar => $vval)
							{
							$vt = $syntax['table'];
							$vt = ereg_replace('#1',$vvar, $vt);
							$vHTML .=  '<option value="'.$vt.'">'.$vvar."</option>\n";
							}
						$vHTML .=  "</select>\n";
						$vHTML .=  '<input type="button" name="Add_sql_table" onClick="javascript: AddSqlAttribute(sql_table_list, sql); " value="Add table">       '."\n";

						$vHTML .=  ' Fields: <select name="sql_field_list" size="1" >'."\n";
						$vHTML .=  '	<option value=""></option>'."\n";
						$vHTML .=  "	</select>\n";
						$vHTML .=  '<input type="button" name="Add_sql_field" onClick="javascript: AddSqlAttribute(sql_field_list, sql); " value="Add field">       '."\n";
						//$vHTML .=  '<input type="button" name="sql_field_list_button" onClick="javascript: openWindowGiveMeFields(false,document.Form.sql_table_list, document.Form.sql_field_list);" value="Update field">       '."\n";


						$vHTML .=  '<br />Variables: <select name="sql_variables_list" size="1" >'."\n";
						$vHTML .=  '	<option value=""></option>'."\n";
						for ($t = 0; $t < count($s_xml_conf['elements']); $t++)
							{
							if (	(isset($s_xml_conf['elements'][$t]['table'])) and
								(isset($s_xml_conf['elements'][$t]['field'])) )
								{
								$vt = $s_xml_conf['elements'][$t]['table'].'.'.$s_xml_conf['elements'][$t]['field'];
								$vHTML .=  '	<option value="__'.$vt.'__">'.$vt.'</option>'."\n";
								}

							}
						$vHTML .=  "	</select>\n";
						$vHTML .=  '<input type="button" name="Add_sql_variables" onClick="javascript: AddSqlAttribute(sql_variables_list, sql); " value="Add variable">       '."\n";
						$vHTML .=  "<br />\n";
						}

				$vHTML .=  '<textarea name="sql" cols="100" rows="8" onChange="javascript: CheckSqlChange();">'.htmlentities($s_xml_conf['elements'][$vpos]['sql'])."</textarea>\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#sql'."'".')">'.$vHelpChar.'</a>';
				$vHTML .=  '<br /><input type="button" name="sql_run_button" onClick="javascript: openWindowSql(document.Form.sql,0);" value="Run">       '."\n";
				$vHTML .=  '<input type="button" name="sql_edit_button" onClick="javascript: window.open(document.Form.href.value);" value="Edit query">       '."\n";
				$vHTML .='</td></tr>';

				$vCheckSqlChange .= '	if (document.Form.sql.value.indexOf("__") != -1)'."\n";
				$vCheckSqlChange .= '		{'."\n";
				$vCheckSqlChange .= '		document.Form.sql_run_button.disabled="disabled";'."\n";
				$vCheckSqlChange .= '		}'."\n";
				$vCheckSqlChange .= '	else	{'."\n";
				$vCheckSqlChange .= '		document.Form.sql_run_button.disabled="";'."\n";
				$vCheckSqlChange .= '		}'."\n";

				//We go to run the sql instrucction to use the value of the fields in id, desc
				//so, we go to do that if there is not variable or params in the sql instrucction...
				$match = '';
				$vSql = $s_xml_conf['elements'][$vpos]['sql'];
				if (strpos(strtoupper($vSql),'WHERE')>-1)
					{
					$vSql = substr($vSql, 0, strpos(strtoupper($vSql), "WHERE"));
					}
				if (( !((@preg_match("|.*(__(\w*)__).*|U", $vSql, $match)) ) and
				   ( !(@preg_match("|.*(__(\w*)\.(\w*)__).*|U", $vSql, $match)) ))
				   and (!empty($vSql)))
					{

					$recSql->debug = true;


					if (($s_xml_conf['elements'][$vpos]['type']=='combobox') or
						($s_xml_conf['elements'][$vpos]['type']=='radio'))
						{
						$recSql = &$dbhandle->Execute($vSql);
						}
					else	{
						$recSql = &$dbhandle->SelectLimit($vSql,1);
						}
					if (!$recSql)
						{
						//echo $recSql->ErrorMsg();
						}
					else
						{
						for ($i=0, $max=$recSql->FieldCount(); $i < $max; $i++)
							{
							$fld = $recSql->FetchField($i);
							//$type = $recSql->MetaType($fld->type);
							$sqlFields[] = $fld->name;
							}
						}
					}
				}
			else	{
				$vHead .= '<input name="sql" type="hidden" value="">';
				$vHead .= '<input name="sql_run_button" type="hidden" value="">';
				}


			//Attr: id
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['id_attr']))
				{
				$vAttrUsed[] = 'id';
				$s_xml_conf['elements'][$vpos]['id'] = (isset($s_xml_conf['elements'][$vpos]['id'])) ? $s_xml_conf['elements'][$vpos]['id'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['id_attr']=='m')
					{
					$vHTML .=  '<strong>id: </strong>';
					}
				else	{
					$vHTML .=  'id: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				if (count($sqlFields)>0)
					{
					$vHTML .=  '<select name="id" size="1" >'."\n";
					$vHTML .=  '<option value="" ></option>'."\n";
					for ($t = 0; $t < count($sqlFields); $t++)
						if ($sqlFields[$t]==$s_xml_conf['elements'][$vpos]['id'])
							{
							$vHTML .=  '<option value="'.$sqlFields[$t].'" selected>'.$sqlFields[$t]."</option>\n";
							}
						else	{
							$vHTML .=  '<option value="'.$sqlFields[$t].'">'.$sqlFields[$t]."</option>\n";
							}
					$vHTML .=  "</select>\n";
					}
				else	{
					$vHTML .=  '<input name="id" type="text"  value="'.$s_xml_conf['elements'][$vpos]['id'].'">'."\n";
					}
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#id'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}


			//Attr: desc
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['desc_attr']))
				{
				$vAttrUsed[] = 'desc';
				$s_xml_conf['elements'][$vpos]['desc'] = (isset($s_xml_conf['elements'][$vpos]['desc'])) ? $s_xml_conf['elements'][$vpos]['desc'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['desc_attr']=='m')
					{
					$vHTML .=  '<strong>desc: </strong>';
					}
				else	{
					$vHTML .=  'desc: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				if (count($sqlFields)>0)
					{
					$vHTML .=  '<select name="desc" size="1" >'."\n";
					$vHTML .=  '<option value="" ></option>'."\n";
					for ($t = 0; $t < count($sqlFields); $t++)
						if ($sqlFields[$t]==$s_xml_conf['elements'][$vpos]['desc'])
							{
							$vHTML .=  '<option value="'.$sqlFields[$t].'" selected>'.$sqlFields[$t]."</option>\n";
							}
						else	{
							$vHTML .=  '<option value="'.$sqlFields[$t].'">'.$sqlFields[$t]."</option>\n";
							}
					$vHTML .=  "</select>\n";
					}
				else	{
					$vHTML .=  '<input name="desc" type="text"  value="'.$s_xml_conf['elements'][$vpos]['desc'].'">'."\n";
					}
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#desc'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: Default
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['default_attr']))
				{
				$vAttrUsed[] = 'default';
				$s_xml_conf['elements'][$vpos]['default'] = (isset($s_xml_conf['elements'][$vpos]['default'])) ? $s_xml_conf['elements'][$vpos]['default'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['default_attr']=='m')
					{
					$vHTML .=  '<strong>Default: </strong>';
					}
				else	{
					$vHTML .=  'Default: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				if 	( (($s_xml_conf['elements'][$vpos]['type']=='combobox') or
					($s_xml_conf['elements'][$vpos]['type']=='radio')) and
					(count($sqlFields)>0) and
					(!empty($s_xml_conf['elements'][$vpos]['desc'])) and
					(!empty($s_xml_conf['elements'][$vpos]['id'])) )
					{
					$vId = $s_xml_conf['elements'][$vpos]['id'];
					$vDesc = $s_xml_conf['elements'][$vpos]['desc'];
					if ( (array_key_exists($vId, $recSql->fields)) and
					   (array_key_exists($vDesc, $recSql->fields)) )
						{

						$vHTML .=  '<select name="default">'."\n";
						$vHTML .=  '<option value="" ></option>'."\n";
						while ((!$recSql->EOF))
							{

							if ($s_xml_conf['elements'][$vpos]['default']==$recSql->fields[$vId])
								{
								$vHTML .=  '<option value="'.$recSql->fields[$vId].'" selected>'.$recSql->fields[$vDesc].'</option>'."\n";
								}
							else	{
								$vHTML .=  '<option value="'.$recSql->fields[$vId].'">'.$recSql->fields[$vDesc].'</option>'."\n";
								}
							$recSql->MoveNext();
							}
						$vHTML .=  "</select>\n";
						}
					else
						{
						$vHTML .=  '<input name="default" type="text"  value="'.$s_xml_conf['elements'][$vpos]['default'].'">'."\n";
						}
					}
				else	{
					$vHTML .=  '<input name="default" type="text"  value="'.$s_xml_conf['elements'][$vpos]['default'].'">'."\n";
					}
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#default'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: propval
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['propval_attr']))
				{
				$vAttrUsed[] = 'propval';
				$s_xml_conf['elements'][$vpos]['propval'] = (isset($s_xml_conf['elements'][$vpos]['propval'])) ? $s_xml_conf['elements'][$vpos]['propval'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['propval_attr']=='m')
					{
					$vHTML .=  '<strong>propval: </strong>';
					}
				else	{
					$vHTML .=  'propval: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="propval">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['propval']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['propval']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#propval'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}
			//Attr: showempty
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['showempty_attr']))
				{
				$vAttrUsed[] = 'showempty';
				$s_xml_conf['elements'][$vpos]['showempty'] = (isset($s_xml_conf['elements'][$vpos]['showempty'])) ? $s_xml_conf['elements'][$vpos]['showempty'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['showempty_attr']=='m')
					{
					$vHTML .=  '<strong>showempty: </strong>';
					}
				else	{
					$vHTML .=  'showempty: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="showempty">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['showempty']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['showempty']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#showempty'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}


			//Attr: datefrmenter
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['datefrmenter_attr']))
				{
				$vAttrUsed[] = 'datefrmenter';
				$s_xml_conf['elements'][$vpos]['datefrmenter'] = (isset($s_xml_conf['elements'][$vpos]['datefrmenter'])) ? $s_xml_conf['elements'][$vpos]['datefrmenter'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['datefrmenter_attr']=='m')
					{
					$vHTML .=  '<strong>datefrmenter: </strong>';
					}
				else	{
					$vHTML .=  'datefrmenter: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<input name="datefrmenter" type="text"  value="'.$s_xml_conf['elements'][$vpos]['datefrmenter'].'">'."\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#datefrmenter'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: datefrmsave
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['datefrmsave_attr']))
				{
				$vAttrUsed[] = 'datefrmsave';
				$s_xml_conf['elements'][$vpos]['datefrmsave'] = (isset($s_xml_conf['elements'][$vpos]['datefrmsave'])) ? $s_xml_conf['elements'][$vpos]['datefrmsave'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['datefrmsave_attr']=='m')
					{
					$vHTML .=  '<strong>datefrmsave: </strong>';
					}
				else	{
					$vHTML .=  'datefrmsave: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<input name="datefrmsave" type="text"  value="'.$s_xml_conf['elements'][$vpos]['datefrmsave'].'">'."\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#datefrmsave'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: action
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['action_attr']))
				{
				$vAttrUsed[] = 'action';
				$s_xml_conf['elements'][$vpos]['action'] = (isset($s_xml_conf['elements'][$vpos]['action'])) ? $s_xml_conf['elements'][$vpos]['action'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['action_attr']=='m')
					{
					$vHTML .=  '<strong>action: </strong>';
					}
				else	{
					$vHTML .=  'action: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="element_component" size="1" >'."\n";
				for ($t = 0; $t < count($s_xml_conf['elements']); $t++)
					{

					if ( (!empty($s_xml_conf['elements'][$t]['table'])) and (!empty($s_xml_conf['elements'][$t]['field'])) )
						{
						$st = $s_xml_conf['elements'][$t]['table'];
						$st .= '.'.$s_xml_conf['elements'][$t]['field'];

						$vHTML .= '<option value="__'.($st).'__">'.$st."</option>\n";

						if ( (!empty($s_xml_conf['elements'][$t]['fieldst'])) )
							{
							$st = $s_xml_conf['elements'][$t]['table'];
							$st .= '.'.$s_xml_conf['elements'][$t]['fieldst'];

							$vHTML .= '<option value="__'.($st).'__">'.$st."</option>\n";
							}

						if ( (!empty($s_xml_conf['elements'][$t]['summaryfield'])) )
							{
							$st = $s_xml_conf['elements'][$t]['table'];
							$st .= '.'.$s_xml_conf['elements'][$t]['summaryfield'];

							$vHTML .= '<option value="__'.($st).'__">'.$st."</option>\n";
							}
						if ( (!empty($s_xml_conf['elements'][$t]['huckfield'])) )
							{
							$st = $s_xml_conf['elements'][$t]['table'];
							$st .= '.'.$s_xml_conf['elements'][$t]['huckfield'];

							$vHTML .= '<option value="__'.($st).'__">'.$st."</option>\n";
							}
						if ( (!empty($s_xml_conf['elements'][$t]['orderfield'])) )
							{
							$st = $s_xml_conf['elements'][$t]['table'];
							$st .= '.'.$s_xml_conf['elements'][$t]['orderfield'];

							$vHTML .= '<option value="__'.($st).'__">'.$st."</option>\n";
							}


						}

					}

				$vHTML .= "</select>=\n";
				if (count($sqlFields)>0)
					{
					$vHTML .=  '<select name="sql_field" size="1" >'."\n";
					//$vHTML .=  '<option value="" ></option>'."\n";
					for ($t = 0; $t < count($sqlFields); $t++)
						$vHTML .=  '<option value="'.$sqlFields[$t].'">'.$sqlFields[$t]."</option>\n";
					$vHTML .=  "</select>\n";
					}
				else	{
					//$vHTML .=  '<input name="sql_field" type="text"  value="'.$s_xml_conf['elements'][$vpos]['desc'].'">'."\n";
					}
				$vHTML .=  '<input name="button_add_action" type="button"  value="Add action" onClick="javascript: Add_Action(element_component, sql_field, action);">'."\n";

				$vHTML .=  '<textarea name="action" cols="100" rows="4">'.htmlentities($s_xml_conf['elements'][$vpos]['action'])."</textarea>\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#action'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: delimitedchar
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['delimitedchar_attr']))
				{
				$vAttrUsed[] = 'delimitedchar';
				$s_xml_conf['elements'][$vpos]['delimitedchar'] = (isset($s_xml_conf['elements'][$vpos]['delimitedchar'])) ? $s_xml_conf['elements'][$vpos]['delimitedchar'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['delimitedchar_attr']=='m')
					{
					$vHTML .=  '<strong>delimitedchar: </strong>';
					}
				else	{
					$vHTML .=  'delimitedchar: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="delimitedchar" type="text"  value="'.$s_xml_conf['elements'][$vpos]['delimitedchar'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#delimitedchar'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: tableref
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['tableref_attr']))
				{
				$vAttrUsed[] = 'tableref';
				$s_xml_conf['elements'][$vpos]['tableref'] = (isset($s_xml_conf['elements'][$vpos]['tableref'])) ? $s_xml_conf['elements'][$vpos]['tableref'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['tableref_attr']=='m')
					{
					$vHTML .=  '<strong>tableref: </strong>';
					}
				else	{
					$vHTML .=  'tableref: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="tableref" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['tableref'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#tableref'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: Len
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['len_attr']))
				{
				$vAttrUsed[] = 'len';
				$s_xml_conf['elements'][$vpos]['len'] = (isset($s_xml_conf['elements'][$vpos]['len'])) ? $s_xml_conf['elements'][$vpos]['len'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['len_attr']=='m')
					{
					$vHTML .=  '<strong>Len Show: </strong>';
					}
				else	{
					$vHTML .=  'Len Show: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="len" type="text"  value="'.$s_xml_conf['elements'][$vpos]['len'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#len'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}


			//Attr: lenedit
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['lenedit_attr']))
				{
				$vAttrUsed[] = 'lenedit';
				$s_xml_conf['elements'][$vpos]['lenedit'] = (isset($s_xml_conf['elements'][$vpos]['lenedit'])) ? $s_xml_conf['elements'][$vpos]['lenedit'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['lenedit_attr']=='m')
					{
					$vHTML .=  '<strong>Len Edit: </strong>';
					}
				else	{
					$vHTML .=  'Len Edit: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="lenedit" type="text"  value="'.$s_xml_conf['elements'][$vpos]['lenedit'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#lenedit'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: Lines
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['lines_attr']))
				{
				$vAttrUsed[] = 'lines';
				$s_xml_conf['elements'][$vpos]['lines'] = (isset($s_xml_conf['elements'][$vpos]['lines'])) ? $s_xml_conf['elements'][$vpos]['lines'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['lines_attr']=='m')
					{
					$vHTML .=  '<strong>lines: </strong>';
					}
				else	{
					$vHTML .=  'lines: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="lines" type="text"  value="'.$s_xml_conf['elements'][$vpos]['lines'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#lines'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: Depend
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['depend_attr']))
				{
				$vAttrUsed[] = 'depend';
				$s_xml_conf['elements'][$vpos]['depend'] = (isset($s_xml_conf['elements'][$vpos]['depend'])) ? $s_xml_conf['elements'][$vpos]['depend'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['depend_attr']=='m')
					{
					$vHTML .=  '<strong>depend: </strong>';
					}
				else	{
					$vHTML .=  'depend: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="depend" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['depend'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#depend'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: Mandatory
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['mandatory_attr']))
				{
				$vAttrUsed[] = 'mandatory';
				$s_xml_conf['elements'][$vpos]['mandatory'] = (isset($s_xml_conf['elements'][$vpos]['mandatory'])) ? $s_xml_conf['elements'][$vpos]['mandatory'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['mandatory_attr']=='m')
					{
					$vHTML .=  '<strong>Mandatory: </strong>';
					}
				else	{
					$vHTML .=  'Mandatory: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="mandatory">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['mandatory']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['mandatory']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#mandatory'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: ReadOnly
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['disabled_attr']))
				{
				$vAttrUsed[] = 'disabled';
				$s_xml_conf['elements'][$vpos]['disabled'] = (isset($s_xml_conf['elements'][$vpos]['disabled'])) ? $s_xml_conf['elements'][$vpos]['disabled'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['disabled_attr']=='m')
					{
					$vHTML .=  '<strong>ReadOnly: </strong>';
					}
				else	{
					$vHTML .=  'ReadOnly: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="disabled">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['disabled']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['disabled']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#disabled'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}
			//Attr: virtual
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['virtual_attr']))
				{
				$vAttrUsed[] = 'virtual';
				$s_xml_conf['elements'][$vpos]['virtual'] = (isset($s_xml_conf['elements'][$vpos]['virtual'])) ? $s_xml_conf['elements'][$vpos]['virtual'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['virtual_attr']=='m')
					{
					$vHTML .=  '<strong>virtual: </strong>';
					}
				else	{
					$vHTML .=  'virtual: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="virtual">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['virtual']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['virtual']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#virtual'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: specialchars
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['specialchars_attr']))
				{
				$vAttrUsed[] = 'specialchars';
				$s_xml_conf['elements'][$vpos]['specialchars'] = (isset($s_xml_conf['elements'][$vpos]['specialchars'])) ? $s_xml_conf['elements'][$vpos]['specialchars'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['specialchars_attr']=='m')
					{
					$vHTML .=  '<strong>specialchars: </strong>';
					}
				else	{
					$vHTML .=  'specialchars: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="specialchars">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['specialchars']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['specialchars']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#specialchars'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: br
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['br_attr']))
				{
				$vAttrUsed[] = 'br';
				$s_xml_conf['elements'][$vpos]['br'] = (isset($s_xml_conf['elements'][$vpos]['br'])) ? $s_xml_conf['elements'][$vpos]['br'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['br_attr']=='m')
					{
					$vHTML .=  '<strong>Br: </strong>';
					}
				else	{
					$vHTML .=  'Br: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="br">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['br']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['br']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#br'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: hr
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['hr_attr']))
				{
				$vAttrUsed[] = 'hr';
				$s_xml_conf['elements'][$vpos]['hr'] = (isset($s_xml_conf['elements'][$vpos]['hr'])) ? $s_xml_conf['elements'][$vpos]['hr'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['hr_attr']=='m')
					{
					$vHTML .=  '<strong>Hr: </strong>';
					}
				else	{
					$vHTML .=  'Hr: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="hr">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['hr']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['hr']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#hr'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: brln
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['brln_attr']))
				{
				$vAttrUsed[] = 'brln';
				$s_xml_conf['elements'][$vpos]['brln'] = (isset($s_xml_conf['elements'][$vpos]['brln'])) ? $s_xml_conf['elements'][$vpos]['brln'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['brln_attr']=='m')
					{
					$vHTML .=  '<strong>BrLn: </strong>';
					}
				else	{
					$vHTML .=  'BrLn: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="brln">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['brln']=='true')  {
					$vHTML .=  '<option value="true" selected>'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}
				elseif ($s_xml_conf['elements'][$vpos]['brln']=='false')  {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false" selected>'.$button_strings['No'].'</option>'."\n";
					}
				else {
					$vHTML .=  '<option value="true">'.$button_strings['Yes'].'</option>'."\n";
					$vHTML .=  '<option value="false">'.$button_strings['No'].'</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#brln'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: view
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['view_attr']))
				{
				$vAttrUsed[] = 'view';
				$s_xml_conf['elements'][$vpos]['view'] = (isset($s_xml_conf['elements'][$vpos]['view'])) ? $s_xml_conf['elements'][$vpos]['view'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['view_attr']=='m')
					{
					$vHTML .=  '<strong>View: </strong>';
					}
				else	{
					$vHTML .=  'View: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="view" type="text"  value="'.$s_xml_conf['elements'][$vpos]['view'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#view'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: indiv
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['indiv_attr']))
				{
				$vAttrUsed[] = 'indiv';
				$s_xml_conf['elements'][$vpos]['indiv'] = (isset($s_xml_conf['elements'][$vpos]['indiv'])) ? $s_xml_conf['elements'][$vpos]['indiv'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['indiv_attr']=='m')
					{
					$vHTML .=  '<strong>indiv: </strong>';
					}
				else	{
					$vHTML .=  'indiv: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  'Div id:<input name="indiv" type="text"  value="'.$s_xml_conf['elements'][$vpos]['indiv'].'">'."\n";

				$s_xml_conf['elements_attrs'][$vtypPos]['inframe_attr'] = (isset($s_xml_conf['elements_attrs'][$vtypPos]['inframe_attr'])) ? $s_xml_conf['elements_attrs'][$vtypPos]['inframe_attr'] : "";
				$vAttrUsed[] = 'inframe';
				$s_xml_conf['elements'][$vpos]['inframe'] = (isset($s_xml_conf['elements'][$vpos]['inframe'])) ? $s_xml_conf['elements'][$vpos]['inframe'] : '';
				$vHTML .=  '<br />Into frame: <select name="inframe">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['inframe']=='1')
					{
					$vHTML .=  '<option value="1" selected>Yes</option>'."\n";
					$vHTML .=  '<option value="0">No</option>'."\n";
					}
				else   	{
					$vHTML .=  '<option value="1">Yes</option>'."\n";
					$vHTML .=  '<option value="0" selected>No</option>'."\n";
					}
				$vHTML .=  '</select>';
				
				$s_xml_conf['elements_attrs'][$vtypPos]['divheigth_attr'] = (isset($s_xml_conf['elements_attrs'][$vtypPos]['divheigth_attr'])) ? $s_xml_conf['elements_attrs'][$vtypPos]['divheigth_attr'] : "";
				$vAttrUsed[] = 'divheigth';
				$s_xml_conf['elements'][$vpos]['divheigth'] = (isset($s_xml_conf['elements'][$vpos]['divheigth'])) ? $s_xml_conf['elements'][$vpos]['divheigth'] : '';
				$vHTML .=  ' Frame heigth:<input name="divheigth" type="text" size="4"  value="'.$s_xml_conf['elements'][$vpos]['divheigth'].'">'."\n";

				
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#indiv'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}				
				
			//Attr: cols
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['cols_attr']))
				{
				$vAttrUsed[] = 'cols';
				$s_xml_conf['elements'][$vpos]['cols'] = (isset($s_xml_conf['elements'][$vpos]['cols'])) ? $s_xml_conf['elements'][$vpos]['cols'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['cols_attr']=='m')
					{
					$vHTML .=  '<strong>cols/width: </strong>';
					}
				else	{
					$vHTML .=  'cols/width: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="cols" type="text"  value="'.$s_xml_conf['elements'][$vpos]['cols'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#cols'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: rows
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['rows_attr']))
				{
				$vAttrUsed[] = 'rows';
				$s_xml_conf['elements'][$vpos]['rows'] = (isset($s_xml_conf['elements'][$vpos]['rows'])) ? $s_xml_conf['elements'][$vpos]['rows'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['rows_attr']=='m')
					{
					$vHTML .=  '<strong>rows/height: </strong>';
					}
				else	{
					$vHTML .=  'rows/height: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="rows" type="text"  value="'.$s_xml_conf['elements'][$vpos]['rows'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#rows'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: redirect
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['redirect_attr']))
				{
				$vAttrUsed[] = 'redirect';
				$s_xml_conf['elements'][$vpos]['redirect'] = (isset($s_xml_conf['elements'][$vpos]['redirect'])) ? $s_xml_conf['elements'][$vpos]['redirect'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['redirect_attr']=='m')
					{
					$vHTML .=  '<strong>redirect: </strong>';
					}
				else	{
					$vHTML .=  'redirect: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="redirect" type="text"  value="'.$s_xml_conf['elements'][$vpos]['redirect'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#redirect'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}


			//Attr: checked
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['checked_attr']))
				{
				$vAttrUsed[] = 'checked';
				$s_xml_conf['elements'][$vpos]['checked'] = (isset($s_xml_conf['elements'][$vpos]['checked'])) ? $s_xml_conf['elements'][$vpos]['checked'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['checked_attr']=='m')
					{
					$vHTML .=  '<strong>checked: </strong>';
					}
				else	{
					$vHTML .=  'checked: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="checked" size="1" type="text"  value="'.$s_xml_conf['elements'][$vpos]['checked'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#checked'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: unchecked
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['unchecked_attr']))
				{
				$vAttrUsed[] = 'unchecked';
				$s_xml_conf['elements'][$vpos]['unchecked'] = (isset($s_xml_conf['elements'][$vpos]['unchecked'])) ? $s_xml_conf['elements'][$vpos]['unchecked'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['unchecked_attr']=='m')
					{
					$vHTML .=  '<strong>unchecked: </strong>';
					}
				else	{
					$vHTML .=  'unchecked: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="unchecked" size="1" type="text"  value="'.$s_xml_conf['elements'][$vpos]['unchecked'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#unchecked'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}


			//Attr: summaryfield
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['summaryfield_attr']))
				{
				$vAttrUsed[] = 'summaryfield';
				$s_xml_conf['elements'][$vpos]['summaryfield'] = (isset($s_xml_conf['elements'][$vpos]['summaryfield'])) ? $s_xml_conf['elements'][$vpos]['summaryfield'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['summaryfield_attr']=='m')
					{
					$vHTML .=  '<strong>summaryfield: </strong>';
					}
				else	{
					$vHTML .=  'summaryfield: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="summaryfield" type="text"  value="'.$s_xml_conf['elements'][$vpos]['summaryfield'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#summaryfield'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: delimitersummaryfield
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['delimitersummaryfield_attr']))
				{
				$vAttrUsed[] = 'delimitersummaryfield';
				$s_xml_conf['elements'][$vpos]['delimitersummaryfield'] = (isset($s_xml_conf['elements'][$vpos]['delimitersummaryfield'])) ? $s_xml_conf['elements'][$vpos]['delimitersummaryfield'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['delimitersummaryfield_attr']=='m')
					{
					$vHTML .=  '<strong>delimitersummaryfield: </strong>';
					}
				else	{
					$vHTML .=  'delimitersummaryfield: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="delimitersummaryfield" type="text"  value="'.$s_xml_conf['elements'][$vpos]['delimitersummaryfield'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#delimitersummaryfield'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: pathcopy
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['pathcopy_attr']))
				{
				$vAttrUsed[] = 'pathcopy';
				$s_xml_conf['elements'][$vpos]['pathcopy'] = (isset($s_xml_conf['elements'][$vpos]['pathcopy'])) ? $s_xml_conf['elements'][$vpos]['pathcopy'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['pathcopy_attr']=='m')
					{
					$vHTML .=  '<strong>pathcopy: </strong>';
					}
				else	{
					$vHTML .=  'pathcopy: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="pathcopy" type="text"  value="'.$s_xml_conf['elements'][$vpos]['pathcopy'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#pathcopy'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: pathref
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['pathref_attr']))
				{
				$vAttrUsed[] = 'pathref';
				$s_xml_conf['elements'][$vpos]['pathref'] = (isset($s_xml_conf['elements'][$vpos]['pathref'])) ? $s_xml_conf['elements'][$vpos]['pathref'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['pathref_attr']=='m')
					{
					$vHTML .=  '<strong>pathref: </strong>';
					}
				else	{
					$vHTML .=  'pathref: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="pathref" type="text"  value="'.$s_xml_conf['elements'][$vpos]['pathref'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#pathref'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: groupfilter
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['groupfilter_attr']))
				{
				$vAttrUsed[] = 'groupfilter';
				$s_xml_conf['elements'][$vpos]['groupfilter'] = (isset($s_xml_conf['elements'][$vpos]['groupfilter'])) ? $s_xml_conf['elements'][$vpos]['groupfilter'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['groupfilter_attr']=='m')
					{
					$vHTML .=  '<strong>groupfilter: </strong>';
					}
				else	{
					$vHTML .=  'groupfilter: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';

				$vHTML .=  '<select name="groupfilter">'."\n";
				$vHTML .=  '<option value="" ></option>'."\n";
				if ($s_xml_conf['elements'][$vpos]['groupfilter']=='fast')
					{
					$vHTML .=  '<option value="fast" selected>Fast</option>'."\n";
					$vHTML .=  '<option value="">All</option>'."\n";
					}
				else   	{
					$vHTML .=  '<option value="fast">Fast</option>'."\n";
					$vHTML .=  '<option value="" selected>All</option>'."\n";
					}

				$vHTML .=  "</select>\n";

				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#groupfilter'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: statusmsg
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['statusmsg_attr']))
				{
				$vAttrUsed[] = 'statusmsg';
				$s_xml_conf['elements'][$vpos]['statusmsg'] = (isset($s_xml_conf['elements'][$vpos]['statusmsg'])) ? $s_xml_conf['elements'][$vpos]['statusmsg'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['statusmsg_attr']=='m')
					{
					$vHTML .=  '<strong>statusmsg: </strong>';
					}
				else	{
					$vHTML .=  'statusmsg: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="statusmsg" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['statusmsg'].'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#statusmsg'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: href
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['href_attr']))
				{
				$vAttrUsed[] = 'href';
				$s_xml_conf['elements'][$vpos]['href'] = (isset($s_xml_conf['elements'][$vpos]['href'])) ? $s_xml_conf['elements'][$vpos]['href'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['href_attr']=='m')
					{
					$vHTML .=  '<strong>href: </strong>';
					}
				else	{
					$vHTML .=  'href: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="href" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['href'].'">'."\n";
				$vHTML .=  '<input type="button" name="href_button" onClick="javascript: window.open(document.Form.href.value);" value=" '.$button_strings['Go'].' ">       '."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#href'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}


			//Attr: comment
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['comment_attr']))
				{
				$vAttrUsed[] = 'comment';
				$s_xml_conf['elements'][$vpos]['comment'] = (isset($s_xml_conf['elements'][$vpos]['comment'])) ? $s_xml_conf['elements'][$vpos]['comment'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['comment_attr']=='m')
					{
					$vHTML .=  '<strong>comment: </strong>';
					}
				else	{
					$vHTML .=  'comment: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<textarea name="comment" cols="100" rows="4">'.htmlentities($s_xml_conf['elements'][$vpos]['comment'])."</textarea>\n";
				//$vHTML .=  '<input name="comment" size="100" type="text"  value="'.htmlentities($s_xml_conf['elements'][$vpos]['comment']).'">'."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#comment'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}

			//Attr: help
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['help_attr']))
				{
				$vAttrUsed[] = 'help';
				$s_xml_conf['elements'][$vpos]['help'] = (isset($s_xml_conf['elements'][$vpos]['help'])) ? $s_xml_conf['elements'][$vpos]['help'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['help_attr']=='m')
					{
					$vHTML .=  '<strong>help: </strong>';
					}
				else	{
					$vHTML .=  'help: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="help" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['help'].'">'."\n";
				$vHTML .=  '<input type="button" name="help_button" onClick="javascript: window.open(\''.$Confs["HelpAdminUrl"].'\'+document.Form.help.value);" value=" '.$button_strings['Go'].' ">       '."\n";
				$vHTML .=  '<a href="javascript: Redirect('."'".'help/help.htm#help'."'".')">'.$vHelpChar.'</a>';
				$vHTML .='</td></tr>';
				}
			//Attr: itf
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['itf_attr']))
				{
				$vAttrUsed[] = 'itf';
				$s_xml_conf['elements'][$vpos]['itf'] = (isset($s_xml_conf['elements'][$vpos]['itf'])) ? $s_xml_conf['elements'][$vpos]['itf'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['itf_attr']=='m')
					{
					$vHTML .=  '<strong>itf: </strong>';
					}
				else	{
					$vHTML .=  'itf: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="itf" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['itf'].'">'."\n";
				$vHTML .='</td></tr>';
				}
			//Attr: hispid
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['hispid_attr']))
				{
				$vAttrUsed[] = 'hispid';
				$s_xml_conf['elements'][$vpos]['hispid'] = (isset($s_xml_conf['elements'][$vpos]['hispid'])) ? $s_xml_conf['elements'][$vpos]['hispid'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['hispid_attr']=='m')
					{
					$vHTML .=  '<strong>hispid: </strong>';
					}
				else	{
					$vHTML .=  'hispid: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="hispid" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['hispid'].'">'."\n";
				$vHTML .='</td></tr>';
				}
			//Attr: ABCD
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['ABCD_attr']))
				{
				$vAttrUsed[] = 'ABCD';
				$s_xml_conf['elements'][$vpos]['ABCD'] = (isset($s_xml_conf['elements'][$vpos]['ABCD'])) ? $s_xml_conf['elements'][$vpos]['ABCD'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['ABCD_attr']=='m')
					{
					$vHTML .=  '<strong>ABCD: </strong>';
					}
				else	{
					$vHTML .=  'ABCD: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="ABCD" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['ABCD'].'">'."\n";
				$vHTML .='</td></tr>';
				}
			//Attr: darwincore
			if (isset($s_xml_conf['elements_attrs'][$vtypPos]['darwincore_attr']))
				{
				$vAttrUsed[] = 'darwincore';
				$s_xml_conf['elements'][$vpos]['darwincore'] = (isset($s_xml_conf['elements'][$vpos]['darwincore'])) ? $s_xml_conf['elements'][$vpos]['darwincore'] : '';
				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				if ($s_xml_conf['elements_attrs'][$vtypPos]['darwincore_attr']=='m')
					{
					$vHTML .=  '<strong>darwincore: </strong>';
					}
				else	{
					$vHTML .=  'darwincore: ';
					}
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="darwincore" size="100" type="text"  value="'.$s_xml_conf['elements'][$vpos]['darwincore'].'">'."\n";
				$vHTML .='</td></tr>';
				}


			//We go to show the value of the field...
			$vHTML .=  '</table>';
			$vHTML .=  '<hr>';
			$vHTML .=  '<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableForm">';
			if 	( (isset($s_xml_conf['elements'][$vpos]['table'])) and
				  (isset($s_xml_conf['elements'][$vpos]['field'])) )
				{
				$vTable = $s_xml_conf['elements'][$vpos]['table'];
				$vField = $s_xml_conf['elements'][$vpos]['field'];
				if (!isset($s_fields_value[$vTable][$vField]))
					{
					$s_fields_value[$vTable][$vField] = '';
					}

				$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><div align="right">';
				$vHTML .=  'Value: ';
				$vHTML .=  '</div></td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
				$vHTML .=  '<input name="value_edited" size="100" type="text"  value="'.$s_fields_value[$vTable][$vField].'">'."\n";
				$vHTML .='</td></tr>';
				}

			//check the attributes don´t checked...
			foreach ($s_xml_conf['elements'][$vpos] as $vv=>$va)
				if (!in_array($vv,$vAttrUsed))
					{
					$warning .= '<br />The attribute "'.$vv.'" has been declared and not checked...';
					}
			//check the types don´t defined in the $s_xml_conf['elements_attrs']
			for ($i=0; $i<count($s_xml_conf['elements']); $i++)
				{
				if ((isset($s_xml_conf['elements'][$i]['type'])) and
				   (!empty($s_xml_conf['elements'][$i]['type'])))
					{
					$tcheckType = false;
					for ($t=0; $t<count($s_xml_conf['elements_attrs']); $t++)
						{
						if ($s_xml_conf['elements'][$i]['type']==$s_xml_conf['elements_attrs'][$t]['content'])
							{
							$tcheckType = true;
							}
						}
					if (!$tcheckType)
						{
						$warning .= '<br /> The type "'.$s_xml_conf['elements'][$i]['type'].'" of the component "'.$s_xml_conf['elements'][$i]['content'].'" is not defined to check, add it in the xml conf file...';
						}
					}

				}

		}
	elseif (isset($HTTP_PARAM_VARS['table']))
		{

		}
	else
		{
		//redirect('run.php?mod='.$HTTP_PARAM_VARS['mod']);
		}

	$vHTML .=  '</table>';
	$vHTML .=  '<br /><br />';
	if (!($HTTP_PARAM_VARS['data']=='ViewXml') )
		{
		$vButtons .=  '<input name="Refresh" type="submit" value="'.$button_strings['Refresh'].'">     ';
		$vButtons .=  '<input name="Insert" type="submit" value="'.$button_strings['Insert'].'">       ';
		$vButtons .=  '<select name="ActionInsert" size="1">';
		if ((isset($HTTP_PARAM_VARS['ActionInsert'])) and
			($HTTP_PARAM_VARS['ActionInsert'])=="before")
			{
			$vButtons .=  '<option value="before" selected>Before the active element</option>'."\n";
			$vButtons .=  '<option value="after" >After the active element</option>'."\n";
			}
		else	{
			$vButtons .=  '<option value="before" >Before the active element</option>'."\n";
			$vButtons .=  '<option value="after" selected>After the active element</option>'."\n";
			}

		$pathMod = pathinfo($HTTP_PARAM_VARS['mod']);
		$xmlDirectory = $Confs["configuration-path"].$pathMod["dirname"];


		$vButtons .=  '</select>';

		$vButtons .=  '<input name="Save" type="submit" value="'.$button_strings['Save'].'">     ';
		$vButtons .=  '<input name="DeleteAsk" type="button" onClick="javascript: ConfirmDelete();" value="Delete">     ';
		$vButtons .=  '<input name="Delete" type="hidden" value="false">';
		$vButtons .=  '<input name="SaveXML" type="submit" value="Save to xml conf file">     ';
		$vButtons .=  '<input name="CreateXML" type="button" onClick="javascript: CreateXMLFile('."'".$pathMod["dirname"]."/'".');" value="Create xml conf file">     ';
		//if ($HTTP_PARAM_VARS['data']=='elements')
			{
			$vButtons .=  '<input name="ShowElements" type="button" onClick="javascript: window.open('."'".'elements_info.php?mod='.$HTTP_PARAM_VARS['mod']."'".');" value="Show elements">     ';
			$vButtons .=  '<input name="ShowElements" type="button" onClick="javascript: window.open('."'".'conf_info.php?mod='.$HTTP_PARAM_VARS['mod']."'".');" value="Show all configurations">     ';
			}

		}


	//Save to xml file
	if (isset($HTTP_PARAM_VARS['SaveXML']))
		{
		//require_once('lib/xpath/XPath.class.php');
		//create the xpath object
		$xPath =& new XPath();
		$xPath->setSkipWhiteSpaces(TRUE);
		$xPath->importFromFile(getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['mod']);
		//echo "Inicialmente...".$xPath->exportAsHtml();
		//echo "<hr>";

		$aResult = $xPath->evaluate("/Main");
		if (count($aResult)==0)
			{
			$aReturnValue = $xPath->appendChild("", "<Main/>");
			}

		if ($HTTP_PARAM_VARS['data']=='connection')
			{
			//check if exist the element with the language defined...
			//echo "/Main/elements[@lang='".$ADODB_LANG."']/*";
			$aResult = $xPath->evaluate("/Main/connection");
			if (count($aResult)>0)
				{
				if (count($aResult)>1)
					{
					echo "Must be a mistake because there is more than 1 tables tag defined with the lang: ".$ADODB_LANG;
					}
				else	{
					//delete all the childs of the elements tag....
					$xPath->removeChild("/Main/connection/*");
					//echo "Los datos han sido borrados...".$xPath->exportAsHtml();
					//echo "<hr>";
					}
				}
			else	{
				//is necessary create a child, we supose that main exist....so we go to add the element tag with the attribute lang
				$aReturnValue = $xPath->appendChild("/Main", "<connection/>");
				//$aReturnValue = $xPath->setAttribute("/Main/tables[last()]", "lang", $ADODB_LANG);
				//echo "Ha sido agregado el tag element con el attributo lang...".$xPath->exportAsHtml();
				//echo "<hr>";
				}

			//save all the values to xml
			$vNotAttribute = array();
			$vNotAttribute[] = 'tagname';
			$vNotAttribute[] = 'content';

			for ($t = 0; $t < count($s_xml_conf['connection']); $t++)
				{
				$vcontent = (isset($s_xml_conf['connection'][$t]['content'])) ? $s_xml_conf['connection'][$t]['content'] : '';
				$aReturnValue = $xPath->appendChild("/Main/connection", "<".$s_xml_conf['connection'][$t]['tagname'].">".$vcontent."</".$s_xml_conf['connection'][$t]['tagname'].">");
				foreach($s_xml_conf['connection'][$t] as $vvar => $vval)
					{
					if ((!in_array($vvar, $vNotAttribute)) and
					   (!empty($vval)))
						{
						$aReturnValue = $xPath->setAttribute("/Main/connection/*[last()]", $vvar, htmlentities($vval));
						}
					}
				}


			for ($t = 0; $t < count($s_xml_conf['db']); $t++)
				{
				echo count($s_xml_conf['db']);
				$vcontent = (isset($s_xml_conf['db'][$t]['content'])) ? $s_xml_conf['db'][$t]['content'] : '';
				$aReturnValue = $xPath->appendChild("/Main/connection/db", "<".$s_xml_conf['db'][$t]['tagname'].">".htmlentities(urlencode($vcontent))."</".$s_xml_conf['db'][$t]['tagname'].">");
				//foreach($s_xml_conf['connection'][$t] as $vvar => $vval)
				//	{
				//	if ((!in_array($vvar, $vNotAttribute)) and
				//	   (!empty($vval)))
				//		{
				//		$aReturnValue = $xPath->setAttribute("/Main/connection/db/*[last()]", $vvar, htmlentities($vval));
				//		}
				//	}
				}
			}


			if ($HTTP_PARAM_VARS['data']=='Searchs')
				{
				//check if exist the element with the language defined...
				//echo "/Main/elements[@lang='".$ADODB_LANG."']/*";
				$aResult = $xPath->evaluate("/Main/searchs");
				if (count($aResult)>0)
					{
					if (count($aResult)>1)
						{
						echo "Must be a mistake because there is more than 1 tables tag defined with the lang: ".$ADODB_LANG;
						}
					else	{
						//delete all the childs of the elements tag....
						$xPath->removeChild("/Main/searchs/*");
						//echo "Los datos han sido borrados...".$xPath->exportAsHtml();
						//echo "<hr>";
						}
					}
				else	{
					//is necessary create a child, we supose that main exist....so we go to add the element tag with the attribute lang
					$aReturnValue = $xPath->appendChild("/Main", "<searchs/>");
					//$aReturnValue = $xPath->setAttribute("/Main/tables[last()]", "lang", $ADODB_LANG);
					//echo "Ha sido agregado el tag element con el attributo lang...".$xPath->exportAsHtml();
					//echo "<hr>";
					}

				//save all the values to xml
				$vNotAttribute = array();
				$vNotAttribute[] = 'tagname';
				$vNotAttribute[] = 'content';
				for ($t = 0; $t < count($s_xml_conf['searchs']); $t++)
					{
					$vcontent = (isset($s_xml_conf['searchs'][$t]['content'])) ? $s_xml_conf['searchs'][$t]['content'] : '';
					$aReturnValue = $xPath->appendChild("/Main/searchs", "<".$s_xml_conf['searchs'][$t]['tagname'].">".htmlentities(urlencode($vcontent))."</".$s_xml_conf['searchs'][$t]['tagname'].">");
					foreach($s_xml_conf['searchs'][$t] as $vvar => $vval)
						{
						if ((!in_array($vvar, $vNotAttribute)) and
						   (!empty($vval)))
							{
							$aReturnValue = $xPath->setAttribute("/Main/searchs/*[last()]", $vvar, htmlentities(urlencode($vval)));
							}
						}
					}


				}

		if ($HTTP_PARAM_VARS['data']=='tables')
			{
			//check if exist the element with the language defined...
			//echo "/Main/elements[@lang='".$ADODB_LANG."']/*";
			$aResult = $xPath->evaluate("/Main/tables");
			if (count($aResult)>0)
				{
				if (count($aResult)>1)
					{
					echo "Must be a mistake because there is more than 1 tables tag defined with the lang: ".$ADODB_LANG;
					}
				else	{
					//delete all the childs of the elements tag....
					$xPath->removeChild("/Main/tables/*");
					//echo "Los datos han sido borrados...".$xPath->exportAsHtml();
					//echo "<hr>";
					}
				}
			else	{
				//is necessary create a child, we supose that main exist....so we go to add the element tag with the attribute lang
				$aReturnValue = $xPath->appendChild("/Main", "<tables/>");
				//$aReturnValue = $xPath->setAttribute("/Main/tables[last()]", "lang", $ADODB_LANG);
				//echo "Ha sido agregado el tag element con el attributo lang...".$xPath->exportAsHtml();
				//echo "<hr>";
				}

			//save all the values to xml
			$vNotAttribute = array();
			$vNotAttribute[] = 'tagname';
			$vNotAttribute[] = 'content';
			$vNotAttribute[] = 'field';
			for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
				{
				//$vcontent = (isset($s_xml_conf['elements'][$t]['content'])) ? $s_xml_conf['elements'][$t]['content'] : '';
				$aReturnValue = $xPath->appendChild("/Main/tables", "<".$s_xml_conf['tables'][$t]['tagname']."/>");
				foreach($s_xml_conf['tables'][$t] as $vvar => $vval)
					{
					if ((!in_array($vvar, $vNotAttribute)) and
					   (!empty($vval)))
						{
						$aReturnValue = $xPath->setAttribute("/Main/tables/*[last()]", $vvar, htmlentities($vval));
						}
					}
				}
			}

		if ($HTTP_PARAM_VARS['data']=='elements')
			{
			//check if exist the element with the language defined...
			//echo "/Main/elements[@lang='".$ADODB_LANG."']/*";
			$aResult = $xPath->evaluate("/Main/elements[@lang='".$ADODB_LANG."']");
			if (count($aResult)>0)
				{
				if (count($aResult)>1)
					{
					echo "Must be a mistake because there is more than 1 elements tag defined with the lang: ".$ADODB_LANG;
					}
				else	{
					//delete all the childs of the elements tag....
					$xPath->removeChild("/Main/elements[@lang='".$ADODB_LANG."']/*");
					//echo "Los datos han sido borrados...".$xPath->exportAsHtml();
					//echo "<hr>";
					}
				}
			else	{
				//is necessary create a child, we supose that main exist....so we go to add the element tag with the attribute lang
				$aReturnValue = $xPath->appendChild("/Main", "<elements/>");
				$aReturnValue = $xPath->setAttribute("/Main/elements[last()]", "lang", $ADODB_LANG);
				//echo "Ha sido agregado el tag element con el attributo lang...".$xPath->exportAsHtml();
				//echo "<hr>";
				}

			//save all the values to xml
			$vNotAttribute = array();
			$vNotAttribute[] = 'tagname';
			$vNotAttribute[] = 'content';
			$vGroupFilterExist = false;
			for ($t = 0; $t < count($s_xml_conf['elements']); $t++)
				{
				$vcontent = (isset($s_xml_conf['elements'][$t]['content'])) ? $s_xml_conf['elements'][$t]['content'] : '';
				$aReturnValue = $xPath->appendChild("/Main/elements[@lang='".$ADODB_LANG."']", "<".$s_xml_conf['elements'][$t]['tagname'].">".htmlentities(urlencode($vcontent))."</".$s_xml_conf['elements'][$t]['tagname'].">");
				foreach($s_xml_conf['elements'][$t] as $vvar => $vval)
					{
					if ((!in_array($vvar, $vNotAttribute)) and
					   (!empty($vval)))
						{
						if ($vvar=='groupfilter')
							{
							$vGroupFilterExist = true;
							}
						$aReturnValue = $xPath->setAttribute("/Main/elements[@lang='".$ADODB_LANG."']/*[last()]", $vvar, htmlentities(urlencode($vval)));
						}
					}
				}

			if ($vGroupFilterExist==true)
				{
				echo "si";
				$aResult = $xPath->evaluate("/Main/groupfilter");
				if (count($aResult)>0)
					{
					$xPath->removeChild("/Main/groupfilter/*");
					}
				else	{
					$aReturnValue = $xPath->appendChild("/Main", "<groupfilter></groupfilter>");
					}

				$aReturnValue = $xPath->appendChild("/Main/groupfilter", "<value>fast</value>");
				$aReturnValue = $xPath->setAttribute("/Main/groupfilter/*[last()]", "es", "Entrada rApida");
				$aReturnValue = $xPath->setAttribute("/Main/groupfilter/*[last()]", "en", "Fast enter");

				$aReturnValue = $xPath->appendChild("/Main/groupfilter", "<value></value>");
				$aReturnValue = $xPath->setAttribute("/Main/groupfilter/*[last()]", "es", "Todos los datos");
				$aReturnValue = $xPath->setAttribute("/Main/groupfilter/*[last()]", "en", "All data");
				}
			else	{
				echo "no";
				}
			}

		if ($HTTP_PARAM_VARS['data']=='Links')
			{
			//check if exist the element with the language defined...
			//echo "/Main/links[@lang='".$ADODB_LANG."']/*";
			$aResult = $xPath->evaluate("/Main/links[@lang='".$ADODB_LANG."']");
			if (count($aResult)>0)
				{
				if (count($aResult)>1)
					{
					echo "Must be a mistake because there is more than 1 links tag defined with the lang: ".$ADODB_LANG;
					}
				else	{
					//delete all the childs of the links tag....
					$xPath->removeChild("/Main/links[@lang='".$ADODB_LANG."']/*");
					//echo "Los datos han sido borrados...".$xPath->exportAsHtml();
					//echo "<hr>";
					}
				}
			else	{
				//is necessary create a child, we supose that main exist....so we go to add the element tag with the attribute lang
				$aReturnValue = $xPath->appendChild("/Main", "<links/>");
				$aReturnValue = $xPath->setAttribute("/Main/links[last()]", "lang", $ADODB_LANG);
				//echo "Ha sido agregado el tag element con el attributo lang...".$xPath->exportAsHtml();
				//echo "<hr>";
				}

			//save all the values to xml
			$vNotAttribute = array();
			$vNotAttribute[] = 'tagname';
			$vNotAttribute[] = 'content';
			for ($t = 0; $t < count($s_xml_conf['links']); $t++)
				{
				$vcontent = (isset($s_xml_conf['links'][$t]['content'])) ? $s_xml_conf['links'][$t]['content'] : '';
				$aReturnValue = $xPath->appendChild("/Main/links[@lang='".$ADODB_LANG."']", "<".$s_xml_conf['links'][$t]['tagname'].">".htmlentities(urlencode($vcontent))."</".$s_xml_conf['links'][$t]['tagname'].">");
				foreach($s_xml_conf['links'][$t] as $vvar => $vval)
					{
					if ((!in_array($vvar, $vNotAttribute)) and
					   (!empty($vval)))
						{
						$aReturnValue = $xPath->setAttribute("/Main/links[@lang='".$ADODB_LANG."']/*[last()]", $vvar, htmlentities(urlencode($vval)));
						}
					}
				}
			}

		if ($HTTP_PARAM_VARS['data']=='configuration')
			{
			//check if exist the element with the language defined...
			//echo "/Main/links[@lang='".$ADODB_LANG."']/*";
			$aResult = $xPath->evaluate("/Main/configuration/info");
			if (count($aResult)>0)
				{
				if (count($aResult)>1)
					{
					echo "Must be a mistake because there is more than 1 links tag defined with the lang: ".$ADODB_LANG;
					}
				else	{
					//delete all the childs of the links tag....
					$xPath->removeChild("/Main/configuration/info");
					//echo "Los datos han sido borrados...".$xPath->exportAsHtml();
					//echo "<hr>";
					}
				}
			else	{
				//is necessary create a child, we supose that main exist....so we go to add the element tag with the attribute lang
				$aResult = $xPath->evaluate("/Main/configuration");
				if (count($aResult)>0)
					{
					$aReturnValue = $xPath->appendChild("/Main", "<configuration/>");
					}
				//echo "Ha sido agregado el tag element con el attributo lang...".$xPath->exportAsHtml();
				//echo "<hr>";
				}

			//save all the values to xml
			$vcontent = (isset($s_xml_conf['configuration'][0]['content'])) ? $s_xml_conf['configuration'][0]['content'] : '';
			$aReturnValue = $xPath->appendChild("/Main/configuration", "<info>".htmlentities(urlencode($vcontent))."</info>");
			$aReturnValue = $xPath->setAttribute("/Main/configuration/*[last()]", 'lang', $ADODB_LANG);
			}


		//save into xml file
		$xPath->exportToFile(getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['mod']);
		if (!copy(getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['mod'], getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['mod'].'.bak'))
			{
			print("failed to copy <br />".getcwd().'/'.$Confs["configuration-path"].$HTTP_PARAM_VARS['mod'].'.bak');
			}
		//echo "Ha sido actualizado...".$xPath->exportAsHtml();
		//echo "<hr>";
		}
	} //cierre del if (!empty($xmlFile))


if (!empty($xmlFile))
	{
	$vMenu .=  "\n".'<br /><br /><ul id="Panel">'."\n";
	foreach ($vSectionName as $vnam => $vval)
		{

		if ($vSection==$vnam) {

			// Show the panel of the section selected/active...
			$vMenu .='      <li><a id="menupanel_1" class="current" href="javascript: NewPanel('."'".$vnam."'".');">'
				.'&nbsp;&nbsp;'.$vval.'&nbsp;&nbsp;</a></li>'."\n";
			}
		else
			{
			// Show the panel of the section don´t selected/active...
			$vMenu .='      <li><a id="menupanel_1" href="javascript: NewPanel('."'".$vnam."'".');">'
				.'&nbsp;&nbsp;'.$vval.'&nbsp;&nbsp;</a></li>'."\n";

			}
		}
	$vMenu  .='      </ul> '."\n";
	}
print "</head>\n";
print "<BODY ONLOAD='JS_OnLoad()'>\n";

print $vHead;
print '<br />';
print $vButtons;
print $vMenu;
print '<hr />';
print $vHTML;
print '<hr />';
print $vButtons;
print '</form>';

	?>
	<script language="JavaScript" type="text/JavaScript">
	<!--

	<?php echo $vScript; ?>

	function JS_OnLoad()
	{
		<?php echo $vScriptIni; ?>
		CheckSqlChange();

	}

	function Redirect(url)
	{
	//	document.Form.UrlGo.value=url;
	//	document.Form.Refresh.click();
		location.href = url;
	}

	function NewPanel(dir)
	{
		document.Form.data.value = dir;
		document.Form.submit();
	}

	function CheckTableChange(vtableName)
	{
	<?php echo $vCheckTableChange; ?>
	}

	function CheckTypeChange()
	{
		<?php echo $vCheckTypeChange; ?>
	}

	function CheckSqlChange()
	{
		<?php echo $vCheckSqlChange; ?>
	}


	function Updatefieldsdef()
	{
	if (document.Form.field_createTable.value != '')
		{
		  vlin = document.Form.field_createTable.value;
		  document.Form.field_createTable.value = "";
		  vlin += " "+document.Form.type_createTable.options[document.Form.type_createTable.selectedIndex].value;

		  if (document.Form.size_createTable.value != '')
		  	{
			vlin += " ("+document.Form.size_createTable.value+")";
			document.Form.size_createTable.value = "";
			}
		  if (document.Form.fieldAUTOINCREMENT.checked)
		  	{
			vlin += " AUTOINCREMENT";
			}
		  if (document.Form.fieldPRIMARYKEY.checked)
		  	{
			vlin += " PRIMARY";
			}
		  if (document.Form.fieldNOTNULL.checked)
		  	{
			vlin += " NOTNULL";
			}
		  if (document.Form.fieldDEFDATE.checked)
		  	{
			vlin += " DEFDATE";
			}
		  if (document.Form.fieldDEFTIMESTAMP.checked)
		  	{
			vlin += " DEFTIMESTAMP";
			}
		  if (document.Form.fieldNOQUOTE.checked)
		  	{
			vlin += " NOQUOTE";
			}
		  if (document.Form.fieldDEFAULT.value != '')
		  	{
			vlin += " DEFAULT "+document.Form.fieldDEFAULT.value;
			document.Form.fieldDEFAULT.value = "";
			}
		  if (document.Form.fieldCONSTRAINTS.value != '')
		  	{
			vlin += " CONSTRAINTS '"+document.Form.fieldCONSTRAINTS.value+"'";
			document.Form.fieldCONSTRAINTS.value = "";
			}
		document.Form.def_createTable.value += vlin+", ";
		}
	}

	function openWindowSql(vsql, cond)
	{
		if (vsql.value.indexOf("__") == -1)
			{
			if (vsql.value.length>0)
				{
				theURL = 'TableView.php?sql='+vsql.value+'&mod='+document.Form.mod.value;
				if (cond>0)
					{
					theURL += '&conf_type='+cond;
					}
				window.open(theURL);
				}

			}
	}

	function ChangeMod()
	{
		//document.Form.data.value = 'elements';
		//document.Form.element.value = '0';
		//document.Form.LastElement.value = '0';
		//document.Form.Search.value = 'Seleccionar';
		document.Form.submit();
	}

	function goMod(vmod)
	{
		document.Form.mod.value = vmod;
		document.Form.submit();
	}

	function ConfirmDelete()
	{
		if (confirm("Are you sure that you want to delete this element?"))
			{
			document.Form.Delete.value = 'true';
			document.Form.submit();
			}
	}

	function CreateXMLFile(vpath)
	{
		vfile = prompt('Type the name of the xml file (and path): ', vpath);

		if ((vfile!=vpath) && (vfile))
			{
			alert(vfile);
			document.Form.modNew.value = vfile;
			document.Form.submit();
			}
	}

	function ChangeTable(vtableName, vfield)
	{
	<?php echo $vScriptGlobal; ?>

		cant = vfield.options.length;
		for(var i=0; i<cant; i++)
			{
			vfield.options[i]=null;
			}
		for(var s=0; s<fieldsArrayCount[vtableName]; s++)
			{
			vfield.options[s]= fieldsArray[vtableName+String(s)];
			}
		vfield.selectedIndex = 0;
	}

	function Add_Action(vcomponent, vfield, vsave)
	{
		if (vfield.type=='select-one')
			{
			if (vsave.length==0)
				{
				vsave.value = vcomponent.options[vcomponent.selectedIndex].value+'='+vfield.options[vfield.selectedIndex].value;
				}
			else	{
				vsave.value += ';'+vcomponent.options[vcomponent.selectedIndex].value+'='+vfield.options[vfield.selectedIndex].value;
				}
			}
		else	{
			if (vsave.length==0)
				{
				vsave.value = vcomponent.options[vcomponent.selectedIndex].value+'='+vfield.value;
				}
			else	{
				vsave.value += ';'+vcomponent.options[vcomponent.selectedIndex].value+'='+vfield.value;
				}
			}
	}

	function UpdateField()
	{

		fieldsArray = new Array();
		fieldsArrayCount = new Array();

		fieldsArray['a',0] = new Option("", "");
		fieldsArray['a',1] = new Option("Rojo", "Color Rojo");
		fieldsArray['a',2] = new Option("Azul", "Color azul");
		fieldsArray['b',0] = new Option("", "");
		fieldsArray['b',1] = new Option("Rojo", "Color Rojo");
		fieldsArray['b',2] = new Option("Azul", "Color azul");
		fieldsArray['b',3] = new Option("Azul", "Color azul");
		fieldsArrayCount['a'] = 3;
		fieldsArrayCount['b'] = 4;


		cant = document.Form.field.options.length;
		i = fieldsArrayCount['a'];
		alert(i);
		for(var i=0; i<cant; i++)
			{
			document.Form.field.options[0]=null;
			}

		for(var i=0; i<fieldsArrayCount['a']; i++)
			{
			document.Form.field.options[i]=fieldsArray['a',i];
			}
		//document.Form.field.options[i] = fieldsArray['a',i];
	}

	function listboxUpdate(list, vsave, vselected, vdelimiter)
	{
		vsave.value = "";
		vselected.value = "";
		for(var i=0; i<list.options.length; i++) {
			if (vsave.value == "")
				{
				vsave.value = list.options[i].value;
				}
			else	{
				vsave.value += vdelimiter+list.options[i].value;
				}

			if (list.selectedIndex==i) {
				vselected.value = list.options[i].value;
				}
			}
	}

	function listboxSummary(vSummary, list, vdelimiter)
	{
		var vsave = "";
		for(var i=0; i<list.options.length; i++)
			{

			if (vsave == "")
				{

				vsave = list.options[i].text;
				}
			else	{
				vsave += vdelimiter+list.options[i].text;
				}
			}
		vSummary.value = vsave;

	}

	function listboxAdd(combo, list, vsave, vselected, vdelimiter)
	{
		if (combo.value != "") {
			for(var i=0; i<list.options.length; i++) {
				if (list.options[i].value==combo.value) {
					return;
					}
				}
			var no = new Option();
			no.value = combo.value;
			no.text = combo.options[combo.selectedIndex].text
			list.options[list.options.length] = no;
			}
		listboxUpdate(list, vsave, vselected, vdelimiter);
	}

	function listboxAddGen(combo, generator, list, vsave, vselected, vdelimiter)
	{
		vposi = list.options.length;
		if ((combo.value != "") && (combo.generator != "")){
			for(var i=0; i<list.options.length; i++)
				{
				if (combo.value==list.options[i].value.substring(0,list.options[i].value.indexOf(":")))
					{
					vposi = i;
					}
				}

			var no = new Option();
			no.value = combo.options[combo.selectedIndex].text+":"+generator.value;
			no.text = combo.options[combo.selectedIndex].text+":"+generator.value;
			list.options[vposi] = no;
			}
		listboxUpdate(list, vsave, vselected, vdelimiter);
	}

	function listboxAddFk(combo, table, field, list, vsave, vselected, vdelimiter)
	{
		vposi = list.options.length;
		if ((combo.value != "") && (combo.table != "") && (combo.field != "")){
			for(var i=0; i<list.options.length; i++)
				{
				if (combo.value==list.options[i].value.substring(0,list.options[i].value.indexOf("=")))
					{
					vposi = i;
					}
				}
			var no = new Option();
			no.value = combo.value+"="+table.value+"."+field.value;
			no.text = combo.value+"="+table.value+"."+field.value;
			list.options[vposi] = no;
			}
		listboxUpdate(list, vsave, vselected, vdelimiter);
	}

	function listboxAddfknull(list1, list2, vsave, vselected, vdelimiter)
	{
		vposi = list2.options.length;
		if (list1.selectedIndex>-1)
			{
			vvalue = list1.options[list1.selectedIndex].value;
			vvalue = vvalue.substring(0,vvalue.indexOf("="))
			}
		else	{
			return
			}

		for(var i=0; i<list2.options.length; i++)
			{
			if (vvalue == list2.options[i].value)
				{
				return;
				}
			}
		var no = new Option();
		no.value = vvalue;
		no.text = vvalue;
		list2.options[vposi] = no;

		listboxUpdate(list2, vsave, vselected, vdelimiter);
	}

	function listboxUp(list, vsave, vselected, vdelimiter)  {
		for(var i=0; i<list.options.length; i++) {
			if (list.options[i].selected) {
				if (i==0) {
					return;
					}
				var no = new Option();

				no.value = list.options[i-1].value;
				no.text = list.options[i-1].text

				list.options[i-1].value = list.options[i].value;
				list.options[i-1].text = list.options[i].text;
				list.options[i] = no;
				list.selectedIndex = i-1;

				listboxUpdate(list, vsave, vselected, vdelimiter);
				return;
				}
			}

	}

	function listboxDown(list, vsave, vselected, vdelimiter)  {
		for(var i=0; i<list.options.length-1; i++) {
			if (list.options[i].selected) {
				var no = new Option();

				no.value = list.options[i+1].value;
				no.text = list.options[i+1].text

				list.options[i+1].value = list.options[i].value;
				list.options[i+1].text = list.options[i].text;
				list.options[i] = no;
				list.selectedIndex = i+1;

				listboxUpdate(list, vsave, vselected, vdelimiter);
				return;
				}
			}

	}

	function listboxDel(list, vsave, vselected, vdelimiter)  {
		for(var i=0; i<list.options.length; i++) {
			if (list.options[i].selected) {
				for(var j=i; j<list.options.length-1; j++) {
					list.options[j].value = list.options[j+1].value;
					list.options[j].text = list.options[j+1].text;
					}
				list.options.length -= 1;

				listboxUpdate(list, vsave, vselected, vdelimiter);
				return;
				}
			}

	}

	function listboxDelFk(list, vsave, vselected, vdelimiter)
	{
		for(var i=0; i<list.options.length; i++)
			{
			if (list.options[i].selected)
				{
				//delete the item selected in fkNull....
				vvalue = list.options[i].value;
				vvalue = vvalue.substring(0,vvalue.indexOf("="))
				for(var ia=0; ia<document.Form.fknull_listbox.options.length; ia++)
					{
					if (vvalue == document.Form.fknull_listbox.options[ia].value)
						{
						document.Form.fknull_listbox.selectedIndex = ia;
						listboxDel(document.Form.fknull_listbox, document.Form.fknull, document.Form.fknull_selected, vdelimiter)
						}
					}

				//delete the item selected in fk....
				for(var j=i; j<list.options.length-1; j++) {
					list.options[j].value = list.options[j+1].value;
					list.options[j].text = list.options[j+1].text;
					}
				list.options.length -= 1;

				listboxUpdate(list, vsave, vselected, vdelimiter);
				return;
				}
			}

	}



	function listboxClear(list, vsave, vselected, vdelimiter)  {
		list.options.length = 0;
		listboxUpdate(list, vsave, vselected, vdelimiter);
	}

	var tableFieldsArray = new Array();
	var tableFieldsArrayCount = 0;

	function UpdateTableFields(vfield)
	{
	alert(tableFieldsArrayCount);
		cant = vfield.options.length;
		for(var i=0; i<cant; i++)
			{
			vfield.options[i]=null;
			}

		for(var s=0; s<tableFieldsArrayCount; s++)
			{
			alert(tableFieldsArray[s]);
			vfield.options[s]= tableFieldsArray[s];
			}
		//vfield.selectedIndex = tableFieldsArrayCount;
	}

	function jsrsBrowserSniff()
	{
	  if (document.layers) return "NS";
	  if (document.all) return "IE";
	  if (document.getElementById) return "MOZ";
	  return "OTHER";
	}


	function openWindowGiveMeFields(isList, vfield, vfieldSt)
	{
		if (vfield.selectedIndex>-1)
			{
			vv = vfield.options[vfield.selectedIndex].text;
			theURL = 'GiveMeFields.php?mod=<?php echo $HTTP_PARAM_VARS['mod']; ?>&opener=Form&field='+vfieldSt.name+'&tablecheck='+vv;
			window.open(theURL,'','width=500,height=250');
			}

	}

	function AddSqlAttribute(attrib, content)
	{
		if (attrib.selectedIndex>-1)
			{
				content.value += attrib.options[attrib.selectedIndex].value;
			}
	}

	function AddSqlAttribute1(attrib, content)
	{
		if (attrib.value.length>-1)
			{
				content.value += attrib.value;
			}
	}
	//-->
	</script>

<?php

	if (isset($sqlarray))
		{
		echo "<br /><br />";
		print_r($sqlarray);
		}
	
	echo "<hr /><a href='/collman-test/help/fwad.htm'>Herramienta de configuración</a>";	
	echo "<br />";
		
	require('./inc/script_end.inc.php');

?>











