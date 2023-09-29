<?php
// File           script_start.inc.php
// Purpose        includes and initialisations needed in every Main script
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004, 2005 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/10/18 09:22:43 lb>
//
// $Id: script_start.inc.php,v 1.26.2.1 2005/01/17 21:30:01 lbrueckner Exp $

//apd_set_pprof_trace();

// if $vGoFast is true: there is not a access to the database and xmlconf file....
$vGoFast = (isset($vGoFast)) ? $vGoFast : false;

//Main configuration...
//A special configuration of every site is defined in a file "configuration.php" in the Configuration path of the site
require('./inc/configuration.inc.php');


if ($Confs["DEBUG"] === TRUE) error_reporting(E_ALL);

// warnings and messages are collected in this strings, the output happens in panels/info.php
$vFatalError = false;
//$vSessionName = "sesion_not_initialized";
$message   = '';
$warning   = '';
$error     = '';
$db_error  = '';
$php_error = '';
$debug     = array();
$externcmd = '';
$dbhandle = false;
$vModName = "";
$s_connection['conected'] = false;
//$s_connection['user_register'] = 'anonymus';
//$s_connection['user_type'] = 'invited';

$HTTP_PARAM_VARS = array();  //Get or Post params...
if (isset($HTTP_GET_VARS['mod']))
	{
	//if (count($HTTP_GET_VARS)==1)  //if there is only one paramater then reinit the session vars...
	//	cleanup_session($HTTP_PARAM_VARS['mod']) ;
	$HTTP_PARAM_VARS = $HTTP_GET_VARS;
	$s_GET = $HTTP_GET_VARS;
	}
elseif (isset($HTTP_POST_VARS['mod']))
	{
	$HTTP_PARAM_VARS = $HTTP_POST_VARS;
	$s_POST = $HTTP_POST_VARS;
	}
else	{
	$HTTP_PARAM_VARS['mod'] ='';
	//echo "Is not possible to init without a module ....";
	//exit;
	}

if (isset($HTTP_GET_VARS['version']))
	{
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf8" /></head>
	<body>
	<?php
	echo ("Versión del programa FWAD: ".htmlentities($Confs["fwa_version"]) );
	echo '<br />[ <a href="javascript: history.back();">Retornar</a> ] </body></html>';
	exit;
	}


	
//$vModName = strtolower(basename($HTTP_PARAM_VARS['mod']));
if (!empty($HTTP_PARAM_VARS['mod']))
	{
	$vModName = strtolower(($HTTP_PARAM_VARS['mod']));
	if (strpos($vModName,'.')>0)
		{
		$vSessionName = substr($vModName, 0, strpos($vModName,'.'));
		$vSessionName = str_replace('/' , '_', $vSessionName); 
		}

	$pathModPart = pathinfo($HTTP_PARAM_VARS['mod']);
	$xmlConfDirectory = getcwd().'/'.$Confs["configuration-path"].$pathModPart["dirname"].'/';
	$xmlFile = $pathModPart['basename'];
	$xmlpathMod = $xmlConfDirectory.$xmlFile;
	
	if (!file_exists ($xmlpathMod))
		{
		$vFatalError = true;
		$error .= "No se encuentra el archivo de configuraciOn: ".$xmlpathMod;
		}
	$pathMod = $Confs["configuration-path"].'/'.$pathModPart["dirname"].'/';
	$pathApp = substr($xmlConfDirectory,0,strpos($xmlConfDirectory,"\\htdocs"));  // c:\xampp
	if (empty($pathApp))
		{
		$pathApp = substr($xmlConfDirectory,0,strpos($xmlConfDirectory,"\\www"));  // to ald versions...c:\site
		}

	}
else
	{
	$vFatalError = true;
	$error .= "No se ha definido archivo xml de configuraciOn... ";
	}

//Load configuration templates
$Confs["template-defined"]	= false;


if ( isset( $HTTP_PARAM_VARS['s_id']) )
	{
	//session_id($HTTP_PARAM_VARS['s_id']);
	}
else	
	{
	//session_name($vSessionName);
	}

