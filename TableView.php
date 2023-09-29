<?php
// File           GiveMeLst.php / Phyllacanthus
// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//
require('./inc/script_start.inc.php');
include_once('./lib/adodb/tohtml.inc.php');

echo html_body();

//fields need to work in the sql query and combobox...
$HTTP_GET_VARS['sql'] = (isset($HTTP_GET_VARS['sql']))? $HTTP_GET_VARS['sql'] : "";


$vSql = urldecode(stripcslashes($HTTP_GET_VARS['sql']));
echo '<form name="Form">';
echo  '<input name="Submit" type="submit" onClick="window.close()" value="Cerrar" > <br><br>';
$vpos = 0;
if ((isset($HTTP_GET_VARS['pos'])) )
	{
	$vpos = (int)$HTTP_GET_VARS['pos'];
	$vpos = $vpos + 0;
	$vposi = ($vpos-100<=0) ? 0: $vpos-100;
	if  ($vpos>0)
		{
		echo '<a href="'.$vpath.'/TableView.php?pos='.($vposi).'&mod='.$HTTP_GET_VARS['mod'].'&sql='.htmlentities(urlencode($vSql)).'">      &lt;&lt;</a>';
		}
	else	{
		echo '      &lt;&lt;';
		}
	}
else
	{
	$HTTP_GET_VARS['pos'] = 0;
	$vpos = 0;
	echo '      &lt;&lt;';
	}

echo '<input name="pos" size="6" type="text" value="'.$vpos.'" onChange="javascript: document.Form.submit();">';
echo '<input name="mod" size="6" type="hidden" value="'.$HTTP_GET_VARS['mod'].'">';
echo '<a href="'.$vpath.'/TableView.php?pos='.($vpos+100).'&mod='.$HTTP_GET_VARS['mod'].'&sql='.htmlentities(urlencode($vSql)).'"> &gt;&gt;</a>';
echo '<hr />';
echo 'Database: <input name="use_database" type="text" value="'.$s_connection['database'].'">';
if (!empty($s_connection['database']))
	{
	echo '<br />SQL:<br /><textarea name="sql" cols="100" rows="8">'.htmlentities($vSql).'</textarea>';
	}
echo '<br /><br /><input type="submit" value="Ejecutar"><hr />';


//Query...
if (!empty($HTTP_GET_VARS['sql']))
	{
	if ((isset($HTTP_GET_VARS['conf_type'])) )
		{
		$vcc = $HTTP_GET_VARS['conf_type']+0;
		$dbhandle = false;
		$vtype_db = $s_xml_conf['connection'][$vcc]['type'];
		if ($vtype_db)
			{
			$dbhandle = &ADONewConnection($vtype_db);   //create de connection
			}
		if (($dbhandle) )
			{
			  if($vtype_db == "odbc")
					{
					if(PERSISTANT_CONNECTIONS)
						{
						$dbhandle->PConnect($s_xml_conf['connection'][$vcc]['database'], $s_xml_conf['connection'][$vcc]['user'],$s_xml_conf['connection'][$vcc]['pswd'], $s_xml_conf['connection'][$vcc]['locale']);
						}
					else 	$dbhandle->Connect($s_xml_conf['connection'][$vcc]['database'], $s_xml_conf['connection'][$vcc]['user'],$s_xml_conf['connection'][$vcc]['pswd'], $s_xml_conf['connection'][$vcc]['locale']);
					}
				if($vtype_db == "access")
					{

					if(PERSISTANT_CONNECTIONS)
						{
						//$dbhandle->PConnect($s_connection['database'], $s_connection['user'],$s_connection['pswd'], $s_connection['locale']);
						$dbhandle->PConnect("Driver={Microsoft Access Driver (*.mdb)};Dbq=".$s_xml_conf['connection'][$vcc]['database'].";Uid=".$s_xml_conf['connection'][$vcc]['user'].";Pwd=".$s_xml_conf['connection'][$vcc]['pswd'].";");
						}
					else
						{
						//$dbhandle->Connect($s_connection['database'], $s_connection['user'],$s_connection['pswd'], $s_connection['locale']);
						$dbhandle->Connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=".$s_xml_conf['connection'][$vcc]['database'].";Uid=".$s_xml_conf['connection'][$vcc]['user'].";Pwd=".$s_xml_conf['connection'][$vcc]['pswd'].";");
						}
					}
				else if (($vtype_db == "ibase") or ($vtype_db == "firebird"))
					{
					if(PERSISTANT_CONNECTIONS)
						{
						$dbhandle->PConnect($s_xml_conf['connection'][$vcc]['hostname'].":".$s_xml_conf['connection'][$vcc]['database'],$s_xml_conf['connection'][$vcc]['user'],$s_xml_conf['connection'][$vcc]['pswd']);
						}
					else 	{
						$dbhandle->Connect($s_xml_conf['connection'][$vcc]['hostname'].":".$s_xml_conf['connection'][$vcc]['database'],$s_xml_conf['connection'][$vcc]['user'],$s_xml_conf['connection'][$vcc]['pswd']);
						}
					}
				else 	{
					if(PERSISTANT_CONNECTIONS)
						{
						$dbhandle->PConnect($s_xml_conf['connection'][$vcc]['hostname'],$s_xml_conf['connection'][$vcc]['user'],$s_xml_conf['connection'][$vcc]['pswd'], $s_xml_conf['connection'][$vcc]['database'],$s_xml_conf['connection'][$vcc]['locale']);
						}
					else $dbhandle->Connect($s_xml_conf['connection'][$vcc]['hostname'],$s_xml_conf['connection'][$vcc]['user'],$s_xml_conf['connection'][$vcc]['pswd'],$s_xml_conf['connection'][$vcc]['database'],$s_xml_conf['connection'][$vcc]['locale']);
					}
			}
		}

	if (strpos(strtoupper($vSql),'SELECT ')>-1)
		{

		$rec = $dbhandle->SelectLimit($vSql,100,$vpos);
		if ($rec === FALSE)
			{

			$db_error .= $dbhandle->ErrorMsg();
			echo $dbhandle->ErrorMsg();
			}
		else	{
			echo rs2html($rec,false,false,true,false);
			}
		echo  '<input name="Submit" type="submit" onClick="window.close()" value="Cerrar" >';
		}
	else
		{
		$rec = $dbhandle->Execute($vSql);
		if ($rec === FALSE) {
			$db_error .= $dbhandle->ErrorMsg();
			echo $dbhandle->ErrorMsg();
			}
		}
	 }
echo "</form>";

require('./inc/script_end.inc.php');
?>
