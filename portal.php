<?php
require('./lib/xpath/XPath.class.php');

$debug = 0;

$vpath = (parse_url($_SERVER["REQUEST_URI"]));
$vv = (dirname($vpath['path'])=="\\")? '': dirname($vpath['path']);
if ($_SERVER["SERVER_SOFTWARE"]=='DWebPro')
	{
	$vpath = 'http://127.0.0.1:8080'.$vv.'/';
	}
else	{
	$vpath = 'http://'.$_SERVER["HTTP_HOST"].$vv.'/';
	}


if (!$dom = domxml_open_file($vpath."webcnf.xml")) {
  echo "Error parsing el xml...\n";
  exit;	}
$root = $dom->document_element();

$xmlConfDirectory = getcwd();
$pathApp = substr($xmlConfDirectory,0,strpos($xmlConfDirectory,"\\htdocs"));  

$node_array = $root->get_elements_by_tagname("Host");
$host = $node_array[0]->get_content();  //"localhost:d:\databases\collman.gdb";
$host = mb_eregi_replace("__pathApp__", $pathApp, $host);

$username = $node_array[0]->get_attribute('username');  //"SYSDBA";
$password = $node_array[0]->get_attribute('password');  //"masterkey";

$node_array = $root->get_elements_by_tagname("Image");
$imageLink = urldecode($node_array[0]->get_content());	//'http://templo/fotos/2002_Expedición a Cuba (Gerardo)';
$imageWidth= $node_array[0]->get_attribute('width');
$imageHeight= $node_array[0]->get_attribute('height');
$imageCols= $node_array[0]->get_attribute('cols');

$node_array = $root->get_elements_by_tagname("configuration");
$ShowCount = $node_array[0]->get_attribute('ShowCount');
$ShowBlank = ($node_array[0]->get_attribute('ShowBlank')=="true");

if (empty($_REQUEST["inidb"])) {
  $ShowIni = 0;  }
 else
  {$ShowIni=$_REQUEST["inidb"];}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
	<HEAD>
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
		<META NAME="generator" CONTENT="GoLive CyberStudio">
<!-- TemplateBeginEditable name="doctitle" -->
<TITLE>Portal CollMan</TITLE>
<!-- TemplateEndEditable --> <!-- TemplateBeginEditable name="head" --><!-- TemplateEndEditable -->
<link href="styles/default.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
<!--
function JS_OnLoad()
{
	posiciona()
	empezar = true;
}

//-->
</script>
</HEAD>

<BODY ONLOAD='JS_OnLoad()' BGCOLOR="#FFFFFF" >
<link href="styles/default.css" rel="stylesheet" type="text/css">
<?php 
	if (file_exists ("configuration/collman/head_ald.php"))
		{
		require("configuration/collman/head_ald.php");
		}	
?>

	<TABLE WIDTH="796" HEIGHT="22" BORDER="0" CELLSPACING="0" CELLPADDING="0">
		<TR>
		    <td WIDTH="24%" VALIGN="TOP">
		      <CENTER>
			<A HREF="portal.php"><strong>Inicio</strong></A><br>
<?php

//$host = "127.0.0.1:D:/mandy/collman/databases/collman.gdb";

exit;

$dbh = ibase_connect ( $host, $username, $password ) or die ("error in db connect");
$stmt='select "wwwId", "GroupName", "ParentYN", "Title", "Link", "LinkIdOrder" from "tblwww" left join "tblwww_link" on "tblwww"."wwwId"="tblwww_link"."wwwFk" where "ParentYN"=1 order by "GroupName"';
$query = ibase_prepare($stmt);
$consulta = ibase_query ($dbh, $stmt);
$vName = '';
while ($fila = ibase_fetch_object ($consulta)) {
   if ($vName!=$fila->GroupName) {
     $vName = $fila->GroupName;
     print "<hr><strong>".$vName."</strong><br>";
   }
   $vIns = '';
   if (is_numeric($fila->LinkIdOrder)) {
     $vIns = '&idOrder='.$fila->LinkIdOrder;
     }
   if (is_numeric($fila->Link)) {
     print '<a href="portal.php' . htmlspecialchars('?Action=Show&id='.$fila->Link.$vIns).'">'.$fila->Title."</a><br>";
    }
   else
    {
     print '<a href="'. htmlspecialchars($fila->Link).'">'.$fila->Title."</a><br>";
    }
}