$vSessionName = 'fwa';
//$vSessionName = $vModName;
session_name( $vSessionName );
if (isset($HTTP_PARAM_VARS['s_id']))	
	session_id( $HTTP_PARAM_VARS['s_id'] );
session_start();

$HTTP_PARAM_VARS['s_id'] = session_id();

error_reporting(E_ALL & ~E_NOTICE);  //E_ALL & ~E_NOTICE & ~E_STRICT

if ($Confs["DEBUG"]) $start_time = @microtime();


if (isset($HTTP_PARAM_VARS['Language']))
	{
	$HTTP_SESSION_VARS['s_cust']['language'] = $HTTP_PARAM_VARS['Language'];
	}

$s_lasterrors = array();
global $LANGUAGE;
$LANGUAGE = (isset($HTTP_SESSION_VARS['s_cust']['language']) ? $HTTP_SESSION_VARS['s_cust']['language'] : $Confs["LANGUAGE"]);
$ADODB_LANG = $LANGUAGE;
require('./inc/lang/' . $LANGUAGE. '.inc.php');

if (file_exists ($xmlConfDirectory.'configuration.php'))
		{
		require($xmlConfDirectory.'configuration.php');
		}	

require('./lib/adodb/adodb-errorhandler.inc.php');
require('./lib/adodb/adodb.inc.php');
require('./inc/session.inc.php');
require('./inc/functions.inc.php');
set_error_handler('error_handler');

require('./lib/xpath/XPath.class.php');

if ($Confs["DEBUG"]  ||  $Confs["DEBUG_HTML"]) {
    include('./inc/debug_funcs.inc.php');
    }



ini_set('magic_quotes_runtime', '0');

//if (!in_array('interbase', get_loaded_extensions())) {
//    @dl('interbase.so')  ||  @dl('interbase.dll');
//}

send_http_headers();
//echo "Session: ".session_name()."=".session_id()."\n";


if (isset($HTTP_SESSION_VARS['s_init']))
	{
	//    if (!in_array('interbase', get_loaded_extensions())) {
	//
	//        die($ERRORS['NO_IBASE_MODULE']);
	//    }

	$ver = phpversion();
	if ($ver[0] == 5  && ini_get('register_long_arrays') == 0)
		{
		die('You have to change your php-configuration and set register_long_arrays=On to use ibWebAdmin with php5!');
		}
		
	initialize_session( $vModName );
	//fallback_session();
	}
else 	
	{
    localize_session_vars( $vModName );
	}


//Action logout...
if ( (isset($HTTP_PARAM_VARS['logout'])) && (!isset($HTTP_POST_VARS['loginsubmit'])) )
	{
	$s_connection['user_register'] = '';
	$s_connection['user_type'] = '';
	unset($HTTP_POST_VARS['login_user']);
	unset($HTTP_PARAM_VARS['logout']);
	}


// this string is filled in the panels and echoed in script_end.inc.php
// to avoid problems ns4.7 has with javascript inside of tables
$js_stack = '';

// the different tasks storing their sql-statements in this string
// for joint execution just before the panel-output
$sql =  '';



if ($Confs["DEBUG_HTML"]) ob_start();

if ($vGoFast)  //We test if the session hat this variable defined...if the variable are not defined we go to open the xml conf file with all the attributes..
	{
	if (isset($s_connection['type']))
		{
		if (!empty($s_connection['type']))
			{
			require('./inc/driver_'.$s_connection['type'].'.php');
			}
		else
			{
			$vGoFast = false;
			}
		}
	else
		{
		$vGoFast = false;
		}
	}


