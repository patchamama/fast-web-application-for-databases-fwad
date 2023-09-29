<?php
// File           GiveMeLst.php / Phyllacanthus
// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//

//'" onClick="'."javascript:showWaitScreen('".$message_strings['Searching']."')"


require('./inc/script_start.inc.php');

//echo htmlentities(stripcslashes(($HTTP_PARAM_VARS['sql'])));
//exit;

$vType = (isset($HTTP_PARAM_VARS['type']))? $HTTP_PARAM_VARS['type']:'normal';
$vSqlId = (isset($HTTP_PARAM_VARS['sqlid']))? $HTTP_PARAM_VARS['sqlid']:'0';

$vReportHtmlResult = array();
$vCantRowsShow = 100;
$vColsPerRecord = 0; 
$vScript = "";
$vSql = "";


//Configuration of the report
// row -> position of the row where will be print the field
// col -> position of the col where will be print the field
// rowblank -> especified if will be print this row in case of this row is empty
// Group -> especified if will be group this field (true: the field will be print if hat a new value, this field must to be sorted....)

$vReportConfField = array();
$vReportConfField[0] = array("row"=>0, "col"=>0, "rowblank"=>false, "Group"=>true, "SpaceBefore"=>1, "html"=>'<h2>__field__</h2>');
$vReportConfField[1] = array("row"=>1, "col"=>0, "rowblank"=>false, "Group"=>true, "SpaceBefore"=>1, "html"=>'<h3>__field__</h3>');
$vReportConfField[2] = array("row"=>2, "col"=>2, "rowblank"=>false, "Group"=>true, "SpaceBefore"=>1, "html"=>'<b>__field__</b>');
$vReportConfField[3] = array("row"=>3, "col"=>0, "rowblank"=>false, "Group"=>false,"SpaceBefore"=>0, "html"=>'');
$vReportConfField[4] = array("row"=>3, "col"=>1, "rowblank"=>false, "Group"=>false,"SpaceBefore"=>0, "html"=>'');
$vReportConfField[5] = array("row"=>3, "col"=>2, "rowblank"=>false, "Group"=>false,"SpaceBefore"=>0, "html"=>'');

$HTTP_PARAM_VARS['sql'] = urldecode($HTTP_PARAM_VARS['sql']);
//echo htmlentities($HTTP_PARAM_VARS['sql']);

