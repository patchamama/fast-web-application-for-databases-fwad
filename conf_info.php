<?php
// File           edit.php / Phyllacanthus
// Purpose        Edit any structure defined in a xml file, is posible actions as save, insert, search
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//
require('./inc/script_start.inc.php');
print "<BODY>\n";
$vHTMLError = '';


$info_elements = array();
$info_elements_error = array();
$velements_count = array();
$vcatalogues = array();
$vmark = 0;

$idx_sql = 'SELECT I.RDB$INDEX_NAME AS INAME, I.RDB$RELATION_NAME AS TNAME, S.RDB$FIELD_NAME AS FNAME, S.RDB$FIELD_POSITION AS POS FROM RDB$INDICES I JOIN RDB$INDEX_SEGMENTS S ON S.RDB$INDEX_NAME=I.RDB$INDEX_NAME WHERE (I.RDB$SYSTEM_FLAG IS NULL  OR  I.RDB$SYSTEM_FLAG=0) AND I.RDB$FOREIGN_KEY IS NULL';


	$pathMod = pathinfo($HTTP_PARAM_VARS['mod']);
	$xmlDirectory = getcwd().'/configuration/'.$pathMod["dirname"].'/';
	//$xmlFile = $pathMod['basename'];
	$xmlFile = '';
	
  $directory = dir($xmlDirectory);

  // Run through all files of the current directory.
  while ( $entry = $directory->read())
	{
	    // Check whether it's an XML file.
	    if ( eregi("\.xml$", $entry) )
		{
		      // Skip files that end .new.xml
		     if (eregi("\.new\.xml$", $entry)) continue;

		     $vvvmod = $pathMod["dirname"]."/$entry";


			$vpage ='';
			//$vHTML .= '<table BORDER="1" CELLSPACING="1"><tr>';
			//$vHTML .= '	<TH>';
			//$vHTML .= '	<TH><strong>Informaci&oacute;n</strong>';
			//$vHTML .= '	<TH><strong>Identidad</strong>';
			//$vHTML .= '	<TH><strong>Atributo</strong>';
			//$vHTML .= '	<TH><strong>Tipo</strong>';
			//$vHTML .= '	<TH><strong>Referencia</strong>';
				//$vHTML .= '	<TH><strong>Obligatorio</strong>';
			//$vHTML .= '	<TH><strong>P&aacute;gina</strong>';
			//$vHTML .= '</tr>';
			$vpos = 0;

			$vtablesPk = array();
			$vtablesFk = array();
			
			$dom =& new XPath();
			$dom->setSkipWhiteSpaces(TRUE);
			$dom->importFromFile($xmlDirectory."/$entry");
						
			$s_xml_conf = array();
			$s_xml_conf['init'] = 0;

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

			
			for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
				{
				if (isset($s_xml_conf['tables'][$t]['pk']))
					{
					$vtablesPk[$s_xml_conf['tables'][$t]['name']] = split (';',$s_xml_conf['tables'][$t]['pk']);
					}
				if (isset($s_xml_conf['tables'][$t]['fk']))
					{
					$vtablesFk[$s_xml_conf['tables'][$t]['name']] = split (';',$s_xml_conf['tables'][$t]['fk']);
					$vv = $vtablesFk[$s_xml_conf['tables'][$t]['name']];
					for ($yt = 0; $yt < count($vv); $yt++)
						{
						$a = split('=',$vv[$yt]);
						if (count($a)==2)
							{
							$b = split('\.',$a[1]);
							if (count($b)==2)
								{
								$vtablesPk[$b[0]][] = $b[1];
								}
							}
						}
					}
				}
			for ($t = 0; $t < count($s_xml_conf['elements']); $t++)
				{

				$vinfo = '';
				$ventro = false;
				
				
				if ($s_xml_conf['elements'][$t]['tagname']=='element')
					{
					$velements_count[$s_xml_conf['elements'][$t]['type']] = (isset($velements_count[$s_xml_conf['elements'][$t]['type']]))? $velements_count[$s_xml_conf['elements'][$t]['type']]+1 : 1;
					
					
					
					$ventro = (	(isset($s_xml_conf['elements'][$t]['table'])) and
							(isset($s_xml_conf['elements'][$t]['field']))	);
					if ($ventro)
						{
						$vTable = $s_xml_conf['elements'][$t]['table'];
						$vField = $s_xml_conf['elements'][$t]['field'];
						$vt = $syntax['table'];
						$vt = ereg_replace('#1',$vTable, $vt);
						$vvfield = $syntax['field'];
						$vvfield = ereg_replace('#1',$vField, $vvfield);
						
						
						//$vHTML .= '<tr>';
						$vpos++;
						//$vHTML .= '	<td>'.($vpos).'.</td>';
						$info_elements[$vTable][$vField]['mod'][] =  $vvvmod;
						$vvinfo = '-';

						if ( 	(isset($s_xml_conf['elements'][$t]['querylabel'])) and
							(!empty($s_xml_conf['elements'][$t]['querylabel'])) )
							{
							$vvinfo = $s_xml_conf['elements'][$t]['querylabel'];
							//$vHTML .= '	<td>'.$s_xml_conf['elements'][$t]['querylabel'].'</td>';
							}
						elseif ( (isset($s_xml_conf['elements'][$t]['content'])) and
							(!empty($s_xml_conf['elements'][$t]['content'])) )	
							{
							//$vHTML .= '	<td>'.$s_xml_conf['elements'][$t]['content'].'</td>';
							$vvinfo = $s_xml_conf['elements'][$t]['content'];
							}
						
						if ( 	(isset($s_xml_conf['elements'][$t]['help'])) and
							(!empty($s_xml_conf['elements'][$t]['help'])) )
							{
							$info_elements[$vTable][$vField]['help'][] = "<a href='".'configuration/'.$pathMod["dirname"]."/".$s_xml_conf['elements'][$t]['help']."'>?</a>";
							
							}
						else	{
							$info_elements[$vTable][$vField]['help'][] = "";
							}
						
						$vmarkNow = '';
													
						if ( ($s_xml_conf['elements'][$t]['type']=='combobox') ||
						     ($s_xml_conf['elements'][$t]['type']=='radio') ||
						     ($s_xml_conf['elements'][$t]['type']=='listbox_listbox') ||
						     ($s_xml_conf['elements'][$t]['type']=='listbox_combobox') ||
						     ($s_xml_conf['elements'][$t]['type']=='listbox_textbox_list') ||
						     ($s_xml_conf['elements'][$t]['type']=='checkbox_multi') 
						   )
						  
							{
							$vmark++;
							$vcatalogues[$vTable][$vField][$vvvmod]['sql'] = $s_xml_conf['elements'][$t]['sql'];
							$vcatalogues[$vTable][$vField][$vvvmod]['id'] = $s_xml_conf['elements'][$t]['id'];
							$vcatalogues[$vTable][$vField][$vvvmod]['desc'] = $s_xml_conf['elements'][$t]['desc'];
							$vcatalogues[$vTable][$vField][$vvvmod]['info'] = $vvinfo;
							$vmarkNow = 'catalog'.($vmark);
							$vcatalogues[$vTable][$vField][$vvvmod]['mark'] = $vmarkNow;
							$vcatalogues[$vTable][$vField][$vvvmod]['ref'] = "";
							$info_elements[$vTable][$vField]['catMark'][] =  "<a href='#".$vmarkNow."'>*</a>";
							}
						else	
							{
							$info_elements[$vTable][$vField]['catMark'][] = "";
							}
						
						$info_elements[$vTable][$vField]['info'][] = $vvinfo;
						

						//$vHTML .= '<td>'.$s_xml_conf['elements'][$t]['table'].'</td>';
						if ( 	(isset($s_xml_conf['elements'][$t]['mandatory'])) and
							($s_xml_conf['elements'][$t]['mandatory']=='true') )
							{
							$info_elements[$vTable][$vField]['mandatory'][] = true;
							//$vHTML .= '	<td><b>'.$s_xml_conf['elements'][$t]['field'].'</b></td>';
							}
						else
							{
							if (array_key_exists ($vTable,$vtablesPk))
								{
								$vtt = $vtablesPk[$vTable];
								if (in_array ($vField,$vtt))
									{
									$info_elements[$vTable][$vField]['mandatory'][] = true;
									//$vHTML .= '	<td><b>'.$s_xml_conf['elements'][$t]['field'].'</b></td>';
									}
								else
									{
									$info_elements[$vTable][$vField]['mandatory'][] = false;
									//$vHTML .= '	<td>'.$s_xml_conf['elements'][$t]['field'].'</td>';
									}
								}
							}


						$vSql = 'SELECT '.$vvfield.' FROM '.$vt;
						if ($rec = $dbhandle->SelectLimit($vSql,1))
							{
							//for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++) {
							//	$fld = $rec->FetchField($i);
								$fld = $rec->FetchField(0);
								$type = $rec->MetaType($fld->type);
							//	$flds[$fld->name] = $type;
							//	}
							//Metatypes defined by adoDB
							//C,X,B: Text
							//D, T: Date
							//L: Logic
							//I, N, R :Numeric
							$vType = array();
							$vType['C']="Texto"; $vType['X']="Memo"; $vType['B']="Memo";
							$vType['D']="Fecha"; $vType['T']="Fecha"; $vType['L']="Lógico";
							$vType['I']="Numérico";  $vType['N']="Numérico";  $vType['R']="Real";
							$info_elements[$vTable][$vField]['type'][] = $vType[$type];
							//$vHTML .= '	<td>'.$vType[$type].'</td>';


							if ( 	(isset($s_xml_conf['elements'][$t]['sql'])) and
								(!empty($s_xml_conf['elements'][$t]['sql'])) )
								{
								$vsql = $s_xml_conf['elements'][$t]['sql'];
								$vvUp = strtolower($vsql);

								$vv = substr ($vsql, 0, strpos($vvUp,'from'));
								$vvUp = strtolower($vv);

								$vv = substr ($vv, strpos($vvUp,'select')+6);
								$vvUp = strtolower($vv);
								$vvf = '';

								if (strpos($vvUp,' as '))
									{
									while (strpos($vv,'  '))
										{
										$vv = ereg_replace('  ',' ', $vv);
										}

									$vv = substr ($vv, 0, strpos($vvUp,' as '.'"'.strtolower($s_xml_conf['elements'][$t]['id']).'"'));
									$vv = ereg_replace('"','', $vv);
									while (strpos($vv,','))
										{
										$vv = substr ($vv, strpos($vv,',')+1);
										}

									//$vHTML .= '	<td ALIGN="middle">'.$vv.'</td>';
									$vvf = $vv;
									}
								else	{
									//$vHTML .= '	<td ALIGN="middle">'.$s_xml_conf['elements'][$t]['id'].'</td>';
									$vvf = $s_xml_conf['elements'][$t]['id'];
									}

								$vvUp = strtolower($vsql);
								$vv = substr ($vsql, strpos($vvUp,'from')+5);

								//if (strpos($vv,'"'))
								//	{
									$vv = substr ($vv, strpos($vv,'"')+1);
									$vv = substr ($vv, 0,strpos($vv,'"'));
								//	}
								//else	{
								//	$vv = substr ($vv, 0,strpos($vv,' '));
								//	}
								if (empty($vvf)) {
									$vvf = $s_xml_conf['elements'][$t]['id'];
									}
								$info_elements[$vTable][$vField]['ref'][] = $vv.' ('.trim($vvf).')';
								if (!empty($vmarkNow))
									{
									$vcatalogues[$vTable][$vField][$vvvmod]['ref'] = $vv.' ('.trim($vvf).')';
									}
								//$vHTML .= '	<td ALIGN="middle">'.$vv.' ('.trim($vvf).')'.'</td>';

								}
							else	{
								$info_elements[$vTable][$vField]['ref'][] = '-';
								//$vHTML .= '	<td></td>';
								}


			//				if ( 	(isset($s_xml_conf['elements'][$t]['mandatory'])) and
			//					($s_xml_conf['elements'][$t]['mandatory']=='true') )
			//					{
			//					$vHTML .= '	<td ALIGN="middle">X</td>';
			//					}
			//				else	{
			//					if (	(array_key_exists ($vTable,$vtablesPk))
			//							)
			//						{
			//						$vtt = $vtablesPk[$vTable];
			//						if (in_array ($vField,$vtt))
			//							{
			//							$vHTML .= '	<td ALIGN="middle">X</td>';
			//							}
			//						else	{
			//							$vHTML .= '	<td></td>';
			//							}
			//						}
			//					else	{
			//						$vHTML .= '	<td></td>';
			//						}
			//
			//					}


							//$vHTML .= '	<td>'.$vpage.'</td>';
							if (empty($vpage))
								{
								$info_elements[$vTable][$vField]['page'][] = '-';
								}
							else	{
								$info_elements[$vTable][$vField]['page'][] = $vpage;
								}


							//$vHTML .= '</tr>';
							}
						else	
							{
							$info_elements_error[$vTable][] = '<br><a href="edit.php?mod='.$vvvmod."&searchelement=".$vTable.".".$vField.'">Error: field '.$vField.' not found. </a>'.$vSql;
							//$vHTMLError .=  '<br><a href="edit.php?mod='.$vvvmod."&searchelem=$vt.$vvfield".'">Error en campo '.$vvfield.' de la tabla '.$vt.'</a>';
							}
						}
					else
						{
						 
						
						}


					}
				elseif ($s_xml_conf['elements'][$t]['tagname']=='section')
					{
					$vpage =$s_xml_conf['elements'][$t]['content'];
					}

				}
			//$vHTML .= '</table><br><br>';

			if (count($vtablesFk)>0)
				{
				//$vHTML .= '<table BORDER="1" CELLSPACING="1"><tr>';
				//$vHTML .= '	<TH>';
				//$vHTML .= '	<TH><strong>Tabla master</strong>';
				//$vHTML .= '	<TH><strong>Tabla detalle</strong>';
				//$vHTML .= '	<TH><strong>relación</strong>';
				//$vHTML .= '</tr>';
				$vv = 0;
				foreach ($vtablesFk as $key=>$val)
					{
					for ($t = 0; $t < count($val); $t++)
						{
						$vt = $val[$t];
						$a = split('=',$vt);
						if (count($a)==2)
							{
							$b = split('\.',$a[1]);
							if (count($b)==2)
								{
								$vv++;
								$info_elements[$key][$a[0]]['ref'][] = '»'.$b[0].' ('.$b[1].')';
								$info_elements[$key][$a[0]]['mod'][] = $vvvmod;
								
								$info_elements[$key][$a[0]]['info'][]= '-';
								$info_elements[$key][$a[0]]['mandatory'][]= '';	
								
								$vt = $syntax['table'];
								$vt = ereg_replace('#1',$key, $vt);
								$vvtfield = $syntax['field'];
								$vvtfield = ereg_replace('#1',$a[0], $vvtfield);
								$vSql = 'SELECT '.$vvtfield.' FROM '.$vt;
								if ($rec = $dbhandle->SelectLimit($vSql,1))
									{
									//for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++) {
									//	$fld = $rec->FetchField($i);
										$fld = $rec->FetchField(0);
										$type = $rec->MetaType($fld->type);
									//	$flds[$fld->name] = $type;
									//	}
									//Metatypes defined by adoDB
									//C,X,B: Text
									//D, T: Date
									//L: Logic
									//I, N, R :Numeric
									$vType = array();
									$vType['C']="Texto"; $vType['X']="Memo"; $vType['B']="Memo";
									$vType['D']="Fecha"; $vType['T']="Fecha"; $vType['L']="Lógico";
									$vType['I']="Numérico";  $vType['N']="Numérico";  $vType['R']="Real";
									$info_elements[$key][$a[0]]['type'][] = $vType[$type];
									}
								else
									{
									$info_elements[$key][$a[0]]['type'][]= '';
									}
								
								$info_elements[$key][$a[0]]['page'][]= '-';
								$info_elements[$key][$a[0]]['catMark'][]= '';
								$info_elements[$key][$a[0]]['help'][] = '';
								
								 
								
								//$vHTML .= '	<tr>';
								//$vHTML .= '	<td>'.$vv.'</td>';
								//$vHTML .= '	<td>'.$b[0].'</td>';
								//$vHTML .= '	<td>'.$key.'</td>';
								//$vHTML .= '	<td>'.$b[0].'.'.$b[1].'='.$key.'.'.$a[0].'</td>';
								//$vHTML .= '	</tr>';
								//$vtablesPk[$b[0]][] = $b[1];
								}
							}
						}
					}
				//$vHTML .= '</table>';
				}

		}

	}

  // Close the directory.
  $directory->close();
  