//Open the xml configuration file...
if (!$vFatalError)	//(!empty($HTTP_PARAM_VARS['mod']))
			//In this case we can find the xml file and it exist...
	{

	$vpath = (parse_url($_SERVER["REQUEST_URI"]));
	$vv = (dirname($vpath['path'])=="\\")? '': dirname($vpath['path']);
	if ($_SERVER["SERVER_SOFTWARE"]=='DWebPro')
		{
		$vpath = 'http://127.0.0.1:8080'.dirname($vpath['path']);
		}
	else	{
		$vpath = 'http://'.$_SERVER["HTTP_HOST"].dirname($vpath['path']);
		}

	//------------------------------------------------------------------
	//XPATH section with xml conf...
	$s_xml_conf['init'] = (isset($s_xml_conf['init'])) ? $s_xml_conf['init'] : "0";
	if ( ($s_xml_conf['init'] == 0) or ($s_xml_conf['lang'] != $ADODB_LANG))
		{
		$dom =& new XPath();
		$dom->setSkipWhiteSpaces(TRUE);


		if (!$dom->importFromFile($xmlpathMod))
			{
			//$error .= sprintf($ERRORS['NO_FILE_CONFIGURATION'], $HTTP_PARAM_VARS['mod']);
			$error .= "No se puede abrir el archivo: ".$xmlpathMod;
			}
		else	{
			//Process the xml file...search the Main tag
			$aResult = $dom->evaluate("/Main/connection/inc");
			//$root = $dom->document_element();
			//$elements = $root->get_elements_by_tagname("inc");
			//case of exist inc tag (include file)
			//if (count($elements)>0)
			if (count($aResult)>0)
				{
				//$child = $elements[0];
				//if (!$domTmp = domxml_open_file($vpath.'/'.$child->get_content()))
				$domTmp =& new XPath();
				$domTmp->setSkipWhiteSpaces(TRUE);

				//if (!$domTmp->importFromFile($xmlConfDirectory.$dom->getData("/Main/connection/inc[1]") ))

				if (!$domTmp->importFromFile(getcwd().'/'.$Confs["configuration-path"].$dom->getData("/Main/connection/inc[1]") ))
					{
					$error .= sprintf($ERRORS['NO_FILE_CONFIGURATION'], $HTTP_PARAM_VARS['mod']);
					}
				else	{
					$elements = $domTmp;
					$aResult = $domTmp->evaluate("/Main/connection/db");
					//$root = $domTmp->document_element();
					//$elements = $root->get_elements_by_tagname("db");
					}  //
				}
			else	{
				//$root = $dom->document_element();
				//$elements = $root->get_elements_by_tagname("db");
				$aResult = $dom->evaluate("/Main/connection/db");
				$elements = $dom;
				}

			//case of db tag (configuration of the db
			if (count($aResult)>0)
				{
				$entered = 0;
				for ($i = 0; $i < count($aResult); $i++)
					{
					//$child = $elements[$i];
					//only we go to process the first
					//if ((!empty($child->tagname)) and (!$entered))
					if ((!$entered))
						{
						$entered = 1;

						$s_connection['database'] = $elements->getAttributes("/Main/connection/db[1]",'database');
						$s_connection['database'] = mb_eregi_replace("__pathApp__", $pathApp, $s_connection['database']);

						$s_connection['hostname'] = $elements->getAttributes("/Main/connection/db[1]",'hostname');
						$s_connection['type'] =  $elements->getAttributes("/Main/connection/db[1]",'type');
						$s_connection['user'] = $elements->getAttributes("/Main/connection/db[1]",'user');
						$s_connection['password'] = $elements->getAttributes("/Main/connection/db[1]",'pswd');
						$s_connection['locale'] = $elements->getAttributes("/Main/connection/db[1]",'locale');

						//$s_connection['database'] = $child->get_attribute('database');
						//$s_connection['hostname'] = $child->get_attribute('hostname');
						//$s_connection['type'] = $child->get_attribute('type');
						//$s_connection['user'] = $child->get_attribute('user');
						//$s_connection['password'] = $child->get_attribute('pswd');
						//$s_connection['locale'] = $child->get_attribute('locale');

						$s_connection['role'] = '';

						$vSql = stripcslashes(urldecode($elements->getData("/Main/connection[1]/db[1]/register[1]")));
						$s_connection['register'] = $vSql;

						//we go to process the child with tag name "register"
						//$child1 = $child->first_child();
						//while ($child1)
						//	{
						//	if ((!(empty($child1->tagname))) and
						//	    ($child1->tagname=='register'))
						//		{
						//		$vSql = $child1->get_content();
						//		}
						//	$child1 = $child1->next_sibling();
						//	}
						//unset($child1);
						}
					}


				}

			if (isset($domTmp))
				{
				unset($domTmp);
				}

			//$root = $dom->document_element();

			//if (($s_xml_conf['init'] == 0) or ($s_xml_conf['lang'] != $ADODB_LANG))
				{
				$s_xml_conf = array();
				$s_xml_conf['init'] = 1;
				$s_xml_conf['lang'] = $ADODB_LANG;

				//Procesing the "configuration" tag
				process_xml_path($dom, '/Main','configuration');

				//Procesing the "connection" tag
				process_xml_path($dom, '/Main','connection');
				process_xml_path($dom, '/Main/connection', 'db');

				//Procesing the "tables" tag
				process_xml_path($dom, '/Main','tables', 'lang', $ADODB_LANG);

				//Procesing the "searchs" tag
				process_xml_path($dom, '/Main','searchs', 'lang', $ADODB_LANG);

				//Procesing the "links" tag
				process_xml_path($dom, '/Main','links', 'lang', $ADODB_LANG);

				//Procesing the "elements" tag
				process_xml_path($dom, '/Main','elements', 'lang', $ADODB_LANG);
				}
			}
		}


	//Read the relation with other modules (QueryRel.xml)
	//if ( !isset($s_xml_conf['QueryModRelations']) )
		{
		$xmlQueryRel = $xmlConfDirectory.'QueryRel.xml';
		if (file_exists($xmlQueryRel))
			{
			$domQueryRel =& new XPath();
			$domQueryRel->setSkipWhiteSpaces(TRUE);

			if (!$domQueryRel->importFromFile($xmlQueryRel))
				{
				//$error .= sprintf($ERRORS['NO_FILE_CONFIGURATION'], $HTTP_PARAM_VARS['mod']);
				$error .= "Error al abrir el archivo: ".$xmlQueryRel;
				}
			else	{
				//Process the xml file...
				$xmlModulesResult = $domQueryRel->evaluate("/Main/modules");
				for ($i = 0; $i < count($xmlModulesResult); $i++)
					{
					$s_xml_conf['QueryModRelations'][$i]['moduleMaster'] = $domQueryRel->getAttributes("/Main/modules[".($i+1)."]",'moduleMaster');
					$s_xml_conf['QueryModRelations'][$i]['moduleDetail'] = $domQueryRel->getAttributes("/Main/modules[".($i+1)."]",'moduleDetail');

					$s_xml_conf['QueryModRelations'][$i]['moduleMasterInfo'] = $domQueryRel->getAttributes("/Main/modules[".($i+1)."]",'moduleMasterInfo');
					$s_xml_conf['QueryModRelations'][$i]['moduleDetailInfo'] = $domQueryRel->getAttributes("/Main/modules[".($i+1)."]",'moduleDetailInfo');

					$xmlRelationsResult = $domQueryRel->evaluate("/Main/modules/relation");
					for ($a = 0; $a < count($xmlRelationsResult); $a++)
						{
						//MasterTablename="tblITF" MasterFieldName="ITFId"  aliasMasterTablename=""
						//DetailTablename="tblITF_Clon" DetailFieldName="ITFFk" aliasDetailTablename=""
						//MasterDetailRel="true" dbType="I"/>

						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['MasterTablename'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'MasterTablename');
						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['MasterFieldName'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'MasterFieldName');
						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['aliasMasterTablename'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'aliasMasterTablename');

						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['DetailTablename'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'DetailTablename');
						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['DetailFieldName'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'DetailFieldName');
						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['aliasDetailTablename'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'aliasDetailTablename');

						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['aliasDetailTablename'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'aliasDetailTablename');
						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['Type'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'Type');

						$s_xml_conf['QueryModRelations'][$i]['relation'][$a]['from'] = $domQueryRel->getAttributes("/Main/modules/relation[".($a+1)."]",'from');
						}
					}

				    //The relation is using the alias/tablename
//				    <table tablename="tblITF_Clon" alias="tblITF_Clon">
//					<PrimaryKey>
//						 <Attribute Type='I'>ClonId</Attribute>
//					</PrimaryKey>
//					<ForeignKey MasterTableName="tblITF">
//					  <Attribute Type='I' MasterFieldName="ITFId" DetailFieldName="ITFFk" />
//					</ForeignKey>
//				    </table>
				unset($s_xml_conf['QueryTableRelations']);
				$xmlModulesResult = $domQueryRel->evaluate("/Main/tables/table");
				for ($i = 0; $i < count($xmlModulesResult); $i++)
					{

					$vTable =  $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]",'tablename');
					$vAlias = $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]",'alias');
					$vAlias = (empty ($vAlias) ) ? $vTable : $vAlias;

					$s_xml_conf['QueryTableRelations'][$vAlias]['tablename'] = $vTable;
					$s_xml_conf['QueryTableRelations'][$vAlias]['alias'] = $vAlias;

					//Checking the primary keys....
					$xmlRelationsResult = $domQueryRel->evaluate("/Main/tables/table[".($i+1)."]/PrimaryKey/Attribute");
					for ($a = 0; $a < count($xmlRelationsResult); $a++)
						{
						$s_xml_conf['QueryTableRelations'][$vAlias]['PrimaryKey'][$a]['Type'] = $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]/PrimaryKey/Attribute[".($a+1)."]",'Type');
						$s_xml_conf['QueryTableRelations'][$vAlias]['PrimaryKey'][$a]['Field'] = $domQueryRel->getData("/Main/tables/table[".($i+1)."]/PrimaryKey/Attribute[".($a+1)."]");
						}


					//Checking the foreign keys....
					$xmlRelationsResult = $domQueryRel->evaluate("/Main/tables/table[".($i+1)."]/ForeignKey");
					for ($a = 0; $a < count($xmlRelationsResult); $a++)
						{
						//$s_xml_conf['QueryTableRelations'][$vAlias]['ForeignKey'][$a]['MasterTableName']
						$vAliasFk = $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]/ForeignKey[".($a+1)."]",'MasterTableName');
						$s_xml_conf['QueryTableRelations'][$vAliasFk]['masterFor'][] = $vAlias;

						$s_xml_conf['QueryTableRelations'][$vAlias]['ForeignKey'][$vAliasFk]['ifMod'] = $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]/ForeignKey[".($a+1)."]",'ifMod');
						$s_xml_conf['QueryTableRelations'][$vAlias]['ForeignKey'][$vAliasFk]['ifnotMod'] = $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]/ForeignKey[".($a+1)."]",'ifnotMod');
						$xmlRelationsResultAttrs = $domQueryRel->evaluate("/Main/tables/table[".($i+1)."]/ForeignKey/Attribute");

						for ($aa = 0; $aa < count($xmlRelationsResultAttrs); $aa++)
							{
							$s_xml_conf['QueryTableRelations'][$vAlias]['ForeignKey'][$vAliasFk][$aa]['Type'] = $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]/ForeignKey[".($a+1)."]/Attribute[".($aa+1)."]",'Type');
							$s_xml_conf['QueryTableRelations'][$vAlias]['ForeignKey'][$vAliasFk][$aa]['MasterFieldName'] = $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]/ForeignKey[".($a+1)."]/Attribute[".($aa+1)."]",'MasterFieldName');
							$s_xml_conf['QueryTableRelations'][$vAlias]['ForeignKey'][$vAliasFk][$aa]['DetailFieldName'] = $domQueryRel->getAttributes("/Main/tables/table[".($i+1)."]/ForeignKey[".($a+1)."]/Attribute[".($aa+1)."]",'DetailFieldName');
							}
						}


					}


				}
			}
		}

	} ///