if  ( ( (isset($HTTP_PARAM_VARS['ExportAction'])) || ($vType=='excel_out') ) 
	&& (isset($HTTP_PARAM_VARS['sql'])) )
			{

			$trec = false;
			
			$vpos = 0;
			if ((isset($HTTP_PARAM_VARS['pos'])) )
				{
				$vpos = (int)$HTTP_PARAM_VARS['pos'];
				$vpos = $vpos + 0;
				}
			
			if  (isset($HTTP_PARAM_VARS['sql']))
				{
				$vSql = stripcslashes($HTTP_PARAM_VARS['sql']);
				}
						
			if  (isset($HTTP_PARAM_VARS['ExportAction']))
				{
				include_once "Spreadsheet/Excel/Writer.php";

				$xls =& new Spreadsheet_Excel_Writer();
				$xls->send("datos.xls");
				$sheet =& $xls->addWorksheet('Hoja XLS');
				//$format =& $xls->addFormat();
				//$format->setBold();
				//$format->setColor("blue");
				//$sheet->write(1,0,2);
				//$sheet->write(1,1,$value,$format);
				//$sheet->write(1,1,$value);
				
				$trec = $dbhandle->Execute($vSql);
				$warning .= "1. ".htmlentities($vSql);
				}
			else				
				{								
				$trec = $dbhandle->SelectLimit($vSql,$vCantRowsShow,$vpos);
				$warning .= "2. ".htmlentities($vSql);
				}
			
			
			
			if ($trec === FALSE)
				{
				$error .= $dbhandle->ErrorMsg();
				}
			else	
				{
			
				$ncols = $trec->FieldCount();
				$fname = array();
				$vvvcampo = array();
				$PosRowToPrint = -1;
				for ($i=0; $i < $ncols; $i++) 
					{
					$field = $trec->FetchField($i);
					$fname[] = $field->name;
					$vvvcampo[$i] = "zzzzz";
					if (($vType!='excel_out') && (isset($HTTP_PARAM_VARS['ExportAction'])))
						{
						$PosRowToPrint = 0;
						$sheet->write($PosRowToPrint,$i,htmlspecialchars(utf8_decode($field->name)));
						}
					}


				//Here we know if we go to print a field blank...and the maximun number of rows that will be print per record
				$vRowsBlank = array();
				$vRowsPerRecord = 0;  //number of Rows to print per record...
				$vColsPerRecord = 0;  //number of Cols to print per record...
				for ($i=0; $i < $ncols; $i++) 
					{
					if (isset($vReportConfField[$i]))
						{
						$vRowsPerRecord = ($vRowsPerRecord>$vReportConfField[$i]["row"]) ? $vRowsPerRecord : $vReportConfField[$i]["row"];
						$vColsPerRecord = ($vColsPerRecord>$vReportConfField[$i]["col"]) ? $vColsPerRecord : $vReportConfField[$i]["col"];
						$vRow = $vReportConfField[$i]["row"];
						$vRowsBlank[$vRow] = (isset($vRowsBlank[$vRow]))? ($vRowsBlank[$vRow] || $vReportConfField[$i]["rowblank"]) : $vReportConfField[$i]["rowblank"];
						$vRowsGroup[$vRow] = (isset($vRowsGroup[$vRow]))? ($vRowsGroup[$vRow] || $vReportConfField[$i]["Group"]) : $vReportConfField[$i]["Group"];
						$vSpaceBefore[$vRow] = (isset($vSpaceBefore[$vRow]))? ($vSpaceBefore[$vRow] || $vReportConfField[$i]["SpaceBefore"]) : $vReportConfField[$i]["SpaceBefore"];
						}
					}

				// Here we store initially the values...
				$vReportTableResult = array();


				while (!$trec->EOF)
					{
					$PosRowToPrint++;
					$vReportTableResult = array();

					for ($i=0; $i < $ncols; $i++) 
						{

						$vvfield = $fname[$i];
						$vvvcampo_print = true;
						if ($vType=='excel_out')
							{
							if (isset($vReportConfField[$i]))
								{
								if ($vReportConfField[$i]["Group"])
									{
									if ($vvvcampo[$i]!=$trec->fields[$vvfield])
										{
										$vvvcampo[$i]=$trec->fields[$vvfield];
										}
									else
										{
										$vvvcampo_print = false;
										}
									}
								else	{
									$vfila = $vReportConfField[$i]["row"];
									}
								$vfila = $vReportConfField[$i]["row"];
								$vcol = $vReportConfField[$i]["col"];
								}

							if ($vvvcampo_print)
								{
								$vvalue = $trec->fields[$vvfield];
								if  (isset($HTTP_PARAM_VARS['ExportAction']))
									{
									}
								else
									{
									if (strpos($vReportConfField[$i]["html"],'__field__')>-1)
										{
										$vvalue = ereg_replace("__field__",$vvalue, $vReportConfField[$i]["html"]);
										}
									}
								
												
								$vReportTableResult[$vfila][$vcol] = $vvalue;

								//$sheet->write($vfila+$PosRowToPrint,$vcol,$trec->fields[$vvfield]);
								}
							else	{
								$vReportTableResult[$vfila][$vcol] = "";
								}


							}
						else
							{
							$vfila = $PosRowToPrint;
							$vcol = $i;
							if  (isset($HTTP_PARAM_VARS['ExportAction']))
								{
								$sheet->write($vfila,$vcol,$trec->fields[$vvfield]);
								}

							}


						}
					$trec->MoveNext();


					if ($vType=='excel_out')
						{
						$vSumRows = $vRowsPerRecord;
						$pprow = $PosRowToPrint;

						for ($irow=0; $irow <= $vRowsPerRecord; $irow++)
							{

							if (!$vRowsBlank[$irow])
								{
								$vShowRow = false;
								for ($icol=0; ($icol <= $vColsPerRecord) && (!$vShowRow); $icol++) 
									{
									$vShowRow = ( (isset($vReportTableResult[$irow][$icol])) && (!empty($vReportTableResult[$irow][$icol])) );
									}
								if ($vShowRow)
									{
									if (($vSpaceBefore[$irow]) &&  ($vRowsGroup[$irow]))
										{
										$pprow += $vSpaceBefore[$irow];
										$vSumRows += $vSpaceBefore[$irow];
										}

									for ($icol=0; ($icol <= $vColsPerRecord); $icol++) 
										{
										if (isset($vReportTableResult[$irow][$icol]))
											{
											if  (isset($HTTP_PARAM_VARS['ExportAction']))
												{
												$sheet->write($irow+$pprow,$icol,$vReportTableResult[$irow][$icol]);
												}
											else	{
												$vReportHtmlResult[$irow+$pprow][$icol] = $vReportTableResult[$irow][$icol];
												}
											}
										
										}

									}
								else
									{
									$pprow--;
									$vSumRows--;
									}
								}
							else	{

								for ($icol=0; ($icol <= $vColsPerRecord); $icol++) 
									{
									if (isset($vReportTableResult[$irow][$icol]))
										{
										if  (isset($HTTP_PARAM_VARS['ExportAction']))
											{
											$sheet->write($irow+$pprow,$icol,$vReportTableResult[$irow][$icol]);
											}
										else	{
											$vReportHtmlResult[$irow+$pprow][$icol] = $vReportTableResult[$irow][$icol];
											}
										}
									}
								}
							}
						$PosRowToPrint += $vSumRows;

						}

					}
					
				}
			if  (isset($HTTP_PARAM_VARS['ExportAction']))
				{
				$xls->close();	 
				}
			
			}	