//print_r($info_elements);
//$info_elements[$vTable][$vField]['info'][]
//$info_elements[$vTable][$vField]['mandatory'][]	
//$info_elements[$vTable][$vField]['type'][]
//$info_elements[$vTable][$vField]['ref'][]
//$info_elements[$vTable][$vField]['page'][]
//$info_elements[$vTable][$vField]['catMark'][]
//$info_elements[$vTable][$vField]['mod'][]
//$info_elements[$vTable][$vField]['help'][]

$vHTML = "";
$vHTMLTables = "<table><tr>";

ksort($info_elements);
$vvvfields = array('type','info','ref','page','mod');
$ttab = -1;
$ttabcant = 4;
foreach ($info_elements as $ttableN =>$tfield )
	{
	$ttab++;
	if ($ttab==$ttabcant)		
		{
		$ttab = 0;
		$vHTMLTables .= "</tr><tr>";
		}
		
	$vHTMLTables .= "<td><a href='#$ttableN'>$ttableN</a></td>";
	
	
	$vHTML .= '<br><br>';
	$vHTML .= "<b><a name='$ttableN'>$ttableN</a></b>";
	$vHTML .= "<br>";
	$vHTML .= '<table  BORDER="1" CELLSPACING="1">';
	$vHTML .= "<tr><td>Attribut</td><td>Type</td><td>Description</td><td>Reference</td><td>Page</td><td>Module</td></tr>";
	
	foreach ($tfield as $tfieldN => $tval)
		{
		$vHTML .= "<tr>";
		
		$vHTML .= "<td>";
		if (isset($info_elements[$ttableN][$tfieldN]['mandatory']) &&
			(count($info_elements[$ttableN][$tfieldN]['mandatory'])>0) )
			{
			
			for ($t = 0; $t < count($info_elements[$ttableN][$tfieldN]['mandatory']); $t++)
				{
				if ($t>0)
					{
					$vHTML .= "<br>";
					}
				if ((isset($info_elements[$ttableN][$tfieldN]['mandatory'][$t])) &&
				    ($info_elements[$ttableN][$tfieldN]['mandatory'][$t]==true) )
					{
					$vHTML .= "<b>";
					$vHTML .= $tfieldN;
					$vHTML .= "</b>";
					}
				else	
					{
					$vHTML .= $tfieldN;
					}
				}
			}
		else
			{
			$vHTML .= $tfieldN;
			}
		$vHTML .= "</td>";
				
		foreach ($vvvfields as $tvalN)
			{
			
			$vHTML .= "<td>";
			if (isset($info_elements[$ttableN][$tfieldN][$tvalN]))
				{
				for ($t = 0; $t < count($info_elements[$ttableN][$tfieldN][$tvalN]); $t++)
					{

					if ($t>0)
						{
						$vHTML .= "<br>";
						}
					if (($tvalN=='info') && 
					    isset($info_elements[$ttableN][$tfieldN]['mod'][$t]) && 
					    !empty($info_elements[$ttableN][$tfieldN]['mod'][$t]))
						{
						$ttt = 'run.php?mod='.$info_elements[$ttableN][$tfieldN]['mod'][$t];
						if (isset($info_elements[$ttableN][$tfieldN]['page'][$t]) && 
						    !empty($info_elements[$ttableN][$tfieldN]['page'][$t]))
							{
							$ttt .= '&data='.$info_elements[$ttableN][$tfieldN]['page'][$t];
							}
						$vHTML .= '<a href="'.$ttt.'">'.htmlentities($info_elements[$ttableN][$tfieldN][$tvalN][$t]).'</a>';;
						if (!empty($info_elements[$ttableN][$tfieldN]['catMark'][$t]))
							{
							$vHTML .= $info_elements[$ttableN][$tfieldN]['catMark'][$t];
							}
						if (!empty($info_elements[$ttableN][$tfieldN]['help'][$t]))
							{
							$vHTML .= $info_elements[$ttableN][$tfieldN]['help'][$t];
							}
						
						}
					else
						{
						$vHTML .= htmlentities($info_elements[$ttableN][$tfieldN][$tvalN][$t]);
						}
					}
				}
			$vHTML .= "</td>";
			}
		$vHTML .= "</tr>";
		}
	$vHTML .= "</table>";
	
	if (isset($info_elements_error[$ttableN]))
			{
			for ($e = 0; $e < count($info_elements_error[$ttableN]); $e++)
				{
				$vHTML .= $info_elements_error[$ttableN][$e];
				}
		}
	}