/* close db */
//ibase_close($dbh);
print '<hr><A HREF="portal.php'. htmlspecialchars('?Action=SiteMap').'"><strong>Mapa del sitio</strong></A><br>'
?>
    </CENTER></td>
	<TD WIDTH="2%">&nbsp;</TD>

    <TD WIDTH="74%">
 <?php
 if (!empty($_REQUEST["Action"])){
 	//$host = "d:/Colecciones/collman.gdb";

	//$username = "SYSDBA";
	//$password = "masterkey";

	//$dbh = ibase_connect ( $host, $username, $password ) or die ("error in db connect");
	$stmt='';
	if ($_REQUEST["Action"]=="SiteMap") {
		$stmt='select "wwwId", "LinkIdOrder", "GroupName", "Title", "Link", "LinkId", "LinkIdOrder", "Image_link" from "tblwww" left join "tblwww_link" on ("tblwww"."wwwId"="tblwww_link"."wwwFk") order by "'.'"GroupName"';
	  }
	if (($_REQUEST["Action"]=="ShowImage") and (!empty($_REQUEST["id"]))){
		$stmt='select "ImageId", "Folder", "FileName", "SourceTypeFk", "PersonFk", "DateString", "Quality", "Description", "SourceTypeDesc", "TeamCache", "tblLoc_Locality"."LocalityName" from "tblImages" left join "tblLoc_Locality" on "tblImages"."LocalityFk"="tblLoc_Locality"."LocalityId" left join "tblImages_SourceTypeQualifier" on "tblImages"."SourceTypeFk"="tblImages_SourceTypeQualifier"."SourceTypeId" left join "tblContactsTeam" on "tblImages"."PersonFk"="tblContactsTeam"."TeamId" where "ImageId" ='.$_REQUEST["id"];
		$query = ibase_prepare($stmt);
		$consulta = ibase_query ($dbh, $stmt);

		$fila = ibase_fetch_object ($consulta);
		$entro = 1;
		$stprint = '';
		$stprint .= '<table width="90%" border="0" cellspacing="0" cellpadding="4" align="center">';
		$stprint .= '        <tr> ';
		$stprint .= '          <td rowspan="9" valign="top">';
		$stprint .= '            <A HREF="'.$imageLink . '/' .$fila->Folder. '/' .$fila->FileName.'"><img src="'.$imageLink . '/' .$fila->Folder. '/' .$fila->FileName.'" width="400" height="400"></A>';
		$stprint .= '		   </td>';
		$stprint .= '        </tr>';
		$stprint .= '          <td width="100" align="right" valign="top"><!-- TemplateBeginEditable name="Label1" -->';
		$stprint .= '            <b>Carpeta:</b></td>';
		$stprint .= '          <td width="100%" valign="top"><!-- TemplateBeginEditable name="Value1" -->';
		$stprint .= '            '.$fila->Folder.'<!-- TemplateEndEditable --></td>';
		$stprint .= '        <tr> ';
		$stprint .= '          <td align="right" valign="top"><!-- TemplateBeginEditable name="Label2" -->';
		$stprint .= '            <b>Archivo:</b></td>';
		$stprint .= '          <td valign="top">';
		$stprint .= '            '.$fila->FileName.'<!-- TemplateEndEditable --></td>';
		$stprint .= '        </tr>';
		$stprint .= '        <tr> ';
		$stprint .= '          <td align="right" valign="top"><!-- TemplateBeginEditable name="Label3" -->';
		$stprint .= '            <b>Tipo:</b><!-- TemplateEndEditable --></td>';
		$stprint .= '          <td valign="top"><!-- TemplateBeginEditable name="Value3" --> '.$fila->SourceTypeDesc.'<!-- TemplateEndEditable --></td>';
		$stprint .= '        </tr>';
		$stprint .= '        <tr> ';
		$stprint .= '          <td align="right" valign="top"><!-- TemplateBeginEditable name="Label4" -->';
		$stprint .= '            <b>Persona:</b><!-- TemplateEndEditable --></td>';
		$stprint .= '          <td valign="top"><!-- TemplateBeginEditable name="Value4" -->';
		$stprint .= '            '.$fila->TeamCache.'<!-- TemplateEndEditable --></td>';
		$stprint .= '        </tr>';
		$stprint .= '        <tr> ';
		$stprint .= '          <td align="right" valign="top"><!-- TemplateBeginEditable name="Label4" -->';
		$stprint .= '            <b>Fecha:</b><!-- TemplateEndEditable --></td>';
		$stprint .= '          <td valign="top"><!-- TemplateBeginEditable name="Value4" -->';
		$stprint .= '            '.$fila->DateString.'<!-- TemplateEndEditable --></td>';
		$stprint .= '        </tr>';
		$stprint .= '        <tr> ';
		$stprint .= '          <td align="right" valign="top"><!-- TemplateBeginEditable name="Label4" -->';
		$stprint .= '            <b>Localidad:</b><!-- TemplateEndEditable --></td>';
		$stprint .= '          <td valign="top"><!-- TemplateBeginEditable name="Value4" -->';
		$stprint .= '            '.$fila->LocalityName.'<!-- TemplateEndEditable --></td>';
		$stprint .= '        </tr>';
		if (!empty($fila->Description)) {
			$blob_data = ibase_blob_info($fila->Description);
			$blob_hndl = ibase_blob_open($fila->Description);
			$stprint .= '        <tr> ';
			$stprint .= '          <td align="right" valign="top"><!-- TemplateBeginEditable name="Label4" -->';
			$stprint .= '            <b>Descripción:</b><!-- TemplateEndEditable --></td>';
			$stprint .= '          <td valign="top"><!-- TemplateBeginEditable name="Value4" -->';
			$stprint .= '            '.ibase_blob_get($blob_hndl, $blob_data[0]).'<!-- TemplateEndEditable --></td>';
			$stprint .= '        </tr>';
			ibase_blob_close($blob_hndl);  }
		$stprint .= '      </table>';
		print $stprint;
	  }

	$vAction = substr($_REQUEST["Action"],0,4);
	$vPartAction = substr($_REQUEST["Action"],4,99);
	$node_array = $root->get_elements_by_tagname("cnf".$vPartAction);
	if (($vAction=="Show") and (!empty($_REQUEST["id"])) and (!empty($node_array))){

		$vCount = $node_array[0]->get_attribute('count');
		$stprint = '';
		$vImage = array();
		// Recorremos todas las consultas definidas por Order y actualizamos los campos declarados para este
		for ($vOrder = 1; $vOrder<=$vCount; $vOrder++) {

			$node_array = $root->get_elements_by_tagname("SQL".$vPartAction);
			$stmt = '';
			$vResume = false;
			$vImageShow = false;
			$vImage = array();
			$vFoundResume = false;
			$vFields = array();
			// Buscamos el lugar donde estA la consulta SQL y actualizamos vResume
			if ($vCount>1)  {
				for ($vt = 0; $vt<$vCount; $vt++) {
					if ($vOrder==$node_array[$vt]->get_attribute('Order'))  {
						$stmt=$node_array[$vt]->get_content().$_REQUEST["id"];
						$vResume = $node_array[$vt]->get_attribute('Resume')=='true';
						$vImageShow = $node_array[$vt]->get_attribute('Image')=='true';
						}}
				}
				else
				{
				$stmt=$node_array[0]->get_content().$_REQUEST["id"];
				$vResume = $node_array[0]->get_attribute('Resume')=='true';
				$vImageShow = $node_array[0]->get_attribute('Image')=='true';}

			if ($vImageShow) {
				$vResume = true; }
			$query = ibase_prepare($stmt);
			$consulta = ibase_query ($dbh, $stmt);
			//Recorremos todos los registros obtenidos...
			while ($fila = ibase_fetch_object ($consulta)) {
				if (!$vResume) {
					$stprint .= '<table width="98%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#333333" bgcolor="#F2F2F2" style="border-collapse: collapse">';
					$stprint .= '	<tr> ';
					$stprint .= '	  <td>';
					$stprint .= '			<table width="100%" border="1" cellpadding="4" cellspacing="0" bordercolor="#FFFFFF" class="normal" style="border-collapse: collapse">';
					}
				$entro = 1;
				$node_Show = $root->get_elements_by_tagname("Show".$vPartAction);
				for ($i = 0; $i<count($node_Show); $i++) {
					$node = $node_Show[$i];
					$vField = $node->get_attribute('field');
					if (($vCount==1) or ($node->get_attribute('Order')==$vOrder)) {
						if (!empty($fila->$vField) or ($ShowBlank)) {
							if ($node->has_attribute('memo'))  {
								if (!is_null($fila->$vField)) {
									$blob_data = ibase_blob_info($fila->$vField);
									$blob_hndl = ibase_blob_open($fila->$vField);
									$vMemo = ibase_blob_get($blob_hndl, $blob_data[0]);
									ibase_blob_close($blob_hndl);
									if ($vResume) {
										$vFoundResume = true;
										if ($vImageShow)  {
											$vImage[$fila->Id] =$imageLink . '/' .$fila->Path;
											}
											else
											{
											if (!array_key_exists($node->get_content(), $vFields))  {
													$vFields[$node->get_content()]=$vMemo;}
												else  {
													$vFields[$node->get_content()]=$vFields[$node->get_content()].', '.$vMemo;  }
											}
										}
										else
										{
											$stprint .= '			  <tr> ';
											$stprint .= '				<td width="30%" bgcolor="#E1E1E1"><div align="right">';
											$stprint .= '					<strong>'.urldecode($node->get_content()).'</strong></div></td>';
											$stprint .= '				<td width="70%">';
											$vRef = $node->get_attribute('vref');
											if ($vRef==$vField) {
												$vRef = $vMemo; }
												else  {
												$vRef = $fila->$vField;
												}
											if ($node->has_attribute('href'))  {
													$stprint .= ' <a href="'.urldecode($node->get_attribute('href').$vRef).'">'.$vMemo.'</a>';	  }
												else	{
												  $stprint .= '            '.$vMemo;	}
											$stprint .= '				</td>';
											$stprint .= '			  </tr>';
										}
								  }
								}
								else
								{
									if (($vResume) and (!empty($fila->$vField)))  {
										$vFoundResume = true;
										if ($vImageShow)  {
											$vImage[$fila->Id] =$imageLink . $fila->Path;
											}
											else
											{
											if (!array_key_exists($node->get_content(), $vFields))  {
													$vFields[$node->get_content()]=$fila->$vField;}
												else  {
													$vFields[$node->get_content()]=$vFields[$node->get_content()].', '.$fila->$vField;  }
											}
										}
										else
										{
											$stprint .= '			  <tr> ';
											$stprint .= '				<td width="30%" bgcolor="#E1E1E1"><div align="right">';
											$stprint .= '					<strong>'.urldecode($node->get_content()).'</strong></div></td>';
											$stprint .= '				<td width="70%">';
											$vRef = $node->get_attribute('vref');
											if ($node->has_attribute('href'))  {
													$stprint .= ' <a href="'.urldecode($node->get_attribute('href').$fila->$vRef).'">'.$fila->$vField.'</a>';	  }
												else	{
												  $stprint .= '            '.$fila->$vField;	}
											$stprint .= '				</td>';
											$stprint .= '			  </tr>';
										}

								}
							}
						}
					}  //for
				if (!$vResume) {
					$stprint .= '			</table>';
					$stprint .= '	   </td>';
					$stprint .= '	</tr>';
					$stprint .= '  </table>		';
					}
				}  //while
			if ((($vResume) and ($vFoundResume)) and (!$vImageShow)) {
				$stprint .= '<table width="98%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#333333" bgcolor="#F2F2F2" style="border-collapse: collapse">';
				$stprint .= '	<tr> ';
				$stprint .= '	  <td>';
				$stprint .= '			<table width="100%" border="1" cellpadding="4" cellspacing="0" bordercolor="#FFFFFF" class="normal" style="border-collapse: collapse">';

				foreach($vFields as $k => $v) {
					$stprint .= '			  <tr> ';
					$stprint .= '				<td width="30%" bgcolor="#E1E1E1"><div align="right">';
					$stprint .= '					<strong>'.urldecode($k).'</strong></div></td>';
					$stprint .= '				<td width="70%">';
					$stprint .= '            	 '.$v;
					$stprint .= '				</td>';
					$stprint .= '			  </tr>';
					}
				$stprint .= '			</table>';
				$stprint .= '	   </td>';
				$stprint .= '	</tr>';
				$stprint .= '  </table>		';
				}  //if de vResume
			if ((($vResume) and ($vFoundResume)) and ($vImageShow)) {
				$stprint .= '<TABLE>';
				$stprint .= '        <TR> ';
				$pHor = 0;
				while (list ($clave, $val) = each ($vImage)) {
					$pHor++;
					if ($pHor==$imageCols+1) {
					  $stprint .= '        </TR>';
					  $stprint .= '        <TR> ';
					  $pHor =1; }

					$stprint .= '          <TD ALIGN=CENTER VALIGN=BOTTOM><FONT face="Verdana, Arial, Helvetica, Sans-Serif" size="-2">';
					$stprint .= '		    <a href="'.htmlspecialchars($val).'">';
					$stprint .= '           <IMG SRC="'.$val.'" ALT="'.$val.'" width="'.$imageWidth.'" height="'.$imageHeight.'"><BR></a>';
					$stprint .= '           <a href="portal.php' . htmlspecialchars('?Action=ShowImage&id='.$clave).'">Ver Detalles</a></FONT></TD>';
					}
					if ($pHor==1) {
					  $stprint .= '<td></td><td></td>';}
					else if ($pHor==2) {
					  $stprint .= '<td></td>';}
					$stprint .= '        </TR>';
					$stprint .= '      </TABLE>';
				}

			}
		print $stprint;
	  }

	if (($_REQUEST["Action"]=="Search") or
		($_REQUEST["Action"]=="Show")) {
			if ($_REQUEST["Action"]=="Search") {
				 if	(($_REQUEST["Web"]=="1") and (strlen($_REQUEST["Texto"])>0)) {
				  $stmt='select first '.($ShowCount+1).' skip '.$ShowIni.'"wwwId", "GroupName", "ParentYN", "Title", "Link", "LinkId", "LinkIdOrder", "Image_link", "LinkIdOrder" from "tblwww" left join "tblwww_link" on ("tblwww"."wwwId"="tblwww_link"."wwwFk") where ((upper("GroupName") like '."'%".strtoupper($_REQUEST["Texto"])."%') or (".'upper("Title") like '."'%".strtoupper($_REQUEST["Texto"])."%')) order by ".'"GroupName"';
				  }}
			if ($_REQUEST["Action"]=="Search") {
				 if	(($_REQUEST["Loc"]=="1") and (strlen($_REQUEST["Texto"])>0)) {
				  $stmt='select first '.($ShowCount+1).' skip '.$ShowIni.' "LocalityName" as "Locality", "LatDeg" || '."','".' || "LatMin" || "LatDir" as "Lat", "LonDeg" || '."','".' || "LonMin" || "LonDir" as "Lon", "Paper" as "GridPaper","Cx" || '."'x '".' || "Cy" || '."'y'".' as "Grid", "MunicipioDesc" as "Municipality" from "tblLoc_Locality" left join "tblLoc_MunicipioQualifier" on ("Province_CommunityFk"="MunicipioId") where (upper("LocalityName") like '."'%".strtoupper($_REQUEST["Texto"])."%'".') order by "LocalityName"';
				  }}
			if ($_REQUEST["Action"]=="Search") {
				 if	(($_REQUEST["_phy"]=="1") and (strlen($_REQUEST["Texto"])>0)) {
				  $stmt='select first '.($ShowCount+1).' skip '.$ShowIni.' "PlantId", "FullName", "ImageId"*0 as "IsImage", count(*) as "Cant" from ("tblPlant" LEFT JOIN ("tblPlant_Images" LEFT JOIN "tblImages" ON "tblImages"."ImageId" = "tblPlant_Images"."ImageFk") ON "tblPlant"."PlantId" = "tblPlant_Images"."PlantFk") where (upper("FullName") like '."'%".strtoupper($_REQUEST["Texto"])."%'".')  or ("PlantId" in (select "PlantFk" from "tblPlant_ComunName" where upper("Vernam") like '."'%".strtoupper($_REQUEST["Texto"])."%'".')) group by 1,2,3 order by "FullName"';
				  }}
			if ($_REQUEST["Action"]=="Search") {
				 if	(($_REQUEST["Img"]=="1") and (strlen($_REQUEST["Texto"])>0)) {
				  $stmt='select first '.($ShowCount+1).' skip '.$ShowIni.' "ImageId", "Folder", "FileName", "SourceTypeFk", "PersonFk", "DateString", "Quality", "Description", "SourceTypeDesc", "TeamCache" from ("tblImages" left join "tblImages_SourceTypeQualifier" on ("tblImages"."SourceTypeFk"="tblImages_SourceTypeQualifier"."SourceTypeId")) left join "tblContactsTeam"  on "tblImages"."PersonFk"="tblContactsTeam"."TeamId" where ("Description" like '."'%".$_REQUEST["Texto"]."%'".') order by "Description"';
				  }}
			if ($_REQUEST["Action"]=="Search") {
				 if	(($_REQUEST["Cont"]=="1") and (strlen($_REQUEST["Texto"])>0)) {
				  $stmt='select first '.($ShowCount+1).' skip '.$ShowIni.' "ContactId", "FullName","PhotoLink", "Address", "Title", "WorkPhone","EmailName", "WWWAddress", "Notes", "ContactDesc" from "tblContacts" left join "tblContacts_TypeQualifier" on "tblContacts"."ContactTypeFk" ="tblContacts_TypeQualifier"."ContactTypeId" where ((upper("FullName") like '."'%".strtoupper($_REQUEST["Texto"])."%'".') or ("Notes" like '."'%".$_REQUEST["Texto"]."%'".') or (upper("Abbrev") like '."'%".strtoupper($_REQUEST["Texto"])."%'".')) order by "FullName"';
				  }}
			if ($_REQUEST["Action"]=="Search") {
				 if	(($_REQUEST["Ik"]=="1") and (strlen($_REQUEST["Texto"])>0)) {
				  $stmt='select first '.($ShowCount+1).' skip '.$ShowIni.' "NameId", "FullNameCache" from "tblMoretax_Name" where "FullNameCache" like '."'".$_REQUEST["Texto"]."%'".' order by "FullNameCache"';
				  }}
			if ($_REQUEST["Action"]=="Search") {
				 if	(($_REQUEST["Aut"]=="1") and (strlen($_REQUEST["Texto"])>0)) {
				  $stmt='select first '.($ShowCount+1).' skip '.$ShowIni.' "AuthorId", "Abbrev", "FullName" from "tblMoretax_Author" where ("FullName" like '."'%".$_REQUEST["Texto"]."%')".' or ("Abbrev" like '."'%".$_REQUEST["Texto"]."%')".' order by "FullName"';
				  }}
			if ($_REQUEST["Action"]=="Show")  {
				  $vIns = '';
				  if ((empty($_REQUEST['id']))) {
					$vIns = ' where ("GroupId"=9999) '; }
				   else {
					$vIns = ' where ("GroupId"='.$_REQUEST["id"].') ';}
				   if (!(empty($_REQUEST['idOrder']))) {
					 $vIns .= 'and ("LinkIdOrder"='.$_REQUEST['idOrder'].')';
					 }
				  $stmt='select first '.($ShowCount+1).' skip '.$ShowIni.' "wwwId", "GroupName", "ParentYN", "Title", "Link", "LinkId", "LinkIdOrder", "Image_link" from "tblwww" left join "tblwww_link" on ("tblwww"."wwwId"="tblwww_link"."wwwFk") '.$vIns.' order by "GroupName"';
				  }

			if ((strlen($stmt)>0)) {
				$query = ibase_prepare($stmt);
				$consulta = ibase_query ($dbh, $stmt);

				$vName = '';
				$entro = 0;
				$newTitle = 0;
				$stprint ='';

				if	(! empty($_REQUEST["Loc"])) {
					$posCount = 0;
					$canShow = 0;
					$stprint .='<table width="100%" border="0" cellspacing="0" cellpadding="2" >';
					$stprint .='  <tr> ';
					$stprint .='	<td> <table width="100%" border="1" cellspacing="0" cellpadding="4">';
					$stprint .='		<tr class="ListHeaderColor"> ';
					$stprint .='		  <th width="20%"> Localidad</th>';
					$stprint .='		  <th width="10%" style="text-align: center"> Latitud</th>';
					$stprint .='		  <th width="10%" style="text-align: center"> Longitud</th>';
					$stprint .='		  <th width="20%" style="text-align: center"> Cuadr&iacute;cula</th>';
					$stprint .='		  <th width="40%" style="text-align: center"> Municipio</th>';
					$stprint .='		</tr>';
					while ($fila = ibase_fetch_object ($consulta)) {
						$canShow++;
						if ($posCount<$ShowCount) {
							$posCount++;
							$stprint .='		<tr class="ListColorOdd"> ';
							$stprint .='		  <td>'.$fila->Locality.'</td>';
							$stprint .='		  <td style="text-align: center">'.$fila->Lat.'</td>';
							$stprint .='		  <td style="text-align: center">'.$fila->Lon.'</td>';
							$stprint .='		  <td style="text-align: center">'.$fila->GridPaper.', '.$fila->Grid.'</td>';
							$stprint .='		  <td style="text-align: center">'.$fila->Municipality.'</td>';
							$stprint .='		</tr>';
							}
						}
					$stprint .='	  </table></td>';
					$stprint .='  </tr>';
					$stprint .='</table>';

					}

				elseif	(! empty($_REQUEST["_phy"])) {
					$posCount = 0;
					$canShow = 0;
					while ($fila = ibase_fetch_object ($consulta)) {
					    $entro = 1;
						$vWithImage = '';
						if (!is_null($fila->IsImage)) {
						  $vWithImage = ' ('.$fila->Cant.')';  }
						 $canShow++;
						if ($posCount<$ShowCount) {
							$posCount++;
							$stprint .= '<a href="portal.php' . htmlspecialchars('?Action=ShowPhyllaca&id='.$fila->PlantId).'">'.$fila->FullName.$vWithImage."</a><br>";
							}
						}
					if ($entro == 1) {
					  $stprint = '<b>Taxa encontrados<br></b>'.$stprint;}
					}

				elseif	(! empty($_REQUEST["Ik"])) {
					$posCount = 0;
					$canShow = 0;
					while ($fila = ibase_fetch_object ($consulta)) {
					    $entro = 1;
						$canShow++;
						if ($posCount<$ShowCount) {
							$posCount++;
							$stprint .= '<a href="portal.php' . htmlspecialchars('?Action=ShowIk&id='.$fila->NameId).'">'.$fila->FullNameCache."</a><br>";
							}
						}
					if ($entro == 1) {
					  $stprint = '<b>Taxa encontrados<br></b>'.$stprint;}
					}

				elseif	(! empty($_REQUEST["Aut"])) {
					$posCount = 0;
					$canShow = 0;
					while ($fila = ibase_fetch_object ($consulta)) {
					    $entro = 1;
						$canShow++;
						if ($posCount<$ShowCount) {
							$vIns = '';
							if (!is_null($fila->FullName))  {
							   $vIns = $fila->FullName;}
							$posCount++;
							$stprint .= '<a href="portal.php' . htmlspecialchars('?Action=ShowAut&id='.$fila->AuthorId).'">'.$fila->Abbrev."</a> ".$vIns.'<br>';
							}
						}
					if ($entro == 1) {
					  $stprint = '<b>Taxa encontrados<br></b>'.$stprint;}
					}

				elseif	(! empty($_REQUEST["Cont"])) {
					$posCount = 0;
					$canShow = 0;
					while ($fila = ibase_fetch_object ($consulta)) {
					    $entro = 1;
						$canShow++;
						if ($posCount<$ShowCount) {
							$posCount++;
							$stprint .= '<a href="portal.php' . htmlspecialchars('?Action=ShowContact&id='.$fila->ContactId).'">'.$fila->FullName."</a>";
							if (!empty($fila->EmailName)) {
 								$stprint .= '  (<a href="mailto:'.$fila->EmailName.'">'.$fila->EmailName.'</a>)'; }
							$stprint .= '<br>';
							}
						}
					if ($entro == 1) {
					  $stprint = '<b>Contactos encontrados<br></b>'.$stprint;}
					}

				elseif	(! empty($_REQUEST["Img"])) {
					$posCount = 0;
					$canShow = 0;
					$pHor = 0;
					if (!empty($_REQUEST["MultiImage"])) {
						$stprint .= '<TABLE>';
						$stprint .= '        <TR> ';  }
					while ($fila = ibase_fetch_object ($consulta)) {
					    $entro = 1;
						$canShow++;
						if ($posCount<$ShowCount) {
							$posCount++;
							if (empty($_REQUEST["MultiImage"])) {
								$blob_data = ibase_blob_info($fila->Description);
								$blob_hndl = ibase_blob_open($fila->Description);

								$stprint .= '<a href="portal.php' . htmlspecialchars('?Action=ShowImage&id='.$fila->ImageId).'">'.ibase_blob_get($blob_hndl, $blob_data[0])."</a><br>";

								ibase_blob_close($blob_hndl);}
							else {
								$pHor++;
								if ($pHor==4) {
								  $stprint .= '        </TR>';
								  $stprint .= '        <TR> ';
								  $pHor =1; }
								$blob_data = ibase_blob_info($fila->Description);
								$blob_hndl = ibase_blob_open($fila->Description);
								$stprint .= '          <TD ALIGN=CENTER VALIGN=BOTTOM><FONT face="Verdana, Arial, Helvetica, Sans-Serif" size="-2">';
								$stprint .= '		    <a href="'.htmlspecialchars($imageLink . '/' .$fila->Folder. '/' .$fila->FileName).'">';
								$stprint .= '           <IMG SRC="'.$imageLink . '/' .$fila->Folder. '/' .$fila->FileName.'" ALT="'.$imageLink . '/' .$fila->Folder. '/' .$fila->FileName.'" width="128" height="96"><BR></a>';
								$stprint .= '           <a href="portal.php' . htmlspecialchars('?Action=ShowImage&id='.$fila->ImageId).'">'.ibase_blob_get($blob_hndl, $blob_data[0]).'</a></FONT></TD>';
								ibase_blob_close($blob_hndl);
							    }
						  }
						}
					if (!empty($_REQUEST["MultiImage"])) {
					    if ($pHor==1) {
						  $stprint .= '<td></td><td></td>';}
						else if ($pHor==2) {
						  $stprint .= '<td></td>';}
						$stprint .= '        </TR>';
						$stprint .= '      </TABLE>';  }
					if (($entro == 1) and (empty($_REQUEST["MultiImage"]))){
					  $stprint = '<b>Imagenes encontradas (<a href="portal.php' . htmlspecialchars('?Action=Search&Search=Buscar&Img=1&MultiImage=1&Texto='.$_REQUEST["Texto"]).'">Imagenes</a>)<br></b>'.$stprint;}
					if (($entro == 1) and (!empty($_REQUEST["MultiImage"]))){
					  $stprint = '<b>Imagenes encontradas (<a href="portal.php' . htmlspecialchars('?Action=Search&Search=Buscar&Img=1&Texto='.$_REQUEST["Texto"]).'">Listado</a>)<br></b>'.$stprint;}

					}

				elseif (($_REQUEST["Action"]=="Show") or ($_REQUEST["Web"]=="1")) {
					$posCount = 0;
					$canShow = 0;
					while ($fila = ibase_fetch_object ($consulta)) {
					   $canShow++;
					   if ($posCount<$ShowCount) {
							$posCount++;
							if ($vName!=$fila->GroupName) {
							 if ($entro>0) {
							   $newTitle=1;
								}
							  else  {
							   $stprint .= '<ol start="1">';
							   $entro = 1;
							   }
							 $vName = $fila->GroupName;
							 if ($newTitle==1)
							   $stprint .=("</ul>");
							 if (strlen($fila->LinkId)>0)  {
								 $stprint .= "<li><strong>".$vName.'<a href="portal.php' . htmlspecialchars('?Action=Comment&id='.$fila->LinkId).'">*</a>'."</strong></li>\n";}
								else {
								 $stprint .= "<li><strong>".$vName."</strong></li>\n";}

							 $newTitle = 0;
							 };
						   if (strlen($fila->Title)>0) {
							   if ($newTitle==0)
								 $stprint .= "<ul>";
							   $newTitle = 1;
							   $stprint .= "<li>";

							   if (is_numeric($fila->Link)) {
								   $vIns = '';
								   if (is_numeric($fila->LinkIdOrder)) {
									 $vIns = '&idOrder='.$fila->LinkIdOrder;
									 }
									$stprint .= '<a href="portal.php' . htmlspecialchars('?Action=Show&id='.$fila->Link).$vIns.'">'.$fila->Title."</a>";
								 }
								else
								 {
								  $stprint .= '<a href="'. htmlspecialchars($fila->Link).'">'.$fila->Title."</a>";
								 }
							   if (strlen($fila->Image_link)>0) {
								  $stprint .= '<img src="'.$fila->Image_link.'">'; }
							   $stprint .="<BR>\n";
							   $stprint .= "</li>";
							}
						}
						}
					if ($entro==0) {
						  $stprint .= "No disponemos de información hasta el momento...";}
					  else {
						  $stprint .="</ul>";
						  $stprint .= "</ol>"; }
					$stprint1 = '';
				}

				if (($ShowIni>0) or ($canShow>$posCount)) {
					$stprint1 .= '<table width="100%" border="0" cellspacing="2" cellpadding="4">';
					$stprint1 .= '  <tr> ';
					$stprint1 .= '	<td align="center" bgcolor="#CCCCCC"> ';
					$vIni = '';
					$pos = strpos($_SERVER['QUERY_STRING'], "&inidb=");
					if ($pos>0) {
					  $vIni= substr($_SERVER['QUERY_STRING'],0,$pos); }
					 else
					  {$vIni=$_SERVER['QUERY_STRING'];}
					if ($ShowIni>0) {
						if (($ShowIni-$ShowCount-1)<0) {
						   $tmp=0; }
						  else  {
						   $tmp = ($ShowIni-$ShowCount); }
						$stprint1 .= '<a href="portal.php' . htmlspecialchars('?'.$vIni.'&inidb='.$tmp).'">&lt;&lt; Página anterior</a>'; }
					$stprint1 .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					if ($canShow>$posCount) {
						$stprint1 .= '<a href="portal.php' . htmlspecialchars('?'.$vIni.'&inidb='.($ShowIni+$ShowCount)).'">Próxima página &gt;&gt;</a>'; }
					$stprint1 .= '</td></tr>';
					$stprint1 .= '</table>';
					}
				print $stprint1;
				print $stprint;
				print $stprint1;
		}
	}  /*del search y show*/
 } /*de action*/