include_once('./lib/adodb/tohtml.inc.php');
include_once('./lib/adodb/pivottable.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
  <title>CollMan: Manejo de Colecciones Biológicas</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1">
	<meta name="Description" content="Gestión de información biológica, ecológica, etnobotánica, taxonómica, literatura, Sistemas de informacion geográfica (SIG).">
	<meta name="Keywords" content="CollMan: Manejo de Colecciones Biológicas, bioinformática, ecología, itf, hispid, colecciones biológicas, jardín botánico, herbario, fast web application, fwa, SIG, GIS">
	<meta name="author" content="Red de Jardínes Botánicos Cubanos">
	<meta name="owner" content="JBPR/JBN Licencia GPL.">
	<meta name="robots" content="index, follow">
	<link href="styles/default.css" rel="stylesheet" type="text/css">
	<script src="lib/js/jquery.js"></script>
<?php

if (file_exists ($xmlConfDirectory."head.php"))
		{
		require($xmlConfDirectory."head.php");
		}

set_time_limit(0);

//Percent of the cols in the form
$vLeftPercent = 20;  //field name percent
$vRightPercent = 100-$vLeftPercent; //value percent...
$vFormPercent = 110;

//echo html_body();
$vSectionName = array(  'results' => 'Resultado',
						'query' => 'Consulta');


$HTTP_PARAM_VARS['data'] = (isset($HTTP_PARAM_VARS['data'])) ? $HTTP_PARAM_VARS['data'] : 'results';
$HTTP_PARAM_VARS['pos'] = (isset($HTTP_PARAM_VARS['pos']))? $HTTP_PARAM_VARS['pos'] : 0;
$HTTP_PARAM_VARS['links'] = (isset($HTTP_PARAM_VARS['links']))? $HTTP_PARAM_VARS['links'] : "";
$vSection = $HTTP_PARAM_VARS['data'];

if (isset($HTTP_GET_VARS['sql']))
	{
	$HTTP_PARAM_VARS['sql'] = stripcslashes((($HTTP_GET_VARS['sql'])));
	
	}
	
$vLinks = array();
if (isset($HTTP_GET_VARS['links']))
	{
	$temp = $HTTP_GET_VARS['links'];
	if ( 	(isset($s_temporal_vars[$temp]["value"])) &&
		(isset($s_temporal_vars[$temp]["field"])) )
		{
		$vLinks[$s_temporal_vars[$temp]["field"]] = $s_temporal_vars[$temp]["value"];
		}
	}

?>
<script language="JavaScript" src="lib/js/scripts_inc.js" type="text/javascript"></script>

<script language="JavaScript" type="text/JavaScript">
<!--


function openWindowSql()
{

		if (document.Form.sql.value.length>0)
			{
			theURL = 'TableView.php?sql='+document.Form.sql.value+'&mod='+document.Form.mod.value;
			window.open(theURL);
			}

}

function JS_OnLoad()
{
	posiciona()
	empezar = true;
	document.Form.page_status.value = 'complete';
//	alert(document.Form.page_status.value);
}

//-->
</script>

<BODY ONLOAD='JS_OnLoad()'>
<?php
	//require_once('lib/xpath/XPath.class.php');
	//require('head.html');
	//create the xpath object

	$xPath =& new XPath();
	$xPath->setSkipWhiteSpaces(TRUE);
	
	$xPath->importFromFile(getcwd().'/configuration/'.$HTTP_PARAM_VARS['mod']);

	$aResult = $xPath->evaluate("/Main/queries/query");

	$vType = (isset($HTTP_PARAM_VARS['type']))? $HTTP_PARAM_VARS['type']:'normal';
	$vRowField = '';
	$vColField = '';
	$vSumField = '';
	$vListSumField = array();

	if (isset($HTTP_PARAM_VARS['sqlid']))
		{
		$vsqlid = (int)$HTTP_PARAM_VARS['sqlid']+0;
		}
	else	{
		$vsqlid = 0;
		}

	echo '<form name="Form" method="get" action="PreQuery.php">';
	echo '<input name="data" type="hidden" value="'.$vSection.'">';
	echo '<input name="mod" type="hidden" value="'.$HTTP_PARAM_VARS['mod'].'">';
	echo '<input name="page_status" type="hidden" value="complete">';
	echo '<input name="links" type="hidden" value="'.$HTTP_PARAM_VARS['links'].'">';



	if (count($aResult)>0)
		{
		echo 'Consultas predise&ntilde;adas: ';
		echo '<select name="sqlid" size="1">';
		echo '<option value=""></option>';

		for ($t = 0; $t < count($aResult); $t++)
			{
			//echo '<option value="'.htmlentities(($xPath->getData($aResult[$t]))).'" >'.$xPath->getAttributes($aResult[$t], 'name').'</option>';			   ç

			if ($t==$vsqlid)
				{
				$vType = $xPath->getAttributes($aResult[$t], 'type');
				$vSection = (isset($HTTP_PARAM_VARS['doit']))? 'results': $vSection;
				if ( (isset($HTTP_PARAM_VARS['doit'])) or
				     ($vType =='statistic') or
				     ($vType =='pivot') )
					{
					$vRowField = ($xPath->getAttributes($aResult[$t], 'rowfield'));
					$vColField = ($xPath->getAttributes($aResult[$t], 'colfield'));
					$vSumField = ($xPath->getAttributes($aResult[$t], 'sumfield'));
					$vfrom_part = ($xPath->getAttributes($aResult[$t], 'from_part'));
					$vwhere_part = ($xPath->getAttributes($aResult[$t], 'where_part'));
					$HTTP_PARAM_VARS['sql'] = ($vType=='pivot')? 'nada': $xPath->getData($aResult[$t]);
					$HTTP_PARAM_VARS['pos'] = ( ($vType =='pivot') and (!isset($HTTP_PARAM_VARS['doit'])) )? $HTTP_PARAM_VARS['pos']:0;

					}
				echo '<option value="'.$t.'" selected>'.htmlentities(urldecode($xPath->getAttributes($aResult[$t], 'name'))).'</option>';
				}
			else	{
				echo '<option value="'.$t.'" >'.htmlentities(urldecode($xPath->getAttributes($aResult[$t], 'name'))).'</option>';
				}
			}
		echo '</select>';
		//echo '<input name="doit" onClick="javascript: openWindowSql();" type="button" value="Ejecutar" >';
		echo '<input name="doit" type="submit" value="'.$button_strings['Execute'].'" onClick="'."javascript: showWaitScreen('".$message_strings['Searching']."')".';"><br>';
		echo '<input name="type" type="hidden" value="'.$vType.'">';


		}
	else
		{
		$vType = 'mod';
		}
		
	if ( ($vType=='excel_out') && (isset($HTTP_PARAM_VARS['doit'])) )
		{
		$vScript = 'Redirect("PreQuery.php?pos='.($vposi).'&sqlid='.$vSqlId.'&mod='.$HTTP_GET_VARS['mod'].'&sql='.(urlencode($HTTP_PARAM_VARS['sql'])).'&type='.$vType.'&links='.(($HTTP_PARAM_VARS['links'])).'");';
		}

	if  ( (isset($HTTP_PARAM_VARS['sql'])) and (($vType!='pivot')) )
		{

		//fields need to work in the sql query and combobox...
		$vSql = urldecode($HTTP_PARAM_VARS['sql']);

		//Query...
		//$rec = $dbhandle->Execute($vSql);
		//[orderbygo] => Ordernar por:
		//[orderby] => Familia

		$temp = '';
		$Sql = $vSql;
		$SqlOrderBy = array();
		$SqlHaving = '';
		$SqlGroupBy = array();
		$SqlWhere = '';
		$SqlFrom = '';
		$SqlSelect = array();
		if ( (@eregi ('(.*) union (.*)', $vSql, $match)) )
			{
			//nothing to do...
			}
		else	{
			if (@eregi ('(.*) order by (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$temp = $match[2];
				while (@eregi ('(.*),(.*)', $temp, $match))
					{
					$temp = trim($match[1]);
					$SqlOrderBy[] = trim($match[2]);
					}
				$SqlOrderBy[] = $temp;
				$SqlOrderBy = array_reverse($SqlOrderBy);
				}

			if (@eregi ('(.*) having (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$SqlHaving = $match[2];
				}

			if (@eregi ('(.*) group by (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$temp = $match[2];
				while (@eregi ('(.*),(.*)', $temp, $match))
					{
					$temp = trim($match[1]);
					$SqlGroupBy[] = trim($match[2]);
					}
				$SqlGroupBy[] = $temp;
				$SqlGroupBy = array_reverse($SqlGroupBy);
				}

			if (@eregi ('(.*) where (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$SqlWhere = $match[2];
				}

			if (@eregi ('(.*) from (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$SqlFrom = $match[2];
				}

			if (@eregi ('(.*)select (.*)', $Sql, $match))
				{
				$Sql = $match[1];
				$temp = $match[2];
				while (@eregi ('(.*),(.*)', $temp, $match))
					{
					$temp = trim($match[1]);
					$SqlSelect[] = trim($match[2]);
					}
				$SqlSelect[] = $temp;
				$SqlSelect = array_reverse($SqlSelect);
				}

			}
		if ( ((isset($HTTP_PARAM_VARS['orderasc'])) or
		      (isset($HTTP_PARAM_VARS['orderdesc'])))
		      and
		     ((isset($HTTP_PARAM_VARS['orderby'])) and
		     (!empty($HTTP_PARAM_VARS['orderby']))) )
		     {
		     $torder = split( ":", $HTTP_PARAM_VARS['orderby']);
		     $SqlOrderBy = array();


		     for ($aa=0; $aa<count($SqlSelect); $aa++)
				{
				if (strpos($SqlSelect[$aa],$torder[1])>0)
					{
					$SqlOrderBy[0] = ($aa+1).' ';
					$SqlOrderBy[0] .=  (isset($HTTP_PARAM_VARS['orderasc']))? 'ASC' : 'DESC';
					}
				}
		     if ( (count($SqlOrderBy)==0) and (false) )
				{
				$trec = $dbhandle->SelectLimit($vSql,1);
				if ($trec === FALSE)
					{
					$error .= $dbhandle->ErrorMsg();
					}
				else	{
					for ($i=0, $max=$trec->FieldCount(); ($i < $max) and (count($SqlOrderBy)==0); $i++)
						{
						$fld = $trec->FetchField($i);
						if (($fld->name==$torder[1]) )
							{
							$SqlOrderBy[0] = ($torder[0]+1).' ';
							$SqlOrderBy[0] .=  (isset($HTTP_PARAM_VARS['orderasc']))? 'ASC' : 'DESC';
							}
						}
					}

				}
		     if (count($SqlOrderBy)==0)
				{
				$vf = $syntax['field'];
				$torder = $torder[1];
				$torder = ereg_replace('#1',$torder, $vf);
				$SqlOrderBy[0] = $torder.' ';
				$SqlOrderBy[0] .=  (isset($HTTP_PARAM_VARS['orderasc']))? 'ASC' : 'DESC';
				$vType='normal';
				}

		     $vSection='results';

		     }


		if ( (isset($HTTP_PARAM_VARS['groupbygo']))
			and
			((isset($HTTP_PARAM_VARS['groupby'])) and
			(!empty($HTTP_PARAM_VARS['groupby']))) )
				{
				
				$SqlOrderBy = array();
				$SqlGroupBy = array();
				$SqlHaving = '';
				$SqlSelect = array();
				$tgroup = $HTTP_PARAM_VARS['groupby'];

				$vf = $syntax['field'];
				$tgroup = ereg_replace('#1',$tgroup, $vf);
				$SqlGroupBy[0] = $tgroup;
				$SqlSelect[0] = $tgroup;
				$SqlSelect[1] = 'count(*) as "Cantidad"';
				$vSection='results';
				$vType='normal';
				}
		}

	echo '<br><br>';
	echo "<ul id=\"Panel\"'>\n";
	$vsection_cant = 0;
	
	foreach ($vSectionName as $vnam => $vval)
		{
		$vsection_cant++;
		if ($vSection==$vnam) {

			// Show the panel of the section selected/active...
			echo '      <li><a id="menupanel_'.$vsection_cant.'" class="current" '
					.' href="javascript:NewPanel(\''.$vnam.'\');">'
					.'&nbsp;&nbsp;'.$vval.'&nbsp;&nbsp; </a>'." </li>\n";
	
			}
		else
			{
			// Show the panel of the section don´t selected/active...
			echo '      <li><a id="menupanel_'.$vsection_cant.'" '
					.' href="javascript:NewPanel(\''.$vnam.'\');">'
					.'&nbsp;&nbsp;'.$vval.'&nbsp;&nbsp; </a>'." </li>\n";

			}
		}
	echo "</ul>\n";
	
	echo '<table border="1" width="100%" cellpadding="4" cellspacing="0" class="TableForm">';
	echo '<tr><td class="TableField" >';
	
	if  (isset($HTTP_PARAM_VARS['sql']))
		{
		
		if ( ($vSection=='query') and
		     ($vType!='pivot') )
			{
			$vSql = stripcslashes(urldecode($HTTP_PARAM_VARS['sql']));
			echo '<hr>';
			echo '<div  align="left"><textarea cols="100" rows="10">'.stripcslashes($vSql).'</textarea></div>';
			$rec = $dbhandle->SelectLimit($vSql,1);

			if ($rec === FALSE)
				{
				$error .= $dbhandle->ErrorMsg();
				}
			else	{
				if ($vType!='statistic')
					{
					echo '<br><hr>';

					echo 'Ordernar por:&nbsp;<select name="orderby" size="1">';
					echo '<option value="" ></option>';
					for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++)
						{
						$fld = $rec->FetchField($i);
						$type = $rec->MetaType($fld->type);
						echo '<option value="'.($i).':'.htmlentities($fld->name).'" >'.$fld->name.'</option>';
						}
					echo '</select>';
					echo  '<input name="orderasc" type="submit" value="ASC"  onClick="'."javascript: showWaitScreen('".$message_strings['Searching']."')".';">';
					echo  '<input name="orderdesc" type="submit" value="DESC"  onClick="'."javascript: showWaitScreen('".$message_strings['Searching']."')".';">';

					if (@eregi ('(.*) from (.*)', $vSql, $match))
						{
						$tSql = 'select * from '.$SqlFrom;
						//if (!empty($SqlWhere))
						//	{
						//	$tSql .= ' where '.$SqlWhere;
						//	}
						$trec = $dbhandle->SelectLimit($tSql,1);
						if ($trec === FALSE)
							{
							$error .= $dbhandle->ErrorMsg();
							}
						else	{
							echo  '&nbsp;&nbsp;<input name="groupbygo" type="submit" value="Agrupar por:"  onClick="'."javascript: showWaitScreen('".$message_strings['Searching']."')".';">';
							echo  '&nbsp;<select name="groupby" size="1">';
							echo  '<option value="" ></option>';
							for ($i=0, $max=$trec->FieldCount(); $i < $max; $i++)
								{
								$fld = $trec->FetchField($i);
								$type = $trec->MetaType($fld->type);
								echo '<option value="'.htmlentities($fld->name).'" >'.$fld->name.'</option>';
								}
							echo '</select>';
							}
						}

					}
				}
			}



	
		
		if ( ($vSection=='results') )
			{
			
			if ($vType =='mod')
				{
				
				$warning .= "3. ".htmlentities($vSql);
				$vpos = 0;
				$vposMsg = '';
				if ((isset($HTTP_PARAM_VARS['pos'])) )
					{
					$vpos = (int)$HTTP_PARAM_VARS['pos'];
					$vpos = $vpos + 0;
					$vposi = ($vpos-100<=0) ? 0: $vpos-100;
					if  ($vpos>0)
						{
						$vposMsg = '<a href="PreQuery.php?pos='.($vposi).'&sqlid='.$vSqlId.'&mod='.$HTTP_GET_VARS['mod'].'&sql='.(urlencode($vSql)).'&type='.$vType.'&links='.(($HTTP_PARAM_VARS['links'])).'">      &lt;&lt;</a>';
						}
					else	{
						$vposMsg = '      &lt;&lt;';
						}
					}
				else	{
					$vposMsg = '      &lt;&lt;';
					}

				$vposMsg .= '<input name="pos" size="6" type="text" value="'.$vpos.'" onChange="javascript: document.Form.submit();">';
				$vposMsg .= '<a href="PreQuery.php?pos='.($vpos+100).'&sqlid='.$vSqlId.'&mod='.$HTTP_GET_VARS['mod'].'&sql='.(urlencode($vSql)).'&type='.$vType.'&links='.(($HTTP_PARAM_VARS['links'])).'"> &gt;&gt;</a>';

				$rec = $dbhandle->SelectLimit($vSql,100,$vpos);
				if ($rec === FALSE)
					{
					$error .= $dbhandle->ErrorMsg();
					}
				else	{
					echo $vposMsg;
					echo '<br><hr>';
					echo rs2html($rec,"BORDER='3'",false,false,false,$vLinks);
					echo '<br><hr>';
					echo $vposMsg;
					}

				}

			if (($vType=='statistic') and ((!empty($vColField)) or (!empty($vSumField))))
				{
				$vCols = array();
				$vRows = array();
				$vTITLE = '';


				echo '<br><hr>';
				echo '<TABLE BORDER="3">';
				echo '<tr>';

				if (!empty($vRowField))
					{
					$temp = $vRowField;
					$tField = '';
					if (@eregi ('(.*) as (.*)', $temp, $match))
						{
						$vTITLE = trim($match[2]);
						$temp = trim($match[1]);
						}
					else	{
						$vTITLE = $temp;
						}
					$tField = $temp;

					$vf = $syntax['field'];
					$vf = ereg_replace('#1',$temp, $vf);
					$condField = $vf;
					$temp = 'select distinct '.$vf.' from '.$SqlFrom.' where ';
					$temp .= (!empty($SqlWhere))? $SqlWhere.' and ': '';
					$temp .= $vf.' is not null ';
					$warning .= '<br>4. '.$temp;
					$trec = $dbhandle->Execute($temp);

					if ($trec === FALSE)
						{
						$error .= $dbhandle->ErrorMsg();
						}
					else	{
						while (!$trec->EOF)
							{
							if (!empty($trec->fields[$tField]))
								{
								$vRows[$trec->fields[$tField]] = "'".$trec->fields[$tField]."'";
								}
							$trec->MoveNext();
							}
						}

					}
				$vRows['Total'] = '__ALL__';

				if (!empty($vSumField))
					{
					$temp = $vSumField;
					while (@eregi ('(.*),(.*)', $temp, $match))
						{
						$temp = trim($match[1]);
						$tt = trim($match[2]);
						if (@eregi ('(.*) as (.*)', $tt, $match))
							{
							$vListSumField[] = trim($match[1]);
							$vCols[trim($match[1])] = trim($match[2]);
							}
						else	{
							$vCols[$tt] = $tt;
							$vListSumField[] = $tt;
							}
						}
					if (@eregi ('(.*) as (.*)', $temp, $match))
						{
						$vCols[trim($match[1])] = trim($match[2]);
						$vListSumField[] = trim($match[1]);
						}
					else	{
						$vCols[$temp] = $temp;
						$vListSumField[] = $tt;
						}
					}

				if (!empty($vColField))
					{
					$temp = $vColField;
					while (@eregi ('(.*),(.*)', $temp, $match))
						{
						$temp = trim($match[1]);
						$tt = trim($match[2]);
						if (@eregi ('(.*) as (.*)', $tt, $match))
							{
							$vCols[trim($match[1])] = trim($match[2]);
							}
						else	{
							$vCols[$tt] = $tt;
							}
						}
					if (@eregi ('(.*) as (.*)', $temp, $match))
						{
						$vCols[trim($match[1])] = trim($match[2]);
						}
					else	{
						$vCols[$temp] = $temp;
						}
					}


				if (!empty($vTITLE))
					{
					$vCols[''] = $vTITLE;
					}
				else	{
					$vCols[''] = '';
					}


				$vCols = array_reverse($vCols);

				foreach ($vCols as $key => $val)
					{
					echo '<TH>'.$val.'</TH>';
					if (!empty($key))
						{
						$vf = $syntax['field'];
						$vf = ereg_replace('#1',$key, $vf);
						if (in_array ($key, $vListSumField))
							{
							$vCols[$key] = 'select sum('.$vf.') as "_sum_" from '.$SqlFrom;
							}
						else	{
							$vCols[$key] = 'select distinct '.$vf.' from '.$SqlFrom.' where ';
							$vCols[$key] .= (empty($SqlWhere))? '': $SqlWhere.' and ';
							$vCols[$key] .= $vf.' is not null ';
							}
						}
					}
				echo '</tr>';

				//print_r($vRows);
				foreach ($vRows as $vRkey => $vRval)
					{
					echo '<tr>';
					foreach ($vCols as $key => $val)
						{
						echo '<TD>';

						if (!empty($key))
							{
							$ADODB_COUNTRECS = true;
							if ($vRval=='__ALL__')
								{
								$AddSql = '';
								}
							else	{
								if (@eregi ('(.*) where (.*)', $vCols[$key], $match))
									{
									$AddSql = ' and '.$condField.'='.$vRval;
									}
								else	{
									$AddSql = ' where '.$condField.'='.$vRval;
									}
								}
							$warning .= '<br>5. '.$vCols[$key].$AddSql;
							$trec = $dbhandle->Execute($vCols[$key].$AddSql);
							if ($trec === FALSE)
								{
								$error .= $dbhandle->ErrorMsg();
								}
							else	{
								if (in_array ($key, $vListSumField))
									{
									echo ($trec->fields['_sum_'])+0;
									}
								else	{
									echo $trec->RecordCount();
									}

								}
							}
						else	{
							echo htmlentities($vRkey);
							}
						echo '</TD>';
						}
					echo '</tr>';
					}


				echo '</TABLE>';
				//print_r($vCols);
				}
			elseif ( ($vType=='normal') || ($vType=='excel_out') )
				{
				
				
				
				$vpos = 0;
				$vposMsg = '';
				if ((isset($HTTP_PARAM_VARS['pos'])) )
					{
					$vpos = (int)$HTTP_PARAM_VARS['pos'];
					$vpos = $vpos + 0;
					$vposi = ($vpos-100<=0) ? 0: $vpos-100;
					if  ($vpos>0)
						{
						$vposMsg = '<a href="PreQuery.php?pos='.($vposi).'&sqlid='.$vSqlId.'&mod='.$HTTP_GET_VARS['mod'].'&sql='.(urlencode($vSql)).'&type='.$vType.'&links='.(($HTTP_PARAM_VARS['links'])).'">      &lt;&lt;</a>';
						}
					else	{
						$vposMsg = '      &lt;&lt;';
						}
					}
				else	
					{
					$vposMsg = '      &lt;&lt;';
					}

				$vposMsg .= '<input name="pos" size="6" type="text" value="'.$vpos.'" onChange="javascript: document.Form.submit();">';
				$vposMsg .= '<a href="PreQuery.php?pos='.($vpos+100).'&sqlid='.$vSqlId.'&mod='.$HTTP_GET_VARS['mod'].'&sql='.(urlencode($vSql)).'&type='.$vType.'&links='.(($HTTP_PARAM_VARS['links'])).'"> &gt;&gt;</a>';

				
				
				if ($vType=='excel_out')
					{
					echo $vposMsg;
					echo '<br><hr>';
					//echo rs2html($rec,"BORDER='3'",false,false,false,$vLinks);
					//print_r($vReportHtmlResult);
					echo arrTohtml($vReportHtmlResult,"BORDER='3'",$vColsPerRecord+1);
					echo '<br><hr>';
					echo $vposMsg;
					
					}
				else	
					{
				
					if (count($SqlSelect)>0)
						{
					
						$vSql = 'select ';
						for ($aa=0; $aa<count($SqlSelect); $aa++)
							{
							$vSql .= ($aa==0)? '': ', ';
							$vSql .= $SqlSelect[$aa];
							}
						$vSql .= ' from '.$SqlFrom;
						if (!empty($SqlWhere))
							{
							$vSql .= ' where '.$SqlWhere;
							}
						if (count($SqlGroupBy)>0)
							{
							$vSql .= ' group by ';
							for ($aa=0; $aa<count($SqlGroupBy); $aa++)
								{
								$vSql .= ($aa==0)? '': ', ';
								$vSql .= $SqlGroupBy[$aa];
								}
							}
						if (!empty($SqlHaving))
							{
							$vSql .= ' having '.$SqlHaving;
							}
						if (count($SqlOrderBy)>0)
							{
							$vSql .= ' order by ';
							for ($aa=0; $aa<count($SqlOrderBy); $aa++)
								{
								$vSql .= ($aa==0)? '': ', ';
								$vSql .= $SqlOrderBy[$aa];
								}
							}
						}

					$warning .= "6. ".htmlentities($vSql);				
				
					$rec = $dbhandle->SelectLimit($vSql,100,$vpos);
					if ($rec === FALSE)
						{
						$error .= $dbhandle->ErrorMsg();
						}
					else	{
						echo $vposMsg;
						echo '<br><hr>';
						echo rs2html($rec,"BORDER='3'",false,false,false,$vLinks);
						echo '<br><hr>';
						echo $vposMsg;
						}
					}

				}
			elseif ($vType=='pivot')
				{
	
				$vSql = PivotTableSQL($dbhandle,$vfrom_part,$vRowField,$vColField,$vwhere_part);

				$warning .= "7. ".htmlentities($vSql);
				$vpos = 0;
				$vposMsg = '';
				if ((isset($HTTP_PARAM_VARS['pos'])) )
					{
					$vpos = (int)$HTTP_PARAM_VARS['pos'];
					$vpos = $vpos + 0;
					$vposi = ($vpos-100<=0) ? 0: $vpos-100;
					if  ($vpos>0)
						{
						$vposMsg = '<a href="PreQuery.php?pos='.($vposi).'&sqlid='.$vSqlId.'&mod='.$HTTP_GET_VARS['mod'].'&sqlid='.($vsqlid).'&type='.$vType.'&links='.(($HTTP_PARAM_VARS['links'])).'">      &lt;&lt;</a>';
						}
					else	{
						$vposMsg = '      &lt;&lt;';
						}
					}
				else	{
					$vposMsg = '      &lt;&lt;';
					}

				$vposMsg .= '<input name="pos" size="6" type="text" value="'.$vpos.'" onChange="javascript: document.Form.submit();">';
				$vposMsg .= '<a href="PreQuery.php?pos='.($vpos+100).'&sqlid='.$vSqlId.'&mod='.$HTTP_GET_VARS['mod'].'&sqlid='.($vsqlid).'&type='.$vType.'&links='.(($HTTP_PARAM_VARS['links'])).'"> &gt;&gt;</a>';

				$rec = $dbhandle->SelectLimit($vSql,1);
				$vLinks = array();
				$vTemp = array_keys($rec->fields);
				$pivotvalues = array();
				$huckfields = '';
				$match1 = array();
				$match2 = array();
				$temp = $vRowField;
				$groupfields = array();
				while (@eregi ('(.*),(.*)', $temp, $match1))
					{
					$temp = trim($match1[1]);
					if (@eregi ('(.*) as (.*)', $match1[2], $match2))
						{
						$groupfields[] = ereg_replace('"', '', trim($match2[2]) );;
						}
					else	{
						$groupfields[] = ereg_replace('"', '', trim($match1[2]) );
						}
					}
				if (@eregi ('(.*) as (.*)', $temp, $match2))
					{
					$groupfields[] = ereg_replace('"', '', trim($match2[2]) );
					}
				else	{
					$groupfields[] = ereg_replace('"', '', trim($temp) );
					}

				foreach ($vTemp as $vvalue)
					{
					if (in_array($vvalue, $groupfields)>0)
						{
						$huckfields = (strlen($huckfields)>0) ? $huckfields.' & __'.$vvalue.'__': '__'.$vvalue.'__';
						}
					else 	{
						$pivotvalues[] = $vvalue;
						}
					}
				foreach ($pivotvalues as $vvalue)
					{
					$vLinks[$vvalue] = 'javascript: alert("'.$huckfields.' & '.$vvalue.'")';
					}
				//echo 'vtemp...';
				//print_r($vTemp);
				//echo '<br><br>groupfields...';
				//print_r($groupfields);
				//echo '<br><br>vlinks...';
				//print_r($vLinks);
				//$vLinks['Ornamental (Z)'] = 'javascript: alert("__Familia__ & Ornamental (Z)")';


				$rec = $dbhandle->SelectLimit($vSql,100,$vpos);
				if ($rec === FALSE)
					{
					$error .= $dbhandle->ErrorMsg();
					}
				else	{
					echo $vposMsg;
					echo '<br><hr>';

					echo rs2html($rec,"BORDER='3'",false,false,false,$vLinks,true);
					echo '<br><hr>';
					echo $vposMsg;
					}

				}

			}
		echo '<input name="sql" type="hidden" value="'.urlencode($vSql).'">';
		}
	else	{
		echo $message_strings['No_Data_Exist'];
		}

	echo '</td></tr></table>';
	echo  '<br><input name="GraphAction" type="submit" value="Graficar"    >';
	echo  '	   <input name="ExportAction" type="submit" value="Exportar"    >';
	echo "</form>";

	
	
?>
<script language="JavaScript" type="text/JavaScript">
<!--


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



<?php echo $vScript; ?>

//-->
</script>

<?php

require('./inc/script_end.inc.php');

if (file_exists ($xmlConfDirectory."foot.php"))
		{
		require($xmlConfDirectory."foot.php");
		}
		
		

		
?>


</body>
</html>