<?php
// File           edit.php / Phyllacanthus
// Purpose        Edit any structure defined in a xml file, is posible actions as save, insert, search
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
//
require('./inc/script_start.inc.php');
print "<BODY>\n";

	$vpage ='';
	echo '<table BORDER="1" CELLSPACING="1"><tr>';
	echo '	<TH>';
	echo '	<TH><strong>Informaci&oacute;n</strong>';
	echo '	<TH><strong>Identidad</strong>';
	echo '	<TH><strong>Atributo</strong>';
	echo '	<TH><strong>Tipo</strong>';
	echo '	<TH><strong>Referencia</strong>';
	//echo '	<TH><strong>Obligatorio</strong>';
	echo '	<TH><strong>P&aacute;gina</strong>';
	echo '</tr>';
	$vpos = 0;

	$vtablesPk = array();
	$vtablesFk = array();
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

				echo '<tr>';
				$vpos++;
				echo '	<td>'.($vpos).'.</td>';
				if ( 	(isset($s_xml_conf['elements'][$t]['querylabel'])) and
					(!empty($s_xml_conf['elements'][$t]['querylabel'])) )
					{
					echo '	<td>'.$s_xml_conf['elements'][$t]['querylabel'].'</td>';
					}
				else	{
					echo '	<td>'.$s_xml_conf['elements'][$t]['content'].'</td>';
					}
				echo '	<td>'.$s_xml_conf['elements'][$t]['table'].'</td>';
				if ( 	(isset($s_xml_conf['elements'][$t]['mandatory'])) and
  					($s_xml_conf['elements'][$t]['mandatory']=='true') )
  					{
					  echo '	<td><b>'.$s_xml_conf['elements'][$t]['field'].'</b></td>';
					}
				else
					{
					if (array_key_exists ($vTable,$vtablesPk))
						{
						$vtt = $vtablesPk[$vTable];
						if (in_array ($vField,$vtt))
							{
							echo '	<td><b>'.$s_xml_conf['elements'][$t]['field'].'</b></td>';
							}
						else
							{
					  		echo '	<td>'.$s_xml_conf['elements'][$t]['field'].'</td>';
					  		}
					  	}
					}


				$vSql = 'SELECT '.$vvfield.' FROM '.$vt;
				$rec = $dbhandle->SelectLimit($vSql,1);
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
				echo '	<td>'.$vType[$type].'</td>';

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

						//echo '	<td ALIGN="middle">'.$vv.'</td>';
						$vvf = $vv;
						}
					else	{
						//echo '	<td ALIGN="middle">'.$s_xml_conf['elements'][$t]['id'].'</td>';
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
					echo '	<td ALIGN="middle">'.$vv.' ('.trim($vvf).')'.'</td>';

					}
				else	{
					echo '	<td></td>';
					}


//				if ( 	(isset($s_xml_conf['elements'][$t]['mandatory'])) and
//					($s_xml_conf['elements'][$t]['mandatory']=='true') )
//					{
//					echo '	<td ALIGN="middle">X</td>';
//					}
//				else	{
//					if (	(array_key_exists ($vTable,$vtablesPk))
//							)
//						{
//						$vtt = $vtablesPk[$vTable];
//						if (in_array ($vField,$vtt))
//							{
//							echo '	<td ALIGN="middle">X</td>';
//							}
//						else	{
//							echo '	<td></td>';
//							}
//						}
//					else	{
//						echo '	<td></td>';
//						}
//
//					}


				echo '	<td>'.$vpage.'</td>';


				echo '</tr>';
				}


			}
		elseif ($s_xml_conf['elements'][$t]['tagname']=='section')
			{
			$vpage =$s_xml_conf['elements'][$t]['content'];
			}

		}
	echo '</table><br><br>';

	if (count($vtablesFk)>0)
		{
		echo '<table BORDER="1" CELLSPACING="1"><tr>';
		echo '	<TH>';
		echo '	<TH><strong>Tabla master</strong>';
		echo '	<TH><strong>Tabla detalle</strong>';
		echo '	<TH><strong>relación</strong>';
		echo '</tr>';
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
						echo '	<tr>';
						echo '	<td>'.$vv.'</td>';
						echo '	<td>'.$b[0].'</td>';
						echo '	<td>'.$key.'</td>';
						echo '	<td>'.$b[0].'.'.$b[1].'='.$key.'.'.$a[0].'</td>';
						echo '	</tr>';
						//$vtablesPk[$b[0]][] = $b[1];
						}
					}
				}
			}
		echo '</table>';
		}



require('./inc/script_end.inc.php');

?>