if ((!$vGoFast) && (!$vFatalError))
	{
	//echo $s_connection['database']."----".$s_connection['user']."----".$s_connection['password']."----". $s_connection['locale'];
	if (!empty($HTTP_GET_VARS['use_database']))
		{
		$s_connection['database'] = $HTTP_GET_VARS['use_database'];
		}

	if (!isset($HTTP_GET_VARS['unconnected']) and (!empty($s_connection['type'])))
		{
		$dbhandle = &ADONewConnection($s_connection['type']);   //create de connection
		}

	if (!isset($HTTP_GET_VARS['unconnected']) and (!empty($s_connection['database'])))
		{
		
		if($s_connection['type'] == "odbc")
			{
			if($Confs["PERSISTANT_CONNECTIONS"])
				{
				$s_connection['conected'] = $dbhandle->PConnect($s_connection['database'], $s_connection['user'],$s_connection['password'], $s_connection['locale']);
				}
			else 	$s_connection['conected'] = $dbhandle->Connect($s_connection['database'], $s_connection['user'],$s_connection['password'], $s_connection['locale']);
			}
		else if($s_connection['type'] == "access")
			{

			if($Confs["PERSISTANT_CONNECTIONS"])
				{
				//$dbhandle->PConnect($s_connection['database'], $s_connection['user'],$s_connection['password'], $s_connection['locale']);
				$s_connection['conected'] = $dbhandle->PConnect("Driver={Microsoft Access Driver (*.mdb)};Dbq=".$s_connection['database'].";Uid=".$s_connection['user'].";Pwd=".$s_connection['password'].";");
				}
			else
				{
				//$dbhandle->Connect($s_connection['database'], $s_connection['user'],$s_connection['password'], $s_connection['locale']);
				$s_connection['conected'] = $dbhandle->Connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=".$s_connection['database'].";Uid=".$s_connection['user'].";Pwd=".$s_connection['password'].";");
				}
			}

		else if (($s_connection['type'] == "ibase") or ($s_connection['type'] == "firebird"))
			{
			if($Confs["PERSISTANT_CONNECTIONS"])
				{
				$s_connection['conected'] = $dbhandle->PConnect($s_connection['hostname'].":".$s_connection['database'],$s_connection['user'],$s_connection['password']);
				}
			else 	{
				$s_connection['conected'] = $dbhandle->Connect($s_connection['hostname'].":".$s_connection['database'],$s_connection['user'],$s_connection['password']);
				}
			}
		else 	{
			if($Confs["PERSISTANT_CONNECTIONS"])
				{
				$s_connection['conected'] = $dbhandle->PConnect($s_connection['hostname'],$s_connection['user'],$s_connection['password'], $s_connection['database'],$s_connection['locale']);
				}
			else $s_connection['conected'] = $dbhandle->Connect($s_connection['hostname'],$s_connection['user'],$s_connection['password'],$s_connection['database'],$s_connection['locale']);
			}


		//$dbhandle->Connect($s_connection['database'],$s_connection['user'],$s_connection['password']);
		//$dbhandle->debug = true;


		if (!$s_connection['conected'])
			{
			$db_error = $dbhandle->ErrorMsg();
			$error .= $dbhandle->ErrorMsg();
			$vFatalError = true;
			}
		else	{
			// connecting the database, the handle is used as a global variable,
			// the connection is closed in inc/script_end.inc.php

			//load the database driver

			require('./inc/driver_'.$s_connection['type'].'.php');

			//Store the type of every field used for the tables declared in the module opened...
			if (!isset($s_xml_conf['tables'][0]['field']))
				{
				for ($i=0; $i < count($s_xml_conf['tables']); $i++)
					{
					$vTable = $s_xml_conf['tables'][$i]['name'];
					if (!empty($vTable))
						{
						$vt = $syntax['table'];
						$vt = ereg_replace('#1',$vTable, $vt);
						$vSql = 'SELECT * FROM '.$vt;
						if ($rec = $dbhandle->SelectLimit($vSql,1))
							{
							for ($a=0, $max=$rec->FieldCount(); $a < $max; $a++)
								{
								$fld = $rec->FetchField($a);
								$type = $rec->MetaType($fld->type);
								$s_xml_conf['tables'][$i]['field'][$fld->name] = $type;
								}
							}
						}
					}
				}

$s_connection['user_type'] = 'administrator';
//$s_connection['user_type'] = "guess";
$s_connection['user_register'] = "guess";				
				
			//Init authentication....
			if ((!empty($s_connection['register'])) && (empty($s_connection['user_register'])) ) 
				{
				//$s_connection['user_register'] = 'anonymus';
				//$s_connection['user_type'] = 'invited';

				if (($Confs["AccessAsGuess"]) && (isset($HTTP_POST_VARS['AccessAsGuess'])) )
					{
					$s_connection['user_type'] = 'invited';
					$s_connection['user_register'] = "guess";
					}
				else if ( isset($HTTP_POST_VARS['login_user']) )  
					{
					//contrase�a vac�a d41d8cd98f00b204e9800998ecf8427e
					$vSql = $s_connection['register'];
					$vSql = ereg_replace('__user__',$HTTP_POST_VARS['login_user'], $vSql);
					//$vSql = ereg_replace('__pswd__',md5(trim($HTTP_POST_VARS['login_passwd'])), $vSql);
					$vSql = ereg_replace('__pswd__',(trim($HTTP_POST_VARS['login_passwd'])), $vSql);
					$rec = $dbhandle->Execute(urldecode($vSql));

					if ($rec === FALSE)
						{
						$db_error .= $dbhandle->ErrorMsg();
						}
					else	{
						if ($rec->EOF)
							{
							
							//
							if (strpos($_SERVER["REQUEST_URI"],'?')==0)
								{
								redirect(('http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'?mod='.$HTTP_PARAM_VARS['mod']."&usernotfound") );
								}
							else	
								{
								redirect(('http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&usernotfound" ));
								}
							//js_alert($message_strings["UserNotFound"]);
							exit;
							}
						else	
							{
							$s_connection['user_register'] = $HTTP_POST_VARS['login_user'];
							if (isset($rec->fields['role']))
								{
								$s_connection['user_type'] = $rec->fields['role'];
								}
							else	{
								$s_connection['user_type'] = 'invited';
								}
							$s_connection['user_type'] = ($s_connection['user_type']==1) ? 'administrator' : "invited";
							}
						}
					}
				else
					{
					require('templates/example/index.php');
					
					$smarty->assign('FormParameters','<input name="mod" type="hidden" value="'.$HTTP_PARAM_VARS['mod'].'" /><input name="s_id" type="hidden" value="'.$HTTP_PARAM_VARS['s_id'].'" />'."\n".
						'<input name="loginsubmit" type="hidden" value="loginsubmit" />');
					$smarty->assign('head','');
					$smarty->assign('foot',"");
					$smarty->assign('MSGusernotfound','');
					$smarty->assign('MSGLogin',$button_strings["Login"]);
					$smarty->assign('MSGUsername',$button_strings["Username"]);
					$smarty->assign('MSGPassword',$button_strings["Password"]);
					$smarty->assign('MSGLoginForgotten',$message_strings["LoginForgotten"]);
					$smarty->assign('INPUTLoginUsername','<input type="text" name="login_user" maxlength="12" value="" />');
					$smarty->assign('INPUTloginPasswd','<input type="password" name="login_passwd" maxlength="12" value="" />');
					$smarty->assign('INPUTLoginAsGuest','');
					$smarty->assign('INPUTLoginForgotten','<input name="AdminMessage" type="submit" value="'.$button_strings["SendMessagAdmin"].'" />');
					
					if (isset($HTTP_PARAM_VARS['usernotfound'])) $smarty->assign('MSGusernotfound',$message_strings["UserNotFound"].'<br />');
					if ($Confs["AccessAsGuess"]) 
						$smarty->assign('INPUTLoginAsGuest',"<input name='AccessAsGuess' type='submit' value='".$button_strings["LoginAsGuest"]."' /> <hr />"); 
	
					$smarty->display('login.tpl');
					require("inc/script_end.inc.php");
					exit;

					}



				}
			}

		//Configuration of adoDB
		$ADODB_COUNTRECS=false; //is not possible to use $recordSet->RecordCount()
		$dbhandle->SetFetchMode(ADODB_FETCH_ASSOC);
		}
	}

//checking if there is updates....
$s_firstTime = (isset($s_firstTime)) ? $s_firstTime : "1";
if ($s_firstTime==1)
	{
	if (file_exists ($xmlConfDirectory."updates.php"))
		{
		require($xmlConfDirectory."updates.php");

		}
	}

if (!$s_connection['conected'])
	{
	$error .= "No se ha podido conectar con la base de datos";
	$vFatalError = true;
	}


$vGoFast = false;


//echo html_head('');
?>