/* close db */
ibase_close($dbh);
?>
      <hr>

<table width="98%" border="0" cellspacing="2" cellpadding="4">
  <tr>
    <td align="left">
	<form name="Search_" method="get" action="portal.php">

<h3><strong>B&uacute;squeda</strong></h3>
        <p>
	    <input name="Action" type="hidden" value="Search">
<?php
        $ssB = '';
		$ssSi = 0;

		if ($_REQUEST["Ik"]==1)	{
			$ssB .= '<input name="Ik" type="checkbox" value="1" checked>Taxonom&iacute;a*<br>';
			$ssSi = 1;}
		  else   {
			$ssB .= '<input name="Ik" type="checkbox" value="1">Taxonom&iacute;a*<br>';		}
		if ($_REQUEST["Aut"]==1)	{
			$ssB .= '<input name="Aut" type="checkbox" value="1" checked>Autores* <br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Aut" type="checkbox" value="1">Autores* <br>';}
		if ($_REQUEST["Cont"]==1)	{
			$ssB .= '<input name="Cont" type="checkbox" value="1" checked>Contactos*<br>';
			$ssSi = 1;  }
		  else  {
			$ssB .= '<input name="Cont" type="checkbox" value="1">Contactos*<br>';		}
		if ($_REQUEST["Inst"]==1)	{
			$ssB .= '<input name="Inst" type="checkbox" value="1" checked>Instituciones*<br>';
			$ssSi = 1;  }
		  else  {
			$ssB .= '<input name="Inst" type="checkbox" value="1">Instituciones*<br>';		}
		if ($_REQUEST["Img"]==1)	{
			$ssB .= '<input name="Img" type="checkbox" value="1" checked>Im&aacute;genes*<br>';
			$ssSi = 1;}
		  else	{
			$ssB .= '<input name="Img" type="checkbox" value="1">Im&aacute;genes*<br>'; }
		if ($_REQUEST["Eve"]==1)	{
			$ssB .= '<input name="Eve" type="checkbox" value="1" checked>Eventos<br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Eve" type="checkbox" value="1">Eventos<br>';		}
		if ($_REQUEST["Bib"]==1)	{
			$ssB .= '<input name="Bib" type="checkbox" value="1" checked>Bibliografía<br>';
			$ssSi = 1;}
		  else {
			$ssB .= '<input name="Bib" type="checkbox" value="1">Bibliografía<br>';		}
		//if ($_REQUEST["Her"]==1)	{
		//	$ssB .= '<input name="Her" type="checkbox" value="1" checked>Index Herbariorum<br>';}
		//  else  {
		//	$ssB .= '<input name="Her" type="checkbox" value="1">Index Herbariorum<br>';		}
		if ($_REQUEST["col"]==1)	{
			$ssB .= '<input name="col" type="checkbox" value="1" checked>Colecciones vivas<br>	';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="col" type="checkbox" value="1">Colecciones vivas<br>	';}
		if ($_REQUEST["_phy"]==1)	{
			$ssB .= '<input name="_phy" type="checkbox" value="1" checked>Tax&oacute;n*<br>	';
			$ssSi = 1;			}
		  else  {
			$ssB .= '<input name="_phy" type="checkbox" value="1">Tax&oacute;n*<br>	';}

		if ($_REQUEST["cons"]==1)	{
			$ssB .= '<input name="cons" type="checkbox" value="1" checked>Colecciones conservadas (herbarios)<br>	';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="cons" type="checkbox" value="1">Colecciones conservadas (herbarios)<br>	';		}
		if ($_REQUEST["Loc"]==1)	{
			$ssB .= '<input name="Loc" type="checkbox" value="1" checked>Localidades*<br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Loc" type="checkbox" value="1">Localidades*<br>';		}
		if ($_REQUEST["Soft"]==1)	{
			$ssB .= '<input name="Soft" type="checkbox" value="1" checked>Softwares<br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Soft" type="checkbox" value="1">Softwares<br>';		}
		if ($_REQUEST["Dict"]==1)	{
			$ssB .= '<input name="Dict" type="checkbox" value="1" checked>Diccionarios (Thesaurus)<br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Dict" type="checkbox" value="1">Diccionarios (Thesaurus)<br>';}
		if ($_REQUEST["Ame"]==1)	{
			$ssB .= '<input name="Ame" type="checkbox" value="1" checked>Amenazadas<br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Ame" type="checkbox" value="1">Amenazadas<br>';}
		//if ($_REQUEST["BPH"]==1)	{
		//	$ssB .= '<input name="BPH" type="checkbox" value="1" checked>Botanicum Periodicum Huntianum (BPH)<br>';
		//	$ssSi = 1;}
		//  else  {
		//	$ssB .= '<input name="BPH" type="checkbox" value="1">Botanicum Periodicum Huntianum (BPH)<br>';}

		//if ($_REQUEST["TL2"]==1)	{
		//	$ssB .= '<input name="TL2" type="checkbox" value="1" checked>Taxonomic Literature 2 (TL2)<br>';
		//	$ssSi = 1;}
		//  else  {
		//	$ssB .= '<input name="TL2" type="checkbox" value="1">Taxonomic Literature 2 (TL2)<br>';}
		if ($_REQUEST["Bibl"]==1)	{
			$ssB .= '<input name="Bibl" type="checkbox" value="1" checked>Bibliograf&iacute;a<br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Bibl" type="checkbox" value="1">Bibliograf&iacute;a<br>';}
		if ($_REQUEST["Inv"]==1)	{
			$ssB .= '<input name="Inv" type="checkbox" value="1" checked>Inventarios biológicos<br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Inv" type="checkbox" value="1">Inventarios biológicos<br>';}

		if ($_REQUEST["Med"]==1)	{
			$ssB .= '<input name="Med" type="checkbox" value="1" checked>Medicinales<br>';
			$ssSi = 1;}
		  else  {
			$ssB .= '<input name="Med" type="checkbox" value="1">Medicinales<br>';}

   		if (!empty($_REQUEST["Texto"]))	{
 			$ss = '<input name="Texto" type="text" value="'.$_REQUEST["Texto"].'" size="30" maxlength="100">';}
		  else  {
 			$ss = '<input name="Texto" type="text" value="" size="30" maxlength="100">';}
        $ss .= '<input name="Search" type="submit" id="Search" value="Buscar"><br>';

		if (($_REQUEST["Web"]==1) or ($ssSi==0))	{
	        $ssB = '<input name="Web" type="checkbox" value="1" checked>Este sitio web*<br>'  . $ssB;
			$ssSi = 1;		}
		  else  {
 	        $ssB = '<input name="Web" type="checkbox" value="1">Este sitio web*<br>'  . $ssB;	}
		$ssB = $ss . $ssB;
		print $ssB;
?>
        </p>
      </form></td>
  </tr>
</table>

      </TD>

	       <td width="0%"></TD>
			</TR>
		</TABLE>

<p><br>
<p>
<!-- Rücksprung zum Anfang der CD -->
<hr noshade width=100%>

<?php
  if ($debug==1) {
     print $stmt;  }
  //phpinfo();

//$a = split ('&', $querystring);
//$i = 0;
//while ($i < count ($a)) {
//  $b = split ('=', $a [$i]);
//  echo 'El valor para el parámetro ', htmlspecialchars (urldecode ($b [0])),
//       ' es ', htmlspecialchars (urldecode ($b [1])), "<BR>";
//  $i++;

?>


<?php
	if (file_exists ("configuration/collman/foot_ald.php"))
			{
			require("configuration/collman/foot_ald.php");
			}
?>
</BODY>
</HTML>

