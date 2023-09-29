<?php
// File           run.php / Phyllacanthus
// Purpose        Edit any structure defined in a xml file, is posible actions as save, insert, search
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//

if (!isset($HTTP_GET_VARS['mod']))	
	{
	$HTTP_GET_VARS['mod'] = "collman/itf.xml";
	}

$vGoFast = true;
require('./inc/script_start.inc.php');
//require('./lib/adodb/adodb-datadict.inc.php');

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

echo '<pre>';
print_r($s_xml_conf['QueryModRelations']);
echo '</pre>';
	

echo '<pre>';
print_r($s_xml_conf['QueryTableRelations']);
echo '</pre>';

//------------------------------------------------------------------
echo "<a name='markGo'></a>";

//$vTablesInQuery = array();
//$vTablesInQuery['tblITF'] = ""; 
//$vTablesInQuery['tblITF_Clon'] = ""; 
//$vTablesInQuery['tblITF_Additional'] = ""; 
//$vTablesInQuery['tblITF_Source'] = ""; 
//$vTablesInQuery['tblITF_Origin'] = ""; 
//$vTablesInQuery['tblITF_Verification'] = ""; 
//$vTablesInQuery['tblITF_HAJB'] = ""; 


//Declare the main table/node to begin to search
$vquery_node_search['tblITF'] = "";


$vquery_path_found = array( array() );	 //path that has been checked...

if (false)
//Search the tables/nodes with relation with the main...
foreach ($vquery_node_search as $vTableInCheck => $vtemp)
	{

	//Add to $vquery_node_search the tables details with reference to it...
	if (isset($s_xml_conf['QueryTableRelations'][$vTableInCheck]['masterFor']) )
		{
		for ($a = 1; $a < count($s_xml_conf['QueryTableRelations'][$vTableInCheck]['masterFor']); $a++)
			{ 
			$vtemp = $s_xml_conf['QueryTableRelations'][$vTableInCheck]['masterFor'][$a] ;
			if ( !isset($vquery_node_search[$vtemp] ) )
				{
				 $vquery_node_search[$vtemp] = "";
				}
			}
		}
	
	//Add to $vquery_node_search the tables details with reference to it...
	if (isset($s_xml_conf['QueryTableRelations'][$vTableInCheck]['ForeignKey']) )
		{
		foreach ($s_xml_conf['QueryTableRelations'][$vTableInCheck]['ForeignKey'] as $vkey1 => $vtemp)
			{
			if ( !isset($vquery_node_search[$vkey1] ) )
				{
				 $vquery_node_search[$vkey1] = "";
				}
			}
		}
	


	foreach ($s_xml_conf['QueryTableRelations'] as $vTabletemp => $vtemp)
		{
		if (	($vTabletemp!=$vTableInCheck) && 
			(!isset($vquery_path_found[$vTabletemp][$vTableInCheck])) &&   //this path is not yet checked
			(!isset($vquery_path_found[$vTableInCheck][$vTabletemp]))	)
			{
			echo "<br />".$vTabletemp;
			if (isset($vtemp["ForeignKey"][$vTableInCheck]))
				{
				$vfollow = (!empty($vtemp["ForeignKey"][$vTableInCheck]['ifMod'])) ? ($vtemp["ForeignKey"][$vTableInCheck]['ifMod'] == $xmlFile) : true;
				$vfollow = $vfollow && ( (!empty($vtemp["ForeignKey"][$vTableInCheck]['ifnotMod'])) ? ($vtemp["ForeignKey"][$vTableInCheck]['ifnotMod']!= $xmlFile) : true );
				if ($vfollow)
					{
					echo " se relaciona...";
					}

				}
			}
		}		     
	}
 	
//$vFile = "http://www.juventudrebelde.cu/rss/generales.php";
$vFile = "http://www.granma.cu/granmai_es.xml";
$fd = fopen ($vFile, "r");
while (!feof($fd)) 
	{
	$buffer = fgets($fd);
	echo  htmlentities($buffer)."<br>";
	}
fclose ($fd); 
	 	

require('./inc/script_end.inc.php');

?>