echo "<h2></h2><hr>";

//----------------------------------------------------------------
echo "<h2>Tables</h2><hr>";
$vHTMLTables .= "</tr></table>";
echo $vHTMLTables;
echo "<br><br>";
echo $vHTML;
echo "<br><br>";
echo $vHTMLError;

//----------------------------------------------------------------
echo "<h2>Catalogues</h2><hr>";
//$vcatalogues[$vTable][$vField]['sql']  
//$vcatalogues[$vTable][$vField]['id']  
//$vcatalogues[$vTable][$vField]['desc']  
//$vcatalogues[$vTable][$vField]['info']  
foreach ($vcatalogues as $ttableN =>$tfield )
	{
	foreach ($tfield as $tfieldN => $tval)
		foreach ($tval as $tvalN => $tval2)
		{
		echo "<br><a name='".$vcatalogues[$ttableN][$tfieldN][$tvalN]['mark']."'>";
		echo "Catalogue of values for the field &quot;".$vcatalogues[$ttableN][$tfieldN][$tvalN]['info']."&quot; stored in: ".$tvalN."-&gt;".$ttableN.".".$tfieldN."</a>";
		echo "<br>".$vcatalogues[$ttableN][$tfieldN][$tvalN]['ref'];
		$vSql = $vcatalogues[$ttableN][$tfieldN][$tvalN]['sql'];
		if ($rec = $dbhandle->Execute($vSql))
			{
			echo '<table BORDER="1" CELLSPACING="1">';
			echo "<tr><td>Code</td><td>Description</td></tr>";
			$vId = $vcatalogues[$ttableN][$tfieldN][$tvalN]['id'];
			$vDesc = $vcatalogues[$ttableN][$tfieldN][$tvalN]['desc'];
			while (!$rec->EOF)
				{
				echo "<tr><td>";
				echo htmlentities(trim($rec->fields[$vId]));
				echo "</td>";
				echo "<td>";
				echo htmlentities(trim($rec->fields[$vDesc]));
				echo "</td></tr>";
				$rec->MoveNext();
				}
			echo "</table>";
			echo "<br>";
			}
		else	
			{
			echo "<br>Error in sql: ".$vSql;
			}
		
		}
	}

//----------------------------------------------------------------
echo "<h2>Components</h2><hr>";
ksort($velements_count);
echo '<table BORDER="1" CELLSPACING="1">';
foreach ($velements_count as $ttt => $vvv) 
	{
	echo "<tr><td>";
	echo htmlentities($ttt);
	echo "</td>";
	echo "<td>";
	echo htmlentities($vvv);
	echo "</td></tr>";
	}
echo "</table>";
echo "<br>";

require('./inc/script_end.inc.php');

?>











            
            