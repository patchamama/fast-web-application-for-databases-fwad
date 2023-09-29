<?php
// he cambiado $syntax['select'] por $syntax['select-distinct'] por lo que puede haber algún error futuro, revisar esto...
//linea 1377 (consulta con uso ornamental...)..

// searchquick quickaction
//Crear consulta predefinida con: $s_xml_conf['searchs'][0]['quickquery']  si se posee algún valor esta variable
// y mostrar ayuda con: $s_xml_conf['searchs'][0]['helpquickquery'] motrando el icono de ayuda si tiene algún valor definido de ayuda a mostrar...

// http://localhost/collman-test/templates/example/index.php
// http://smarty.php.net/manual/es/preface.php
// http://www.tufuncion.com/smarty-templates-php
//3878 <input
//5164  4534

// File           run.php / Phyllacanthus
// Purpose        Edit any structure defined in a xml file, is posible actions as save, insert, search
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        6.10
//

require('./inc/script_start.inc.php');

/////////////////////////////////////////////////////////////////////

$s_connection['user_type'] =  'administrator';
/////////////////////////////////////////////////////////////////////

if ((!$s_connection['conected']) || ($vFatalError))
	{
	echo "<body>\n";
	js_alert("Error fatal: ".$error);
	echo "</body>";
	require('./inc/script_end.inc.php');
	echo "</html>";
	exit;
	}


if (!isset($HTTP_PARAM_VARS['giveme_sql']) )
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo $Confs["Meta-Title"]; ?></title>
	<meta http-equiv="Content-type" content="<?php echo $Confs["Meta-Content-type"]; ?>" />
	<meta name="Description" content="<?php echo $Confs["Meta-Title"]; ?>" />
	<meta name="Keywords" content="<?php echo $Confs["Meta-Keywords"]; ?>" />
	<meta name="author" content="<?php echo $Confs["Meta-author"]; ?>" />
	<meta name="owner" content="<?php echo $Confs["Meta-owner"]; ?>" />
	<meta name="robots" content="<?php echo $Confs["Meta-robots"]; ?>" />

	<script type="text/javascript" src="lib/js/jquery.js"></script>
	<script type="text/javascript" src="lib/js/thickbox.js"></script>
	<script type="text/javascript" src="lib/js/thickbox_wait.js"></script>

	<style type="text/css" media="all">

		@import "styles/thickbox.css";
		@import "styles/default.css";
	</style>

	<!-- Preload de image images/loadingAnimation.gif -->
	<script type="text/javascript" >
	image1 = new Image();
	image1.src = "images/loadingAnimation.gif";
	</script>

	</head>
<?php

	echo "<body onload='JS_OnLoad()'>\n";
	
	//Show the user conected and the option to login or Logout

	}

$vAdminUser = ($s_connection['user_type'] == 'administrator');

//$vHelpPath = "help/index.php/";
$vHelpPath = $Confs["HelpUrl"];
$vHelpOpenNewWindow = $Confs["HelpNewWindow"];
$vShowButtons = true;
$vNewButtons = "";
//$vModName = $vModName; //initialized in script_start.inc.php

//echo html_body();

//echo phpinfo();

//Vars used to register the html information
$vHTML = '';
$vHTMLAfterForm = '';
$vQuery = '';
$vQueryOptions = '';
$vQueryOptionsCreated = (isset($s_xml_conf['queryOptions']));
$vQuerySection = '';
$vQueryCount = -1;
$vQueryCountRun = 100;
$vQueryMark = '*';
$vScript='';
$vSearch='';
$vHead = '';
$vLink = '<hr />';
$vComments = '';
$panel = '';
$vButton='';
$vSqlSearch = '';
$vScriptChange = '';
$vScriptIni = '';
$vIsSelected = false;
$vJsDynamicOptionList = false;
$vButtonCant = 0;
$vquery_fields_max_rows = 20;
$vinModName = (isset($HTTP_PARAM_VARS['in_mod'])) ? $HTTP_PARAM_VARS['in_mod'] : $vModName;
$vMultiMod = array();
$s_xml_conf['QueryModRelations'] = (isset($s_xml_conf['QueryModRelations'])) ? $s_xml_conf['QueryModRelations'] : array();
$visFoward = false;  //if true then go to other application

//Percent of the cols in the form
$vLeftPercent = 20;  //field name percent
$vRightPercent = 100-$vLeftPercent; //value percent...
$vFormPercent = 110;

$vPrimaryKeys = '';  //Here will be stored the primarykeys of the xml procesed...
$checkSearch = false;  //if true, the data that is editing is the result of a search, in other case is a new data to insert....
$SEARCH_PARAM = '';   //Value of the field used in search
$recERV_PARAM = 'data,Search,Save,Insert,Delete';   //values not considered to pass in params...
$IsThereTextbox_list = 0; //in case of there is the type 'textbox_list' in the form is true (that is used to include the javascript in the user webpage...)
$IsThereTextbox_add  = 0; //in case of there is the type 'textbox_add' in the form is true (that is used to include the javascript in the user webpage...)
$IsThereCheckbox = 0; //in case of there is the type 'Checkbox' in the form is true (that is used to include the javascript in the user webpage...)
$IsThereListbox  = 0; //in case of there is the type 'Listbox' in the form is true (that is used to include the javascript in the user webpage...)
$IsThereTextbox_combobox = 0;
$IsThereCheckbox_multi = 0;
$ventered = 0; //If there is a textbox or one value in the combobox is true....
$vComponents = array();  //Array that contain the value of all the components declared in the form, could be good to check not double declarations....
$vParamFlow = ''; //That variable contain the params that will propagate in the get parameters....
$vIsDiferentRecord = false; //to know if there is a diferent record in the search of the last record edited..
$vShowForm = true; //If is true then will be showed the form to enter the values....
$vShowFieldSection = true;

if (!isset($vModName))
	{
	$error .= $ERRORS['PARAMS_UNKNOWN'];
	}
else 	
	{
	$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&")."mod=".$vModName."&s_id=".$HTTP_PARAM_VARS['s_id'];
	}

$HTTP_PARAM_VARS['data']= (isset($HTTP_PARAM_VARS['data'])) ? $HTTP_PARAM_VARS['data'] : '';

if (isset($HTTP_PARAM_VARS['Search']))
	{
	if ($HTTP_PARAM_VARS['data']!='all')
		{
		$HTTP_PARAM_VARS['data']='';
		}
	}

//Register in the section vars the values of the post/get if those exist
foreach($HTTP_PARAM_VARS as $vvar => $vval)
	{
	if ((strpos(','.$recERV_PARAM.',',','.$vvar.',')==0) and (strpos($vvar,'__')>0)) {
		$vTable = substr ($vvar, 0,strpos($vvar,'__'));
		$vField = substr ($vvar, strpos($vvar,'__')+2);
		//echo '<br />'.$vTable."__".$vField;
		if (isset($HTTP_PARAM_VARS[$vTable."__".$vField]))
			{
			//echo '='.stripcslashes($HTTP_PARAM_VARS[$vTable."__".$vField]);
			$s_fields_value[$vTable][$vField] = stripcslashes($HTTP_PARAM_VARS[$vTable."__".$vField]);
			}
		}
	}

$vini = 0;
if (isset($HTTP_PARAM_VARS['ini']))
	{
	$vini = (int)$HTTP_PARAM_VARS['ini'] + 0;
	}
elseif (isset($s_ini))
	{
	$vini = $s_ini;
	}
$vini = ($vini<0) ? 0: $vini;
$s_ini = $vini;
$viniLast = ($vini-100<=0) ? 0: $vini-100;
$viniNext = $vini+100;
$HTTP_PARAM_VARS['ini'] = $vini;
//$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&")."ini=".$vini;


//To register the position of the last component used...
if ((isset($HTTP_PARAM_VARS['markGo'])) )
	{
	$vHTML .= '<input name="markGo" type="hidden" value="'.$HTTP_POST_VARS['markGo'].'" />';
	if (IsEmpty($HTTP_PARAM_VARS['markGo']))
		{
		$vHead .= '<A name="markGo"></A>';
		}
	}
else	{
	$HTTP_PARAM_VARS['markGo'] = '';
	$vHTML .= '<input name="markGo" type="hidden" value="" />';
	$vHead .= '<A name="markGo"></A>';
	}

//to redirect to a new page but before is necesary to store the session variables if there was a change.
$vHTML .= '<input name="UrlGo" type="hidden" value="" />';
if ((isset($HTTP_POST_VARS['UrlGo'])) and
   (!IsEmpty($HTTP_POST_VARS['UrlGo'])))
	{
	$vScriptIni .= "\n location.href=".'"'.$HTTP_POST_VARS['UrlGo'].'";';
	}

if (!isset($HTTP_PARAM_VARS['data']))
	{
	$HTTP_PARAM_VARS['data'] = (isset($s_cust['data'])) ? $s_cust['data']: '';
	}
else	{
	$s_cust['data'] = $HTTP_PARAM_VARS['data'];
	}


if (isset($HTTP_PARAM_VARS['FormChanged']))
	{
	if ($HTTP_PARAM_VARS['FormChanged']=="Save")
		{
		$HTTP_PARAM_VARS['Save'] = 'Save';
		$vScriptIni .= "\n".' document.Form.FormChanged.value = "false"';
		}
	elseif ($HTTP_PARAM_VARS['FormChanged']=="SaveAndInsert")
		{
		$HTTP_PARAM_VARS['Save'] = 'SaveAndInsert';
		$vScriptIni .= "\n".' document.Form.FormChanged.value = "false"';
		}
	elseif ($HTTP_PARAM_VARS['FormChanged']=="reload")
		{
		$vScriptIni .= "\n".' document.Form.FormChanged.value = "false"';
		cleanup_session();
		}
	}


//Case of save...
if (isset($HTTP_PARAM_VARS['Save']))
	{
	//if save then go to the begin of the webpage...
	//$vHead .= '<A name="markGo"></A>';
	//Procesing the table tags
	$vSql = '';
	$values = '';
	$listAuton = array();
	$listPk = array();
	$vFatalError = false; //if there is a fatal error, not will be saved the data....

	for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
		{



		if (!$vFatalError)
			{
				{

				$first = true;
				$vTable = get_value($s_xml_conf['tables'][$t],'name');
				$record = array();
				$vPk = get_value($s_xml_conf['tables'][$t],'pk');
				$vFk = get_value($s_xml_conf['tables'][$t],'fk');
				$vFkNull = get_value($s_xml_conf['tables'][$t],'fknull');
				$vAutonumeric = get_value($s_xml_conf['tables'][$t],'autonumeric');
				$NoBodyNull = false;
				$fkisNull = false;
				$listFk = array();
				$IsThereMulti = 0;
				$vDBAction = array();
				//foreach($s_fields_value as $ttable => $vval)
					{
					//if ($vTable==$ttable)
					if (isset($s_fields_value[$vTable]))
						{
						$vcontinue = true;
						$listPk = split(";", $vPk);  //the primary key field list
						$vFkNull = split(";", $vFkNull);  //foreign keys that can have a null value

						$listAuton = array();  //processing the autonumeric field
						if ($vAutonumeric)
							{
							$tt = split(";", $vAutonumeric);
							foreach ($tt as $tt1)  {
								//listAuton have all the autonumerics fields
								if (strpos($tt1,':')==0)
									{
									$vFatalError = true;
									$listAuton = array();
									$error = 'Don?t exist declared the generator in the xml file of the autonumeric field: '.$tt1;
									}
								else	{
									$listAuton[substr ($tt1, 0,strpos($tt1,':'))] = substr ($tt1, strpos($tt1,':')+1);
									}
								}
							}

						if ($vFk)  {  //processing the foreign keys...
							$tt = split(";", $vFk);
							$match = '';
							foreach ($tt as $tt1)  {
								if (!@ereg ('(.+)=(.+)\.(.+)', $tt1, $match))
									{
									if (!@ereg ('(.+)=__(.+)__', $tt1, $match))
										{
										$vFatalError = true;
										$error = 'The format of the foreign key is incorrect in the xml file: '.$tt1;
										$listFk = array();
										}
									else	{
										if ($HTTP_PARAM_VARS[$match[2]])
											{
											$tfk = $match[1];
											$s_fields_value[$vTable][$tfk] = stripcslashes($HTTP_PARAM_VARS[$match[2]]);

											$fkisNull = (($fkisNull) or ((IsEmpty($s_fields_value[$vTable][$tfk])) and (!in_array($tfk, $vFkNull))) );
											$listFk[] = $tfk;  //var with all the foreign keys
											}
										else	{
											//the error must have been reported before....but we go again...
											$vFatalError = true;
											$error = 'The format of the foreign key is incorrect in the xml file: '.$tt1;
											$listFk = array();
											}
										}
									}
								else	{
									$tfk = $match[1];
									$ttp = $match[2];
									$tpk = $match[3];
									$s_fields_value[$vTable][$tfk] = $s_fields_value[$ttp][$tpk];

									$fkisNull = (($fkisNull) or ((IsEmpty($s_fields_value[$ttp][$tpk])) and (!in_array($tpk, $vFkNull))) );
									$listFk[] = $tfk;  //var with all the foreign keys
									}
								}
							}


						//procesing the autonumeric fields...
						foreach (array_keys ($listAuton) as $tt)
							{
							$vcreateGenerator = 0;
							//if the primary key is autonumeric
							if (isset($s_fields_value[$vTable][$tt]))
								{
								if (IsEmpty($s_fields_value[$vTable][$tt]))
									{
									$vcreateGenerator = 1;
									}
								}
							else	{
								$vcreateGenerator = 1;
								}

							if (($vcreateGenerator) and (!IsEmpty($listAuton[$tt])))
								{
								//the value of the primary key autonumeric is null
								//we go to increment it and to obtain the new value...

								//we go to consider that the generator will go to use the same quotes of the table name format....
								$vv = $syntax['table'];
								$vt = $syntax['table'];
								$vf = $syntax['field'];
								$vv = ereg_replace('#1',$listAuton[$tt], $vv);
								$vt = ereg_replace('#1',$vTable, $vt);
								$vf = ereg_replace('#1',$tt, $vf);
								$s_fields_value[$vTable][$tt] = $dbhandle->GenID($vv);

								//Check if exist this value in the database: in this case we go to increase again the value of the generator
								$vSql = 'SELECT * FROM '.$vt.' WHERE '.$vf."=".$s_fields_value[$vTable][$tt];
								$rec = $dbhandle->Execute($vSql);
								while (($rec) && (!$rec->EOF))
									{
									$s_fields_value[$vTable][$tt] = $dbhandle->GenID($vv);
									//Check if exist this value in the database: in this case we go to increase again the value of the generator
									$vSql = 'SELECT * FROM '.$vt.' WHERE '.$vf."=".$s_fields_value[$vTable][$tt];
									$rec = $dbhandle->Execute($vSql);
									}
								//$s_fields_newvalue is a variable used to send the new value to be saved in $s_fields_value when the form is loaded again...
								$s_fields_newvalue[$vTable][$tt] = $s_fields_value[$vTable][$tt];
								}
							}

						//Know the type of every field....
						$vt = $syntax['table'];
						$vt = ereg_replace('#1',$vTable, $vt);
						$vSql = 'SELECT * FROM '.$vt;
						$rec = $dbhandle->SelectLimit($vSql,1);
						for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++) {
							$fld = $rec->FetchField($i);
							$type = $rec->MetaType($fld->type);
							$flds[$fld->name] = $type;
							}
						//Metatypes defined by adoDB
						//C,X,B: Text
						//D, T: Date
						//L: Logic
						//I, N, R :Numeric
						$vType = array();
						$vType['C']="'"; $vType['X']="'"; $vType['B']="'";
						$vType['D']="'"; $vType['T']="'"; $vType['L']="'";
						$vType['I']="";  $vType['N']="";  $vType['R']="";

						//Procesing the primary keys...
						$vWhere = '';
						$first = true;
						foreach ($listPk as $tt)
							{
							if (!isset($s_fields_value[$vTable][$tt]))
								{

								$vFatalError = true;
								$error = 'Is not defined the value of the primary key: '.$tt.' in the table '.$vTable;
								js_alert($error);
								}
							else	{
								$record[$tt] = $s_fields_value[$vTable][$tt];
								$vWhere .= (!$first) ? ' AND ' : '';
								$vf = $syntax['field'];
								$vf = ereg_replace('#1',$tt, $vf);
								$vWhere .= $vf.'='.$vType[$flds[$tt]].$s_fields_value[$vTable][$tt].$vType[$flds[$tt]];
								$first = false;
								}
							}


						if ($vcontinue)
							{
							$vSql = 'SELECT * FROM '.$vt.' WHERE '.$vWhere;
							$rec = $dbhandle->Execute($vSql);

							if ($rec === FALSE) {
								$db_error .= $dbhandle->ErrorMsg();
								echo "<br />Error ejecutando SQL (1): $vSql <br />";
								echo $db_error."<br /><pre>";
								echo "</pre>";
								require('./inc/script_end.inc.php');
								exit;

								$vcontinue = false;
								if ($Confs["DEBUG"] === TRUE) {
									$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
									}
								}
							else	{
								if ($Confs["DEBUG"] === TRUE) {
									$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
									}
								}
							if ($rec->EOF)  {
								$InsertAction = true;
								}
								else
								{
								$InsertAction = false;
								}


							//Check if there is in the table a "listbox_*" type to update every record....
							//We considered that could habe only one "listbox" by table as minimum...
							$vFieldListBox = "";
							$vHuckField = "";

							for ($i = 0; $i < count($s_xml_conf['elements']); $i++)
								{
								$ttable = get_value($s_xml_conf['elements'][$i],'table');

								$tfield = get_value($s_xml_conf['elements'][$i],'field');
								$tSummaryField = get_value($s_xml_conf['elements'][$i],'summaryfield');
								$ttype = get_value($s_xml_conf['elements'][$i],'type');
								$tVirtual = get_value($s_xml_conf['elements'][$i],'virtual');
								$tDelimitedChar = (get_value($s_xml_conf['elements'][$i],'delimitedchar'));
								$tHuckField = get_value($s_xml_conf['elements'][$i],'huckfield');
								$torder = get_value($s_xml_conf['elements'][$i],'orderfield');  //case of is declared a orderfield....
								$vmultiRecords = ((!($tSummaryField==$ttable.'.'.$tfield)) and (!IsEmpty($tHuckField)));
								$tCheckfield = get_value($s_xml_conf['elements'][$i],'check');

								//checking if exist the table and the field in the database....
								if ($tCheckfield=="true")
									{
									$vCheckfieldgo = false;
									for ($t1 = 0; $t1 < count($s_xml_conf['tables']); $t1++)
										{
										if (($ttable == get_value($s_xml_conf['tables'][$t1],'name')) &&
										    (isset($s_xml_conf['tables'][$t1]['field'][$tfield])))
										    {
										    $vCheckfieldgo = true;
										    }
										}
									if (!$vCheckfieldgo)
										{
										$tVirtual = true;
										$tField = '';
										}
									}


								if (($ttable==$vTable) and
									($vmultiRecords) and
									(!$tVirtual) and
									(($ttype=='listbox_listbox') or
									($ttype=='checkbox_multi') or
									($ttype=='listbox_combobox') or
									($ttype=='listbox_textbox_list') or
									($ttype=='listbox_textbox')
									))
									{

									if (isset($s_fields_value[$vTable][$tfield]))
										{
										if ($IsThereMulti)
											{
											$error = 'There is more than one field multirecord in the table...';
											$vFatalError = true;
											}

										$vTemp = $s_fields_value[$vTable][$tfield];
										if (!IsEmpty($s_fields_value[$vTable][$tfield]))
											{
											if (trim($tDelimitedChar)=="")
												{
												$vFieldListBoxVal = array();
												for ($i = 0; $i < strlen($vTemp); $i++)
													{
													$vFieldListBoxVal[]= substr ($vTemp, $i, 1);
													}
												}
											else	{
												$vFieldListBoxVal = split($tDelimitedChar, $vTemp);
												}
											}
										else	{
											$vFieldListBoxVal = '<<IsEmpty>>';
											}

										$vorder = $torder;
										$vFieldListBox = $tfield;
										$vHuckField = $tHuckField;
										$IsThereMulti = 1;
										}
									else	{
										$vFieldListBox = "";
										}
									}
								}

							//Delete the values of the table with "listbox" to reinsert/update after...
							if (!IsEmpty($vFieldListBox))
								{
								//If there is a listbox is mandatory the action "INSERT..." after "Delete..."
								if (IsEmpty($vHuckField))
									{
									$vFatalError = true;
									$error .= 'Don\'t exist the huck field...';
									}
									
								$tt = array(); 
								$tt[] = $vHuckField;
								if (strpos($vHuckField,';')==0)
									{
									$tt = split(";", $vHuckField);
									}
								$vSqlWhere = '';
								
								foreach ($tt as $tt1)
									{
var_dump($vTable, $tt,$s_fields_value[$vTable][$tt1]);
								//exit;
									if (isset($s_fields_value[$vTable][$tt1]))
										{
										if (!(IsEmpty($s_fields_value[$vTable][$tt1])))
											{
											$vSqlWhere = IsEmpty($vSqlWhere)? $tt1."='".$s_fields_value[$vTable][$tt1]."'" : " and ".$tt1."='".$s_fields_value[$vTable][$tt1]."'";
											}
										else	
											{
											$vcontinue = false;
											//echo "val: ".$s_fields_value[$vTable][$tt1];
											$error .= '<br />There is problem with the huck field ['.$vTable.'.'.$tt1.']...';
											}
										}
									else	
										{
										$vcontinue = false;
										$error .= '<br />There is problem with the huck field ['.$vTable.'.'.$tt1.']...';
										}

									}
								$vDBAction[] = 'DELETE FROM '.$vTable.' WHERE '.$vSqlWhere;

								}


							$valpos = 0;

							$valpos++;
						//	$vSql = $InsertAction ? 'INSERT INTO "'.$vTable.'" (' : 'UPDATE "'.$vTable.'" SET ';
							$values = '';
							$first = true;
							$NoBodyNull = false;

							//Procesing the value of the fields not multirecords...
							for ($i = 0; $i < count($s_xml_conf['elements']); $i++)
									{
									$tfield = get_value($s_xml_conf['elements'][$i],'field');
									$ttable = get_value($s_xml_conf['elements'][$i],'table');
									$tfieldst = get_value($s_xml_conf['elements'][$i],'fieldst');
									$tMandatory = (get_value($s_xml_conf['elements'][$i],'mandatory')=='true');
									$tVirtual = get_value($s_xml_conf['elements'][$i],'virtual');
									$tCheckfield = get_value($s_xml_conf['elements'][$i],'check');

									//checking if exist the table and the field in the database....
									if ($tCheckfield=="true")
										{
										$vCheckfieldgo = false;
										for ($t1 = 0; $t1 < count($s_xml_conf['tables']); $t1++)
											{
											if (($ttable == get_value($s_xml_conf['tables'][$t1],'name')) &&
											    (isset($s_xml_conf['tables'][$t1]['field'][$tfield])))
											    {
											    $vCheckfieldgo = true;
											    }
											}
										if (!$vCheckfieldgo)
											{
											$tVirtual = true;
											$tField = '';
											}
										}


									if (	($ttable) and
										(!$tVirtual) and
										($ttable==$vTable)  and
										(isset($s_fields_value[$vTable][$tfield])) and
										($vFieldListBox!=$ttable.'.'.$tfield))
										{
										$val = (IsEmpty($s_fields_value[$vTable][$tfield])) ? 'null' : $s_fields_value[$vTable][$tfield];
										$record[$tfield] = $val;

										if (!in_array($tfield,$listPk))
											{
											$NoBodyNull = (($NoBodyNull) or ($val!='null'));
											}

										//Processing the fieldst tag with additional values of fields...
										if (isset($s_fields_value[$vTable][$tfieldst]))
											{
											$val = (IsEmpty($s_fields_value[$vTable][$tfieldst])) ? 'null' : $s_fields_value[$vTable][$tfieldst];
											$record[$tfieldst] = $val;
											//echo '<br />'. $val.'....'.$dbhandle->qstr($val);
											if (!in_array($tfieldst,$listPk))
												{
												$NoBodyNull = (($NoBodyNull) or ($val!='null'));
												}
											}
										}

									//case of field mandatory and IsEmpty: that is a error...
									if (($ttable==$vTable)  and
										(isset($s_fields_value[$vTable][$tfield])) and
										($tMandatory) and
										(IsEmpty($s_fields_value[$vTable][$tfield])))
										{
										$vcontinue = false;
										$verror = get_value($s_xml_conf['elements'][$i],'content');
										$verror = sprintf($message_strings['E_Mandatory_Empty'], $verror,  $vTable);
										$error .= '<br />'.$verror;
										}
									}

							//Procesing the list of Foreign Keys to insert in the sql sentences....
							foreach ($listFk as $tt)
								if ((!(strpos($vSql,'"'.$tt.'"')>0)) and (isset($s_fields_value[$vTable][$tt])))
									{
									$record[$tt] = $s_fields_value[$vTable][$tt];
									}

							if ($fkisNull)  {
								$warning .= '<br />Is not possible to save because all the foreign keys are null...';
								$vcontinue = false;
								}
							if (!$NoBodyNull) {
								$warning .= '<br /><br />Is not possible to save the table ['.$vTable.'] because all the values are null';
								$vcontinue = false;
								}

							//Procesing the value of the fields multirecords...
							if (!IsEmpty($vFieldListBox))
								{
								$rec = &$dbhandle->Execute('select * from '.$vt.' where '.$vWhere);
								if ($vFieldListBoxVal == '<<IsEmpty>>')
									{
									$vFieldListBoxVal = array();
									$vDBAction[] = $dbhandle->GetInsertSQL($rec, $record, 1);
									}
								else	{
									$vi = 0;
									foreach ($vFieldListBoxVal as $val)
										{
										$record[$vFieldListBox] = $val;
										if (!IsEmpty($vorder))
											{
											$vi++;
											$record[$vorder] = $vi;
											}
										$vDBAction[] = $dbhandle->GetInsertSQL($rec, $record, 1);
										}
									}
								}
							else
								{
								$rec = &$dbhandle->Execute('select * from '.$vt.' where '.$vWhere);
								if ($InsertAction)
									{
									$vDBAction[] = $dbhandle->GetInsertSQL($rec, $record, 1);
									}
								else	{
									$vDBAction[] = $dbhandle->GetUpdateSQL($rec, $record, 1);
									}
								}
							}

					$warning .= "<br />";

							foreach ($vDBAction as $vSql)
								{
								//Delete all the quotes in the table and field names
								$vSql = ereg_replace('"', '', $vSql);
								//Expand the fields to not search in part of their name
								$vSql = ereg_replace('\)', ',)', $vSql);  //insert
								$vSql = ereg_replace('\(', '(,', $vSql);  //insert
								$vSql = ereg_replace('=', ',= ', $vSql);  //update
								while (strpos($vSql,'  ')>-1)
									$vSql = ereg_replace('  ', ' ', $vSql);  //delete the double spaces...
								$vSql = ereg_replace(', ', ',', $vSql);
								$vSql = ereg_replace(' ,', ',', $vSql);

								//insert the format established in the driver to the table
								$vv = $syntax['table'];
								$vv = ereg_replace('#1',$vTable, $vv);
								$vSql = ereg_replace('INSERT INTO '.$vTable, 'INSERT INTO '.$vv, $vSql);
								$vSql = ereg_replace('UPDATE '.$vTable.' SET ', 'UPDATE '.$vv.' SET,', $vSql);
								$vSql = ereg_replace('DELETE FROM '.$vTable, 'DELETE FROM '.$vv, $vSql);
								$vSql = ereg_replace(' WHERE ', ' WHERE,', $vSql);
								$vSql = ereg_replace(' AND ', ' AND,', $vSql);
								//insert the format established in the driver to the field
								foreach ($record as $tfield => $tvalue)
									{
									$vv = $syntax['field'];
									if (($tfield==$vTable) and ($syntax['table']!=$syntax['field']))
										{
										js_alert('ERROR: will have a mistake because there is a field with the same name of the table and both use diferents quotes...');
										exit;
										}
									$vv = ereg_replace('#1',$tfield, $vv);
									$vSql = ereg_replace(','.$tfield.',', ','.$vv.',' , $vSql);
									}
								$vSql = ereg_replace(',\)', ')', $vSql);  //insert
								$vSql = ereg_replace('\(,', '(', $vSql);  //insert
								$vSql = ereg_replace(',=', '=', $vSql);  //update
								$vSql = ereg_replace(',', ', ', $vSql);
								$vSql = ereg_replace(' WHERE,', ' WHERE ', $vSql);
								$vSql = ereg_replace(' AND,', ' AND ', $vSql);
								$vSql = ereg_replace(' SET,', ' SET ', $vSql);

					$warning .= "<br />".$vSql;

								if (($InsertAction ? $NoBodyNull: true) and (!$fkisNull) and ($vcontinue) and (!$vFatalError))
									{
									$dbhandle->StartTrans();
									//$dbhandle->RowLock($vTable,$vWhere);
									$rec = &$dbhandle->Execute($vSql);
									$dbhandle->CompleteTrans();
									if ($rec === FALSE)
										{
										$db_error .= $dbhandle->ErrorMsg();
										echo "<br />Error ejecutando SQL (2): $vSql <br />";
										echo $db_error."<br /><pre>";
										echo "</pre>";
										require('./inc/script_end.inc.php');
										exit;

										$vcontinue = false;
										if ($Confs["DEBUG"] === TRUE)
											{
											$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
											}
										}
									else	{
										if ($Confs["DEBUG"] === TRUE)
											{
											$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
											}
										}

									}
								else	
									{
									$warning .= '<br />The table "'.$vTable.'" was not saved...'."\n";
									}
								if ($db_error != '')
									{
									//js_alert($vTable.'   '.$info_strings['IBError'].': '.$db_error);
									}


							}

						}
					}
				}

			}

		}

	if ($db_error != '') {
		//js_alert($info_strings['IBError']);
		}

	//case of save and after redirect to a new url
	//if (($HTTP_PARAM_VARS['Save'])!=$button_strings['Save'])
	//	{
	//	$vScriptIni .= "\n".' alert("Hemos salvado ya...");';
	//	$vScriptIni .= "\n".' location.href="'.$HTTP_PARAM_VARS['Save'].'";';
	//	}

	//case of we change the record but the last record was not saved and we want to save it....
	 if ($HTTP_PARAM_VARS['Save'] == 'Save')
		{
		unset($HTTP_PARAM_VARS['Save']);
		cleanup_session();
		}
	 elseif ($HTTP_PARAM_VARS['Save'] == 'SaveAndInsert')
		{
		unset($HTTP_PARAM_VARS['Save']);
		$HTTP_PARAM_VARS['Insert'] = 'true';
		cleanup_session();
		}


	} //end of the save section




//delete action section...
if ((isset($HTTP_PARAM_VARS['Delete'])))
	{
	$vSql = '';
	$listPk = array();

	for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
		{
		$vTable = get_value($s_xml_conf['tables'][$t],'name');
		$vPk = get_value($s_xml_conf['tables'][$t],'pk');
		$vFk = get_value($s_xml_conf['tables'][$t],'fk');
		$listFk = array();

		if (isset($s_fields_value[$vTable]))
			{
			$listPk = split(";", $vPk);  //the primary key field list

			if ($vFk)  {  //processing the foreign keys...
				$tt = split(";", $vFk);
				$match = '';
				foreach ($tt as $tt1)  {
					if (!@ereg ('(.+)=(.+)\.(.+)', $tt1, $match))
						{
						if (!@ereg ('(.+)=__(.+)__', $tt1, $match))
							{
							$vFatalError = true;
							$error = 'The format of the foreign key is incorrect in the xml file: '.$tt1;
							$listFk = array();
							}
						else	{
							if ($HTTP_PARAM_VARS[$match[2]])
								{
								$tfk = $match[1];
								$s_fields_value[$vTable][$tfk] = stripcslashes($HTTP_PARAM_VARS[$match[2]]);

								$listFk[] = $tfk;  //var with all the foreign keys
								}
							else	{
								//the error must have been reported before....but we go again...
								$vFatalError = true;
								$error = 'The format of the foreign key is incorrect in the xml file: '.$tt1;
								$listFk = array();
								}
							}
						}
					else	{
						$tfk = $match[1];
						$ttp = $match[2];
						$tpk = $match[3];
						$s_fields_value[$vTable][$tfk] = $s_fields_value[$ttp][$tpk];

						$listFk[] = $tfk;  //var with all the foreign keys
						}
					}
				}


			//Know the type of every field....
			$vt = $syntax['table'];
			$vt = ereg_replace('#1',$vTable, $vt);
			$vSql = 'SELECT * FROM '.$vt;
			$rec = $dbhandle->SelectLimit($vSql,1);
			for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++) {
				$fld = $rec->FetchField($i);
				$type = $rec->MetaType($fld->type);
				$flds[$fld->name] = $type;
				}
			//Metatypes defined by adoDB
			//C,X,B: Text
			//D, T: Date
			//L: Logic
			//I, N, R :Numeric
			$vType = array();
			$vType['C']="'"; $vType['X']="'"; $vType['B']="'";
			$vType['D']="'"; $vType['T']="'"; $vType['L']="'";
			$vType['I']="";  $vType['N']="";  $vType['R']="";


			//Procesing the primary keys...
			$vWhere = '';
			$first = true;
			$vFatalError = FALSE;
			if (count($listPk))
				{
				foreach ($listPk as $tt)
					{
					if (!isset($s_fields_value[$vTable][$tt]))
						{
						echo 'Comment!!! Ist not possible to Delete the element, please add a new line in the configuration file '.$vModName.': <elements><element type="hidden" table="'.$vTable.'" field="'.$tt.'"/>...</elements>';
						$vFatalError = true;
						}
					else	{
						$record[$tt] = $s_fields_value[$vTable][$tt];
						$vWhere .= (!$first) ? ' AND ' : '';
						$vf = $syntax['field'];
						$vf = ereg_replace('#1',$tt, $vf);
						$vFatalError = ( ($vFatalError) and (!empty($s_fields_value[$vTable][$tt])) );
						$vWhere .= $vf.'='.$vType[$flds[$tt]].$s_fields_value[$vTable][$tt].$vType[$flds[$tt]];
						$first = false;
						}
					}

				if (!$vFatalError)
					{
					
					$vSql = 'DELETE FROM '.$vt.' WHERE '.$vWhere;
					$warning .= '<br />'.$vSql;
					$rec = $dbhandle->Execute($vSql);
					}
				}
			}
		}


	$s_active_value = '';

	if (isset($HTTP_PARAM_VARS['SelectSqlParam']))
		{
		unset($HTTP_PARAM_VARS['SelectSqlParam']);
		}

	//We change to insert state and automatically we complete the value of the fields to de default value....
	$HTTP_PARAM_VARS['Insert'] = "true";
	$HTTP_POST_VARS['Insert'] = "true";
	$HTTP_GET_VARS['Insert'] = "true";



	}
//elseif ((isset($HTTP_PARAM_VARS['Delete'])))
//	{
//	$s_active_value = '';
//	$vShowForm = false;
//	$vScriptIni .= "\n".' AskSave("Delete");';
//	}
//end of delete section


//insert action section...
if ((isset($HTTP_PARAM_VARS['Insert'])))
	{
	$s_active_value = '';
	//if ((isset($HTTP_PARAM_VARS['FormChanged'])) and
	//   ($HTTP_PARAM_VARS['FormChanged']=="true"))
	//	{
	//	$vShowForm = false;
	//	$vScriptIni .= "\n".' AskSave("SaveAndInsert");';
	//	}
	//elseif  ((isset($s_FormChanged)) and
	//	($s_FormChanged=="true"))
	//	{
	//	$vShowForm = false;
	//	$vScriptIni .= "\n".' AskSave("SaveAndInsert");';
	//	}
	//else
	//	{
		cleanup_session();
	//	}

	if ($HTTP_PARAM_VARS['data']!='all')
		{
		$HTTP_PARAM_VARS['data']='';
		}

	}  //end of insert section

$HTTP_PARAM_VARS['FormChanged']="false";
$s_FormChanged = "false";


if (!array_key_exists('data', $vComponents))
	{
	$vComponents['data'] = $HTTP_PARAM_VARS['data'];
	$vHTML .= '<input name="data" type="hidden" value="'.$HTTP_PARAM_VARS['data'].'" />';
	//$vSearch .=  '<input name="data" type="hidden" value="'.$HTTP_PARAM_VARS['data'].'" />';
	$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&").'data='.$HTTP_PARAM_VARS['data'];
	}
	
if (isset($HTTP_PARAM_VARS['toiframe']))
	{
	$vHTML .= '<input name="toiframe" type="hidden" value="1" />';
	}

//
// script is called from main form
// File xml with configuration to process
if (!$vFatalError)
	{

	//Form of the fields section..
	//$vHTML =  '<form action="run.php" method="post" enctype="application/x-www-form-urlencoded" name="Form">';
	//$vHTML =  '<form action="run.php" method="post" enctype="multipart/form-data" name="Form">';
//-----------------------------------------------------------------------------------------
	//Process the xml file...search the main tag
	//$root = $dom->document_element();

	//Procesing the search files...
	$vSqlSearch = '';
	$vQueryFrom= "";
	$vQueryId= "";

	//Process every "Search" tag

	$HTTP_PARAM_VARS['query_dest'] = (!isset($HTTP_PARAM_VARS['query_dest']))? '': $HTTP_PARAM_VARS['query_dest'];
	$HTTP_PARAM_VARS['query_run'] = (!isset($HTTP_PARAM_VARS['query_run']))? '': $HTTP_PARAM_VARS['query_run'];
	if (isset($HTTP_PARAM_VARS['query_runClicked']))
		{
		if (empty($HTTP_PARAM_VARS['query_runClicked']))
			{
			unset($HTTP_PARAM_VARS['query_runClicked']);
			}
		}

	if (isset($HTTP_PARAM_VARS['leavequery']))
		{
		unset($HTTP_PARAM_VARS['quickaction']);
		unset($HTTP_PARAM_VARS['query_run']);
		}
	$HTTP_PARAM_VARS['query_run'] = (isset($HTTP_PARAM_VARS['query_runClicked']))? $HTTP_PARAM_VARS['query_runClicked']: $HTTP_PARAM_VARS['query_run'];
	$HTTP_PARAM_VARS['SelectSqlParam'] = (!isset($HTTP_PARAM_VARS['SelectSqlParam']))? '': $HTTP_PARAM_VARS['SelectSqlParam'];

	for ($i = 0; $i < count($s_xml_conf['searchs']); $i++)
		{  //ini of the for

		// is a sql sentence?
		if (get_value($s_xml_conf['searchs'][$i],'tagname')=='sql')
			{
			$vQueryWhere = '';
			$vquickquery = false;
			
	
			
			//the sql tag must be in the end of all tag childs of the search tag...
			if ( 	(!(isset($HTTP_PARAM_VARS['query_runClicked'])) ) 
					&&
					( (isset($HTTP_PARAM_VARS['quickaction'])) &&  //is there a quick query...
					(isset($HTTP_PARAM_VARS['searchquick'])) &&
					(!empty($HTTP_PARAM_VARS['searchquick'])) ) 
				)
				{
				$vquickquery = $HTTP_PARAM_VARS['searchquick'];
				if (!strpos($vquickquery,'%') )
					{
					$vquickquery = "%".trim($vquickquery)."%";
					}
				
				$vSqlSearch = get_value($s_xml_conf['searchs'][$i],'quickquery');
				$vQueryWhere = ereg_replace("__textUp__", strtoupper($vquickquery), get_value($s_xml_conf['searchs'][$i],'wherequickquery'));
				$vQueryWhere = ereg_replace("__text__", ($vquickquery), $vQueryWhere);
				$HTTP_PARAM_VARS['query_dest']='form';
								
				//echo htmlentities($vQueryWhere);
				$vquickquery = true;
				$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&").'searchquick='.$HTTP_PARAM_VARS['searchquick']."&quickaction=".$HTTP_PARAM_VARS['quickaction'];
				}
			else
				{
				$vSqlSearch = get_value($s_xml_conf['searchs'][$i],'content');
				unset($HTTP_PARAM_VARS['quickaction']);
				}

			$match = '';


			//if (@preg_match('|select .+ from (.+) where (.*)=.*|Ui', $vSqlSearch, $match))
			if (@preg_match('|.*select .+ from (.+)( where .*)?|im', $vSqlSearch, $match))
				{

				if ( (($HTTP_PARAM_VARS['query_dest']=='datagrid') or ($HTTP_PARAM_VARS['query_dest']=='excel'))and (isset($HTTP_PARAM_VARS['query_runClicked'])) )
					{

					$visFoward = true;
					$vQuery = $syntax['select-distinct'];
					if ( (isset($s_xml_conf['showqueryfields'])) and (count($s_xml_conf['showqueryfields'])>0) )
						{

						$vfieldshow = '';
						for ($va1 = 0; $va1 < count($s_xml_conf['showqueryfields']); $va1++)
							{
							$vShowDescriptor = false;
							$vShowDescLink = false;

							$vposi = $s_xml_conf['showqueryfields'][$va1]['position'];
							$valias = $s_xml_conf['showqueryfields'][$va1]['name'];

							$vattrib = $syntax['attribute'];
							$vattrib = ereg_replace('#1',$s_xml_conf['elements'][$vposi]['table'], $vattrib);
							if ( ($vShowDescriptor) )
								{

								if ( (isset($s_xml_conf['elements'][$vposi]['fieldst'])) &&
									 ($s_xml_conf['elements'][$vposi]['type']=='textbox_list'))
									{
									$vattrib = ereg_replace('#2',$s_xml_conf['elements'][$vposi]['fieldst'], $vattrib);
									}
								else
									{
									//echo "<br /> estamos 11". $s_xml_conf['showqueryfields'][$va1]['name'];
									if ( (isset($s_xml_conf['elements'][$vposi]['id'])) and
									     (isset($s_xml_conf['elements'][$vposi]['desc'])) and
									     (isset($s_xml_conf['elements'][$vposi]['sql'])) and
									     ($s_xml_conf['elements'][$vposi]['type']!='textbox_combobox')
									      )
										{
										$vvmatch = array();
										$vvtemp = strtolower($s_xml_conf['elements'][$vposi]['sql']);
										//echo "<br />...".$vvtemp."...<br />";
										if ( (@preg_match('|.*select (.+) from (.+)|im', $vvtemp, $vvmatch)) )
											{

											//print_r($vvmatch);
											$vfieldpart = array();
											$vfieldpos = 0;
											$vfieldpart[$vfieldpos] = '';
											$st = $vvmatch[1];
											$stcantopen = 0;
											$stopen = false;

											for ($vpp=0; $vpp<strlen($st); $vpp++)
												{
												if (substr($st,$vpp,1)=="'")
													{
													$stopen = (!$stopen);
													$stcantopen = ($stopen)? $stcantopen+1: $stcantopen-1;
													}
												if ( (substr($st,$vpp,1)=="(") and (!$stopen) )
													{
													$stcantopen = $stcantopen+1;
													}
												if ( (substr($st,$vpp,1)==")") and (!$stopen) )
													{
													$stcantopen = $stcantopen-1;
													}

												if ( (substr($st,$vpp,1)==',') and ($stcantopen==0) )
													{
													$vfieldpos++;
													$vfieldpart[$vfieldpos] = '';
													}
												else
													{
													$vfieldpart[$vfieldpos] .= substr($st,$vpp,1);
													}
												}


											$vvDesc = ereg_replace('#1',$s_xml_conf['elements'][$vposi]['desc'], $syntax['field']);
											$vvId = ereg_replace('#1',$s_xml_conf['elements'][$vposi]['id'], $syntax['field']);

											//echo '<br /><br />';
											//print_r($vfieldpart);
											for ($vpp=0; $vpp<count($vfieldpart); $vpp++)
												{
												$CharAlias = $syntax['alias-char'];

												$vfieldpart[$vpp] = ereg_replace(strtoupper('$CharAlias'),strtolower('$CharAlias'), $vfieldpart[$vpp]);
												$tt = split(strtolower('$CharAlias'), $vfieldpart[$vpp]);
												if (count($tt)>1)
													{
													if ($tt[1]==$vvDesc)
														{
														$vvDesc = $tt[0];

														}
													elseif ($tt[1]==$vvId)
														{
														$vvId = $tt[0];
														}
													}
												}
											//echo '...'.$vvId;
											$tt = $vvmatch[2];

											if (strpos(strtoupper($tt),' ORDER ')>0)
												{
												$vvmatch[2] = substr($tt,0,strpos(strtoupper($vvmatch[2]),' ORDER '));
												}
											if (strpos(strtoupper($tt),' WHERE '))
												{
												$vvmatch[2] .= ' and ';
												}
											else
												{
												$vvmatch[2] .= ' where ';
												}
											$vattrib = '(select '.$vvDesc.' from '.$vvmatch[2].' '.$vvId.'='.ereg_replace('#2',$s_xml_conf['elements'][$vposi]['field'], $vattrib).')';

											}
										else
											{
											$vattrib = ereg_replace('#2',$s_xml_conf['elements'][$vposi]['field'], $vattrib);
											}
										}
									else
										{
										$vattrib = ereg_replace('#2',$s_xml_conf['elements'][$vposi]['field'], $vattrib);
										}
									}
								}
							else
								{
								$vattrib = ereg_replace('#2',$s_xml_conf['elements'][$vposi]['field'], $vattrib);
								}

							$vId = ereg_replace('#1',$vattrib, $syntax['alias']);
							$vId = ereg_replace('#2',$syntax['field'], $vId);

							$vId = ereg_replace('#1',$valias,$vId);

							$vfieldshow .= (empty($vfieldshow))? $vId: ', '.$vId;


							}

						$vQuery = ereg_replace('#1', $vfieldshow, $vQuery);

						}
					else
						{
						$vQuery = $syntax['select-distinct'];
						$vQuery = ereg_replace('#1', $vQueryMark, $vQuery);

						}

					$vQuery = ereg_replace('#2', $match[1], $vQuery);


					}
				else
					{
					$vTT = $syntax['alias']; // '#1 #2';
					$vId = ereg_replace('#1',get_value($s_xml_conf['searchs'][$i],'id'), $vTT);
					$vDesc = ereg_replace('#1',get_value($s_xml_conf['searchs'][$i],'desc'),$vTT );
					$vId = ereg_replace('#2',$syntax['field'], $vId);
					$vDesc = ereg_replace('#2',$syntax['field'], $vDesc);
					$vId = ereg_replace('#1','Id', $vId);
					$vDesc = ereg_replace('#1','Desc', $vDesc);

					$vQueryId = $vId.', '.$vDesc;

					$vQuery = $syntax['select-distinct'];
					$vQuery = ereg_replace('#1',$vQueryId , $vQuery);

					$vQueryFrom= $match[1];


					$vQuery = ereg_replace('#2', $vQueryFrom, $vQuery);
					}

				$vOrderBy = get_value($s_xml_conf['searchs'][$i],'orderby');

				//echo $vQuery;
				//$s_xml_conf['elements'][$velement]['q_conditions'][$vinModName][$vcondition]['value'] = $vvalue;
				$cantlevel = 1;
				$vQueryCond = '';
				$vQueryWherePart = '';
				$vTablesInCond = array();


				if ( (!empty($HTTP_PARAM_VARS['query_run'])) && (!$vquickquery) )
					{

					for ($level=1; $level <= $cantlevel; $level++)
						{
						$vQueryCond = '';
						for ($vnelem=0, $max=count($s_xml_conf['elements']); $vnelem < $max; $vnelem++)
							{

							if (isset($s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName]))
								{

								$vQueryCondField = '';
								for ($vcond=0; $vcond < count($s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName]); $vcond++)
									{

									$cantlevel = ($s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['level']>$cantlevel)? $s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['level']: $cantlevel ;
									$vSqlGo = true;
									if ($s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['level']==$level)
										{
										$vtable = $s_xml_conf['elements'][$vnelem]['table'];
										$vTablesInCond[$vtable] = 1;
										$vfieldst = get_value($s_xml_conf['elements'][$vnelem],'fieldst');
										$vtype = $s_xml_conf['elements'][$vnelem]['type'];
										$vValueBefore = '';
										$vValueAfter = '';
										$vFieldBefore = '';
										$vFieldAfter = '';
										$match = '';
										
										if ( 	($vtype=='textbox_list') && 
												(isset($s_xml_conf['elements'][$vnelem]['field'])) && 
												(isset($s_xml_conf['elements'][$vnelem]['id'])) && 
												($s_xml_conf['elements'][$vnelem]['field'] == $s_xml_conf['elements'][$vnelem]['id']) )
											$vtype='textbox';
								
										if ( ($vtype=='textbox_list') )
											{
											if ( (!empty($vfieldst)) and ($s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['uselink']=='0') )
												{
												$vfield = $s_xml_conf['elements'][$vnelem]['fieldst'];
												$vvalue = $s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['value'];
												$vcomparison = $binaryCOP[$s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['comparison']];
												}

											elseif (@preg_match('|.*select .+ from (.+) (where(.*))?|Uim', $s_xml_conf['elements'][$vnelem]['sql'], $match))
												{
												$vvalue = $s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['value'];
												$vfield = $s_xml_conf['elements'][$vnelem]['field'];
												$vcomparison = $binaryCOP[$s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['comparison']];

												$vtid = ereg_replace('#1', $s_xml_conf['elements'][$vnelem]['id'], $syntax['field']);
												$vValueBefore ='('.ereg_replace('#1', $vtid, $syntax['select-distinct']);
												$vValueBefore = ereg_replace('#2', $match[1], $vValueBefore);
												$vValueBefore = ereg_replace('#1', $vValueBefore,$syntax['where']);
												$vtdesc = ereg_replace('#1', $s_xml_conf['elements'][$vnelem]['desc'], $syntax['field']);
												$vtdesc .= ' '.$vcomparison.' ';
												$vValueBefore = ereg_replace('#2', $vtdesc,$vValueBefore);
												$vValueAfter = ')';
												var_dump($vvalue);
												if ( (!array_key_exists($vvalue, $unaryCOP)) && ($vcomparison == $binaryCOP['like']) && (strpos($vvalue,$syntax['wildcard'])==-1))
													{
													//$vvalue = " '".$syntax['wildcard'].$vvalue.$syntax['wildcard']."'";
													$vvalue = "".$syntax['wildcard'].$vvalue.$syntax['wildcard']."";
													}
												$vcomparison = $multipleCOP['in'];
												}
											else	
												{
												$vSqlGo = false;
												$db_error .= "Is not posible create the sql sentence because is not possible process the field: ".$s_xml_conf['elements'][$vnelem]['content']." with the sql sentence: ".$s_xml_conf['elements'][$vnelem]['sql'];
												}

											}
										elseif ( ($vtype=='checkbox_multi') or
											($vtype=='listbox_listbox') or
											($vtype=='listbox_combobox') or
											($vtype=='listbox_textbox_list') or
											($vtype=='listbox_textbox')
											)
											{
											$vfield = $s_xml_conf['elements'][$vnelem]['field'];
											$vvalue = $s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['value'];
											$vcomparison = $binaryCOP[$s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['comparison']];
											if (!(array_key_exists($vvalue, $unaryCOP)))
												{
												if ($s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['comparison']=='notequals')
													{
													$vcomparison = $unaryLOP["not"].' '.$binaryCOP['like'];
													}
												else	
													{
													$vcomparison = $binaryCOP['like'];
													}
												$vDelimitedChar = get_value($s_xml_conf['elements'][$vnelem],'delimitedchar');
												$vDelimitedChar (empty($vDelimitedChar)) ? "," : $vDelimitedChar;
												$vvalue = $vDelimitedChar.$vvalue.$vDelimitedChar;
											
echo "valor: $vvalue<hr/>";								

												//$vfield = ereg_replace('#2', "'".$vfield."'", $syntax['concatenation-string']);
												//$vfield = ereg_replace('#1', "'".$syntax['wildcard'].",'", $vfield);
											
												//$vfield = ereg_replace('#1', $vfield, $syntax['concatenation-string']);	
												//$vfield = ereg_replace('#2', "',".$syntax['wildcard']."'", $vfield);												
							
												//$vValueBefore = " '".$syntax['wildcard'].",' ".$syntax['concatenation-string']." ";
												//$vValueAfter = 	" ".$syntax['concatenation-string']." ',".$syntax['wildcard']."' ";
												//$vFieldBefore = " ',' ".$syntax['concatenation-string']." ";
												//$vFieldAfter =  " ".$syntax['concatenation-string']." ',' ";
												}

											}
										else	
											{
											$vfield = $s_xml_conf['elements'][$vnelem]['field'];
											$vvalue = $s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['value'];
											$vcomparison = $binaryCOP[$s_xml_conf['elements'][$vnelem]['q_conditions'][$vinModName][$vcond]['comparison']];
											}

										//Process the conditions...
										if ($vSqlGo)
											{
											$vQueryCondField .= (!empty($vQueryCondField))? ' or ': '';
											$vQueryCondField .= $vFieldBefore.ereg_replace('#1', $vtable, $syntax['attribute']);
											$vQueryCondField = ereg_replace('#2', $vfield, $vQueryCondField).$vFieldAfter;
											if ( (!array_key_exists($vvalue, $unaryCOP)) and ($vcomparison == $binaryCOP['like']) and (strpos($vvalue,$syntax['wildcard'])==-1) and (empty($vValueBefore)) and (empty($vValueAfter)) )
												{
												$vvalue = $syntax['wildcard'].$vvalue.$syntax['wildcard'];
												}
											//To know the type of the field used....
											$vsyntax = $syntax['string'];
											for ($to=0; $to < count($s_xml_conf['tables']); $to++)
												if ($s_xml_conf['tables'][$to]['name']==$s_xml_conf['elements'][$vnelem]['table'])
													{
													if (isset($s_xml_conf['tables'][$to]['field'][$vfield]))
														{
														if (	($s_xml_conf['tables'][$to]['field'][$vfield]=='I') or
															($s_xml_conf['tables'][$to]['field'][$vfield]=='R') or
															($s_xml_conf['tables'][$to]['field'][$vfield]=='N')	)
															{
															$vsyntax = $syntax['number'];

															}
														$to = count($s_xml_conf['tables']);
														}
													}

											$vQueryCondField .= (array_key_exists($vvalue, $unaryCOP))? ' '.$unaryCOP[$vvalue].' ':' '.$vcomparison.' '.$vValueBefore.ereg_replace('#1', $vvalue, $vsyntax).$vValueAfter;
											}


										}
									}
								if (!empty($vQueryCondField))
									{
									$vQueryCond .= (!empty($vQueryCond))? ' and ': '';
									$vQueryCond .= ereg_replace('#1', $vQueryCondField, $syntax['brackets']);
									}
								}
							}

						if (!empty($vQueryCond))
							{

							$vQueryWhere .= (!empty($vQueryWhere))? ' '.$binaryCOP["and"].' ': '';
							$vQueryWhere .= ereg_replace('#1', $vQueryCond, $syntax['brackets']);

							}
						echo "<hr />$vQueryWhere";

						}


					//Someone ask me about to return a sql?
					if ( isset($HTTP_PARAM_VARS['giveme_sql']) )
						{
						echo '<?xml version="1.0" encoding="iso-8859-1"?>';
						if ( !empty($db_error) )
							{
							echo "<sql error='".urlencode($db_error)."' module='".$vModName."' in_module='".$vinModName."' tables='' where='' />";
							}
						else	{
							$i = 0;
							$vtables = "";
							foreach ($vTablesInCond as $vkey => $vval)
								{
								$vtables .= ($i>0) ? ";" : "";
								$vtables .= $vkey;
								$i++;
								}
							echo "<sql error='' module='".$vModName."' in_module='".$vinModName."' tables='".urlencode($vtables)."' where='".urlencode($vQueryWhere)."' />";
							}
						exit;
						}
					else	
						{ //maybe there is a SQL query in other module
						//echo "<br /><pre>"; print_r($s_xml_conf['QueryModRelations']); echo "</pre>";
						//echo htmlentities($vQueryWhere);
						for ($i=0; $i < count($s_xml_conf['QueryModRelations']); $i++)
							{

							$vRelModule = ($s_xml_conf['QueryModRelations'][$i]['moduleMaster']==$xmlFile) ? ($s_xml_conf['QueryModRelations'][$i]['moduleDetail']) : "";
							$vRelModule = (empty($vRelModule)) ? (($s_xml_conf['QueryModRelations'][$i]['moduleDetail']==$xmlFile) ? ($s_xml_conf['QueryModRelations'][$i]['moduleMaster']) : "") : $vRelModule;

							if ( !empty($vRelModule) )
								{


								$xml_sqlresult = "query_".($pathModPart["dirname"])."_".(ereg_replace('\.','_', $vRelModule));



								//echo " estamos procesando todo...".$xml_sqlresult;

								if ( (isset($HTTP_PARAM_VARS["query_runClicked"])) &&
									($HTTP_PARAM_VARS["query_runClicked"] == "go") &&
									  (isset($HTTP_PARAM_VARS[$xml_sqlresult.'_result'])) )
									{
									unset($HTTP_PARAM_VARS[$xml_sqlresult.'_result']);
									}

								if (isset($HTTP_PARAM_VARS[$xml_sqlresult.'_result']))
										{
										$HTTP_PARAM_VARS[$xml_sqlresult] = $HTTP_PARAM_VARS[$xml_sqlresult.'_result'];
										}

								if (isset($HTTP_PARAM_VARS[$xml_sqlresult]))
									{


									//echo "<br />uno mas...".htmlentities(urldecode( urldecode($HTTP_PARAM_VARS[$xml_sqlresult])) );

									$match = array();
									$xml_sqlresult_value = htmlentities( urldecode( $HTTP_PARAM_VARS[$xml_sqlresult]) ) ;

									if (@preg_match("|.*tables='(.+)'.*where='(.+)'.*|im", $xml_sqlresult_value, $match))
										{
										//echo " estamos procesando todo...".$xml_sqlresult."....".$xml_sqlresult_value;
										$vHTML .= "<input name='".$xml_sqlresult."_result' value='".htmlentities($HTTP_PARAM_VARS[$xml_sqlresult])."' type='hidden' />";

										//echo "<br /><br /><br /> <br />from: ".htmlentities(urldecode($s_xml_conf['QueryModRelations'][$i]['relation'][0]['from']));
										//echo "<br /><br /><br />   tables: ".urldecode($match[1]);
										$vtablesInCond = split(";", urldecode($match[1]));
										//echo "<br />WherePart: ".urldecode($match[2]);
										if (!empty($vQueryWhere))
											{
											$vQueryWhere .= " and ";
											}
										$vQueryWhere .= urldecode($match[2]);

										$vWherePartInCond = urldecode($match[2]);
										//print_r($match);

										//$vSqlSearch = "SELECT * FROM ".urldecode($s_xml_conf['QueryModRelations'][$i]['relation'][0]['from']);
										$vQueryId = (empty($vQueryId)) ? $vQueryMark : $vQueryId;
										$vQuery = $syntax['select-distinct'];
										$vQuery = ereg_replace('#1',$vQueryId , $vQuery);
										$vQueryFrom= urldecode($s_xml_conf['QueryModRelations'][$i]['relation'][0]['from']);
										$vQuery = ereg_replace('#2', $vQueryFrom, $vQuery);

										}
									}

								}
							}
						}

					}


				if (!empty($vQueryWhere))
					{  //$vQuery have the query without the where part....
					   //$vQueryWhere have the where part
					$vQueryWherePart = $vQueryWhere;
					$vv = $vQueryWhere;
					$vQueryWhere = ereg_replace('#1', $vQuery, $syntax['where']);
					$vQueryWhere = ereg_replace('#2', $vv, $vQueryWhere);
					}
				else
					{
					$vQueryWhere = $vQuery;
					}


				$warning .= '<br />Consulta final: '.$vQueryWhere.'<br />';


				}
			else
				{
				$db_error .= 'There is some problem with the Query declared in the xml conf file....['.$vSqlSearch.']';
				exit;
				}


			if (($HTTP_PARAM_VARS['query_dest']=='form') and
				((!empty($HTTP_PARAM_VARS['query_run'])) or ($vquickquery)) )
				{
				if 	(isset($HTTP_PARAM_VARS['quickaction']))
					{
					$vHTML .=  '<input name="quickaction" type="hidden" value="'.$HTTP_PARAM_VARS['quickaction'].'" />';
					}
				$vHTML .=  '<input name="query_run" type="hidden" value="'.$HTTP_PARAM_VARS['query_run'].'" />';
				$vHTML .=  '<input name="query_dest" type="hidden" value="'.$HTTP_PARAM_VARS['query_dest'].'" />';
				$vSearch .=  '<br /> '.$button_strings['QueryResult'];
				$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&").'query_run='.$HTTP_PARAM_VARS['query_run'].'&query_dest=form';
				}
			elseif (($HTTP_PARAM_VARS['query_dest']=='datagrid') and
				(isset($HTTP_PARAM_VARS['query_runClicked'])) )
				{
				$vShowForm = false;
				$vScriptIni .= "\n location.href=".'"'.'PreQuery.php?data=results&mod='.$vModName.'&s_id='.$HTTP_PARAM_VARS['s_id'].'&page_status=searching&sql='.urlencode($vQueryWhere).'"; ';
				}
			elseif (($HTTP_PARAM_VARS['query_dest']=='excel') and
				(isset($HTTP_PARAM_VARS['query_runClicked'])) )
				{
				$vScriptIni .= "\n location.href=".'"'.'PreQuery.php?data=results&mod='.$vModName.'&s_id='.$HTTP_PARAM_VARS['s_id'].'&page_status=searching&ExportAction=true&sql='.urlencode($vQueryWhere).'"; ';
				}
			else
				{
				$vSearch .=  '<br /> '.get_value($s_xml_conf['searchs'][$i],'msg');
				}



			if (!empty($vOrderBy))
				{
				$vOrderBy = ' order by '.$vOrderBy;
				}

			if ($vShowForm)
				{

				$vfirst = true;
				while ($vfirst)
					{
					$vtQueryCountRun = $vQueryCountRun+1;
					$vtini = $vini;
					if ($vini>0)
						{
						$vtQueryCountRun++;
						$vtini--;
						}


					if (($HTTP_PARAM_VARS['query_dest']=='form') and
						( (!empty($HTTP_PARAM_VARS['query_run'])) or ($vquickquery) ) )
						{
						$vSql1 = $vQueryWhere.$vOrderBy;
						$rec = &$dbhandle->SelectLimit($vSql1,$vtQueryCountRun,$vtini);
						//$rec = &$dbhandle->Execute($vQueryWhere.' '.$vOrderBy);
						}
					else	{
						$vSql1 = $vQuery.$vOrderBy;
						$rec = &$dbhandle->SelectLimit($vSql1,$vtQueryCountRun,$vtini);
						//$rec = &$dbhandle->Execute($vQuery.' '.$vOrderBy);
						}


					if ($rec === FALSE)
						{
						$db_error .= $dbhandle->ErrorMsg();

						echo "<br />Error en consulta principal:<br />$vSql1...".$dbhandle->ErrorMsg();
						require('./inc/script_end.inc.php');
						exit;
						if ($Confs["DEBUG"] === TRUE)
							{
							$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql1 . '[Error]';
							}
						}
					else	
						{
						if ($Confs["DEBUG"] === TRUE)
							{
							$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql1.' [Ok]';
							}
						if (($rec->EOF) and ($vini>0))
							{
							$vfirst = true;
							$vini = 0;
							}
						else	
							{
							$vfirst = false;
							}

	
						
							
						}


					}

					
				if (!($rec === FALSE))
					{
					$vId = 'Id';
					$vDesc = 'Desc';
					$vSelected = '';
					$vLinkLast = '';
					$vLinkNext = '';
					$vIsSelected = false;
					$vName = 'SelectSqlParam';

					if ((isset($HTTP_PARAM_VARS['Insert'])) and (isset($HTTP_PARAM_VARS[$vName])))
						{
						unset($HTTP_PARAM_VARS[$vName]);
						}

					$vTableId = $s_xml_conf['searchs'][0]['tableid'];
					$vFieldId = $s_xml_conf['searchs'][0]['fieldid'];

					if ((isset($HTTP_PARAM_VARS[$vName])) and
					   (!IsEmpty($HTTP_PARAM_VARS[$vName])))
						{
						$vSelected =$HTTP_PARAM_VARS[$vName];

						$checkSearch = true;
						if (((!isset($s_fields_value[$vTableId][$vFieldId])) or
							(IsEmpty($s_fields_value[$vTableId][$vFieldId]))) and
							(!IsEmpty($vTableId)))
							{

							$s_fields_value[$vTableId][$vFieldId] = stripcslashes($HTTP_PARAM_VARS[$vName]);
							}
						elseif	(((isset($s_fields_value[$vTableId][$vFieldId]))) and
							(!IsEmpty($s_fields_value[$vTableId][$vFieldId])) and
							(!IsEmpty($vTableId)))
							{
							$vIsDiferentRecord = (($vIsDiferentRecord) or ($s_fields_value[$vTableId][$vFieldId]!=$HTTP_PARAM_VARS[$vName]));
							}
						}
					elseif ((!IsEmpty($vTableId)) and (!IsEmpty($vFieldId)))
						{
						if ((isset($s_fields_value[$vTableId][$vFieldId])) and
							(!IsEmpty($s_fields_value[$vTableId][$vFieldId])))
							{
							$vSelected =$s_fields_value[$vTableId][$vFieldId];
							$checkSearch = true;
							$HTTP_PARAM_VARS[$vName] = $s_fields_value[$vTableId][$vFieldId];
							}
						}


					$viniLast = ($vini-$vQueryCountRun<=0) ? 0: $vini-$vQueryCountRun;
					$viniNext = $vini+$vQueryCountRun;

					if (!$rec->EOF) 
						{
						$vtSearch =  '<select name="'.$vName.'" size="1" >'."\n";
						$vtSearch .=  '<option value="" >&nbsp;</option>'."\n";
						$vFirstResult = trim($rec->fields[$vId]);

						$vposi = $vini;
						$vtini = $vini;

						if ($vini>0)
							{
							$vvvalue = trim($rec->fields[$vId]);
							$vt = ($vini-$vQueryCountRun<=0) ? 0: $vini-$vQueryCountRun;
							$vLinkLast = 'run.php?'.$vParamFlow.'&Search=Go&ini='.($vt).'&'.$vName.'='.$vvvalue.'#markGo' ;
							$rec->MoveFirst();
							//$vtini++;
							}

					
						$vtSearchoptions = "";
						$vfirstrecordvalue = false;  //With this variable we make the posibility to access direct to the first element when theris not a element selected...
						$vShowValue = false;

						while ( (!$rec->EOF) )
							{
							$vvvalue = trim($rec->fields[$vId]);
							$vposi++;
							$vfirstrecordvalue = ($vfirstrecordvalue)? $vfirstrecordvalue : $vvvalue;
							
							$ventered = 1;
							//if the show values is equal to the field value, select this...
							if ($vposi<=$vtini+$vQueryCountRun)
								{
								if ((trim($vSelected)==trim($vvvalue))  and (strlen($vSelected)>0))
									{
									$SelectedValue = ($vposi).'. '.$rec->fields[$vDesc];
									$vtSearchoptions .=  '<option value="'.$vvvalue.'" selected>'.$rec->fields[$vDesc]."</option>\n";
									//to take the value of the search field in the main form if there is update, save...
									$vIsSelected = true;
									$vfirstrecordvalue = false;

									if (strlen($SEARCH_PARAM)>0)
										{
										$SEARCH_PARAM = $SEARCH_PARAM .'&'.$vName.'='.$vvvalue;
										}
									else  	{
										$SEARCH_PARAM = $vName.'='.$vvvalue;
										}

									}
								 else
									{
									if (!$vIsSelected)
										{
										$vLinkLast = 'run.php?'.$vParamFlow.'&Search=Go&ini='.($vini).'&'.$vName.'='.$vvvalue.'#markGo' ;
										}
									elseif (($vIsSelected) and (IsEmpty($vLinkNext)))
										{
										$vLinkNext = 'run.php?'.$vParamFlow.'&Search=Go&ini='.($vini).'&'.$vName.'='.$vvvalue.'#markGo';
										}
									$vtSearchoptions .=  '<option value="'.$vvvalue.'" >'.$rec->fields[$vDesc]."</option>\n";
									}
								}
							else	{
								if (($vIsSelected) and (IsEmpty($vLinkNext)))
									{
									$vLinkNext = 'run.php?'.$vParamFlow.'&Search=Go&ini='.($vini+$vQueryCountRun).'&'.$vName.'='.$vvvalue.'#markGo';
									}
								}
							$rec->MoveNext();

							//if is a "quick search" show automatically the first value found....
							if ( (!$vIsSelected) && 
								 (($rec->EOF)) && 
								 (!isset($HTTP_PARAM_VARS['Insert'])) && 
								 (!isset($_GET["SelectSqlParam"])) && 
								 (!isset($_GET["toiframe"])) &&
								 (!$vShowValue))
								{  
								$vShowValue = true;
								$rec->MoveFirst();
								$vposi = 0;
								$HTTP_PARAM_VARS['data'] = ($HTTP_PARAM_VARS['data']=='all') ? $HTTP_PARAM_VARS['data']: "";
								$HTTP_PARAM_VARS["SelectSqlParam"] = $vfirstrecordvalue;
								$HTTP_PARAM_VARS['Search'] = '1';
								$HTTP_POST_VARS['Search'] = '1';	
								$vSelected  = $vfirstrecordvalue;
								$vfirstrecordvalue = false;
								$vtSearchoptions = "";
								$checkSearch = true;
								}						
							
							}
	
						$vtSearch .= $vtSearchoptions."</select>\n";
						unset($vtSearchoptions);

						if (($vini>0) or ($vposi>$vtini+$vQueryCountRun))
							{
							if  ($vini>0)
								{
								$vSearch .=  '<a href="javascript:GoPos('.$viniLast.');"> &lt;&lt; </a>';
								}
							else	{
								$vSearch .=  '      &lt;&lt;';
								}
							$vSearch .=  '<input name="ini" size="6" type="text" value="'.$vini.'" onChange="javascript:GoPos(this.value);" />';

							if ($vposi>$vtini+$vQueryCountRun)
								{
								$vSearch .=  '-'.($vini+$vQueryCountRun).'<a href="javascript:GoPos('.$viniNext.');"> &gt;&gt; </a>';
								}
							else
								{
								$vSearch .=  '-'.($vini+$vQueryCountRun).' &gt;&gt; ';
								}
							}
						$vSearch .= $vtSearch;
						unset($vtSearch);
						$vSearch .=  '<input name="Search" type="submit" value="'.$button_strings['Select'].'" onClick="'."javascript:showWaitScreen('".$message_strings['Searching']."')".';" />';		
						}
					else //if ( (isset($HTTP_PARAM_VARS['Search'])) || ($HTTP_PARAM_VARS['quickaction']) )
						{
						
						//js_alert('No se ha encontrado ningún registro que cumpla con las condiciones de la consulta...');
						$vv = $vParamFlow;
						$vv = ereg_replace('query_run='.$HTTP_PARAM_VARS['query_run'],'', $vv);
						$vv = ereg_replace('query_dest=form','', $vv);
						$vv = ereg_replace('&&','&', $vv);
						$vv = ereg_replace('&&','&', $vv);
						$vSearch = '<hr /><p class="TableComment" >'.htmlentities($message_strings["QueryRecordNotFound"]).'</p>';
						$vSearch .= $Confs["LinkIcoBefore"].'<a href="javascript:Redirect('."'".'run.php?'.$vv.'&Search=Go&leavequery=1&SelectSqlParam='.$HTTP_PARAM_VARS['SelectSqlParam'].'#markGo'."'".')">'.$button_strings['LeaveQuery'].'</a>'.$Confs["LinkIcoAfter"];
						$vSearch .= "<hr />";
						//$vShowFieldSection = false;
						}
					}
				}
			}


	}//end of the for


	if ($vShowForm) 
		{
		if ($vIsSelected)
			{  //Show the links Previous and Next?
			if (!IsEmpty($vLinkLast))
				{
				$vSearch  .=  '    <a href="javascript: showWaitScreen('."'".$message_strings['Searching']."')".'; Redirect('."'".rawurlencode($vLinkLast)."'".')">'.$button_strings['Prev'].'</a>';
				}
			else	{
				$vSearch  .=  '    '.$button_strings['Prev'];
				}
			if (!IsEmpty($vLinkNext))
				{
				$vSearch  .=  '    <a href="javascript: showWaitScreen('."'".$message_strings['Searching']."')".'; Redirect('."'".rawurlencode($vLinkNext)."'".')">'.$button_strings['Next'].'</a>';
				}
			else	{
				$vSearch  .=  '    '.$button_strings['Next'];
				}
			}
		else	{
			//$vScriptIni .= "\n".' document.Form.SelectSqlParam.selectedIndex=1;';
			//$HTTP_POST_VARS['Search'] = 'true';
			//$HTTP_PARAM_VARS['SelectSqlParam'] = $vFirstResult;
			//$checkSearch = true;
			//echo 'aqui estamos';
			}
		$vSearch .= '<br />';
			
		if (($HTTP_PARAM_VARS['query_dest']=='form') and
			( (!empty($HTTP_PARAM_VARS['query_run'])) or ($vquickquery) ) )
			{
			//To close the query....
			$vv = $vParamFlow;
			$vv = ereg_replace('query_run='.$HTTP_PARAM_VARS['query_run'],'', $vv);
			$vv = ereg_replace('query_dest=form','', $vv);
			$vv = ereg_replace('&&','&', $vv);
			$vv = ereg_replace('&&','&', $vv);
			$vLink  .=  $Confs["LinkIcoBefore"].'<a href="javascript:Redirect('."'".'run.php?'.$vv.'&Search=Go&leavequery=1&SelectSqlParam='.$HTTP_PARAM_VARS['SelectSqlParam'].'#markGo'."'".')">'.$button_strings['LeaveQuery'].'</a>'.$Confs["LinkIcoAfter"];
			}

		//If there is not a textbox or one value in the combobox then clear the search params....
		if (!$ventered)
			{
			//$vSearch =  '';
			}
		//Inserting the links (tag Links in xml)...
		//Process every "link" tag
		for ($i = 0; $i < count($s_xml_conf['links']); $i++)
			{
			$vLink  .=  '    '.$Confs["LinkIcoBefore"].'<a href="javascript:Redirect('."'".urlencode(get_value($s_xml_conf['links'][$i],'href'))."'".')">'.get_value($s_xml_conf['links'][$i],'content').'</a>'.$Confs["LinkIcoAfter"];
			}

		//Back or volver link
		if ( (isset($HTTP_PARAM_VARS['back'])) &&
			(isset($HTTP_PARAM_VARS['back_data'])) )
			{
			$vHTML .= "<input name='back' type='hidden' value='".($HTTP_PARAM_VARS['back'])."' />";
			$vHTML .= "<input name='back_data' type='hidden' value='".($HTTP_PARAM_VARS['back_data'])."' />";
			$vLink  .=  '    '.$Confs["LinkIcoBefore"].'<a href="javascript:Redirect('."'run.php?mod=".($HTTP_PARAM_VARS['back']).'&s_id='.$HTTP_PARAM_VARS['s_id'].'&data='.(($HTTP_PARAM_VARS['back_data']))."&back='+(document.Form.mod.value)+'&back_data='+escape(document.Form.data.value)".')">'.($button_strings["back"]).'</a>'.$Confs["LinkIcoAfter"];
			}


		if ($HTTP_PARAM_VARS['data']=='all')
			{
			$vLink  .=  '    '.$Confs["LinkIcoBefore"].'<a href="javascript:NewPanel(\'\');">'.$button_strings['ShowPanel'].'</a>'.$Confs["LinkIcoAfter"];
			}
		else	{
			$vLink  .=  '    '.$Confs["LinkIcoBefore"].'<a href="javascript:NewPanel(\'all\');">'.$button_strings['HidePanel'].'</a>'.$Confs["LinkIcoAfter"];
			}

		//We go to define the param groupfilter who defined what fields will be used  in the form
		$HTTP_PARAM_VARS['groupfilter'] = (isset($HTTP_PARAM_VARS['groupfilter']))? $HTTP_PARAM_VARS['groupfilter'] : '';

		if (count($s_xml_conf['configuration'])>0)
			{
			$vExist = false;
			for ($i = 0; $i < count($s_xml_conf['configuration']); $i++)
				{
				if (get_value($s_xml_conf['configuration'][$i],'tagname')=='lang')
					{
					if (!$vExist)
						{
						$vLink  .=  '	'.$button_strings['Language'].'   <select name="Language" size="1" >'."\n";
						$vExist = true;
						}
					if (get_value($s_xml_conf['configuration'][$i],'value')==$ADODB_LANG)
						{
						$vLink  .=  '<option value="'.get_value($s_xml_conf['configuration'][$i],'value').'" selected>'.get_value($s_xml_conf['configuration'][$i],'content')."</option>\n";
						}
					else	{
						$vLink  .=  '<option value="'.get_value($s_xml_conf['configuration'][$i],'value').'" >'.get_value($s_xml_conf['configuration'][$i],'content')."</option>\n";
						}
					}
				}
			if ($vExist)
				{
				$vLink .=  "</select>\n";
				}

			$vExist = false;
			for ($i = 0; $i < count($s_xml_conf['configuration']); $i++)
				{
				if ((get_value($s_xml_conf['configuration'][$i],'tagname')=='groupfilter') and
					(get_value($s_xml_conf['configuration'][$i],'lang')==$ADODB_LANG))
					{
					if (!$vExist)
						{
						$vLink  .=  '	'.$button_strings['workwith'].'   <select name="groupfilter" size="1" >'."\n";
						$vExist = true;
						}
					if (get_value($s_xml_conf['configuration'][$i],'value')==$HTTP_PARAM_VARS['groupfilter'])
						{
						$vLink  .=  '<option value="'.get_value($s_xml_conf['configuration'][$i],'value').'" selected>'.get_value($s_xml_conf['configuration'][$i],'content')."</option>\n";
						}
					else	{
						$vLink  .=  '<option value="'.get_value($s_xml_conf['configuration'][$i],'value').'" >'.get_value($s_xml_conf['configuration'][$i],'content')."</option>\n";
						}
					}
				}
			if ($vExist)
				{
				$vLink .=  "</select>\n";
				}

			}
		}


	//If was selected the button search then initialize the session vars...
	if (isset($HTTP_PARAM_VARS['Search']))
		{

		if ((isset($HTTP_POST_VARS['Search'])))
			{

			//if ((isset($HTTP_PARAM_VARS['FormChanged'])) and
			//   ($HTTP_PARAM_VARS['FormChanged']=="true"))
			//	{
			//	$vShowForm = false;
			//	$vScriptIni .= "\n".' AskSave("Save");';
			//	}
			//elseif ((isset($s_FormChanged)) and
			//	($s_FormChanged=="true"))
			//	{
			//	$vShowForm = false;
			//	$vScriptIni .= "\n".' AskSave("Save");';
			//	}
			//else 	{
				cleanup_session();
			//	}

			//if ($HTTP_PARAM_VARS['data']!='all')
			//	{
			//	$HTTP_PARAM_VARS['data'] = '';
			//	}
 			}
		//elseif (((isset($HTTP_GET_VARS['Search'])) and
		//	(((isset($HTTP_PARAM_VARS['FormChanged'])) and
		//	 ($HTTP_PARAM_VARS['FormChanged']=="true"))
		//	 or
		//	((isset($s_FormChanged)) and
		//	($s_FormChanged=="true"))))
		//	)
		//	{

		//	if ($vIsDiferentRecord)
		//		{
		//		$vShowForm = false;
		//		$vScriptIni .= "\n".' AskSave("Save");';

		//		}
		//	else	{
				//is the same record, so we don?t do nothing...
		//		}
		//	}
		else	
			{
			cleanup_session();
			}

		}
		


	$vHTML .='<input name="FormChangedNow" type="hidden" value="false" />';
	if ((isset($HTTP_PARAM_VARS['FormChanged'])) and
		(($HTTP_PARAM_VARS['FormChanged'])=="true") and
		(!isset($HTTP_POST_VARS['Search'])) and
		(!isset($HTTP_POST_VARS['Save'])) and
		(!isset($HTTP_PARAM_VARS['Insert'])))
		{
		$s_FormChanged = 'true';
		if ($vAdminUser)
			{
			$vHead .= "<input name='status' disabled='true' type='text' size='".(strlen($vModName)+1)."' value='".$vModName."*' /><br />";
			}
		else 	
			{
			$vHead .= "<input name='status' type='hidden' disabled='true' size='".(strlen($vModName)+1)."' value='".$vModName."*' />";
			}
		$vHTML .= '<input name="FormChanged" type="hidden" value="true" />';
		}
	else 	{
		if ((isset($s_FormChanged)) and
		   ($s_FormChanged=='true') and
		   (!isset($HTTP_POST_VARS['Search'])) and
		   (!isset($HTTP_POST_VARS['Save'])) and
		   (!isset($HTTP_PARAM_VARS['Insert'])))
			{
			$s_FormChanged = 'true';
			if ($vAdminUser)
				{
				$vHead .= "<input name='status' type='text' disabled='true' size='".(strlen($vModName)+1)."' value='".$vModName."*' /><br />";
				}
			else 	{
				$vHead .= "<input name='status' type='hidden' disabled='true' size='".(strlen($vModName)+1)."' value='".$vModName."*' />";
				}
			$vHTML .= '<input name="FormChanged" type="hidden" value="true" />';
			}
		else	{
			$s_FormChanged = '';
			if ($vAdminUser)
				{
				$vHead .= "<input name='status' disabled='true' type='text' size='".(strlen($vModName)+1)."' value='".$vModName."' /><br />";
				}
			else 	{
				$vHead .= "<input name='status' type='hidden' disabled='true' size='".(strlen($vModName)+1)."' value='".$vModName."' />";
				}

			$vHTML .='<input name="FormChanged" type="hidden" value="false" />';
			}
		}

///--------------------------------------------------------------------------------------------------------------------------
	
		//Stored the primaryKeys in a var...procesing the "tables" tag
		$elements = $s_xml_conf['tables'];
		//Process every "table" tag
		for ($i = 0; $i < count($s_xml_conf['tables']); $i++)
			{
			if (strlen((get_value($s_xml_conf['tables'][$i],'pk')))>0)
				{
				$listPk = split(";", get_value($s_xml_conf['tables'][$i],'pk'));
				foreach ($listPk as $tt)
					{
					$vPrimaryKeys .= ";".get_value($s_xml_conf['tables'][$i],'name')."__".$tt;
					}
				}
			}
		$vPrimaryKeys .= ";";

		//Edit fields
		$vfirst = (IsEmpty($HTTP_PARAM_VARS['data']));  //used to know if was selected a panel option to show
		$vsection = '';
		$vimages = -1;   //contain the number of images (type='image_view') inserted in the html...
		$vLast = false;  // if true when a panel es showed
		$vdata = (!IsEmpty($HTTP_PARAM_VARS['data']))? $HTTP_PARAM_VARS['data']: false;  //Panel to show...

		$s_active_value = (isset($SelectedValue)) ? $SelectedValue: ((isset($s_active_value)) ? $s_active_value: '');
		if (!empty($s_active_value))
			{
			$vSearch .=  '<br />'.$s_active_value;
			}

		if (1)  //(($checkSearch) or (isset($HTTP_PARAM_VARS['Insert'])) or (isset($HTTP_PARAM_VARS['Save'])))  // if there is a Search or insert action, we show the fields
			{
		
			$vQueryOptions= '';
			$vrecord = array();
			$varNotIsEmpty = true;  //to know if there is a param post not IsEmpty
			if ($checkSearch)  //Case of search
				{
			
				$vv = ereg_replace('#1', $vTableId, $syntax['attribute']);
				$vv = ereg_replace('#2', $vFieldId, $vv);


				//To know the type of the field used....
				$vsyntax = $syntax['string'];
				for ($to=0; $to < count($s_xml_conf['tables']); $to++)
					if ($s_xml_conf['tables'][$to]['name']==$vTableId)
						{
						if (isset($s_xml_conf['tables'][$to]['field'][$vFieldId]))
							{
							if (	($s_xml_conf['tables'][$to]['field'][$vFieldId]=='I') or
								($s_xml_conf['tables'][$to]['field'][$vFieldId]=='R') or
								($s_xml_conf['tables'][$to]['field'][$vFieldId]=='N')	)
								{
								$vsyntax = $syntax['number'];
								}
							$to = count($s_xml_conf['tables']);
							}
						}

				$vv .= '='.ereg_replace('#1', sprintf ("%s", $HTTP_PARAM_VARS['SelectSqlParam']), $vsyntax);

				$vSqlSearch = ereg_replace('#1', $vSqlSearch, $syntax['where']);
				$vSqlSearch = ereg_replace('#2', $vv, $vSqlSearch);

				$warning .= '<br />finalmente: '.$vSqlSearch;
				$varNotIsEmpty = (($varNotIsEmpty) and (strlen(trim($HTTP_PARAM_VARS['SelectSqlParam']))>0));

				if ($varNotIsEmpty)
					{

					$vrecord[0] = &$dbhandle->Execute($vSqlSearch);
					if ($vrecord[0] === FALSE) {
						$db_error .= $dbhandle->ErrorMsg();
						echo "<br />Error ejecutando SQL (3): $vSql <br />";
						echo $db_error."<br /><pre>";
						echo "</pre>";
						require('./inc/script_end.inc.php');
						exit;

						if ($Confs["DEBUG"] === TRUE) {
							$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSqlSearch;
							}
						}
					else	{
						if ($Confs["DEBUG"] === TRUE) {
							$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSqlSearch.' [Ok]';
							}
						}

					}
				}
			else   //Case insert...
				{
				$varNotIsEmpty = true;
				}

			// We go to show the values of all the fields but before
			// we check if there is fields (count of elements>0), if was a query (search->$varIsEmpty=false) or if we want to insert new elements..

			if ((count($s_xml_conf['elements'])>0) and ($vShowForm))
				{

					{
					if ($vdata!='all') //if was'nt selected data the fields will be grouping, dont show the panel...
						{
						$panel  .=  "\n<ul id=\"Panel\">\n";
						}
					//Show the table with all the fields...
										
					// if we must to show all the data (vdata=="all"), we don´t show the Panel_ajax
					$Confs["Panel_Ajax"] = ($vdata == "all") ? false : $Confs["Panel_Ajax"];
					
					$vsection_cant = 0;

					for ($vnelem=0; $vnelem < count($s_xml_conf['elements']); $vnelem++)
						{  //mark element1
						
						if (get_value($s_xml_conf['elements'][$vnelem],'tagname')=='section')
							{  //if is a Section tag (title of the group of fields)...
							
							$vsection_cant++;
							$vsection = get_value($s_xml_conf['elements'][$vnelem],'content');
							$vsectionbr = (get_value($s_xml_conf['elements'][$vnelem],'br')=='true');
							$vgroupfilter = (get_value($s_xml_conf['elements'][$vnelem],'groupfilter'));
							$vShowField = (($HTTP_PARAM_VARS['groupfilter']==$vgroupfilter) or (empty($HTTP_PARAM_VARS['groupfilter'])));

							$vQuerySection = $vsection;
							
							// in case of is not define a section to show then we assign the first section found...
							$vdata = (IsEmpty($vdata)) ? 'panel_'.($vsection_cant): $vdata;
													
							$vLastSectionShow = false;
							if ($vLast)
								{
								$vfirst = false;
								}

							if (($vdata=='all') and ($vShowField) )
								{  // case of show all the data grouping in one page...
								if (!$vLast)
									$vHTML .=  "\n".'<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableForm">';
								if (($vAdminUser) )
									{
									$vHTML  .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><h3><strong>'.$vsection.'</strong></h3></td><td><a href="edit.php?mod='.$vModName.'&s_id='.$HTTP_PARAM_VARS['s_id'].'&element='.($vnelem).'">'.$Confs["EditIco"].'</a></td></tr>';
									$vLastSectionShow = true;
									}
								else	{
									$vHTML  .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField"><h3><strong>'.$vsection.'</strong></h3></td></tr>';
									}
			
								}
							elseif ($vShowField)  //showing the data of the section tag selected...
								{
/*										//delete the data param of the query string and form the new query string....
								$temp = '';
								foreach($HTTP_PARAM_VARS as $vvar => $vval)
									{
									if  ($vvar=='mod') { //(strpos(','.$recERV_PARAM.',',','.$vvar.',')==0) {
										if (strlen($temp)==0)  {
											$temp .= $vvar.'='.$vval;
											}
											else {
											$temp .= '&'.$vvar.'='.$vval;
											}
										}
									}
*/
								if (strlen($SEARCH_PARAM)>0) {
									$temp = $vModName.'&'.$SEARCH_PARAM;
									}
								else  {
									$temp = $vModName;
									}

								if ($vsectionbr)
									{
									$panel  .="</ul> \n <ul id=\"Panel\"'>\n";

									}

								if (!$vfirst) $vHTML  .=  '</table></div>';  //fin del div del panel...
																
								if (($vfirst) or ($vdata=='panel_'.($vsection_cant))) 
									{
									$vHTML  .=  '<div id="panel_'.$vsection_cant.'">';  //inicio del div del panel...
									$vHTML .=  "\n".'<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableForm">';
									$vScriptIni .= "\n".' document.Form.data.value = "'.'panel_'.($vsection_cant).'"; ';
									
									// Show the panel of the section selected/active...
									$panel .='      <li><a id="menupanel_'.$vsection_cant.'" class="current" '
													.' href="javascript:NewPanel(\'panel_'.$vsection_cant.'\');">'
													.'&nbsp;&nbsp;'.$vsection.'&nbsp;&nbsp; </a>'." </li>\n";
									}
								else
									{
									$vHTML  .=  '<div style="display: none;" id="panel_'.$vsection_cant.'">';  //inicio del div del panel...
									$vHTML .=  "\n".'<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableForm">';
									
									// Show the panel of the section don?t selected/active...
									$panel .='      <li><a id="menupanel_'.$vsection_cant.'" '
													.' href="javascript:NewPanel(\'panel_'.$vsection_cant.'\');">'
													.'&nbsp;&nbsp;'.$vsection.'&nbsp;&nbsp; </a>'
													." </li>\n";

									}
								}
							}  //end of section tag
							// case of a field tag...
							//if the field tag is part of the section tag active?...or case of show all the section tags...
							elseif (get_value($s_xml_conf['elements'][$vnelem],'tagname')=='element')
								{ //ini of field tag part
									//Initialize the values of all the params related with the fields processed...
									$vSelected = '';
									$vTable = get_value($s_xml_conf['elements'][$vnelem],'table');  //table name
									$vField = get_value($s_xml_conf['elements'][$vnelem],'field');	//Field to save of read from the db
									$vAlias = get_value($s_xml_conf['elements'][$vnelem],'alias');	//Field to save of read from the db
									$vName = $vTable."__".$vField;  //The combination of table and field is equal to vName
									$vSql = stripcslashes(urldecode(get_value($s_xml_conf['elements'][$vnelem],'sql')));  //sql sentence
									$vId = get_value($s_xml_conf['elements'][$vnelem],'id');	  //Field with the code of the field..
									$vDesc = get_value($s_xml_conf['elements'][$vnelem],'desc');  //Description of the field that is showed to be selected, but not saved in the db
									$vSelected = get_value($s_xml_conf['elements'][$vnelem],'default');	//Default value declare in the xml file to every field...
									$vLen = get_value($s_xml_conf['elements'][$vnelem],'len');   //len of the field in the edition...
									$vLen = (IsEmpty($vLen))? 50: $vLen;
									$vLenEdit = get_value($s_xml_conf['elements'][$vnelem],'lenedit');   //len of the field in the edition...
									$vLenEdit = (IsEmpty($vLenEdit))? 1: $vLenEdit;
									$vType = get_value($s_xml_conf['elements'][$vnelem],'type');   //type of edition of the field...
									$vLines = get_value($s_xml_conf['elements'][$vnelem],'lines');    //lines to show to any memo field...
									$vLines = (IsEmpty($vLines))? 4: $vLines;
									$vFind = $button_strings['Find'];    //Value of the button find..
									$vFind  = (IsEmpty($vFind))? 'Find': $vFind;
									$vLinked = $button_strings['Linked'];  //value of the button linked...
									$vLinked = (IsEmpty($vLinked))? 'Linked': $vLinked;
									$vDepend = get_value($s_xml_conf['elements'][$vnelem],'depend');  //the dependences of the fields
									$vFieldst = get_value($s_xml_conf['elements'][$vnelem],'fieldst');  //value of the field present in the main db and the dicionary, the db take the value of the dictionary and can modified this...
									$vMandatory = (get_value($s_xml_conf['elements'][$vnelem],'mandatory')=='true');
									$vIsEmptyOk = ($vMandatory)? "false": "true";
									$vview = get_value($s_xml_conf['elements'][$vnelem],'view');  //case of type 'image_view' to know what image show...
									$vcols = get_value($s_xml_conf['elements'][$vnelem],'cols');  //case of type 'image_view' to know number of cols to show...
									$vrows = get_value($s_xml_conf['elements'][$vnelem],'rows');  //case of type 'image_view' to know number of rows to show...
									$vredirect = (get_value($s_xml_conf['elements'][$vnelem],'redirect')=='true');
									$vChecked = get_value($s_xml_conf['elements'][$vnelem],'checked');  //value when is checked (if is not defined as default asigned 1...
									$vUnChecked = get_value($s_xml_conf['elements'][$vnelem],'unchecked');  //value when is checked (if is not defined as default asigned 0...
									$vSummaryField = get_value($s_xml_conf['elements'][$vnelem],'summaryfield');  //field used to contain a value of all the options that are in the listbox
									$vOrderField = get_value($s_xml_conf['elements'][$vnelem],'orderfield');  //case of is declared a orderfield....
									$vPathCopy = get_value($s_xml_conf['elements'][$vnelem],'pathcopy');  //path in type="*file*" to know where will be copy the file selected...
									$vPathRef = get_value($s_xml_conf['elements'][$vnelem],'pathref');	//path in type="*file*" to know the final path where will be referenced for a link....
									$vStatusMsg = get_value($s_xml_conf['elements'][$vnelem],'statusmsg');	//message to show in the status bar....
									$vHelp = get_value($s_xml_conf['elements'][$vnelem],'help');	//link of help....
									$vDisabled = (get_value($s_xml_conf['elements'][$vnelem],'disabled')=='true')? "disabled='disabled'": "";	//link of help....
									$vDelimitedChar = get_value($s_xml_conf['elements'][$vnelem],'delimitedchar'); //Used in listbox_listbox and listbox_combobox to separated the items stored in the summaryfield.
									$vDelimitedSummaryChar  = get_value($s_xml_conf['elements'][$vnelem],'delimitersummaryfield'); //Used in listbox_listbox and listbox_combobox to separated the items stored in the delimitedsummaryfield.
									$vDateFrmtEnter =  get_value($s_xml_conf['elements'][$vnelem],'datefrmenter');  //Used in the type date to define the format of the field date in the form
									$vDateFrmtEnter  = (IsEmpty($vDateFrmtEnter))? 'ymd': $vDateFrmtEnter;
									$vDateFrmtSave  =  get_value($s_xml_conf['elements'][$vnelem],'datefrmsave');  //Used in  the type date to define the format of the field date  when will stored in the database
									$vDateFrmtSave  = (IsEmpty($vDateFrmtSave))? 'ymd': $vDateFrmtSave;
									$vHuckField = get_value($s_xml_conf['elements'][$vnelem],'huckfield');
									$vTableRef =  get_value($s_xml_conf['elements'][$vnelem],'tableref');  //Used in table_view to specified the value of the links in the html showed
									$vTableRef = (IsEmpty($vTableRef)) ? false : $vTableRef;
									$vHREF = get_value($s_xml_conf['elements'][$vnelem],'href');
									$vHREF = ereg_replace('"', "'", $vHREF);
									$vAction = get_value($s_xml_conf['elements'][$vnelem],'action');
									$vAction = ereg_replace('"', "'", $vAction);
									$vHREFGo = get_value($s_xml_conf['elements'][$vnelem],'hrefgo');
									$vHREFGo = ereg_replace('"', "'", $vHREFGo);
									$vContent = get_value($s_xml_conf['elements'][$vnelem],'content');
									$vVirtual = (get_value($s_xml_conf['elements'][$vnelem],'virtual')=='true');
									$vBrLn = (get_value($s_xml_conf['elements'][$vnelem],'brln')=='true');
									$vBr = ((get_value($s_xml_conf['elements'][$vnelem],'br')=='true') or (strlen(get_value($s_xml_conf['elements'][$vnelem],'br'))==0));
									$vHr = (get_value($s_xml_conf['elements'][$vnelem],'hr')=='true');
									$vSpecialChars = (get_value($s_xml_conf['elements'][$vnelem],'specialchars')=='true');
									$vComment = (get_value($s_xml_conf['elements'][$vnelem],'comment'));
									$vquerylabel = (get_value($s_xml_conf['elements'][$vnelem],'querylabel'));
									$vgroupfilter = (get_value($s_xml_conf['elements'][$vnelem],'groupfilter'));
									$vFileInc = (get_value($s_xml_conf['elements'][$vnelem],'filename'));
									$vVariable = (get_value($s_xml_conf['elements'][$vnelem],'var'));
									$vCheckfield = (get_value($s_xml_conf['elements'][$vnelem],'check'));
									$vPropval = (get_value($s_xml_conf['elements'][$vnelem],'propval')=='true');
									$vShowEmpty = (get_value($s_xml_conf['elements'][$vnelem],'showempty')=='true');
									$vindiv = (get_value($s_xml_conf['elements'][$vnelem],'indiv'));
									$vinframe = (get_value($s_xml_conf['elements'][$vnelem],'inframe'));
									$vdivheigth = (get_value($s_xml_conf['elements'][$vnelem],'divheigth'));


									$vScript_ = '';
									
									//poner esto en inc/language.php
									$vDateError = 'Error in the value of the date field...';
									$vIsEmptyError = 'Warning: this field is mandatory and it value is IsEmpty...';

									//checking if exist the table and the field in the database....
									if ($vCheckfield=="true")
										{
										$vCheckfieldgo = false;
										for ($t = 0; $t < count($s_xml_conf['tables']); $t++)
											{
											if (($vTable == get_value($s_xml_conf['tables'][$t],'name')) &&
											    (isset($s_xml_conf['tables'][$t]['field'][$vField])))
											    {
											    $vCheckfieldgo = true;
											    }
											}
										if (!$vCheckfieldgo)
											{
											$vType = "virtual";
											$vField = '';
											}
										}

									//If is a virtual component, this not will be saved in the database and the name of the component will be it value...

									if (!IsEmpty($vview))
										{
										$vimages++;
										$vName = 'document.images['.$vimages.'].src';
										//Checking if there is var defined in the vHREF value...
										while ((@preg_match("|.*(__(\w*)\.(\w*)__).*|U", $vview, $match)) )
											{
											$vview = ereg_replace($match[1],("'+document.Form.".$match[2]."__".$match[3].".value+'"), $vview);
											}
										$vScriptIni .= "\n".$vName."='".$vview."';";

										}

									//register the globals variable in the session
									if (!IsEmpty($vField))
										{
										$vrecordPos = 0;
										if ($checkSearch)
											{
											$vrecordCant = count($vrecord);
											for ($vrecordPos = 0; $vrecordPos < count($vrecord); $vrecordPos++)
												{
												$vfieldChecked = (!empty($vAlias)) ? $vAlias : $vField;

												if (	(isset($vrecord[$vrecordPos]->fields)) and
													(is_array($vrecord[$vrecordPos]->fields)) and
													(array_key_exists($vfieldChecked,$vrecord[$vrecordPos]->fields))
													)
														{
														$vSqlSearch = $vrecord[$vrecordPos]->sql;
														break;
														}
												}
											if ($vrecordPos==$vrecordCant)  //is necesary to create a new query....
												{

												for ($vtablePos = 0; $vtablePos < count($s_xml_conf['tables']); $vtablePos++)
													{
													if ($s_xml_conf['tables'][$vtablePos]['name']==$vTable)
														{

														$listPk = array();

														$vPk = get_value($s_xml_conf['tables'][$vtablePos],'pk');
														$vFk = get_value($s_xml_conf['tables'][$vtablePos],'fk');
														$listFk = array();

														$listPk = split(";", $vPk);  //the primary key field list

														if ($vFk)  {  //processing the foreign keys...
															$tt = split(";", $vFk);
															$match = '';
															foreach ($tt as $tt1)  {
																if (!@ereg ('(.+)=(.+)\.(.+)', $tt1, $match))
																	{
																	if (!@ereg ('(.+)=__(.+)__', $tt1, $match))
																		{
																		$vFatalError = true;
																		$error = 'The format of the foreign key is incorrect in the xml file: '.$tt1;
																		$listFk = array();
																		}
																	else	{
																		if ($HTTP_PARAM_VARS[$match[2]])
																			{
																			$tfk = $match[1];
																			$s_fields_value[$vTable][$tfk] = stripcslashes($HTTP_PARAM_VARS[$match[2]]);

																			$listFk[] = $tfk;  //var with all the foreign keys
																			}
																		else	{
																			//the error must have been reported before....but we go again...
																			$vFatalError = true;
																			$error = 'The format of the foreign key is incorrect in the xml file: '.$tt1;
																			$listFk = array();
																			}
																		}
																	}
																else	{
																	$tfk = $match[1];
																	$ttp = $match[2];
																	$tpk = $match[3];
																	$s_fields_value[$vTable][$tfk] = $s_fields_value[$ttp][$tpk];

																	$listFk[] = $tfk;  //var with all the foreign keys
																	}
																}
															}


														//Know the type of every field....
														$vt = $syntax['table'];
														$vt = ereg_replace('#1',$vTable, $vt);
														$vSqlSearch = 'SELECT * FROM '.$vt;
														$rec = $dbhandle->SelectLimit($vSqlSearch,1);
														for ($i=0, $max=$rec->FieldCount(); $i < $max; $i++) {
															$fld = $rec->FetchField($i);
															$type = $rec->MetaType($fld->type);
															$flds[$fld->name] = $type;
															}
														//Metatypes defined by adoDB
														//C,X,B: Text
														//D, T: Date
														//L: Logic
														//I, N, R :Numeric
														$vvvtype = array();
														$vvvtype['C']="'"; $vvvtype['X']="'"; $vvvtype['B']="'";
														$vvvtype['D']="'"; $vvvtype['T']="'"; $vvvtype['L']="'";
														$vvvtype['I']="";  $vvvtype['N']="";  $vvvtype['R']="";


														//Procesing the primary keys...
														$vWhere = '';
														$first = true;
														$vFatalError = FALSE;
														if (count($listPk))
															{
															foreach ($listPk as $tt)
																{
																if (!isset($s_fields_value[$vTable][$tt]))
																	{
																	$vFatalError = true;
																	}
																else	{
																	$record[$tt] = $s_fields_value[$vTable][$tt];
																	$vWhere .= (!$first) ? ' AND ' : '';
																	$vf = $syntax['field'];
																	$vf = ereg_replace('#1',$tt, $vf);
																	$vFatalError = ( ($vFatalError) and (!empty($s_fields_value[$vTable][$tt])) );
																	$vWhere .= $vf.'='.$vvvtype[$flds[$tt]].$s_fields_value[$vTable][$tt].$vvvtype[$flds[$tt]];
																	$first = false;
																	}
																}

															if (!$vFatalError)
																{
																$vSqlSearch = 'SELECT * FROM '.$vt.' WHERE '.$vWhere;
																$vrecord[] = &$dbhandle->Execute($vSqlSearch);
																$vrecordPos = count($vrecord)-1;
																}
															}


														}
													}


												}
											}


										if (isset($s_fields_newvalue[$vTable][$vField]))
											{
											$vSelected= stripcslashes($s_fields_newvalue[$vTable][$vField]);
											$s_fields_value[$vTable][$vField] = stripcslashes($s_fields_newvalue[$vTable][$vField]);
//print "<br />newvalue ".$vName.' = '.$s_fields_value[$vTable][$vField];
											unset($s_fields_newvalue[$vTable][$vField]);
											}
										elseif ((isset($HTTP_PARAM_VARS[$vName])) and (!isset($HTTP_POST_VARS['Search'])) and (!isset($HTTP_POST_VARS['Insert'])))
											{
//print "<br />param ".$vName.' = '.$HTTP_PARAM_VARS[$vName];
											$vSelected= stripcslashes($HTTP_PARAM_VARS[$vName]);
											}
										elseif (isset($s_fields_value[$vTable][$vField]))
											{
//print "<br />sesion ".$vName.' = '.$s_fields_value[$vTable][$vField];
											$vSelected= $s_fields_value[$vTable][$vField];
											}
										elseif (($checkSearch))
											{
											if ($vType=='memo')
												{
												//$blob_data = ibase_blob_info($vrecord[$vrecordPos]->fields[$vField]);
												//$blob_hndl = ibase_blob_open($vrecord[$vrecordPos]->fields[$vField]);
												//$vSelected = ibase_blob_get($blob_hndl, $blob_data[0]);
												if (!empty($vAlias))
													{
													$vv = $dbhandle->BlobDecode($vrecord[$vrecordPos]->fields[$vAlias]);
													$vSelected = !empty($vv) ? $vv : $vSelected;
													}
												else	{
													$vv = $dbhandle->BlobDecode($vrecord[$vrecordPos]->fields[$vField]);
													$vSelected = !empty($vv) ? $vv : $vSelected;
													}

//print "<br />checksearch ".$vName.' = '.$vSelected;

												}
											else  	{
												if (!empty($vAlias))
													{
													$vSelected = !empty($vrecord[$vrecordPos]->fields[$vAlias])?$vrecord[$vrecordPos]->fields[$vAlias]: $vSelected;
													}
												else	{
													$vSelected = !empty($vrecord[$vrecordPos]->fields[$vField])?$vrecord[$vrecordPos]->fields[$vField]: $vSelected;
													}
//print "<br />checksearch ".$vName.' = '.$vrecord[$vrecordPos]->fields[$vField];

												}
											}
										elseif ( (isset($s_fields_lastvalue[$vTable][$vField])) && ($vPropval) )
											{
//print "<br />Propvalue ".$vName.' = '.$s_fields_lastvalue[$vTable][$vField];
											$vSelected= $s_fields_lastvalue[$vTable][$vField];
											}
										else
											{
//print "<br />otro".$vName;
											$s_fields_value[$vTable][$vField] = $vSelected;

											}

										if ($vType=='now_date')
											{
											  $vSelected = date("Ymd");
											}
										elseif ($vType=='now_user')
											{
											  	if (isset($$vVariable))
											  		{
											  		$vSelected = (!empty($$vVariable)) ? $$vVariable: "anonymus";
													}
												else
													{
													$vSelected = "anonymus";
													}
											}

										$s_fields_value[$vTable][$vField] = $vSelected;

										if (strlen($vFieldst)>0)
											{

											if ((isset($HTTP_PARAM_VARS[$vTable."__".$vFieldst])) and (!isset($HTTP_POST_VARS['Search'])) and (!isset($HTTP_POST_VARS['Insert'])))
												{
												$s_fields_value[$vTable][$vFieldst] = $HTTP_PARAM_VARS[$vTable."__".$vFieldst];

												}
											elseif (isset($s_fields_value[$vTable][$vFieldst]))
												{
												//Is not necessary register the value because already is registered...
												}
											elseif ($checkSearch)
												{
												$s_fields_value[$vTable][$vFieldst] = $vrecord[$vrecordPos]->fields[$vFieldst];
												}
											else
												{
												$s_fields_value[$vTable][$vFieldst] = '';
												}
											}

										}

									//Process the dependences
									if (!IsEmpty($vDepend)) {
										$vshowField = false;
										$tt = split(";", $vDepend);
										$vDepExist = false;

										$vScriptChange .="\n".'	vEnabled=false;'."\n";
										foreach ($tt as $tt1)
										  if (strpos($tt1,'combobox:')>-1)
										  	{
										  	if (!$vJsDynamicOptionList)
										  		{
										  		echo '<script src="lib/js/DynamicOptionList.js"></script>';
										  		$vScriptIni .= '	initDynamicOptionLists();'."\n";
										  		$vJsDynamicOptionList = 0;
										  		}
										  	$vJsDynamicOptionList++;

										  	$ttabl = substr ($tt1, strpos($tt1,':')+1,strpos($tt1,'.')- strpos($tt1,':')-1);
											$tfiel = substr ($tt1, strpos($tt1,'.')+1,strpos($tt1,'(')- strpos($tt1,'.')-1);
											$tfielvalues = substr ($tt1, strpos($tt1,'(')+1,strpos($tt1,')')- strpos($tt1,'(')-1);
										  	$vScript .= 'var depend'.($vJsDynamicOptionList).' = new DynamicOptionList();'."\n";
										  	$vScript .= 'depend'.($vJsDynamicOptionList).'.addDependentFields("'.$ttabl.'__'.$tfiel.'","'.$vName.'");'."\n";

											$rec = $dbhandle->Execute($vSql);
											if ($rec === FALSE) 
												{
													$db_error .= $dbhandle->ErrorMsg();
													echo "<br />Error ejecutando SQL (4): $vSql <br />";
													echo $db_error."<br /><pre>";
													print_r($s_xml_conf['elements'][$vnelem]);
													echo "</pre>";
													require('./inc/script_end.inc.php');
													exit;

												if ($Confs["DEBUG"] === TRUE) {
													$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
													}
												}
											else	{
												if ($Confs["DEBUG"] === TRUE) {
													$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
													}
												}
											$vTemp = false;

											while (($rec) && (!$rec->EOF))
												{
												$vvvalue = trim($rec->fields[$vId]);
												if ($vTemp != $rec->fields[$tfielvalues])
													{
													if ($vTemp)
														{
														$vScript .= ');'."\n";
														}
													$vTemp = $rec->fields[$tfielvalues];
													$vScript .= 'depend'.($vJsDynamicOptionList).'.forValue("'.$vTemp.'").addOptionsTextValue("",""';
													}
												$vScript .= ',"'.$rec->fields[$vDesc].'","'.$vvvalue.'"';

												$rec->MoveNext();
												}
											if ($vTemp)
												{
												$vScript .= ');'."\n";
												$vScript .= 'depend'.($vJsDynamicOptionList).'.selectFirstOption = true;'."\n";
												$vScript .= 'depend'.($vJsDynamicOptionList).'.forValue("'.$s_fields_value[$ttabl][$tfiel].'").setDefaultOptions("'.$vSelected.'");'."\n";
												}
											$vScriptChange .= '	if (vName.name=="'.$ttabl.'__'.$tfiel.'") {'."\n";
											$vScriptChange .= '		depend'.($vJsDynamicOptionList).'.change('.$ttabl.'__'.$tfiel.','.$vName.'.value);'."\n";
											$vScriptChange .= '		}'."\n";


										  	}
										  else
											{
											$vDepExist = true;
											$ttabl = substr ($tt1, 0,strpos($tt1,'.'));
											$tfiel = substr ($tt1, strpos($tt1,'.')+1,strpos($tt1,'=')- strpos($tt1,'.')-1);
											$tvalu = substr ($tt1, strpos($tt1,'=')+1);
											$vshowField = (($s_fields_value[$ttabl][$tfiel] == $tvalu) or ($vshowField));

											$vScriptChange .= '	if (vName.name=="'.$ttabl.'__'.$tfiel.'") {'."\n";
											if (!IsEmpty($vview))
												{
												$vScriptChange .='		'.$vName."='".$vview."';\n";
												}
											elseif ($tvalu=='_changed_')
												{

												$vScriptChange .='		vEnabled = true;'."\n";
												}
											else
												{
												$vScriptChange .='		if (vName.value=="'.$tvalu.'") {'."\n";
												$vScriptChange .='			vEnabled = true;'."\n";
												$vScriptChange .= '			}'."\n";
												}
											$vScriptChange .= '		}'."\n";

											if (!isset($s_fields_depend[$ttabl][$tfiel][$tvalu])) {
												$s_fields_depend[$ttabl][$tfiel][$tvalu][] = $vName;
												}
											if (!array_key_exists($tvalu, $s_fields_depend[$ttabl][$tfiel])) {
												$s_fields_depend[$ttabl][$tfiel][$tvalu][] = $vName;
												}
											else	{
												if (!in_array($vName,$s_fields_depend[$ttabl][$tfiel][$tvalu])) {
													$s_fields_depend[$ttabl][$tfiel][$tvalu][] = $vName;
													}
												}
											}

										if (IsEmpty($vview) and ($vDepExist))
											{
											$vScriptChange .= '	if (vName.name=="'.$ttabl.'__'.$tfiel.'") {'."\n";
											$vScriptChange .='		if (vEnabled) {'."\n";
											$vScriptChange .= '			document.Form.'.$vName.'.disabled="";'."\n";
											$vScriptChange .= '			}'."\n";
											$vScriptChange .= '		else 	{'."\n";
											$vScriptChange .= '			document.Form.'.$vName.'.value="";'."\n";
											$vScriptChange .= '			document.Form.'.$vName.'.disabled="disabled";'."\n";
											$vScriptChange .= '			}'."\n";
											$vScriptChange .= '		}'."\n";
											}
										if (!$vshowField and ($vDepExist)) {
											$vDisabled = ' disabled="disabled" ';
											}
										}

									//_____________________________________________________________________________________________
									if (!$vQueryOptionsCreated)
										{
										if (($vType!='hidden') and (!empty($vField)))
											{

											if (!empty($vquerylabel))
												{
												if (!empty($vQuerySection))
													{
													$vQueryCount++;
													$s_xml_conf['queryOptions'][$vQueryCount]['mod'] = $vModName;
													$s_xml_conf['queryOptions'][$vQueryCount]['value'] = -1;
													$s_xml_conf['queryOptions'][$vQueryCount]['text'] = htmlentities('-----'.strtoupper($vsection)."-----");
													$vQuerySection = '';
													}

												$vQueryCount++;
												$s_xml_conf['queryOptions'][$vQueryCount]['mod'] = $vModName;
												$s_xml_conf['queryOptions'][$vQueryCount]['value'] = $vnelem;
												$s_xml_conf['queryOptions'][$vQueryCount]['text'] = $vquerylabel;

												}
											elseif (!empty($vContent))
												{
												if (!empty($vQuerySection))
													{
													$vQueryCount++;
													$s_xml_conf['queryOptions'][$vQueryCount]['mod'] = $vModName;
													$s_xml_conf['queryOptions'][$vQueryCount]['value'] = -1;
													$s_xml_conf['queryOptions'][$vQueryCount]['text'] = htmlentities('-----'.strtoupper($vsection)."-----");
													$vQuerySection = '';
													}

												$vQueryCount++;
												$s_xml_conf['queryOptions'][$vQueryCount]['mod'] = $vModName;
												$s_xml_conf['queryOptions'][$vQueryCount]['value'] = $vnelem;
												$s_xml_conf['queryOptions'][$vQueryCount]['text'] = htmlentities($vContent);

												}

											}
										}


									//_____________________________________________________________________________________________

									$vShowField = (($HTTP_PARAM_VARS['groupfilter']==$vgroupfilter) or (empty($HTTP_PARAM_VARS['groupfilter'])));
									$vShowField = ($vShowField and (!$vVirtual));
									
									
									if ((($vdata!='panel_'.($vsection_cant)) and ($vdata!='all') and (!$Confs["Panel_Ajax"])) or (!$vShowField))
										{
										if (($vTable) and ($vField))
											{
											if (!$vLast)
												{
												$vHTML .=  "\n".'<tr><td width="'.$vLeftPercent.'%" class="TableField">';
												$vHTML .=  "\n".'</td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
												$vLast = true;
												}

											$vHTML .= "\n".'<input name="'.$vName.'" type="hidden" value="'.$vSelected.'" />';
											if ( (!empty($vFieldst)) and (isset($s_fields_value[$vTable][$vFieldst])) )
												{
												$vHTML .= "\n".'<input name="'.$vTable.'__'.$vFieldst.'" type="hidden" value="'.$s_fields_value[$vTable][$vFieldst].'" />';
												}

											if ($vType=='combobox')
												{
												$rec = $dbhandle->Execute($vSql);
												if ($rec === FALSE) {
													$db_error .= $dbhandle->ErrorMsg();
													echo "<br />Error ejecutando SQL (5): $vSql <br />";
													echo $db_error."<br /><pre>";
													print_r($s_xml_conf['elements'][$vnelem]);
													echo "</pre>";
													require('./inc/script_end.inc.php');
													exit;

													if ($Confs["DEBUG"] === TRUE) {
														$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
														}
													}
												else	{
													if ($Confs["DEBUG"] === TRUE) {
														$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
														}
													}
												$vDescTemp = '';

												while ((!$rec->EOF) and (empty($vDescTemp)))
													{
													$vvvalue = trim($rec->fields[$vId]);
													//if the show values is equal to the field value, select this...
													if (($vSelected==$vvvalue)  and (strlen($vSelected)>0))
														{
														$vDescTemp = $rec->fields[$vDesc];
														}
													$rec->MoveNext();
													}
												$vHTML .= "\n".'<input name="'.$vName.'_Desc" type="hidden" value="'.$vDescTemp.'" />';
												}
											}
										}
									else	
										{

										if ($vHr)
											{
											$vHTML .='<hr NOSHADE SIZE=5 />';
											}

										if ($vBr) {  //insert a new break of line...
											//if ($vLast)
											//	{
												//$vHTML .="\n</td></tr>\n";
											//	}
											$vLastSectionShow = false;
											$vHTML .=  "\n".'<tr><td width="'.$vLeftPercent.'%" class="TableField">';
											}

										if ($vBrLn)
											{
											$vHTML .=  '<br />';
											}

										//the field is mandatory-> print in bold format (open a bold mark)...
										if (($vMandatory) or (strpos($vPrimaryKeys,';'.$vName.';')>0))
											{
											$vHTML .=  '<strong>';
											//if the field is mandatory and exist the data, then we go to disabled this field....
											if (($checkSearch) and (strpos(';'.$vPrimaryKeys.';',';'.$vName.';')>0))
												{
												$vDisabled = ' disabled="disabled" ';
												}
											}

										//Register the components declared and check it...
										if (!empty($vTable))
											{
											if (array_key_exists($vName, $vComponents))
												{
												$error = 'The value of the component '.$vName.' has been redeclared and exist already ';
												}
											else	{
												$vComponents[$vName] = $vSelected;
												}
											}


										//update the value of href if there is variables in it....
										if ( (strlen($vHREF)>0) )
											{
											$vHREFOk = true;
											//Checking if there is var defined in the Parameteres value...
											while ((@preg_match("|.*(__(\w*)__).*|U", $vHREF, $match)) )
												{
												if (isset($HTTP_PARAM_VARS[$match[2]]))
													{
													$vHREF = ereg_replace($match[1], sprintf ("%s", $HTTP_PARAM_VARS[$match[2]]), $vHREF);
													if (!array_key_exists($match[2], $vComponents))
														{
														$vComponents[$match[2]] = $vSelected;
														$vHTML .= '<input name="'.$match[2].'" type="hidden" value="'.$HTTP_PARAM_VARS[$match[2]].'" />';
														//$vSearch .=  '<input name="'.$match[2].'" type="hidden" value="'.$HTTP_PARAM_VARS[$match[2]].'"/>';
														$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&").$match[2].'='.$HTTP_PARAM_VARS[$match[2]];
														}
													}
												else	{
													$vHREF = ereg_replace($match[1], '#@#@'.$match[2].'#@#@', $vHREF);
													}
												}

											//Checking if there is var defined in the vHREF value...
											while ((@preg_match("|.*(__(\w*)\.(\w*)__).*|U", $vHREF, $match)) )
												{
												$vHREF = ereg_replace($match[1],("'+document.Form.".$match[2]."__".$match[3].".value+'"), $vHREF);
												}
											$vHREF = ereg_replace('#@#@','__', $vHREF);

											if ((@preg_match("|(.*)->(.*)|", $vHREF, $match)) )
												{
												if (strpos(';'.$match[1].';', ';'.$s_connection['user_type'].';')===false)
													{
													$vHREFOk = false;
													}
												else 	{
													$vHREF = $match[2];
													}

												}
											}
										else	{
											$vHREFOk = false;
											}

										//update the value of hrefGo if there is variables in it....
										if (strlen($vHREFGo)>0)
											{
											$vHREFGoOk = true;
											//Checking if there is var defined in the Parameteres value...
											while ((@preg_match("|.*(__(\w*)__).*|U", $vHREFGo, $match)) )
												{
												if (isset($HTTP_PARAM_VARS[$match[2]]))
													{
													$vHREFGo = ereg_replace($match[1], sprintf ("%s", $HTTP_PARAM_VARS[$match[2]]), $vHREFGo);
													if (!array_key_exists($match[2], $vComponents))
														{
														$vComponents[$match[2]] = $vSelected;
														$vHTML .= '<input name="'.$match[2].'" type="hidden" value="'.$HTTP_PARAM_VARS[$match[2]].'" />';
														//$vSearch .=  '<input name="'.$match[2].'" type="hidden" value="'.$HTTP_PARAM_VARS[$match[2]].'" />';
														$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&").$match[2].'='.$HTTP_PARAM_VARS[$match[2]];
														}
													}
												else	{
													$vHREFGo = ereg_replace($match[1], '#@#@'.$match[2].'#@#@', $vHREFGo);
													}
												}

											//Checking if there is var defined in the vHREF value...
											while ((@preg_match("|.*(__(\w*)\.(\w*)__).*|U", $vHREFGo, $match)) )
												{
												$vHREFGo = ereg_replace($match[1],("'+document.Form.".$match[2]."__".$match[3].".value+'"), $vHREFGo);
												}
											$vHREFGo = ereg_replace('#@#@','__', $vHREFGo);

											}
										else	{
											$vHREFGoOk = false;
											}

										//To show the field name....
										if (($vType!='link_show') and ($vType !='button_update') and ($vType !='button_jscript'))
											{
											//there is a http reference?
											if ($vHREFOk)
												{
												//$vHTML .=  '<a href="javascript:void(0)" onClick="javascript:window.open('."'".get_value($s_xml_conf['elements'][$vnelem],'href')."','','');".'">'.$vContent.'</a>';
												$vHTML .=  '<a href="javascript:Redirect('."'".urlencode($vHREF)."'".')">'.htmlentities($vContent).'</a>';
												}
												else
												{
												$vHTML .=  htmlentities($vContent);
												}
											}

										//Check variables present in the SQL to substitution
										$vSqlOk = true;
										if (strlen($vSql)>0)
											{
											$match = '';
											//Checking if there is var defined in the Parameteres value...
											while ((@preg_match("|.*(__(\w*)__).*|U", $vSql, $match)) )
												{
												if (isset($HTTP_PARAM_VARS[$match[2]]))
													{
													$vSql = ereg_replace($match[1], sprintf ("%s", $HTTP_PARAM_VARS[$match[2]]), $vSql);
													if (!array_key_exists($match[2], $vComponents))
														{
														$vComponents[$match[2]] = $vSelected;
														$vHTML .= '<input name="'.$match[2].'" type="hidden" value="'.$HTTP_PARAM_VARS[$match[2]].'" />';
														//$vSearch .=  '<input name="'.$match[2].'" type="hidden" value="'.$HTTP_PARAM_VARS[$match[2]].'"/>';
														$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&").$match[2].'='.$HTTP_PARAM_VARS[$match[2]];
														}
													}
												else 	{
													$vSql = ereg_replace($match[1], '#@#@'.$match[2].'#@#@', $vSql);
													}
												}

											//Checking if there is var defined in the vSql value...
											$vEncriptParam = array();
											while ((@preg_match("|.*(__(\w*)\.(\w*)__).*|U", $vSql, $match)) )
												{
												if ($vType=='button_update')
													{
													$vEncripPos = ('@@@'.(count($vEncriptParam)+1).'@@@');
													$vEncriptParam[$vEncripPos] = "'+document.Form.".$match[2]."__".$match[3].".value+'";
													$vSql = ereg_replace($match[1],($vEncripPos), $vSql);
													}
												elseif ((isset($s_fields_value[$match[2]][$match[3]])) and
													(!IsEmpty($s_fields_value[$match[2]][$match[3]])))
													{
													$vSql = ereg_replace($match[1],sprintf ("%s", $s_fields_value[$match[2]][$match[3]]), $vSql);
													}
												elseif ($checkSearch)
													{
													//We go to obtain the value directly from the database
													$vTemp = $vrecord[$vrecordPos]->fields[$match[3]];
													$vTemp = sprintf ("%s",$vTemp);
													if (!IsEmpty($vTemp))
														{
														$vSql = ereg_replace($match[1],$vTemp, $vSql);
														}
													else	{
														//Can be a error....
														$vSql = ereg_replace($match[1], '#@#@'.$match[2].".".$match[3].'#@#@', $vSql);
														$vSqlOk = false;
														}

													}
												else	{
													//Can be a error....
													$vSql = ereg_replace($match[1], '#@#@'.$match[2].".".$match[3].'#@#@', $vSql);
													$vSqlOk = false;
													}
												}

											if ( ($vType=='button_update') and (count($vEncriptParam)>0) )
												{
												$vSql = urlencode($vSql);
												foreach ($vEncriptParam as $vkey => $vval)
													{
													$vSql = ereg_replace(urlencode($vkey), $vval, $vSql);
													}
												}
											$vSql = ereg_replace('#@#@', '__', $vSql);

											}
										else	{
											$vSqlOk = false;
											}

										//Check variables present in the TableRef to substitution
										if ($vTableRef)
											{
											$vTableRefOk = true;
											$match = '';
											//Checking if there is var defined in the Parameteres value...
											while ((@preg_match("|.*(__(\w*)__).*|U", $vTableRef, $match)) )
												{
												if (isset($HTTP_PARAM_VARS[$match[2]]))
													{
													$vTableRef = ereg_replace($match[1], sprintf ("%s", $HTTP_PARAM_VARS[$match[2]]), $vTableRef);
													if (!array_key_exists($match[2], $vComponents))
														{
														$vComponents[$match[2]] = $vSelected;
														$vHTML .= '<input name="'.$match[2].'" type="hidden" value="'.$HTTP_PARAM_VARS[$match[2]].'"/>';
														//$vSearch .=  '<input name="'.$match[2].'" type="hidden" value="'.$HTTP_PARAM_VARS[$match[2]].'"/>';
														$vParamFlow .= ((IsEmpty($vParamFlow))? "":"&").$match[2].'='.$HTTP_PARAM_VARS[$match[2]];
														}
													}
												else	{
													$vTableRef = ereg_replace($match[1], "#@#@".$match[2]."#@#@", $vTableRef);
													}
												}
											//Checking if there is var defined in the vTableRef value...
											while ((@preg_match("|.*->.*(__(\w*)\.(\w*)__).*|U", $vTableRef, $match)) )
												{
												if ((isset($s_fields_value[$match[2]][$match[3]])) and
													(!IsEmpty($s_fields_value[$match[2]][$match[3]])))
													{
													$vTableRef = ereg_replace($match[1],sprintf ("%s", $s_fields_value[$match[2]][$match[3]]), $vTableRef);
													}
												elseif ($checkSearch)
													{
													//We go to obtain the value directly from the database
													$vTemp = $vrecord[$vrecordPos]->fields[$match[3]];
													$vTemp = sprintf ("%s",$vTemp);
													if (!IsEmpty($vTemp))
														{
														$vTableRef = ereg_replace($match[1],$vTemp, $vTableRef);
														}
													else	{
														//Can be a error....
														//$vTableRefOk=false;
														$vTableRef = ereg_replace($match[1],"#@#@".$match[2].".".$match[3]."#@#@", $vTableRef);
														}
													}
												else	{
													//Can be a error....
													//$vTableRefOk=false;
													$vTableRef = ereg_replace($match[1],"#@#@".$match[2].".".$match[3]."#@#@", $vTableRef);
													}
												}

											//Checking if there is var defined in the vTableRef value...
											while ((@preg_match("|.*(__(\w*)\.(\w*)__).*|U", $vTableRef, $match)) )
												{
												if ((isset($s_fields_value[$match[2]][$match[3]])) and
													(!IsEmpty($s_fields_value[$match[2]][$match[3]])))
													{
													$vTableRef = ereg_replace($match[1],sprintf ("%s", $s_fields_value[$match[2]][$match[3]]), $vTableRef);
													}
												elseif ($checkSearch)
													{
													//We go to obtain the value directly from the database
													$vTemp = $vrecord[$vrecordPos]->fields[$match[3]];
													$vTemp = sprintf ("%s",$vTemp);
													if (!IsEmpty($vTemp))
														{
														$vTableRef = ereg_replace($match[1],$vTemp, $vTableRef);
														}
													else	{
														//Can be a error....
														//$vTableRefOk=false;
														$vTableRef = ereg_replace($match[1],"#@#@".$match[2].".".$match[3]."#@#@", $vTableRef);
														}
													}
												else	{
													//Can be a error....
													//$vTableRefOk=false;
													$vTableRef = ereg_replace($match[1],"#@#@".$match[2].".".$match[3]."#@#@", $vTableRef);
													}
												}

											$vTableRef = ereg_replace("#@#@","__", $vTableRef);
											}
										else	{
											$vTableRefOk = false;
											}


										//if mandatory field, close the bold format mark...
										if ($vMandatory )
											{
											$vHTML .=  '</strong>';
											}
										//case of line broken, close the br mark...
										if ($vBr)
											{
											$vHTML .=  "\n".'</td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
											}

										if  (!empty($vComment))
											{
											$vHTML .=  '<p class="TableComment">'.nl2br(htmlentities($vComment)).'</p>';
											}

										$vLast = true;
										//$vSelected = ($vSelected);  //Convert the chars not printed in html to be printed...


	//===========================================================================================================================================================
										//Part of every Type of component created...

										if ($vType=='hidden')
											{  //type hidden that must be register to use in some place
											$vHTML .= '<input name="'.$vName.'" type="hidden" value="'.htmlentities($vSelected).'"/>';
											}
										elseif ($vType=='file_inc') //incluir un archivo
											{
											if (($vFileInc) && (file_exists ($xmlConfDirectory.$vFileInc)))
													{
													$vHTML .='</td></tr>';

													require($xmlConfDirectory.$vFileInc);
													//$vHTML .=  '<tr><td width="'.$vLeftPercent.'%" class="TableField">';

													//$vHTML .=  '</td><td class="TableFieldValue" width="'.$vRightPercent.'%">';
													//$vHTML .='</td></tr>';
													}

											}
										elseif ($vType=='label') {  //type hidden that must be register to use in some place
											//nothing...
											}
										elseif ($vType=='comboboxYN') {  //type hidden that must be register to use in some place
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName);
											$vChecked = (IsEmpty($vChecked))? 1: $vChecked;
											$vUnChecked = (IsEmpty($vUnChecked))? 0: $vUnChecked;

											$vHTML .=  '<select name="'.$vName.'" size="1" '.$vDisabled.' onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); ">'."\n";
											$vHTML .=  '<option value="" >&nbsp;</option>'."\n";
											if ($vSelected==$vChecked)  {
												$vHTML .=  '<option value="'.$vChecked.'" selected>'.$button_strings['Yes'].'</option>'."\n";
												$vHTML .=  '<option value="'.$vUnChecked.'">'.$button_strings['No'].'</option>'."\n";
												}
											elseif ($vSelected==$vUnChecked)  {
												$vHTML .=  '<option value="'.$vChecked.'">'.$button_strings['Yes'].'</option>'."\n";
												$vHTML .=  '<option value="'.$vUnChecked.'" selected>'.$button_strings['No'].'</option>'."\n";
												}
											else {
												$vHTML .=  '<option value="'.$vChecked.'">'.$button_strings['Yes'].'</option>'."\n";
												$vHTML .=  '<option value="'.$vUnChecked.'">'.$button_strings['No'].'</option>'."\n";
												}

											$vHTML .=  "</select>\n";
											}
										elseif ($vType=='checkbox') {  //type hidden that must be register to use in some place
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName.'_checked', $vName.'_checked');
											$IsThereCheckbox = 1; //in case of there is the type 'Checkbox' in the form is true (that is used to include the javascript in the user webpage...)
											$vChecked = (IsEmpty($vChecked))? 1: $vChecked;
											$vUnChecked = (IsEmpty($vUnChecked))? 0: $vUnChecked;

											$vHTML .=  '<input name="'.$vName.'_checked" type="checkbox" '.$vDisabled.' onClick="javascript:UpdateChecked(this,'.$vName.','."'".$vChecked."'".','."'".$vUnChecked."'".')" ';
											if ($vSelected==$vChecked)  {
												$vHTML .=  'value="'.$vChecked.'" checked />'."\n";
												}
											else {
												$vSelected = $vUnChecked;
												$vHTML .=  'value="'.$vUnChecked.'" />'."\n";
												}
											//is necessary save the value in a hidden component because if is unchecked then is not trasmmited the value in the get/post param
											$vHTML .='<input type="hidden" name="'.$vName.'" value="'.htmlentities($vSelected).'" />'."\n";

											}
										elseif ($vType=='image_view') {  //type hidden that must be register to use in some place
											$vcols = (IsEmpty($vcols))? 272: $vcols;
											$vrows = (IsEmpty($vrows))?  92: $vrows;
											if (($vredirect)) {
												if (!$vview)
													{
													$vview = $vSelected;
													}
												$vview = ereg_replace( '"', "'", $vview);
												$vHTML .= '<A HREF="javascript:Redirect('."'".$vview."'".')">';
												$vHTML .= '<img border=0 src="'.$vSelected.'"  WIDTH='.$vcols.' HEIGTH='.$vrows.' >';
												$vHTML .= '</A>';
												}
											else	{
												$vHTML .= '<img border="0" src="'.$vSelected.'"  align="left" width="'.$vcols.'" heigth="'.$vrows.'" >';
												}
											}
										elseif (($vType=='combobox') and ($vSqlOk))
											{  //type of field combobox?

											//execute the query with the values to show in the select combobox...
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName);
											$vHTML .=  '<select name="'.$vName.'" id="'.$vName.'" size="1" '.$vDisabled.' onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); ">'."\n";
											if ((strpos($vDepend,'combobox:')>-1) )
												{
												$vHTML .=  '<SCRIPT>depend'.($vJsDynamicOptionList).'.printOptions("'.$vName.'")</SCRIPT>'."\n";
												}
											else	{

												$vHTML .=  '<option value="" >&nbsp;</option>'."\n";
												$rec = $dbhandle->Execute($vSql);
												//echo $vSql;
												if ($rec === FALSE) {
													$db_error .= $dbhandle->ErrorMsg();
													echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
													echo "<br />Error ejecutando SQL (6): $vSql <br />";
													echo $db_error."<br /><pre>";
													print_r($s_xml_conf['elements'][$vnelem]);
													echo "</pre>";
													//require('./inc/script_end.inc.php');
													//exit;
													if ($Confs["DEBUG"] === TRUE) {
														$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
														}
													}
												else	{
													if ($Confs["DEBUG"] === TRUE) {
														$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
														}
													}

												$vDescTemp = '';
												$vSelected = trim($vSelected);

												while (($rec) && (!$rec->EOF))
													{
													//if the show values is equal to the field value, select this...
													$vvvalue = (trim($rec->fields[$vId]));
													$vDescTemp = $rec->fields[$vDesc];
													if (!empty($vDescTemp))
														{
														if (($vSelected==$vvvalue)  and (strlen($vSelected)>0))
															{
															
															$vHTML .=  '<option value="'.$vvvalue.'" selected>'.htmlentities($vDescTemp)."</option>\n";
															}
														else	{
															$vHTML .=  '<option value="'.$vvvalue.'" >'.htmlentities($vDescTemp)."</option>\n";
															}
														}
													
													$rec->MoveNext();
													}
												}

											$vHTML .=  "</select>\n";
											$vHTML .= '<input name="'.$vName.'_Desc" type="hidden" value="'.htmlentities($vDescTemp).'" />';

											if ($vHREFGo)
												{
												$vHTML .='<input type="button" name="'.$vName.'_button" onClick="javascript:Redirect('."'".$vHREFGo."'".')" value=" '.htmlentities($button_strings['Go']).' "/>       '."\n";
												}
											}
										elseif (($vType=='radio') and ($vSqlOk))
											{  //type of field radiobuttons?
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName);
											$vcolsCant = (IsEmpty($vcols))? 1: $vcols;
											$vcolsi = 0;

											//execute the query with the values to show in the select combobox...
											$rec = $dbhandle->Execute($vSql);

											if ($rec === FALSE) {
												$db_error .= $dbhandle->ErrorMsg();
												echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
												echo "<br />Error ejecutando SQL (7): $vSql <br />";
												echo $db_error."<br /><pre>";
												print_r($s_xml_conf['elements'][$vnelem]);
												echo "</pre>";
												//require('./inc/script_end.inc.php');
												//exit;

												if ($Confs["DEBUG"] === TRUE) {
													$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
													}
												}
											else	{
												if ($Confs["DEBUG"] === TRUE) {
													$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
													}
												}

											$vHTML .=  "\n<table border='0'><tr>\n";
											if ($vShowEmpty)
												{
												if (IsEmpty($vSelected))
													{
													$vHTML .=  '<td><label><i><input type="radio" name="'.$vName.'" value=""  onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".');" checked />';
													}
												else
													{
													$vHTML .=  '<td><label><i><input type="radio" name="'.$vName.'" value=""  onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".');" />';
													}
												$vHTML .=  htmlentities($info_strings['empty']).'</i></label></td>'."\n";
												$vcolsi = 1;
												}

											if ($vcolsi==$vcolsCant) {
												$vHTML .=  "</tr>";
												}
											//$vHTML .=  "<tr>";
											while (($rec) && (!$rec->EOF)) {
												if (!empty($rec->fields[$vDesc]))
													{
													if ($vcolsi==$vcolsCant) {
														$vHTML .=  "<tr>";
														$vcolsi = 0;
														}
													$vcolsi++;
													$vHTML .=  "<td><label>";
													//if the show values is equal to the field value, select this...
													$vvvalue = trim($rec->fields[$vId]);

													if (($vSelected==$vvvalue)  and (strlen($vSelected)>0)){
														$vHTML .=  '<input type="radio" name="'.$vName.'" value="'.$vvvalue.'"  onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".');"  checked />';
														}
													else	{
														$vHTML .=  '<input type="radio" name="'.$vName.'" value="'.$vvvalue.'"  onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); " />';
														}
													$vHTML .=  htmlentities($rec->fields[$vDesc]).'</label></td>'."\n";
													if ($vcolsi==$vcolsCant) {
														$vHTML .=  "</tr>";
														}
													}
												$rec->MoveNext();
												}
											if ($vcolsi!=$vcolsCant) {  //has not been closed the file...
													$vHTML .=  "</tr>";
													}
											$vHTML .=  "</table>\n";
											}
										elseif ($vType=='link_show')
											{
											$vindiv = (empty($vindiv)) ? "test" : $vindiv;
											$vdivheigth = ( (($vdivheigth)+0)!=0) ? (($vdivheigth)+0) : 500 ; 
											$vinframe = (empty($vinframe)) ? "1" : $vinframe;
											$vHTML .=  "\n";
											if ( ($vinframe) && (!isset($HTTP_PARAM_VARS['toiframe'])) )
												{
												$vHTML .=  '<a href="javascript: showinfodiv(\''.$vindiv.'\', \''.urlencode($vHREF).'&data=all&toiframe\', 1, 1,'.$vdivheigth.');">'.htmlentities($vContent).'</a> ';
												}
											else
												{
												if ($vHREFOk)
													{
													//$vHTML .=  '<a href="javascript:void(0)" onClick="javascript:window.open('."'".urldecode($vHREF)."','','');".'">'.htmlentities($vContent).'</a>';
													$vHTML .=  '<a href="javascript:Redirect('."'".urlencode($vHREF)."'".')">'.htmlentities($vContent).'</a>';
													}
												else 	{
													$vHTML .=  '<a href="javascript:void(0)" onClick="javascript:alert('."'".'Has not been introduced all the values to enter in this link...'."'".");".'">'.htmlentities($vContent).'</a>';
													}
												}

											}
										elseif ($vType=='div_section')
											{
											$vHTML .=  "\n<div id='$vId'></div>\n";
											}
										elseif ($vType=='table_view')
											{
											$vHTML .=  "\n";

											if ($vSqlOk)
												{
												$vindiv = (empty($vindiv)) ? "test" : $vindiv;
												$vdivheigth = ( (($vdivheigth)+0)!=0) ? (($vdivheigth)+0) : 500 ; 
												$vinframe = (empty($vinframe)) ? "1" : $vinframe;
												include_once('./lib/adodb/tohtml.inc.php');
												$trec = &$dbhandle->Execute($vSql);  //no se puede ejectutar $rec->MoveFirst();
												if ($trec === FALSE) {
													$db_error .= $dbhandle->ErrorMsg();
													echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
													echo "<br />Error ejecutando SQL (8): $vSql <br />";
													echo $db_error."<br /><pre>";
													print_r($s_xml_conf['elements'][$vnelem]);
													echo "</pre>";
													//require('./inc/script_end.inc.php');
													//exit;
													}
												

												if (($trec) && (!$trec->EOF))
													{
													$vShowInIframe = true;
													if (!$vBrLn)
														{
														$vHTML .=  "</td></tr></table>\n";
														$vHTML .=  '<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableFieldValue">'."\n";
														$vHTML .=  '<tr><td class="TableFieldValue">'."\n";
														}
													
													$vLinks = array();
													if ($vTableRef)
														{
														$match = '';
														$vTemp = split("\|",$vTableRef);
														foreach ($vTemp as $vtt)
															{
															if (@ereg("__(.+)__->(.+)", $vtt, $match))
																{
																if ( ($vinframe) && (!isset($HTTP_PARAM_VARS['toiframe'])) )
																	{
																	//$vLinks[$match[1]] =  'javascript: $("div#diviframe_'.$vnelem.'").html(\'<iframe	id="tableview'.$vnelem.'" name="iframe_'.$vnelem.'" src="'.$match[2].'&data=all&toiframe'.'" width="100%" height="500" scrolling="auto" align="top" frameborder="0" class="wrapper">Esta opci&oacute;n no trabajar&aacute; correctamente. Su navegador no soporta IFRAMES.</iframe>\');';
																	$vLinks[$match[1]] =  'javascript: showinfodiv("'.$vindiv.'", "'.$match[2].'&data=all&toiframe", 1, 1,'.$vdivheigth.'); ';
																	}
																else
																	{
																	$vLinks[$match[1]] = 'javascript:Redirect("'.$match[2].'")';
																	}
																}
															}

														}
														
													$vHTML .=  rs2html($trec,"BORDER='3'",false,$vSpecialChars,false, $vLinks); # obtenemos un texto en formato HTML
								
													//show in a iframe the reference to the first link in table_view if it is not showed in other iframe...
													//$vHTML  .=  '<div id="clonesframe"></div>';  //position where will be show the in a iframe the subformular ...
													//$vHTML .=  '<iframe	id="id_tableview" name="iframe_tableview" src="run.php?mod=collman/Clon.xml&Search=true&SelectSqlParam=434&data=all&toiframe" width="100%" height="500" scrolling="auto" align="top" frameborder="3" class="wrapper">Esta opci&oacute;n no trabajar&aacute; correctamente. Su navegador no soporta IFRAMES.</iframe>';
													
													if (!$vBrLn)
														{
														$vHTML .=  "</td></tr></table>\n";
														$vHTML .=  '<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableField">';
														}
													}
												}
											}
										elseif ($vType=='field_view')
											{
											if ($vSqlOk)
												{
												$trec = &$dbhandle->Execute($vSql);  //no se puede ejectutar $rec->MoveFirst();
												if ($trec === FALSE) {
													$db_error .= $dbhandle->ErrorMsg();
													echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
													echo "<br />Error ejecutando SQL (9): $vSql <br />";
													echo $db_error."<br /><pre>";
													print_r($s_xml_conf['elements'][$vnelem]);
													echo "</pre>";
													//require('./inc/script_end.inc.php');
													//exit;
	
													}

												if (($trec) && (!$trec->EOF))
													{
													$vLinks = array();
													if ($vTableRef)
														{
														$match = '';
														$vTemp = split("\|",$vTableRef);
														foreach ($vTemp as $vtt)
															{
															if (@ereg("__(.+)__->(.+)", $vtt, $match))
																{
																$vLinks[$match[1]] = 'javascript:Redirect("'.$match[2].'")';
																}
															}

														}

													$vvvalue = '';
													if (!empty($vDesc))
														{
														while (!$trec->EOF )
															{
															$vvvalue .= (!empty($vvvalue)) ? $vDelimitedChar : '';
															$match[1] = '';
															$match[2] = '';


															if (array_key_exists($vDesc, $vLinks))
																{
																$vtt = FALSE;
																$vtt = $vLinks[$vDesc];

																//Processing the values to change the value of the fields...that can exist in format __fielname__
																while (@ereg("(__(.+)__)", $vtt, $match))
																	{
																	$vtt = ereg_replace($match[1],sprintf ("%s", $trec->fields[$match[2]]) , $vtt);
																	}
																$match[1] ="<A HREF='".urldecode($vtt)."'>";
																//$match[1] ='<A href="javascript:void(0)" onClick="javascript:window.open('."'".urldecode($vtt)."','','');".'">';
																$match[2] ="</A>";
																$vvvalue .= $match[1].trim($trec->fields[$vDesc]).$match[2];
																}
															else	{
																$vvvalue .= trim($trec->fields[$vDesc]);
																}
															$trec->MoveNext();
															}
														}
													$vHTML .=  htmlentities($vvvalue);
													//$vHTML .=  rs2html($trec,false,false,$vSpecialChars,false, $vLinks); # obtenemos un texto en formato HTML
													}
												}
											}
										elseif ($vType=='now_date')
											{
	 											$vHTML .='  <input type="hidden" name="'.$vName.'" value="'.$vSelected.'" />'."\n";
											}
										elseif ($vType=='now_user')
											{
												$vHTML .='  <input type="hidden" name="'.$vName.'" value="'.$vSelected.'" />'."\n";
											}
										elseif (($vType=='textbox') or ($vType=='date') or ($vType=='password') or ($vType=='link') or ($vType=='textbox_number'))
											{  //case textbox...
											if ($vType=='date')
												{
												$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName.'_date');
												$vHTML .='  <input type="hidden" name="'.$vName.'" value="'.$vSelected.'" />'."\n";
												$vHTML .=  '<input name="'.$vName.'_date" type="text" '.$vDisabled.' OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
												$vHTML .=  'value=""';
												$vHTML .=  ' onChange="javascript:CheckDate(this, document.Form.'.$vName.','."'".$vDateFrmtEnter."'".' ,'."'".$vDateFrmtSave."'".' ,'.$vIsEmptyOk.','."'".$vDateError."'".', 1)" ';
												$vLen = 10;
												//Script to ini the value of the field date in the format of introduction
												//echo date("Y-m-d");
												$vScriptIni .= "\n".'	CheckDate(document.Form.'.$vName.', document.Form.'.$vName.'_date,'."'".$vDateFrmtSave."'".' ,'."'".$vDateFrmtEnter."'".' ,1,"", 0); ';

												}
											elseif ($vType=='textbox_number')
												{
												$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName);
												$vHTML .=  '<input name="'.$vName.'" type="text" '.$vDisabled.' OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
												$vHTML .=  'value="'.$vSelected.'"';
												$vHTML .=  ' onChange="javascript:CheckNumeric(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')" ';
												}

											else if ($vType=='password')
												{
												$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName);
												$vHTML .=  '<input name="'.$vName.'" type="password" '.$vDisabled.' OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
												$vHTML .=  'value="'.htmlentities($vSelected).'"';
												$vHTML .=  ' onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')" ';
												}
											else
												{
												$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName);
												$vHTML .=  '<input name="'.$vName.'" type="text" '.$vDisabled.' OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
												$vHTML .=  'value="'.htmlentities($vSelected).'"';
												$vHTML .=  ' onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')" ';
												}

											if (strlen($vLen)>0)   {
												$vLenShow = ($vLen>100)? 100: $vLen;
												$vLenEdit = ($vLen>$vLenEdit)? $vLen: $vLenEdit;
												$vHTML .=  ' size="'.$vLenShow.'" maxlength="'.$vLenEdit.'" ';
												}
											 $vHTML .=  " />\n";
											 if ($vType=='link') {
												$vHTML .='<input type="button" onClick="javascript:OpenLink(document.Form.'.$vName.'.value);" value=" '.$button_strings['Go'].' " />       ';
												}
											 if ($vType=='date') {
												$vHTML .='<input type="button" onClick="javascript:Today(document.Form.'.$vName.'_date, document.Form.'.$vName.','."'".$vDateFrmtEnter."'".' ,'."'".$vDateFrmtSave."'".' ,'.$vIsEmptyOk.','."'".$vDateError."'".', 1);" value="Hoy!" />       ';
												}

											}
										elseif ((strpos($vType,'textbox_combobox')===0) and ($vSqlOk))//(($vType=='textbox_combobox') or ($vType=='textbox_combobox_link') or ($vType=='textbox_combobox_link_file'))
											{

											//Combobox part...
											//execute the query with the values to show in the select combobox...
											$IsThereTextbox_combobox = 1;
											$vThereIsComboSelected = 0;
											$vHTMLtemp = '';
											$rec = $dbhandle->Execute($vSql);
											if ($rec === FALSE) {
												$db_error .= $dbhandle->ErrorMsg();
												echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
												echo "<br />Error ejecutando SQL (10): $vSql <br />";
												echo $db_error."<br /><pre>";
												print_r($s_xml_conf['elements'][$vnelem]);
												echo "</pre>";
												//require('./inc/script_end.inc.php');
												//exit;

												if ($Confs["DEBUG"] === TRUE) {
													$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
													}
												}
											else	{
												if ($Confs["DEBUG"] === TRUE) {
													$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
													}
												}

											while (($rec) && (!$rec->EOF))
												{
												//if the show values is equal to the field value, select this...
												$vvvalue = trim($rec->fields[$vId]);
												if (($vSelected==$vvvalue)  and (strlen($vSelected)>0)){
													   $vHTMLtemp .=  '<option value="'.$vvvalue.'" selected>'.$rec->fields[$vDesc]."</option>\n";
													   $vThereIsComboSelected = 1;
													   }
													  else
													   {
													   $vHTMLtemp .=  '<option value="'.htmlentities($vvvalue).'" >'.htmlentities($rec->fields[$vDesc])."</option>\n";
													   }
												$rec->MoveNext();
												}
												
											if ($vThereIsComboSelected)
												{
												$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName.'_radio');
												$vHTML .=  '<input type="radio" name="'.$vName.'_radio" value="1" onClick="textbox_comboboxUpdate(document.Form.'.$vName.', document.Form.'.$vName.'_radio, document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_textbox)" checked />';
												$vHTML .=  '<select name="'.$vName.'_combobox" size="1" '.$vDisabled.' onChange="javascript:textbox_comboboxChange(document.Form.'.$vName.', this)">'."\n";
												$vHTML .=  '<option value="" >&nbsp;</option>'."\n";
												$vHTML .= $vHTMLtemp;
												$vHTML .=  "</select>\n";

												 //Textbox part...
												 $vHTML .=  '<br /><input type="radio" name="'.$vName.'_radio" value="2" onClick="textbox_comboboxUpdate(document.Form.'.$vName.', document.Form.'.$vName.'_radio, document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_textbox)" />';
												 $vHTML .=  '<input name="'.$vName.'_textbox" type="text" disabled="true" onChange="javascript:textbox_comboboxChange(document.Form.'.$vName.', this)"';
												 }
											else 	
												{
												$vHTML .=  '<input type="radio" name="'.$vName.'_radio" value="1" onClick="textbox_comboboxUpdate(document.Form.'.$vName.', document.Form.'.$vName.'_radio, document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_textbox)" />';
												$vHTML .=  '<select name="'.$vName.'_combobox" size="1" disabled="true" onChange="javascript:textbox_comboboxChange(document.Form.'.$vName.', this)" >'."\n";
												$vHTML .=  '<option value="" >&nbsp;</option>'."\n";
												$vHTML .= $vHTMLtemp;
												$vHTML .=  "</select>\n";

												 //Textbox part...
												 $vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName.'_textbox');
												 $vHTML .=  '<br /><input type="radio" name="'.$vName.'_radio" value="2" onClick="textbox_comboboxUpdate(document.Form.'.$vName.', document.Form.'.$vName.'_radio, document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_textbox)" checked />';
												 $vHTML .=  '<input name="'.$vName.'_textbox" type="text" '.$vDisabled.' onChange="javascript:textbox_comboboxChange(document.Form.'.$vName.', this)"';
												 if (strlen($vSelected)>0)
													{
													$vHTML .=  'value="'.htmlentities($vSelected).'"';
													}
													else
													{
													$vHTML .=  'value=""';
													}
												}


											if (strlen($vLen)>0)
												{
												$vLenShow = ($vLen>100)? 100: $vLen;
												$vLenEdit = ($vLen>$vLenEdit)? $vLen: $vLenEdit;
												$vHTML .=  ' size="'.$vLenShow.'" maxlength="'.$vLenEdit.'" ';
												}
											 $vHTML .=  " />\n";
											 if (strpos($vType,'link')>0) {
												$vHTML .='<input type="button" name="'.$vName.'_button" onClick="javascript:window.open(document.Form.'.$vName.'_textbox.value);" value=" '.$button_strings['Go'].' " />       '."\n";
												}
											 else	{
												//we declare this component to can be used by the file component that follow...
												$vHTML .='<input type="hidden" name="'.$vName.'_button" value="" />'."\n";
												}
											 if (strpos($vType,'file')>0) {
												$vHTML .='<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />'."\n";
												//$vName.'_file
												//
												// if $vName.'_fileLoaded == 0 -> not has been loaded a file,
												// if $vName.'_fileLoaded == 1 -> has been loaded a file,
												// if $vName.'_fileLoaded == 2 -> is necessary to load now the file

												$vActionLoaded = '';
												if (isset($HTTP_PARAM_VARS[$vName.'_fileLoaded']))
													{
													$vActionLoaded = $HTTP_PARAM_VARS[$vName.'_fileLoaded'];
													}
												else if (isset($s_fields_value[$vTable][$vField.'_file']))
													{
													$vActionLoaded = $s_fields_value[$vTable][$vField.'_fileLoaded'];
													}
												else	{
													$vActionLoaded = '';
													}

												if (!IsEmpty($vActionLoaded))
													{
													  if ($vActionLoaded=="2")
														{

														if (is_uploaded_file($HTTP_POST_FILES[$vName.'_file']['tmp_name']))
															{
															if (file_exists($vPathCopy.$HTTP_POST_FILES[$vName.'_file']['name']))
																{
																$vFileName = substr ($HTTP_POST_FILES[$vName.'_file']['name'], 0,strpos($HTTP_POST_FILES[$vName.'_file']['name'],'.'));
																$vFileExt = substr ($HTTP_POST_FILES[$vName.'_file']['name'], strpos($HTTP_POST_FILES[$vName.'_file']['name'],'.')+1);
																$vI = 0;
																while (file_exists($vPathCopy.$vFileName.sprintf("%d", ++$vI).'.'.$vFileExt)) ;
																copy($HTTP_POST_FILES[$vName.'_file']['tmp_name'], $vPathCopy.$vFileName.sprintf("%d", $vI).'.'.$vFileExt);
																$vScriptIni .= "\n".'	textbox_comboboxFromFileUpdate(document.Form.'.$vName.', document.Form.'.$vName.'_radio, document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_textbox, "'.$vPathRef.$vFileName.sprintf("%d", $vI).'.'.$vFileExt.'", document.Form.'.$vName.'_fileLoaded,'."''".', document.Form.'.$vName.'_button); ';
																// to know if was downloaded the file....
																$warning .= 'The file loaded '.$HTTP_POST_FILES[$vName.'_file']['name'].' was renamed as '.$vFileName.sprintf("%d", $vI).'.'.$vFileExt.' because there was other with the same name, it was saved fine...';
																$vHTML .='<input name="'.$vName.'_fileLoaded" type="hidden" value="1" />'."\n";
																}
															else	{
																if (!copy($HTTP_POST_FILES[$vName.'_file']['tmp_name'], $vPathCopy.$HTTP_POST_FILES[$vName.'_file']['name']))
																	{
																	$error .= "File was not copied to: ".$vPathCopy.$HTTP_POST_FILES[$vName.'_file']['name'];
																	// to know if was downloaded the file....
																	$vHTML .='<input name="'.$vName.'_fileLoaded" type="hidden" value="0" />'."\n";
																	}
																else	{
																	$vScriptIni .= "\n".'	textbox_comboboxFromFileUpdate(document.Form.'.$vName.', document.Form.'.$vName.'_radio, document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_textbox, "'.$vPathRef.$HTTP_POST_FILES[$vName.'_file']['name'].'", document.Form.'.$vName.'_fileLoaded,'."''".', document.Form.'.$vName.'_button); ';
																	// to know if was downloaded the file....
																	$vHTML .='<input name="'.$vName.'_fileLoaded" type="hidden" value="1" />'."\n";
																	$message .= 'The loaded file '.$HTTP_POST_FILES[$vName.'_file']['name'].' was saved fine...';
																	}
																}
															}
														else 	{
															$error .= "Possible file upload attack. Filename: " . $HTTP_POST_FILES[$vName.'_file']['name'];
															$error .= "<br />Temporal Filename: " . $HTTP_POST_FILES[$vName.'_file']['tmp_name'];
															}

														}
													else	{
														// to know if was downloaded the file....
														$vHTML .='<input name="'.$vName.'_fileLoaded" type="hidden" value="'.$HTTP_PARAM_VARS[$vName.'_fileLoaded'].'" />'."\n";
														}

													}
												else 	{
													// to know if was downloaded the file....
													$vHTML .='<input name="'.$vName.'_fileLoaded" type="hidden" value="0" />'."\n";
													}

												$vHTML .='<br />Archivo: <input name="'.$vName.'_file" type="file" onBlur="javascript:textbox_comboboxFromFileUpdate(document.Form.'.$vName.', document.Form.'.$vName.'_radio, document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_textbox, this.value , document.Form.'.$vName.'_fileLoaded, '."'".$vPathRef."'".', document.Form.'.$vName.'_button); " value="" />       '."\n";

												}
											//We save the values really hidden...
											$vHTML .= '<input name="'.$vName.'" type="hidden" value="'.$vSelected.'" />'."\n";

											}
										elseif ( ($vType=='memo') or
											 ($vType=='memo_add') or
											 ($vType=='memo_combobox_add'))
											{  //case memo...
											if ($vType=='memo_add')
												{
												//_______________________________________________________________
												$vtFieldst = $vField.'_fieldst';
												$vNamelst = $vField.'_fieldvalue';
												$IsThereTextbox_list = 1; //in case of there is the type 'textbox_list' in the form is true (that is used to include the javascript in the user webpage...)
												$vIstLinked = false;  //is linked the description value with the key value...

												//Search the value of the field in the dictionary db to show in the textbox...
												$vHTML .=  '<input name="'.$vTable."__".$vtFieldst.'" type="text" '.$vDisabled.' onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
												$vHTML .=  'value=""';
												//size of the edit field...
												if (strlen($vLen)>0)   {
													$vLenShow = ($vLen>50)? 50: $vLen;
													$vLenEdit = ($vLen>$vLenEdit)? $vLen: $vLenEdit;
													$vHTML .=  ' size="'.$vLenShow.'" maxlength="'.$vLenEdit.'" ';
													}
												 $vHTML .=  " />\n";
												 //button to execute the search and link actions...
												$vHTML .=  '<input name="'.$vNamelst.'_Button" type="button" '.$vDisabled.' onClick="memo_textboxlist_Add';
												$vHTML .=  "('GiveMeLst.php','".urlencode($vContent)."','height=150,dependent=1',this, document.Form.".$vNamelst.", document.Form.".$vTable."__".$vtFieldst.",     '".$vId."','".$vDesc."','".htmlentities(urlencode($vSql))."',this.value,'".$vTable.'__'.$vtFieldst."','".$vNamelst."'".',document.Form.'.$vName.', '."'".$vDelimitedChar."'".', '."'".$vFind."'".', '."'".$button_strings['Add']."'".')"';
												//what we go to do, a link o a search?...
												if ($vIstLinked)
													{
													$vHTML .=  ' value="'.$button_strings['Add'].'" />'."\n";
													}
													else
													{
													$vHTML .=  ' value="'.$vFind.'" />'."\n";
													}
												//hidden field with the value of the Id/code of the field present in the dictionary db
												//that is changed when any option is selected...
												$vHTML .=  '<input name="'.$vNamelst.'" type="hidden" value="" />'."\n";
												//$vHTML .=  '	<input name="'.$vName.'_ButtonAdd" type="button" '.$vDisabled.' onClick="memo_textboxlist_Add(document.Form.'.$vNamelst.', document.Form.'.$vTable."__".$vtFieldst.', document.Form.'.$vName.', '."'".$vDelimitedChar."'".')"';

												//$vHTML .=  ' value="'.$button_strings['Add'].'">'."\n";
												$vHTML .=  '<br />';
												//_______________________________________________________________
												}

											if ($vType=='memo_combobox_add')
												{
												//_______________________________________________________________
												//execute the query with the values to show in the select combobox...
												$rec = $dbhandle->Execute($vSql);
												if ($rec === FALSE) {
													$db_error .= $dbhandle->ErrorMsg();
													echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
													echo "<br />Error ejecutando SQL (11): $vSql <br />";
													echo $db_error."<br /><pre>";
													print_r($s_xml_conf['elements'][$vnelem]);
													echo "</pre>";
													//require('./inc/script_end.inc.php');
													//exit;
	
													if ($Confs["DEBUG"] === TRUE) {
														$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
														}
													}
												else	{
													if ($Confs["DEBUG"] === TRUE) {
														$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
														}
													}

												$vHTML .=  '	<select name="'.$vName.'_combobox" size="1" '.$vDisabled.'>'."\n";
												$vHTML .=  '	<option value="" >&nbsp;</option>'."\n";
												while (($rec) && (!$rec->EOF))
													{
													//if the values to show are equal to the field value, select this...
													$vvvalue = trim($rec->fields[$vId]);
													if (isset($s_fields_value[$vTable][$vField.'_combobox']))
														{
														if ($s_fields_value[$vTable][$vField.'_combobox']==$vvvalue)
															{
															$vHTML .=  '	<option value="'.$vvvalue.'" selected>'.htmlentities($rec->fields[$vDesc])."</option>\n";
															}
														else	{
															$vHTML .=  '	<option value="'.$vvvalue.'" >'.htmlentities($rec->fields[$vDesc])."</option>\n";
															}
														}
													else	{
														$vHTML .=  '	<option value="'.$vvvalue.'" >'.htmlentities($rec->fields[$vDesc])."</option>\n";
														}
													$rec->MoveNext();
													}
												$vHTML .=  "	</select>\n";

												$vHTML .=  '	<input name="'.$vName.'_ButtonAdd" type="button" '.$vDisabled.' onClick="memo_combobox_Add('.$vName.'_combobox, document.Form.'.$vName.', '."'".$vDelimitedChar."'".')"';

												$vHTML .=  ' value="'.$button_strings['Add'].'" />'."\n";
												$vHTML .=  '<br />';
												//_______________________________________________________________
												}

											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName);
											 $vHTML .=  '<textarea name="'.$vName.'" '.$vDisabled.' onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
											if (strlen($vLen)>0)   {
												$vHTML .=  ' cols="'.$vLen.'"';
												}
											if (strlen($vLines)>0)   {
												$vHTML .=  ' rows="'.$vLines.'"';
												}
											$vHTML .=  '>';

											if (strlen($vSelected)>0)  {
												$vHTML .=  htmlentities($vSelected);
												}
											 $vHTML .=  "</textarea>\n";
											}

										elseif (($vType=='textbox_list') and ($vSqlOk))
											{  //case the textbox_list...
											$vtFieldst = (empty($vFieldst)) ? $vField.'_fieldst': $vFieldst;
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vTable."__".$vtFieldst);
											$IsThereTextbox_list = 1; //in case of there is the type 'textbox_list' in the form is true (that is used to include the javascript in the user webpage...)
											$vIstLinked = false;  //is linked the description value with the key value...

											//Search the value of the field in the dictionary db to show in the textbox...
											if (empty($vFieldst)) 
												{
												$vHTML .=  '<input name="'.$vTable."__".$vtFieldst.'" type="text" '.$vDisabled.' onChange="javascript: '.$vName.'.value = '.$vTable."__".$vtFieldst.'.value;"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
												}
											else
												{
												$vHTML .=  '<input name="'.$vTable."__".$vtFieldst.'" type="text" '.$vDisabled.' OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
												}

											if (strlen($vSelected)>0)
												{
												//We supose that was used the primary key as Id variable (unique)
												$vSql1 = $vSql;
												//create a new sql sentences with the primary key
												if (strpos(strtoupper($vSql1), "WHERE")>0)
													{
													$vSql1 = substr($vSql1, 0, strpos(strtoupper($vSql1), "WHERE"));
													}

												$vtId = ereg_replace('#1',$vId,$syntax['field']);
												$vv = ereg_replace('#1',$vSelected."",$syntax['string']);

												$vSql1 = $vSql1.' where ('.$vtId.'='.$vv.')';

												$rec = $dbhandle->Execute($vSql1);
												if ($rec === FALSE) {
													$db_error .= $dbhandle->ErrorMsg();
													echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
													echo "<br />Error ejecutando SQL (12): $vSql1 <br />";
													echo $db_error."<br /><pre>";
													print_r($s_xml_conf['elements'][$vnelem]);
													echo "</pre>";
													require('./inc/script_end.inc.php');
													exit;
	
													if ($Confs["DEBUG"] === TRUE) {
														$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql1 . '[Error]';
														}
													}
												else	{
													if ($Confs["DEBUG"] === TRUE) {
														$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql1.' [Ok]';
														}
													}
												}

											if ((strlen($vFieldst)==0) and (strlen($vSelected)>0))
												{  //case of there is not a field in the main db with the value of the dictionary db (with changes)...as locality...
												$vIstLinked = ((strlen($rec->fields[$vDesc])>0));
												$vHTML .=  'value="'.htmlentities($rec->fields[$vDesc]).'"';
												
												}
											elseif (strlen($vFieldst)>0)  //if there is a field in the main db with the dictionary field but that can be modified...
												{
												if ((strlen($vSelected)>0) and
												   ($rec->fields[$vDesc]!=$s_fields_value[$vTable][$vFieldst]))
												   {
												   if ((empty($s_fields_value[$vTable][$vFieldst])) and (!empty($rec->fields[$vDesc])))
													{
													$s_fields_value[$vTable][$vFieldst] = $rec->fields[$vDesc];

													}
												   else	{
													$vComments .= '<br />The field: "'.$vContent.'" has a value in the codec different to the value of the field, so: ['.$rec->fields[$vDesc].']<>['.$s_fields_value[$vTable][$vFieldst].']';
													}
												   }
												$vIstLinked = ((strlen($s_fields_value[$vTable][$vFieldst])>0) and (strlen($vSelected)>0));
												$vHTML .=  'value="'.htmlentities($s_fields_value[$vTable][$vFieldst]).'"';

												}
											else  //strlen($vSelected)==0  //there is not selected value, is blank...
												{
												$vHTML .=  'value=""';
												}

											//size of the edit field...
											if (strlen($vLen)>0)   {
												$vLenShow = ($vLen>100)? 100: $vLen;
												$vLenEdit = ($vLen>$vLenEdit)? $vLen: $vLenEdit;
												$vHTML .=  ' size="'.$vLenShow.'" maxlength="'.$vLenEdit.'" ';
												}
											 $vHTML .=  " />\n";

											if (empty($vFieldst))
												$vLinked = $vFind; 
											 
											//button to execute the search and link actions...
											$vHTML .=  '<input name="'.$vName.'_Button" type="button" '.$vDisabled.' onClick="openWindowTextbox_lst';
											$vHTML .=  "('GiveMeLst.php','".urlencode($vContent)."','height=150,dependent=1',this, document.Form.".$vName.", document.Form.".$vTable."__".$vtFieldst.",     '".$vId."','".$vDesc."','".htmlentities(urlencode($vSql))."','".$vLinked."','".$vTable.'__'.$vtFieldst."','".$vName."')";
											
											//what we go to do, a link o a search?...
											if ($vIstLinked)
												{
												$vHTML .=  '" value="'.$vLinked.'" />'."\n";
												}
												else
												{
												$vHTML .=  '" value="'.$vFind.'" />'."\n";
												}
											//hidden field with the value of the Id/code of the field present in the dictionary db
											//that is changed when any option is selected...
											$vHTML .=  "<input name=".$vName." type='hidden' value='".$vSelected."' />  \n";

											}
										elseif (($vType=='textbox_add') and ($vSqlOk))
											{  //case the textbox_list...
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName);
											$IsThereTextbox_add  = 1; //in case of there is the type 'textbox_add' in the form is true (that is used to include the javascript in the user webpage...)
											$vHTML .=  '<input name="'.$vName.'" type="text" '.$vDisabled.' onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';

											if (strlen($vSelected)>0)  {
												$vHTML .=  'value="'.$vSelected.'"';
												}
												else
												{
												$vHTML .=  'value=""';
												}

											if (strlen($vLen)>0)   {
												$vLenShow = ($vLen>100)? 100: $vLen;
												$vLenEdit = ($vLen>$vLenEdit)? $vLen: $vLenEdit;
												$vHTML .=  ' size="'.$vLenShow.'" maxlength="'.$vLenEdit.'" ';
												}
											 $vHTML .=  " />\n";

											//button to execute the search and link actions...
											$vHTML .=  '<input name="'.$vName.'_Button" type="button" '.$vDisabled.' onClick="openWindowTextbox_add';
											$vHTML .=  "('AddBox.php','','width=500,height=250', document.Form.".$vName.",'".$vId."','".$vDesc."','".htmlentities(urlencode($vSql))."','".$vName."', '".$vDelimitedChar.'_'."')";

											$vHTML .=  '" value="'.$vFind.'" />'."\n";

											}
										elseif (($vType=='button_update') and ($vSqlOk))
											{
											$IsThereTextbox_add  = 1;
											$vButtonCant++;
											$vHTML .=  '<input name="button'.($vButtonCant).'" type="button" '.$vDisabled.' onClick="openWindow_buttonUpdate';

											$vHTML .=  "('UpdateValues.php','','width=500,height=250',"."'".htmlentities(urlencode($vAction))."'".", "."'".$vSql."'"." )";
											//$vHTML .=  "('UpdateValues.php','','',"."'".htmlentities(urlencode($vAction))."'".", "."'".$vSql."'"." )";
											$vHTML .=  '" value="'.htmlentities($vContent).'" />'."\n";
											}
										elseif (($vType=='button_jscript'))
											{

											$vButtonCant++;
											$vHTML .=  '<input name="button'.($vButtonCant).'" type="button" '.$vDisabled.' onClick="javascript:button_js'.($vButtonCant).'();"';
											//$vHTML .=  '<input name="button'.($vButtonCant).'" type="button" '.$vDisabled.' onClick="alert('."'veamos'".')"';
											//$vHTML .=  '<input name="button'.($vButtonCant).'" type="button" '.$vDisabled.' onClick="alert('."'veamos'".')" value="veamos">';

											$vHTML .=  ' value="'.htmlentities($vContent).'" />'."\n";

											$vScript .=  'function button_js'.($vButtonCant).'() ';
											$vScript .=  '{ '."\n";;
											$vScript .= $vAction."\n";
											$vScript .=  '}'."\n";


											}
										elseif (($vType=='listbox_combobox') and ($vSqlOk))
											{  //case the textbox_list...
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName.'_listbox');
											$IsThereListbox  = 1; //in case of there is the type 'Listbox' in the form is true (that is used to include the javascript in the user webpage...)
											$vvtemp = array();
											$vvtempselected ='';
											$vrows = (IsEmpty($vrows))? 9: $vrows;

											$vmultiRecords = ((!($vSummaryField==$vTable.'.'.$vField)) and (!IsEmpty($vHuckField)));

											//execute the query with the values to show in the select combobox...
											$rec = $dbhandle->Execute($vSql);

											if ($rec === FALSE) {
												$db_error .= $dbhandle->ErrorMsg();
												echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
												echo "<br />Error ejecutando SQL (13): $vSql <br />";
												echo $db_error."<br /><pre>";
												print_r($s_xml_conf['elements'][$vnelem]);
												echo "</pre>";
												require('./inc/script_end.inc.php');
												exit;

												if ($Confs["DEBUG"] === TRUE) {
													$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
													}
												}
											else	{
												if ($Confs["DEBUG"] === TRUE) {
													$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
													}
												}

											$vHTML .=  "\n<table border='0'>\n";
											$vHTML .=  "<tr>\n";
											$vHTML .=  "	<td>\n";
											$vHTML .=  '	<select name="'.$vName.'_combobox" size="1" '.$vDisabled.'>'."\n";
											$vHTML .=  '	<option value="" >&nbsp;</option>'."\n";

											if ($vmultiRecords)
												{

												if ($checkSearch) //the values of the form are the result of a search at this moment......
													{
													$vrecTemp = &$dbhandle->Execute($vSqlSearch);
													if ($recTemp === FALSE) {
														$db_error .= $dbhandle->ErrorMsg();
														echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
														echo "<br />Error ejecutando SQL (14): $vSqlSearch <br />";
														echo $db_error."<br /><pre>";
														print_r($s_xml_conf['elements'][$vnelem]);
														echo "</pre>";
														require('./inc/script_end.inc.php');
														exit;
		
														}

													$vSelected = '';
													$vOrderSelected = array();
													$vvOrderPos = -1;
													while (($vrecTemp) && (!$vrecTemp->EOF))
														{
														$vvOrderPos = (!empty($vOrderField)) ? $vrecTemp->fields[$vOrderField]: $vvOrderPos+1;
														if (isset($vOrderSelected[$vvOrderPos]))
															{
															$warning .= '<br />There is two values with the same order in the field '.$vName;
															}
														else	{
															$vOrderSelected[$vvOrderPos] = $vrecTemp->fields[$vField];
															}

														$vrecTemp->MoveNext();
														}
													ksort($vOrderSelected);

													foreach ($vOrderSelected as $vkey => $vval)
														{
														$vSelected .= (IsEmpty($vSelected)) ? $vval : $vDelimitedChar.$vval;
														}
													}
												}
											else 	{
												//$vSelected already will have the value of the field.
												}

											if (!IsEmpty($vSelected)) {
												$s_fields_value[$vTable][$vField.'_combobox'] = '';
												if (trim($vDelimitedChar)=="")
													{
													$vvtemp1 = array();
													for ($i = 0; $i < strlen($vSelected); $i++)
														{
														$vvtemp1[]= substr ($vSelected, $i, 1);
														}
													}
												else	{
													$vvtemp1 = split($vDelimitedChar,$vSelected);
													}
												foreach ($vvtemp1 as $tt)
													{
													$vvtemp[sprintf ("%s", $tt)] = '';
													}
												if (isset($s_fields_value[$vTable][$vField.'_selected']))
													{
													$vvtempselected =sprintf ("%s",$s_fields_value[$vTable][$vField.'_selected']);
													}
												else	{
													$vvtempselected = '';
													}

												}


											while (($rec) && (!$rec->EOF)){
												//if the values to show are equal to the field value, select this...
												$vvvalue = trim($rec->fields[$vId]);
												if (isset($s_fields_value[$vTable][$vField.'_combobox']))
													{
													if (array_key_exists(sprintf ("%s", $vvvalue), $vvtemp))
														{
														$vvtemp[sprintf ("%s", $vvvalue)] = $rec->fields[$vDesc];
														}
													if ($s_fields_value[$vTable][$vField.'_combobox']==$vvvalue) {
														$vHTML .=  '	<option value="'.$vvvalue.'" selected>'.$rec->fields[$vDesc]."</option>\n";
														}
													else	{
														$vHTML .=  '	<option value="'.$vvvalue.'" >'.htmlentities($rec->fields[$vDesc])."</option>\n";
														}
													}
												else	{

													$vHTML .=  '	<option value="'.$vvvalue.'" >'.htmlentities($rec->fields[$vDesc])."</option>\n";
													}
												$rec->MoveNext();
												}
											$vHTML .=  "	</select>\n";
											$vHTML .=  "	</td>\n";
											$vHTML .=  "	<td>\n";
											//Button add
											$vHTML .=  '	<input name="'.$vName.'_ButtonAdd" type="button" '.$vDisabled.' onClick="listboxAdd(document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"';
											$vHTML .=  ' value="'.$button_strings['Add'].'" />'."\n";

											$vHTML .=  "	</td>\n";
											$vHTML .=  "</tr>\n";
											$vHTML .=  "<tr>\n";
											$vHTML .=  "	<td>\n";

											$vHTML .=  '	<select size="'.$vrows.'" multiple name="'.$vName.'_listbox" onChange="listboxUpdate(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".') ">'."\n";
											foreach ($vvtemp as $tt=>$vv)  {

												if ($tt==$vvtempselected) {
													$vHTML .=  '	<option value="'.$tt.'" selected>'.htmlentities($vv)."</option>\n";
													}
												else 	{
													$vHTML .=  '	<option value="'.$tt.'" >'.htmlentities($vv)."</option>\n";
													}
												}
											$vHTML .=  "	</select>\n";
											$vHTML .=  "	</td>\n";
											$vHTML .=  "	<td>\n";
											$vHTML .=  "		<table border='0'>\n";
											if ( (!IsEmpty($vSummaryField))) {
												$vSummaryFieldTable = substr ($vSummaryField, 0,strpos($vSummaryField,'.'));
												$vSummaryFieldField = substr ($vSummaryField, strpos($vSummaryField,'.')+1);
												$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonSummary" type="button" '.$vDisabled.' onClick="listboxSummary(document.Form.'.$vSummaryFieldTable.'__'.$vSummaryFieldField.', document.Form.'.$vName.'_listbox, '."'".$vDelimitedSummaryChar."'".')" value="Resumir" /></td></tr>'."\n";
												}
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonDel" type="button" '.$vDisabled.' onClick="listboxDel(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')" value="'.$button_strings['Delete'].'" /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonDel" type="button" '.$vDisabled.' onClick="listboxClear(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')" value="'.$button_strings['Start'].'" /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonUp" type="button" '.$vDisabled.' onClick="listboxUp(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Up'].'" /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonUp" type="button" '.$vDisabled.' onClick="listboxDown(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Down'].'" /></td></tr>'."\n";
											$vHTML .=  "		</table>\n";
											$vHTML .=  "	</td>\n";
											$vHTML .=  "</tr>\n";
											$vHTML .=  "</table>\n";

											//We save the values really hidden (to process the multivalues....)
											$vHTML .= '<input name="'.$vName.'" type="hidden" value="'.$vSelected.'" />';
											if (isset($s_fields_value[$vTable][$vField.'_selected'])) {
												$vHTML .= '<input name="'.$vName.'_selected" type="hidden" value="'.$s_fields_value[$vTable][$vField.'_selected'].'" />';
												}
											else	{
												$vHTML .= '<input name="'.$vName.'_selected" type="hidden" value="" />';
												}

											}
										elseif (	(($vType=='listbox_textbox_list') and ($vSqlOk)) or
												($vType=='listbox_textbox')  )
											{  //case the textbox_list...
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName.'_listbox');
											$IsThereListbox  = 1; //in case of there is the type 'Listbox' in the form is true (that is used to include the javascript in the user webpage...)
											$vvtemp = array();
											$vvtempselected ='';
											$vrows = (IsEmpty($vrows))? 9: $vrows;

											$vmultiRecords = ((!($vSummaryField==$vTable.'.'.$vField)) and (!IsEmpty($vHuckField)));


											//$rec = $dbhandle->Execute($vSql);
											//if ($rec === FALSE) {
											//	$db_error .= $dbhandle->ErrorMsg();
											//	if ($Confs["DEBUG"] === TRUE) {
											//		$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql . '[Error]';
											//		}
											//	}
											//else	{
											//	if ($Confs["DEBUG"] === TRUE) {
											//		$s_sql_log[gmstrftime ("%b %d %Y %H:%M:%S",substr ($start_time, strpos($start_time,' ')+1))][] = $vSql.' [Ok]';
											//		}
											//	}


											if ($vmultiRecords)
												{
												if ($checkSearch) //the values of the form are the result of a search at this moment......
													{
													$vrecTemp = &$dbhandle->Execute($vSqlSearch);
												if ($recTemp === FALSE) {
													$db_error .= $dbhandle->ErrorMsg();
													echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
													echo "<br />Error ejecutando SQL (15): $vSqlSearch <br />";
													echo $db_error."<br /><pre>";
													print_r($s_xml_conf['elements'][$vnelem]);
													echo "</pre>";
													require('./inc/script_end.inc.php');
													exit;
	
													
													}
													$vSelected = '';
													$vOrderSelected = array();
													$vvOrderPos = -1;
													while (($vrecTemp) && (!$vrecTemp->EOF))
														{
														$vvOrderPos = (!empty($vOrderField)) ? $vrecTemp->fields[$vOrderField]: $vvOrderPos+1;
														if (isset($vOrderSelected[$vvOrderPos]))
															{
															$warning .= '<br />There is two values with the same order in the field '.$vName;
															}
														else	{
															$vOrderSelected[$vvOrderPos] = $vrecTemp->fields[$vField];
															}

														$vrecTemp->MoveNext();
														}
													ksort($vOrderSelected);
													foreach ($vOrderSelected as $vkey => $vval)
														{
														$vSelected .= (IsEmpty($vSelected)) ? $vval : $vDelimitedChar.$vval;
														}
													}
												}
											else 	{
												//$vSelected already will have the value of the field.
												}

											if (!IsEmpty($vSelected))
												{
												if (trim($vDelimitedChar)=="")
													{
													$vvtemp1 = array();
													for ($i = 0; $i < strlen($vSelected); $i++)
														{
														$vvtemp1[]= substr ($vSelected, $i, 1);
														}
													}
												else	{
													$vvtemp1 = split($vDelimitedChar,$vSelected);
													}
												foreach ($vvtemp1 as $tt)
													{
													$vvtemp[sprintf ("%s", $tt)] = sprintf ("%s", $tt);
													}
												if (isset($s_fields_value[$vTable][$vField.'_selected']))
													{
													$vvtempselected =sprintf ("%s",$s_fields_value[$vTable][$vField.'_selected']);
													}
												else	{
													$vvtempselected = '';
													}

												}


											if (isset($vrecord[$vrecordPos]) and ($vType=='listbox_textbox_list'))
												{
												$vSqltmp = $vSql;
												if (@preg_match('|.*(select .+ from .+)(where(.*))?|im', $vSql, $match))
													{
													$vSqltmp = $match[1];
													}
												$fld = $rec->FetchField($vrecord[$vrecordPos]->fields[$vField]);
												$type = $rec->MetaType($fld->type);

												if (	($type=='I') or
													($type=='R') or
													($type=='N')	)
													{
													$vsyntax = $syntax['number'];
													}
												else	{
													$vsyntax = $syntax['string'];
													}
												foreach ($vvtemp as $vvar=>$vval)
													{
													$vsqlt = ereg_replace('#1',$vId, $syntax['field']);
													$vsqlt .= '='.ereg_replace('#1',sprintf('%s',$vvar), $vsyntax);
													$rec = $dbhandle->Execute($vSqltmp.' where '.$vsqlt);
													if ( (!$rec->EOF ) )
														{
														$vvtemp[$vvar] = $rec->fields[$vDesc];
														}
													else	{
														$warning .= '<br />Was not found in the descriptor field in the Sql query: '.$vSql.' where '.$vsqlt;
														}
													}
												}




											$vHTML .=  "\n<table border='0'>\n";
											$vHTML .=  "<tr>\n";
											$vHTML .=  "	<td>\n";

											//_______________________________________________________________
											$vtFieldst = $vField.'_fieldst';
											$vNamelst = $vField.'_fieldvalue';
											$IsThereTextbox_list = 1; //in case of there is the type 'textbox_list' in the form is true (that is used to include the javascript in the user webpage...)
											$vIstLinked = false;  //is linked the description value with the key value...

											//Search the value of the field in the dictionary db to show in the textbox...
											$vHTML .=  '<input name="'.$vTable."__".$vtFieldst.'" type="text" '.$vDisabled.' onChange="javascript:CheckChange(this, '.$vIsEmptyOk.','."'".$vIsEmptyError."'".')"  OnFocus="javascript:PutStatusMsg('."'".$vStatusMsg."'".'); "';
											$vHTML .=  'value=""';
											//size of the edit field...
											if (strlen($vLen)>0)   {
												$vLenShow = ($vLen>50)? 50: $vLen;
												$vLenEdit = ($vLen>$vLenEdit)? $vLen: $vLenEdit;
												$vHTML .=  ' size="'.$vLenShow.'" maxlength="'.$vLenEdit.'" ';
												}
											 $vHTML .=  " />\n";
											 if ($vType=='listbox_textbox_list')
											 	{
												 //button to execute the search and link actions...
												$vHTML .=  '<input name="'.$vNamelst.'_Button" type="button" '.$vDisabled.' onClick="openWindowTextbox_lst';
												$vHTML .=  "('GiveMeLst.php','".urlencode($vContent)."','height=150,dependent=1',this, document.Form.".$vNamelst.", document.Form.".$vTable."__".$vtFieldst.",     '".$vId."','".$vDesc."','".htmlentities(urlencode($vSql))."','".$vLinked."','".$vTable.'__'.$vtFieldst."','".$vNamelst."')";
												//what we go to do, a link o a search?...
												if ($vIstLinked)
													{
													$vHTML .=  '" value="'.$vLinked.'" />'."\n";
													}
													else
													{
													$vHTML .=  '" value="'.$vFind.'">'."\n";
													}
												}
											//hidden field with the value of the Id/code of the field present in the dictionary db
											//that is changed when any option is selected...
											$vHTML .=  '<input name="'.$vNamelst.'" type="hidden" value="" />'."\n";
											//_______________________________________________________________



											$vHTML .=  "	</td>\n";
											$vHTML .=  "	<td>\n";
											//Button add
											 if ($vType=='listbox_textbox_list')
											 	{
												$vHTML .=  '	<input name="'.$vName.'_ButtonAdd" type="button" '.$vDisabled.' onClick="listbox_textboxlist_Add(document.Form.'.$vNamelst.', document.Form.'.$vTable."__".$vtFieldst.', document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"';
												}
											else	{
												$vHTML .=  '	<input name="'.$vName.'_ButtonAdd" type="button" '.$vDisabled.' onClick="listbox_textbox_Add(document.Form.'.$vTable."__".$vtFieldst.', document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"';
												}
											$vHTML .=  ' value="'.$button_strings['Add'].'" />'."\n";

											$vHTML .=  "	</td>\n";
											$vHTML .=  "</tr>\n";
											$vHTML .=  "<tr>\n";
											$vHTML .=  "	<td>\n";

											$vHTML .=  '	<select size="'.$vrows.'" multiple name="'.$vName.'_listbox" onChange="listboxUpdate(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".') ">'."\n";
											foreach ($vvtemp as $tt=>$vv)
												{
												if ($tt==$vvtempselected) {
													$vHTML .=  '	<option value="'.$tt.'" selected>'.htmlentities($vv)."</option>\n";
													}
												else 	{
													$vHTML .=  '	<option value="'.$tt.'" >'.htmlentities($vv)."</option>\n";
													}
												}
											$vHTML .=  "	</select>\n";
											$vHTML .=  "	</td>\n";
											$vHTML .=  "	<td>\n";
											$vHTML .=  "		<table border='0'>\n";
											if ( (!IsEmpty($vSummaryField))) {
												$vSummaryFieldTable = substr ($vSummaryField, 0,strpos($vSummaryField,'.'));
												$vSummaryFieldField = substr ($vSummaryField, strpos($vSummaryField,'.')+1);
												$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonSummary" type="button" '.$vDisabled.' onClick="listboxSummary(document.Form.'.$vSummaryFieldTable.'__'.$vSummaryFieldField.', document.Form.'.$vName.'_listbox, '."'".$vDelimitedSummaryChar."'".')" value="Resumir" /></td></tr>'."\n";
												}
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonDel" type="button" '.$vDisabled.' onClick="listboxDel(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')" value="'.$button_strings['Delete'].'" /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonDel" type="button" '.$vDisabled.' onClick="listboxClear(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')" value="'.$button_strings['Start'].'" /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonUp" type="button" '.$vDisabled.' onClick="listboxUp(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Up'].'" /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonUp" type="button" '.$vDisabled.' onClick="listboxDown(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Down'].'" /></td></tr>'."\n";
											$vHTML .=  "		</table>\n";
											$vHTML .=  "	</td>\n";
											$vHTML .=  "</tr>\n";
											$vHTML .=  "</table>\n";

											//We save the values really hidden (to process the multivalues....)
											$vHTML .= '<input name="'.$vName.'" type="hidden" value="'.$vSelected.'" />';
											if (isset($s_fields_value[$vTable][$vField.'_selected'])) {
												$vHTML .= '<input name="'.$vName.'_selected" type="hidden" value="'.$s_fields_value[$vTable][$vField.'_selected'].'" />';
												}
											else	{
												$vHTML .= '<input name="'.$vName.'_selected" type="hidden" value="" />';
												}

											}
										elseif (($vType=='listbox_listbox') and ($vSqlOk))
											{
											$vScriptIni .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName, $vName.'_listbox');
											$IsThereListbox  = 1; //in case of there is the type 'Listbox' in the form is true (that is used to include the javascript in the user webpage...)
											$vvtemp = array();
											$vvtempselected ='';
											$vrows = (IsEmpty($vrows))? 9: $vrows;

											$vmultiRecords = ((!($vSummaryField==$vTable.'.'.$vField)) and (!IsEmpty($vHuckField)));

											//execute the query with the values to show in the select combobox...
											$rec = $dbhandle->Execute($vSql);
											if ($rec === FALSE) {
												$db_error .= $dbhandle->ErrorMsg();
												echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
												echo "<br />Error ejecutando SQL (16): $vSql <br />";
												echo $db_error."<br /><pre>";
												print_r($s_xml_conf['elements'][$vnelem]);
												echo "</pre>";
												require('./inc/script_end.inc.php');
												exit;

												
												}


											$vHTML .=  "\n<table border='0'>\n";
											$vHTML .=  "<tr>\n";
											$vHTML .=  "	<td>Datos</td>\n";
											$vHTML .=  "	<td></td>\n";
											$vHTML .=  "	<td>Seleccionados</td>\n";
											$vHTML .=  "</tr>\n";

											$vHTML .=  "<tr>\n";
											$vHTML .=  "	<td>\n";
											$vHTML .=  '	<select name="'.$vName.'_combobox" size="'.$vrows.'" '.$vDisabled.'>'."\n";

											if ($vmultiRecords)
												{
												if ($checkSearch) //the values of the form are the result of a search at this moment......
													{
													$vrecTemp = &$dbhandle->Execute($vSqlSearch);
													if ($vrecTemp === FALSE) {
														$db_error .= $dbhandle->ErrorMsg();
														echo "<br />Error ejecutando SQL (17): $vSqlSearch <br />";
														echo $db_error."<br /><pre>";
														print_r($s_xml_conf['elements'][$vnelem]);
														echo "</pre>";
														require('./inc/script_end.inc.php');
														exit;
														}
													$vSelected = '';
													$vOrderSelected = array();
													$vvOrderPos = -1;
													while (($vrecTemp) && (!$vrecTemp->EOF))
														{
														$vvOrderPos = (!empty($vOrderField)) ? $vrecTemp->fields[$vOrderField]: $vvOrderPos+1;
														if (isset($vOrderSelected[$vvOrderPos]))
															{
															$warning .= '<br />There is two values with the same order in the field '.$vName;
															}
														else	{
															$vOrderSelected[$vvOrderPos] = $vrecTemp->fields[$vField];
															}

														$vrecTemp->MoveNext();
														}
													ksort($vOrderSelected);
													foreach ($vOrderSelected as $vkey => $vval)
														{
														$vSelected .= (IsEmpty($vSelected)) ? $vval : $vDelimitedChar.$vval;
														}
													}
												}
											else 	{
												//$vSelected already will have the value of the field.
												}

											if (!IsEmpty($vSelected)) {
												$s_fields_value[$vTable][$vField.'_combobox'] = '';
												if (trim($vDelimitedChar)=="")
													{
													$vvtemp1 = array();
													for ($i = 0; $i < strlen($vSelected); $i++)
														{
														$vvtemp1[]= substr ($vSelected, $i, 1);
														}
													}
												else	{
													$vvtemp1 = split($vDelimitedChar,$vSelected);
													}
												foreach ($vvtemp1 as $tt)  {
													$vvtemp[sprintf ("%s", $tt)] = '';
													}
												if (isset($s_fields_value[$vTable][$vField.'_selected']))
													{
													$vvtempselected =sprintf ("%s",$s_fields_value[$vTable][$vField.'_selected']);
													}
												else	{
													$vvtempselected = '';
													}

												}

											while (($rec) && (!$rec->EOF)){
												//if the values to show are equal to the field value, select this...
												$vvvalue = trim($rec->fields[$vId]);
												if (isset($s_fields_value[$vTable][$vField.'_combobox']))
													{
													if (array_key_exists(sprintf ("%s", $vvvalue), $vvtemp))
														{
														$vvtemp[sprintf ("%s", $vvvalue)] = $rec->fields[$vDesc];
														}
													if (trim($s_fields_value[$vTable][$vField.'_combobox'])==trim($vvvalue)) {
														$vHTML .=  '	<option value="'.$vvvalue.'" selected>'.htmlentities($rec->fields[$vDesc])."</option>\n";
														}
													else	{
														$vHTML .=  '	<option value="'.$vvvalue.'" >'.htmlentities($rec->fields[$vDesc])."</option>\n";
														}
													}
												else	{

													$vHTML .=  '	<option value="'.$vvvalue.'" >'.htmlentities($rec->fields[$vDesc])."</option>\n";
													}
												$rec->MoveNext();
												}
											$vHTML .=  "	</select>\n";
											$vHTML .=  "	</td>\n";
											$vHTML .=  "	<td>\n";
											//Button add
											$vHTML .=  "		<table border='0'>\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonAdd" type="button" '.$vDisabled.' onClick="listboxAdd(document.Form.'.$vName.'_combobox, document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"';
											$vHTML .=  ' value=" > " /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonDel" type="button" '.$vDisabled.' onClick="listboxDel(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')" value=" < " /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonDel" type="button" '.$vDisabled.' onClick="listboxClear(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')" value="'.$button_strings['Start'].'" /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonUp" type="button" '.$vDisabled.' onClick="listboxUp(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Up'].'" /></td></tr>'."\n";
											$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonUp" type="button" '.$vDisabled.' onClick="listboxDown(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')"  value="'.$button_strings['Down'].'" /></td></tr>'."\n";
											if ((!IsEmpty($vSummaryField))) {
												$vSummaryFieldTable = substr ($vSummaryField, 0,strpos($vSummaryField,'.'));
												$vSummaryFieldField = substr ($vSummaryField, strpos($vSummaryField,'.')+1);
												$vHTML .=  '		<tr><td><input name="'.$vName.'_ButtonSummary" type="button" '.$vDisabled.' onClick="listboxSummary(document.Form.'.$vSummaryFieldTable.'__'.$vSummaryFieldField.', document.Form.'.$vName.'_listbox, '."'".$vDelimitedSummaryChar."'".')" value="Resumir" /></td></tr>'."\n";
												}
											$vHTML .=  "		</table>\n";
											$vHTML .=  "	</td>\n";
											$vHTML .=  "	<td>\n";
											$vHTML .=  '	<select size="'.$vrows.'" multiple name="'.$vName.'_listbox" onChange="listboxUpdate(document.Form.'.$vName.'_listbox, document.Form.'.$vName.', document.Form.'.$vName.'_selected, '."'".$vDelimitedChar."'".')">'."\n";
											foreach ($vvtemp as $tt=>$vv)
												{
												if ($tt==$vvtempselected)
													{
													$vHTML .=  '	<option value="'.$tt.'" selected>'.htmlentities($vv)."</option>\n";
													}
												else 	{
													$vHTML .=  '	<option value="'.$tt.'" >'.htmlentities($vv)."</option>\n";
													}
												}
											$vHTML .=  "	</select>\n";
											$vHTML .=  "	</td>\n";
											$vHTML .=  "</tr>\n";
											$vHTML .=  "</table>\n";

											//We save the values really hidden (to process the multivalues....)
											$vHTML .= '<input name="'.$vName.'" type="hidden" value="'.$vSelected.'" />';
											if (isset($s_fields_value[$vTable][$vField.'_selected']))
												{
												$vHTML .= '<input name="'.$vName.'_selected" type="hidden" value="'.$s_fields_value[$vTable][$vField.'_selected'].'" />';
												}
											else	{
												$vHTML .= '<input name="'.$vName.'_selected" type="hidden" value="" />';
												}


											}
										elseif (($vType=='checkbox_multi') and ($vSqlOk))
											{  //case the checkbox_multi..
											$IsThereCheckbox_multi  = 1; //in case of there is the type 'Listbox' in the form is true (that is used to include the javascript in the user webpage...)
											$vScriptIniTmp = '';
											$vvtemp = array();
											$vvtempselected ='';
											$vrows = (IsEmpty($vrows))? 9: $vrows;
											//$vSelected = trim($vSelected);

											$vmultiRecords = ((!($vSummaryField==$vTable.'.'.$vField)) and (!IsEmpty($vHuckField)));

											//execute the query with the values to show in the select checkbox...

											$rec = $dbhandle->Execute($vSql);
											if ($rec === FALSE) {
												$db_error .= $dbhandle->ErrorMsg();
												echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
												echo "<br />Error ejecutando SQL (18): $vSql <br />";
												echo $db_error."<br /><pre>";
												print_r($s_xml_conf['elements'][$vnelem]);
												echo "</pre>";
												require('./inc/script_end.inc.php');
												exit;

												
												}

											if ($vmultiRecords)
												{
												if ($checkSearch) //the values of the form are the result of a search at this moment......
													{
													$vrecTemp = &$dbhandle->Execute($vSqlSearch);
													if ($recTemp === FALSE) {
														$db_error .= $dbhandle->ErrorMsg();
														echo "[<a href=\"edit.php?mod=".($HTTP_PARAM_VARS['mod'])."&s_id=".($HTTP_PARAM_VARS['s_id'])."&element=".$vnelem."\">Modificar XML para corregir</a>]";
														echo "<br />Error ejecutando SQL (19): $vSqlSearch <br />";
														echo $db_error."<br /><pre>";
														print_r($s_xml_conf['elements'][$vnelem]);
														echo "</pre>";
														require('./inc/script_end.inc.php');
														exit;
		
														}
													$vSelected = '';
													$vOrderSelected = array();
													$vvOrderPos = -1;
													while (($vrecTemp) && (!$vrecTemp->EOF))
														{
														$vvOrderPos = (!empty($vOrderField)) ? $vrecTemp->fields[$vOrderField]: $vvOrderPos+1;
														if (isset($vOrderSelected[$vvOrderPos]))
															{
															$warning .= '<br />There is two values with the same order in the field '.$vName;
															}
														else	{
															$vOrderSelected[$vvOrderPos] = $vrecTemp->fields[$vField];
															}

														$vrecTemp->MoveNext();
														}
													ksort($vOrderSelected);
													foreach ($vOrderSelected as $vkey => $vval)
														{
														$vSelected .= (IsEmpty($vSelected)) ? $vval : $vDelimitedChar.$vval;
														}
													}
												}
											else 	{
												//$vSelected already will have the value of the field.
												}

											if (!IsEmpty($vSelected)) {
												if (trim($vDelimitedChar)=="")
													{
													$vvtemp1 = array();
													for ($i = 0; $i < strlen($vSelected); $i++)
														{
														$vvtemp1[]= substr ($vSelected, $i, 1);
														}
													}
												else	{
													$vvtemp1 = split($vDelimitedChar,$vSelected);
													}

												foreach ($vvtemp1 as $tt)  {
													$vvtemp[trim(sprintf ("%s", $tt))] = '';
													}
												}

											$vcolsCant = (IsEmpty($vcols))? 1: $vcols;
											$vHTML .=  "\n<table border='0'>\n";
											$vcolsi = 0;
											$vcantCheck = 0;
											$vHTML .=  "<tr>";
											while (($rec) && (!$rec->EOF))
												{
												$vcantCheck++;
												if ($vcolsi==$vcolsCant) {
													$vHTML .=  "<tr>";
													$vcolsi = 0;
													}
												$vcolsi++;
												$vHTML .=  "<td><label>";

												$vScriptIniTmp .= CheckFocus($HTTP_PARAM_VARS['markGo'], $vName.sprintf ("%d", $vcantCheck), $vName.sprintf ("%d", $vcantCheck));

												//if the values to show are equal to the field value then check this...
												$vvvalue = trim($rec->fields[$vId]);
												if (array_key_exists(trim(sprintf ("%s", $vvvalue)), $vvtemp))
													{
													$vHTML .=  '<input name="'.$vName.sprintf ("%d", $vcantCheck).'" type="checkbox" '.$vDisabled.' onClick="CheckBoxMultiUpdate(this, document.Form.'.$vName.', '."'".$vDelimitedChar."'".')" value="'.htmlentities($vvvalue).'" checked />'."\n";
													}
												else	{
													$vHTML .=  '<input name="'.$vName.sprintf ("%d", $vcantCheck).'" type="checkbox" '.$vDisabled.' onClick="CheckBoxMultiUpdate(this, document.Form.'.$vName.', '."'".$vDelimitedChar."'".')" value="'.htmlentities($vvvalue).'" />'."\n";
													}

												$vHTML .=  $rec->fields[$vDesc].'</label></td>'."\n";
												if ($vcolsi==$vcolsCant)
													{
													$vHTML .=  "</tr>";
													}
												$rec->MoveNext();
												}
											if ($vcolsi!=$vcolsCant) {  //has not been closed the file...
												$vHTML .=  "</tr>";
												}
											$vHTML .=  "</table>\n";


											if ( (!IsEmpty($vSummaryField))) {
												$vSummaryFieldTable = substr ($vSummaryField, 0,strpos($vSummaryField,'.'));
												$vSummaryFieldField = substr ($vSummaryField, strpos($vSummaryField,'.')+1);
												$vHTML .=  '<input name="'.$vName.'_ButtonSummary" type="button" '.$vDisabled.' onClick="CheckBoxMultiSummary(document.Form.'.$vSummaryFieldTable.'__'.$vSummaryFieldField.', document.Form.'.$vName.', '."'".$vDelimitedSummaryChar."'".')" value="Resumir" /></tr>'."\n";
												}

											//We save the values really hidden (to process the multivalues....)
											$vHTML .= '<input name="'.$vName.'" type="hidden" value="'.$vSelected.'" />';
											$vScriptIni .= $vScriptIniTmp;

											}
										else   //any other unknown var will be declare with a hidden enter...
											{

											$vHTML .= '<input name="'.$vName.'" type="hidden" value="'.$vSelected.'" />';
											}
	//===============================================================================================================================================================================
										//in case of there is a help we put the symbol after the field in edition..
										if (strlen($vHelp)>0)
											{
											//$vHelpPath = $pathMod."help/".$s_xml_conf['lang']."/".$vHelp.".php";
											$vHelp = $vHelpPath.$vHelp;
											//echo "<a href='$vHelp'>$vHelp</a><br />";
											if ($vHelpOpenNewWindow)
												{
												$vHTML .=  '<a href="'.htmlentities($vHelp.'?keepThis=true&TB_iframe=true&height=400&width=800').'" title="'.$message_strings['Help'].'" class="thickbox">'.$Confs["HelpIco"].'</a>';
												}
											else	
												{
												$vHTML .=  '<a href="javascript:Redirect('."'".$vHelp."'".')">'.$Confs["HelpIco"].'</a>';
												}
											}

										if ($vAdminUser)
											{
											$vHTML .='<a href="edit.php?mod='.$vModName.'&s_id='.$HTTP_PARAM_VARS['s_id'].'&amp;element='.($vnelem).'">'.$Confs["EditIco"].'</a>';
											//$vHTML .='<a href="javascript:void(0)" onClick="javascript:window.open('."'".'edit.php?mod='.$vModName.'&element='.($vnelem)."','','');".'">'.$Confs["EditIco"].'</a>';
											}
										}
										
								}  //end of field tag part
								
							
						}  //end of mark element 1  
						
					//close the table and form...
					$vHTML .='</td></tr>';
					$vHTML .='</table>';
					//if thereis a Panel must to have a div opened that must be closed...
					if (!empty($panel)) $vHTML  .=  '</div>';  //fin del div del panel...
					}
				
				
				
					//if was selected one section, we show the end of the panel...
					if (($vdata!='all')  )
					{
						
						{
						$vHTML  .=  '<div '. (($vdata!='panel_goquery')? 'style="display: none;"': "") .' id="panel_goquery">';  //inicio del div del panel...
							$vHTML .=  "\n".'<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableForm">';
							$vHTML .=  '<tr><td>';
											
								
								
								$panel .=	'       <li><a id="menupanel_goquery" '
										.'href="javascript:NewPanel(\'panel_goquery\');">'
										.'&nbsp;&nbsp;'.$button_strings['Query'].'&nbsp;&nbsp;'
										.'</a>'
										."</li>\n";

								$vHTML .=  "\n".'<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableFieldValue">';
									{
									$vHTML .=  "\n".'<tr><td class="TableBar" >';
									$vHTML .=  $button_strings['OutputTo'];
									$HTTP_PARAM_VARS['query_dest'] = (!isset($HTTP_PARAM_VARS['query_dest']))? 'form': $HTTP_PARAM_VARS['query_dest'];
									$vHTML .=  ' <select name="query_dest" >'."\n";
									$vv = array("form","datagrid","excel");
									//$vv = array("form","datagrid","text","html","pdf","doc","csv","xml","xmlABCD","xmlDarwinCore","HISPID3","ITF2");
									foreach ($vv as $va)
										{
										$tt = ($va==$HTTP_PARAM_VARS['query_dest']) ? 'selected': '';
										$vHTML .=  '<option value="'.$va.'" '.$tt.'>'.$OutputType_strings[$va].'</option>'."\n";
										}
									$vHTML .=  '</select>'."\n";
									$vHTML .=  '	<input type="button" value="'.$button_strings["Select"].'" onClick="fquery_run_go() ;" />';
									$vHTML .=  '	<input name="query_runClicked" type="hidden" value="" />';

									$vHTML .=  '	|	';
									$vHTML .=  '	<input name="query_save" type="button" onClick="" value="'.$button_strings["SaveQuery"].'" />'."\n";
									$vHTML .=  '	<input name="query_load" type="button" onClick="" value="'.$button_strings["LoadQuery"].'" />'."\n";
									$vHTML .=  '	<input name="query_clear" type="button" onClick="" value="'.$button_strings["InitQuery"].'" />'."\n";
									}
								$vHTML .=  "</td></tr></table>\n";

								//Section of queries parameters...
									//--------------------------------------------------------------------

									$vHTML .=  '<table width="'.$vFormPercent.'%" border="1" cellpadding="4" cellspacing="0" class="TableFieldValue">'."\n";
									$vHTML  .=  '<tr><td class="TableField">'."\n";
										{
										//$vquery_fields_max_rows =  ($vQueryCount>15)? 15 : $vQueryCount;
										//combo.options[combo.selectedIndex].text
										//-------------------------------------------------------------------------------------
										$HTTP_PARAM_VARS['query_ModOptions'] = (!isset($HTTP_PARAM_VARS['query_ModOptions']))? $vModName : $HTTP_PARAM_VARS['query_ModOptions'];

										$vHTML .='<input name="query_ModOptionsActive" type="hidden" value="'.$vModName.'" />';
										$vFirstModShow = false;
										$vHTMLTemp = "";

										for ($i=0; $i < count($s_xml_conf['QueryModRelations']); $i++)
											{
											$vRelFile = ($s_xml_conf['QueryModRelations'][$i]['moduleMaster']==$xmlFile) ? ($s_xml_conf['QueryModRelations'][$i]['moduleDetail']) : "";
											$vRelFile = (empty($vRelFile)) ? (($s_xml_conf['QueryModRelations'][$i]['moduleDetail']==$xmlFile) ? ($s_xml_conf['QueryModRelations'][$i]['moduleMaster']) : "") : $vRelFile;

											if ( !empty($vRelFile) )
												{


												if (!$vFirstModShow)
													{
													$vFirstModShow = true;
													$vHTML .=  "\n".'	<select name="query_ModOptions" size="1" onChange="javascript: '."  GiveMeModQueryOptions(document.Form.query_ModOptions.value, 'query_ModQueryOptions');  ".'">'."\n";
													$vRelFileInfo = ($s_xml_conf['QueryModRelations'][$i]['moduleMaster']==$xmlFile) ? ($s_xml_conf['QueryModRelations'][$i]['moduleMasterInfo']) : "";
													$vRelFileInfo = (empty($vRelFileInfo)) ? (($s_xml_conf['QueryModRelations'][$i]['moduleDetail']==$xmlFile) ? ($s_xml_conf['QueryModRelations'][$i]['moduleDetailInfo']) : "") : $vRelFileInfo;
													$vHTML .=  '	<option value="'.$vModName.'" >'.htmlentities($vRelFileInfo)."</option>\n";
													}

												$vRelFileInfo = ($s_xml_conf['QueryModRelations'][$i]['moduleMaster']==$xmlFile) ? ($s_xml_conf['QueryModRelations'][$i]['moduleDetailInfo']) : "";
												$vRelFileInfo = (empty($vRelFileInfo)) ? (($s_xml_conf['QueryModRelations'][$i]['moduleDetail']==$xmlFile) ? ($s_xml_conf['QueryModRelations'][$i]['moduleMasterInfo']) : "") : $vRelFileInfo;

												$v_xmlRelfile = ($pathModPart["dirname"])."/".$vRelFile;
												$vHTML .=  '	<option value="'.$v_xmlRelfile.'"';
												$vMultiMod[$v_xmlRelfile] = "query_".($pathModPart["dirname"])."_".(ereg_replace('\.','_', $vRelFile));
												$vHTMLTemp .= '<input name="'.($vMultiMod[$v_xmlRelfile]).'" type="hidden" value="" />';
												if ($HTTP_PARAM_VARS['query_ModOptions']==($pathModPart["dirname"])."/".$vRelFile)
													{
													$vHTML .= ' selected';
													$vScriptIni .= "GiveMeModQueryOptions(document.Form.query_ModOptions.value, 'query_ModQueryOptions');";

													}
												$vHTML .=  ' >'.htmlentities($vRelFileInfo)."</option>\n";

												//$vHTML .=  '<div id="query_'.$vRelFile.'">Aqui estamos</div>';
												//$vScriptIni .= "  GiveMeModQueryOptions('".($pathModPart["dirname"])."/".$vRelFile."', 'query_ModQueryOptions');  ";
												}
											}
										if ($vFirstModShow)
											{
											$vHTML .= "\n".'	</select>'."\n";
											$vHTML .= '<input name="query_multiModOptions" type="hidden" value="1" />';
											$vHTML .= $vHTMLTemp;
											}
										else	{
											$vHTML .= '<input name="query_multiModOptions" type="hidden" value="0" />';
											}

										//-------------------------------------------------------------------------------------

										$HTTP_PARAM_VARS['conditions'] = (isset($HTTP_PARAM_VARS['conditions']))? $HTTP_PARAM_VARS['conditions']: '1';

										if ($HTTP_PARAM_VARS['conditions']=='1')
											{
											$vHTML .=  '<input name="query_work" type="button" onClick="javascript:change_query_work();" value="'.$button_strings['Conditions'].htmlentities('>>').'" />';
											}
										else	{
											$vHTML .=  '<input name="query_work" type="button" onClick="javascript:change_query_work();" value="'.$button_strings['Fields'].htmlentities('>>').'" />';
											}
										$vHTML .='<input name="conditions" type="hidden" value="'.$HTTP_PARAM_VARS['conditions'].'" />';
										$vHTML .='<br /><br /><br />';

										//$vHTML .=  '<input name="test" type="button" onClick=" '.("$.get('run.php?mod='+document.Form.query_ModOptionsActive.value+'&query_run=go&giveme_sql=true&in_mod=".$vModName."',function(xml){ var txt = $('sql', xml).attr('where'); alert('where: '+txt); });").'" value="Test de parametro: gime_sql xml1...">';
										//$vHTML .=  '<input name="test" type="button" onClick=" '.("$.get('run.php?mod='+document.Form.query_ModOptionsActive.value+'&query_run=go&giveme_sql=true&in_mod=".$vModName."',function(txt){ alert(txt); });").'" value="Test de parametro: gime_sql text...">';

										//$vHTML .=  '<input name="test" type="button" onClick="javascript: js_giveme_sql(); " value="Test de parametro: giveme_sql xml2...">';

										//$vHTML .=  '</td></tr><tr><td class="TableField">';
										//$vHTML .=  '<strong>'.$button_strings['Fields'].'</strong>';
										//$vHTML .=  '</td></tr><tr><td class="TableField">';
										
										//-------------------------------------------------------------------------------------

										$vHTML .=  '<div id="query_ModQueryOptions">';

											$vHTML .=  "\n".'	<select name="query_fields" size="'.($vquery_fields_max_rows).'" onClick="javascript:openWindowGiveMeQueryConditions(document.Form.query_fields.value, 1, document.Form.query_values,0);">'."\n";

											$vQueryCount = count($s_xml_conf['queryOptions']);
											for ($i=0; $i < $vQueryCount; $i++)
												{
												$vvvpos = ($s_xml_conf['queryOptions'][$i]['value']);
												if (isset($s_xml_conf['elements'][$vvvpos]['q_conditions'][$vinModName]))
													{
													$vqm = (count($s_xml_conf['elements'][$vvvpos]['q_conditions'][$vinModName])>0)? $vQueryMark	: '';
													}
												else	{
													$vqm = '';
													}
												$vHTML .= '	<option value="'.($s_xml_conf['queryOptions'][$i]['value']).'" >'.$vqm.$s_xml_conf['queryOptions'][$i]['text']."</option>\n";
												}


											$vHTML .=  "\n".'	</select>'."\n";
										$vHTML .=  '</div>';

										$vScriptIni .= "  openWindowGiveMeQueryConditions(document.Form.query_fields.value, 1, document.Form.query_values,0);  \n";

										$vHTML .=  "\n".'</td><td width="90%"  valign="top" class="TableFieldValue">';
										//--------------------------------------------------------------
										$vHTML .=  '<div id="query_editval"></div>';
										//---------------------------------------------------------------
										$vHTML .=  '<div id="query_values">';
											//-------------------------------------------------------
											$vHTML .=  "\n".'	<input name="query_changevalue" type="button" onClick="javascript:ChangeSelectedQueryConditions();" value="'.$button_strings['Change'].'" />'."\n";
											$vHTML .=  "\n".'	<input name="query_delvalue" type="button" onClick="javascript:DeleteSelectedQueryConditions('."'".$vModName."'".');" value="'.$button_strings['Delete'].'" />'."\n";
											$vHTML .=  "\n".'	<input name="query_delvalues" type="button" onClick="javascript:DeleteAllQueryConditions();" value="'.$button_strings['DeleteAll'].'" />'."\n";
											$vHTML .=  "\n".'	<input name="query_showconditions" type="button" onClick="javascript:showConditions();" value="'.$button_strings["ShowAll"].'" />'."\n";
											$vHTML .=  "\n".'<br />';

											if ($HTTP_PARAM_VARS['conditions']=='1')
												{
												$vHTML .=  "\n".'	Nivel: <input name="level_dec" type="button" onClick="alert();" value="-" />'."\n";
												$vHTML .=  "\n".'	<input name="cant_levelmax" type="hidden" value="1" />'."\n";
												$vHTML .=  "\n".'	<input name="level_value" type="text" readonly="true" size="1" value="1" />'."\n";
												$vHTML .=  "\n".'	<input name="level_inc" type="button" onClick="" value="+" />'."\n";
												}
											else	{
												$vHTML .=  "\n".'	<input name="goUp" type="button" onClick="javascript:QueryConditionsUp(document.Form.query_values);" value="'.$button_strings['Up'].'" />'."\n";
												$vHTML .=  "\n".'	<input name="goDown" type="button" onClick="javascript:QueryConditionsDown(document.Form.query_values);" value="'.$button_strings['Down'].'" />'."\n";
												}
											//-------------------------------------------------------
											$vHTML .=  '<div id="query_selectvalues">';
												if ($HTTP_PARAM_VARS['conditions']=='1')
													{
													$vHTML .=  "\n".'<br /><select name="query_values" size="'.$vquery_fields_max_rows.'" onClick="javascript:SelectQueryConditions();">'."\n";
													$vHTML .=  "\n".'	<option value="" >--- '.$query_strings["notFieldSelected"].' ---</option>';
													$vHTML .=  "\n".'	</select>'."\n";
													}
												else	{
													$vHTML .=  "\n".'<br /><select name="query_values" size="'.$vquery_fields_max_rows.'" onClick="javascript:SelectQueryConditions();">'."\n";
													$vHTML .=  "\n".'	<option value="" >&nbsp;</option>';
													$vHTML .=  "\n".'	</select>'."\n";
													}
											$vHTML .=  '</div>';
										$vHTML .=  '</div>';
									}
								$vHTML .=  "\n".'</td></tr></table>';
																			
										
							//close the table and form...
							$vHTML .='</td></tr>';
							$vHTML .='</table>';
						$vHTML .=  '</div>';  //fin del div del panel...

						}
					
					
					}
				
				if (!empty($panel))
					$panel  .="</ul>\n";					

				if ($vShowButtons)
					{
					$vButton ='<br /><input name="Insert" type="submit" value="'.$button_strings['Insert'].'" onClick="'."javascript:showWaitScreen('".$message_strings['Inserting']."')".';" />        '.$vButton;
					//there is data, then we go to show the button Save and Refresh...
					$vButton .=  '<input name="Save" type="submit" value="'.$button_strings['Save'].'" onClick="'."javascript:showWaitScreen('".$message_strings['Saving']."')".';" />     ';
					$vButton .=  '<input name="Refresh" type="submit" value="'.$button_strings['Refresh'].'" onClick="'."javascript:showWaitScreen('".$message_strings['Updating']."')".';" />    ';
					if ( ( ($vAdminUser) or (true))
						and (isset($HTTP_PARAM_VARS['SelectSqlParam'])) and (!empty($HTTP_PARAM_VARS['SelectSqlParam'])) )
						{
						$vButton .=  '<input name="Delete" type="submit" value="'.$button_strings['Delete'].'" />     ';
						}
					}
				$vButton .= $vNewButtons;

				$vHTML .='<input name="mod" type="hidden" value="'.$vModName.'" />';
				$vHTML .='<input name="s_id" type="hidden" value="'.$HTTP_PARAM_VARS['s_id'].'" />';
				$vHTML .='<input name="page_status" type="hidden" value="complete" />';
				//$vHTML .='<input name="ini" type="hidden" value="'.$HTTP_PARAM_VARS['ini'].'">';
				//$vHTML .='<br /><input name="Insert" type="button" onClick="javascript:Redirect('."'run.php?Insert=1&".$vParamFlow."'".');" value="'.$button_strings['Insert'].'">       ';
				}
			}






//			$vButton .=  "</form>\n";

	//Print the values to the html
	//$vSearch .=  "</form>\n";
	}
else
	{
	$error .= $ERRORS['PARAMS_UNKNOWN'];
	}


?>
<script language="JavaScript" type="text/JavaScript">
<!--
//var FormChanged = false;
var defaultIsEmptyOK = true;

// Some of the function was created by Eric Krock, 18 Feb 97

function makeArray(n) {
//*** BUG: If I put this line in, I get two error messages:
//(1) Window.length can't be set by assignment
//(2) daysInMonth has no property indexed by 4
//If I leave it out, the code works fine.
//   this.length = n;
   for (var i = 1; i <= n; i++) {
      this[i] = 0
   }
   return this
}

// Notify user that contents of field theField are invalid.
// String s describes expected contents of theField.value.
// Put select theField, pu focus in it, and return false.

function warnInvalid (theField, s)
{
    //theField.focus()
    alert("["+theField.value+"] "+s);
    theField.value = '';
    theField.select();
    return false;
}

// Notify user that required field theField is IsEmpty.
// String s describes expected contents of theField.value.
// Put focus in theField and return false.
function warnIsEmpty (theField, s)
{
	//theField.focus()
	if (warnIsEmpty.arguments.length == 1) s = "Warning: the field is IsEmpty...";
 	alert(s);
	return false;
}

// Check whether string s is IsEmpty.
function isIsEmpty(s)
{   return ((s == null) || (s.length == 0))
}


function PutStatusMsg(StatusMsg)
{
	window.status = StatusMsg;
}

function ShowComponents()
{
	var msgWindow=window.open("");
	for (var i = 0; i < document.Form.elements.length; i++)
		{
		msgWindow.document.write("<br />"+document.Form.elements[i].name + "=" + document.Form.elements[i].value +" ("+ document.Form.elements[i].type +")");
		if (document.Form.elements[i].type=='select-one')
			{
			msgWindow.document.write(" text= "+document.Form.elements[i].options[document.Form.elements[i].selectedIndex].text);
			}
		}
}

function PutChange(vName)
{
	//the combobox components have a hidden component with the name contatenated with '_Desc' that have the value of the text showed by it...
        if (vName.type=='select-one')
        	{
		for (var i = 0; i < document.Form.elements.length; i++)
			{
			if (document.Form.elements[i].name==vName.name+'_Desc')
				{
				document.Form.elements[i].value = vName.options[vName.selectedIndex].text;
				}
			}
        	}

	document.Form.markGo.value = vName.name;
	var vEnabled = false;
	if (document.Form.FormChanged.value != "true")
		{
		document.Form.status.value = document.Form.status.value+"*";
		}
	document.Form.FormChanged.value = "true";
	document.Form.FormChangedNow.value = "true";

	<?php echo $vScriptChange; ?>
}

function AskSave(vparam)
{


	if (vparam=='Delete')
		{
		if (confirm("<?php echo $message_strings['AskDelete']; ?>"))
			{
			document.Form.FormChanged.value = vparam;
			document.Form.submit();
			}
		else	{
			//nothing to do...
			document.Form.FormChanged.value = "reload";
			document.Form.submit();
			}
		}
	else
		{

		if (confirm("<?php echo $message_strings['AskSave']; ?>"))
			{
			document.Form.FormChanged.value = vparam;
			document.Form.submit();
			}
		else	{
			//nothing to do...
			document.Form.FormChanged.value = "reload";
			document.Form.submit();
			}
		}
}

function GoPos(vini)
{
	document.Form.ini.value = vini;
	document.Form.submit();
}

function showinfodiv(vdiv, vurl, iniframe, showwait, vheigth)
	{
	if (showwait)
		TB_Wait();
		
	if (iniframe)
		{
		vurl = '<iframe	id="idframe'+vdiv+'" name="niframe'+vdiv+'" src="'+vurl+'" width="100%" height="'+vheigth+'" scrolling="auto" align="top" frameborder="1" class="wrapper">Esta opci&oacute;n no trabajar&aacute; correctamente. Su navegador no soporta IFRAMES.</iframe>' ;
		}
	$("div#"+vdiv).html(vurl).ready( TB_Wait_Stop() );
	
	}																

function Redirect(url)
{
// if (document.Form.FormChanged.value=="true")
//  	{
//    	if (confirm("There is changes in the form, do you want to save the changes before load the new data?"))
//  		{
//  		document.Form.FormChanged.value = "false";
//  		document.Form.Save.click();
//  		}
//  	else	{
//  		//nothing to do...
//  		location.href=url;
//  		}
//  	}
//  else	{
//  	location.href=url;
//  	}
	if (document.Form.FormChangedNow.value == "true")
		{
		document.Form.UrlGo.value=url;
		document.Form.Refresh.click();
		}
	else
		{
		if (url.indexOf('?')>0)
			{
			url += '&back='+document.Form.mod.value+'&back_data='+escape(document.Form.data.value);
			}
		location.href=url;
		}
}

function CheckChange(theField, IsEmptyOK, s)
{

	var vEnabled = false;
	FormChanged = true;
	var msg;
	if (CheckChange.arguments.length < 2) IsEmptyOK = defaultIsEmptyOK;
	if (CheckChange.arguments.length == 3)
		{
		msg = s;
		}
	else
		{
		msg = 'Warning: the field is IsEmpty...';
		}
	if ((IsEmptyOK == false) && (isIsEmpty(theField.value))) return warnIsEmpty(theField, msg);
	//if ((IsEmptyOK == true) && (isIsEmpty(theField.value))) return true;
	PutChange(theField);
}

function CheckNumeric(theField, IsEmptyOK, s)
{
	if (isNaN(theField.value))
		{
		alert('Error: the value entered is not numeric...');
		theField.value = '';
		}
	else
		{
		CheckChange(theField, IsEmptyOK, s);
		}
}


function NewPanel(dir)
{
	if (document.Form.data.value==dir) exit;
	
	if (document.Form.data.value=="") document.Form.data.value="panel_1";

	var x;
	x=$("#menu"+document.Form.data.value);
	x.removeClass("current");
	
  
	if ( (<?php echo ($Confs["Panel_Ajax"]) ? "false" : "true"; ?>) || (dir=="all") ) 
		{
		showWaitScreen("<?php echo $message_strings['Updating']; ?>");
		document.Form.data.value = dir;
		document.Form.submit();
		}
	else if (document.Form.data.value != dir)
		{
		$("div#"+document.Form.data.value).hide();
		
		$("div#"+dir).show();
		document.Form.data.value = dir;
		
		//var x;
		x=$("#menu"+document.Form.data.value);
		x.addClass("current");
		}
}

//Validation of the fiel date
//-----------------------------------------------
var daysInMonth = makeArray(12);
daysInMonth[1] = 31;
daysInMonth[2] = 29;   // we use a function to check the number of days of this month
daysInMonth[3] = 31;
daysInMonth[4] = 30;
daysInMonth[5] = 31;
daysInMonth[6] = 30;
daysInMonth[7] = 31;
daysInMonth[8] = 31;
daysInMonth[9] = 30;
daysInMonth[10] = 31;
daysInMonth[11] = 30;
daysInMonth[12] = 31;

function daysInFebruary (year)
{   // February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (  ((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0) ) ) ? 29 : 28 );
}

function CheckDate(theField, FieldSave, formatEnter, formatSave, IsEmptyOK, s, vWithAccept)
{// we go to use here the format of date "dd/mm/aaaa"
		
	FieldSave.value = theField.value;
	//alert(FieldSave.name+"..."+FieldSave.value);
	PutChange(FieldSave);
	return true;
	exit;
	
	var msg;
	if (CheckDate.arguments.length < 5) IsEmptyOK = defaultIsEmptyOK;
	if (CheckDate.arguments.length == 6)
		{
		msg = s;
		}
	else
		{
		msg = 'Error in the format of the date field...';
		}
	if ((IsEmptyOK == true) && (isIsEmpty(theField.value))) return true;
	if ((IsEmptyOK == false) && (isIsEmpty(theField.value))) return warnIsEmpty(theField);
	var vfrmt = "";
	var vd = -1;
	var vm = -1;
	var vy = -1;
	vent = 0;

	format = formatEnter.toUpperCase();
	for(var i=0; i<format.length; i++)
		{
		vch = format.substring(i,i+1);

		if (vch=='D')
			{
			vfrmt += "([0-9]{1,2})";
			vent++;
			vd = vent;
			}
		else if (vch=='M')
			{
			vfrmt += "([0-9]{1,2})";
			vent++;
			vm = vent;
			}
		else if (vch=='Y')
			{
			vfrmt += "([0-9]{4})";
			vent++;
			vy = vent;
			}
		else 	{
			vfrmt += "[-/]";
			}
		}
    	re = new RegExp(vfrmt, "");
	if (vpar = re.exec(theField.value))
		{
		if (vd>-1)
			{
			intDay = parseInt(vpar[vd],10);
			}
		if (vy>-1)
			{
			intYear = parseInt(vpar[vy],10);
			}
		if (vm>-1)
			{
			intMonth = parseInt(vpar[vm],10);
			if ((intMonth < 1) || (intMonth > 12)) return warnInvalid(theField,'1.'+msg);
			}

		if ((vd>-1) && (vm>-1))
			{
			if (intDay > daysInMonth[intMonth]) return warnInvalid('2.'+theField,msg);
			if ((intMonth == 2) && (intDay > daysInFebruary(intYear))) return warnInvalid(theField,'3.'+msg);
			}


		format = formatSave.toUpperCase();
		vfrmt = "";
		if (intDay<10)
			{
			intDay = '0'+intDay;
			}
		if (intMonth<10)
			{
			intMonth = '0'+intMonth;
			}

		for(var i=0; i<format.length; i++)
			{
			vch = format.substring(i,i+1);
			if (vch=='D')
				{
				vfrmt += intDay;
				}
			else if (vch=='M')
				{
				vfrmt += intMonth;
				}
			else if (vch=='Y')
				{
				vfrmt += intYear;
				}
			else	{
				vfrmt += vch;
				}
		}
		FieldSave.value = vfrmt;
		if (vWithAccept)
			{
			PutChange(FieldSave);
			}
		return true;
		}
	else	{
		return warnInvalid(theField,msg);
		}

}


function QueryConditionsUp(tlist)
{
	for (var i=0; i<tlist.options.length; i++) {
		if (tlist.options[i].selected) {
			if (i==0) {
				return;
				}
			var no = new Option();

			no.value = tlist.options[i-1].value;
			no.text = tlist.options[i-1].text

			tlist.options[i-1].value = tlist.options[i].value;
			tlist.options[i-1].text = tlist.options[i].text;
			tlist.options[i] = no;
			tlist.selectedIndex = i-1;
			vposition1 = i;
			vposition2 = i-1;
			//theURL = 'ChangeQueryFieldNames.php?mod=<?php echo $vModName; ?>&position1='+(vposition1)+'&position2='+(vposition2);
			theURL = 'jQueryChangeQueryFieldNames.php?mod=<?php echo $vModName; ?>&s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&position1='+(vposition1)+'&position2='+(vposition2);

			$.get(theURL,function(txt){ });

			//$("div#query_editval").hide();
			//$("div#query_values").show();

			//window.open(theURL,'','width=1,height=1,dependent=1');
			return;
			}
		}

}

function QueryConditionsDown(list)
{
	for(var i=0; i<list.options.length-1; i++) {
		if (list.options[i].selected) {
			var no = new Option();

			no.value = list.options[i+1].value;
			no.text = list.options[i+1].text

			list.options[i+1].value = list.options[i].value;
			list.options[i+1].text = list.options[i].text;
			list.options[i] = no;
			list.selectedIndex = i+1;
			vposition1 = i;
			vposition2 = i+1;
			theURL = 'jQueryChangeQueryFieldNames.php?mod=<?php echo $vModName; ?>&s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&position1='+(vposition1)+'&position2='+(vposition2);

			$.get(theURL,function(txt){ });

			//$("div#query_editval").hide();
			//$("div#query_values").show();

			//window.open(theURL,'','width=1,height=1,dependent=1');
			return;
			}
		}

}

function radioUpdate()
{
	if (document.Form.radio_check[0].checked)  //combobox
		{
		document.Form.query_compare.disabled = "disabled";
		document.Form.query_value.disabled = "disabled";
		}
	if (document.Form.radio_check[1].checked)  //combobox
		{
		document.Form.query_compare.disabled = "disabled";
		document.Form.query_value.disabled = "disabled";
		}
	if (document.Form.radio_check[2].checked)  //combobox
		{
		document.Form.query_compare.disabled = "";
		document.Form.query_value.disabled = "";
		}
}

//Change the state in Query->Fields/conditions
function change_query_work()
{
 vmod=document.Form.query_ModOptionsActive.value;
 if (document.Form.conditions.value=='1')
 	{
 	document.Form.conditions.value = '0';
 	document.Form.query_work.value = "<?php echo $button_strings['Fields'].('>>'); ?>";
 	}
 else
 	{
 	document.Form.conditions.value = '1';
 	document.Form.query_work.value = "<?php echo $button_strings['Conditions'].('>>'); ?>";
 	}
 TB_Wait();
 theURL = 'jQueryUpdate_query_work.php?mod='+vmod+'&s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&in_mod=<?php echo $vModName; ?>&conditions='+document.Form.conditions.value+'&vquery_fields_rows='+<?php echo $vquery_fields_max_rows; ?>;
 $.get(theURL,function(txt){
	$("div#query_values").html(txt);
	openWindowGiveMeQueryConditions(document.Form.query_ModOptionsActive.value, document.Form.query_fields.value, 1, document.Form.query_values,0);
	TB_Wait_Stop();
	});

}

function fquery_run_go()
{

	TB_Wait();
	if (document.Form.query_multiModOptions.value=="1")
		{

		<?php
		echo " vcantMod =".(count($vMultiMod)).";\n";
		foreach ($vMultiMod as $vkey => $vval)
			{
			echo "$.get('run.php?s_id=".$HTTP_PARAM_VARS['s_id']."&mod=".$vkey."&query_run=go&giveme_sql=true&in_mod=".$vModName."',\n";
			echo "function(txt){ \n";
			echo "vcantMod = vcantMod -1;\n";
			//echo "alert(escape(txt)); \n";
			echo "document.Form.".$vval.".value= escape(txt);\n";
			echo "if (vcantMod == 0) {document.Form.query_runClicked.value='go'; document.Form.submit();} ";
			echo "});\n";
			}

		?>

		}
	else	
		{
		document.Form.query_runClicked.value='go';
		document.Form.submit();
		}

}



function GiveMeModQueryOptions(vmodnew,vdiv)
{
	TB_Wait();
	document.Form.query_ModOptionsActive.value = vmodnew;
	vrows = <?php echo $vquery_fields_max_rows; ?>; //&cantrows=

	theURL = 'jQueryModQueryOptions.php?in_mod=<?php echo $vModName; ?>&s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmodnew+'&cantrows='+vrows;
	//$.get(theURL,function(txt){ $("div#"+vdiv).html(txt); alert(txt); });
	//$("div#"+vdiv).show();

	$.get(theURL,function(txt){
		$("div#"+vdiv).html(txt);
		$("div#"+vdiv).show();
		openWindowGiveMeQueryConditions(document.Form.query_fields.value, 1, document.Form.query_values,0);
		TB_Wait_Stop();
		});

}

function js_giveme_sql()
{
	$.get('run.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+document.Form.query_ModOptionsActive.value+'&query_run=go&giveme_sql=true&in_mod=<?php $vModName; ?>&data=query&query_dest=form&query_runClicked=go',
		function(xml)
		{
		var from = $('sql', xml).attr('from');
		var where = $('sql', xml).attr('where');
		var error = $('sql', xml).attr('error');
		var module = $('sql', xml).attr('module');
		var in_module = $('sql', xml).attr('in_module');

		txt = "from: "+from+"\n"+"where: "+where+"\n"+"error: "+error+"\n"+"module: "+module+"\n"+"in_module: "+in_module+"\n";

		alert(txt);
		});
}

function showConditions()
{
	vmod = document.Form.query_ModOptionsActive.value;
	//alert(vmod);
	vrows = <?php echo $vquery_fields_max_rows; ?>; //&cantrows=
	$("div#query_values").show();
	$("div#query_editval").hide();
	$("div#query_editval").html("");

	theURL = 'jQueryShowConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmod+'&in_mod=<?php echo $vModName; ?>&opener=Form';
	$.get(theURL,function(txt){ $("div#query_selectvalues").html(txt); });
	$("div#query_editval").hide();
	$("div#query_values").show();

}

function selectqueryvalue(vvalue)
{
	vtelement= document.Form.query_fields;
	for (var j=0; j<vtelement.options.length; j++)
		{
		if (vvalue==vtelement.options[j].value)
			{
			vtelement.selectedIndex = j;
			return;
			}
		}	
}

function DeleteCondition(vvalue, vcondpos)
{
	selectqueryvalue(vvalue)
	
	vmod = document.Form.query_ModOptionsActive.value;
	velement= vvalue;   //document.Form.query_fields.value;
	vlevel = 1;
	vfield = document.Form.query_values;
	vcondition = vcondpos; //vfield.selectedIndex;
	viscond = 1; //document.Form.conditions.value;

	vqueryMark = '<?php echo $vQueryMark; ?>';
	if (vfield.options.length==1)  //after will be deleted it...
		{
		vv = document.Form.query_fields.options[document.Form.query_fields.selectedIndex].text;
		if (vv.indexOf(vqueryMark) == 0) 
			{
			document.Form.query_fields.options[document.Form.query_fields.selectedIndex].text = vv.substring(vqueryMark.length);
			}
		}		


	theURL = 'jQueryDeleteQueryConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmod+'&mark=<?php echo $vQueryMark; ?>&all=0&in_mod=<?php echo $vModName; ?>&opener=Form&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&condition='+vcondition+'&iscondition='+viscond;
	$.get(theURL,function(txt){
		openWindowGiveMeQueryConditions(document.Form.query_fields.value, 1, document.Form.query_values,0);
		}).ready();
		
}

function openWindowGiveMeQueryConditions(velement, vlevel, vfield, vselected)
{
	vmod = document.Form.query_ModOptionsActive.value;
	//alert(vmod);
	vrows = <?php echo $vquery_fields_max_rows; ?>; //&cantrows=
	$("div#query_values").show();
	$("div#query_editval").hide();
	$("div#query_editval").html("");

	if (velement == '')
		{
		velement = -1;
		}
	viscond = document.Form.conditions.value;

	if (viscond!='1')
		{
		vtelement= document.Form.query_fields;
		vtfield = document.Form.query_values;

		if ((vtelement!='-1') && (vtfield.selectedIndex>-1) && (vtelement.selectedIndex>-1))
			{
			vtvalue = vtelement.options[vtelement.selectedIndex].text;
			for (var j=0; j<vtfield.options.length; j++)
				{
				if (vtvalue==vtfield.options[j].text)
					{
					return;
					}
				}
			}
		}

	$vcond = document.Form.conditions.value;
	//theURL = 'GiveMeQueryConditions.php?mod='+vmod+'&in_mod=<?php echo $vModName; ?>&opener=Form&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&iscondition='+$vcond;
	//window.open(theURL,'','width=400,height=200,dependent=1');

	theURL = 'jQueryGiveMeQueryConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmod+'&in_mod=<?php echo $vModName; ?>&opener=Form&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&iscondition='+$vcond+'&cantrows='+vrows+'&elem_selected='+vselected;
	$.get(theURL,function(txt){ $("div#query_selectvalues").html(txt); });
	$("div#query_editval").hide();
	$("div#query_values").show();

	//vfield.selectedIndex = vselected;
	//vfield.focus();
	//alert(vselected);
}

function AcceptNewFieldName(ffield, vnew_name, vposition)
{
	ffield.options[ffield.selectedIndex].text= vnew_name;

	theURL = 'jQuerySaveQueryFieldName.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod=<?php echo $vModName; ?>&position='+vposition+'&name='+escape(vnew_name);
	$.get(theURL,function(txt){ });
	$("div#query_editval").hide();
	$("div#query_values").show();
}

function AcceptValueSelectQueryCond(vField, isvchecklink, vacond, vaelement)
{
	vmod = document.Form.query_ModOptionsActive.value;
	vSaveOk = true;
	vCompMsg = '';
	vmark = '<?php echo $vQueryMark; ?>';
	vlevel = 1;

	vselected = vField.selectedIndex;
	if (document.Form.radio_check[2].checked)
		{
		vcomparison = document.Form.query_compare.value;
		vCompMsg = '['+document.Form.query_compare.options[document.Form.query_compare.selectedIndex].text+'] ';
		if (document.Form.query_value.type=='select-one')
			{
			//combobox
			vvalue = document.Form.query_value.options[document.Form.query_value.selectedIndex].value;
			vtext = document.Form.query_value.options[document.Form.query_value.selectedIndex].text;
			}
		if (document.Form.query_value.type=='text')
			{
			//textbox
			vvalue = document.Form.query_value.value;
			vtext = vvalue;
			}
		}
	if (document.Form.radio_check[0].checked)
		{
		vcomparison = 'equals';
		vvalue = "isnull";
		vtext = '<?php echo $query_strings["isnull"]; ?>';
		}
	if (document.Form.radio_check[1].checked)  //combobox
		{
		vcomparison = 'equals';
		vvalue = "isnotnull";
		vtext = '<?php echo $query_strings["isnotnull"]; ?>';
		}

	vuselink = '0';
	if (document.Form.checklink.value!='disabled')
		{
		if (document.Form.checklink.checked)
			{
			vuselink = '1';
			}
		}

	//Search if exist already the condition now selected, if not exist can be updated....
	cant = vField.options.length;
	for(var i=0; i<cant; i++)
		{
		if (vField.options[i].text == vCompMsg+vtext)
			{
			vSaveOk = false;
			}
		}

	if (vSaveOk == false)
		{
		if (vuselink!=isvchecklink)
			{
			vSaveOk = true;
			}
		}

	if ((vSaveOk) && (vvalue!='') && (vtext!=''))
		{
		//Save the new value in the option that has been changed
		vField.options[vselected].text= vCompMsg+vtext;

		//Insert a empty value in the end if was edited this option
		if (vField.options.length==vselected+1)
			{
			//vField.options[vselected+1].text=  '';
			//vField.options[vselected+1].value= '';
			}

		if (vmark)
			{
			vv = document.Form.query_fields.options[document.Form.query_fields.selectedIndex].text;

			if (!(vv.indexOf(vmark) == 0))
				{
				document.Form.query_fields.options[document.Form.query_fields.selectedIndex].text = vmark+ vv;
				}
			}

		//alert(vvalue+'...'+escape(vvalue)+'...'+unescape(vvalue));

		theURL = 'jQuerySaveQueryConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&uselink='+escape(vuselink)+'&mod='+vmod+'&in_mod=<?php echo $vModName; ?>&element='+vaelement+'&condition='+vacond+'&comparison='+escape(vcomparison)+'&level='+vlevel+'&value='+escape(vvalue)+'&text='+escape(vtext);
		//alert(theURL);
		//window.open(theURL,'','width=0,height=10,dependent=1');
		$.get(theURL,function(txt){
			//alert(txt);
			openWindowGiveMeQueryConditions(document.Form.query_fields.value, 1, document.Form.query_values, vselected);
			});
		}
	else	
		{
		//alert( "No estamos grabando...");
		}


	//$("div#query_values").show();
	//$("div#query_editval").hide();
	//alert('llego al final...CompMsg: '+vCompMsg+' comparison:'+vcomparison+' value:'+vvalue+' text:'+vtext);
}


function SelectQueryConditions()
{
        vmod = document.Form.query_ModOptionsActive.value;

        velement= document.Form.query_fields.value;
        vfield = document.Form.query_values;

        vlevel = 1;
        //vcondition = vfield.selectedIndex;
        vcondition = -1;
        $viscond = document.Form.conditions.value;

	if (velement == '')
		{
		velement = '-1';
		}

	if ( ((velement!='-1') && (vfield.selectedIndex+1==vfield.options.length) && ($viscond=='1')) )
		{
		//theURL = 'SelectQueryConditions.php?mark=<?php echo $vQueryMark; ?>&mod=<?php echo $vModName; ?>&opener=Form&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&condition='+vcondition+'&iscondition='+$viscond;
		//window.open(theURL,'','width=800,height=200,dependent=1');

		theURL = 'jQuerySelectQueryConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmod+'&mark=<?php echo $vQueryMark; ?>&in_mod=<?php echo $vModName; ?>&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&condition='+vcondition+'&iscondition='+$viscond;
		$("div#query_values").hide();
		$.get(theURL,function(txt){ $("div#query_editval").html(txt); });
		$("div#query_editval").show();



		//$("div#query_editval").load("ajax-test.php",{name:"John"});



		}
}

function ChangeSelectedQueryConditions()
{
	vmod = document.Form.query_ModOptionsActive.value;
        velement= document.Form.query_fields.value;
        vlevel = 1;
        vfield = document.Form.query_values;
        vcondition = vfield.selectedIndex;
        $viscond = document.Form.conditions.value;

	if (velement == '')
		{
		velement = '-1';
		}
	if ( (vfield.selectedIndex+1==vfield.options.length) && ($viscond=='1') )
		{
		vcondition = '-1';
		}

	if ( ((velement!='-1') || ($viscond!='1')) && (vcondition > -1))
		{
		//theURL = 'SelectQueryConditions.php?mark=<?php echo $vQueryMark; ?>&mod=<?php echo $vModName; ?>&opener=Form&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&condition='+vcondition+'&iscondition='+$viscond;
		//window.open(theURL,'','width=0,height=200,dependent=1');

		theURL = 'jQuerySelectQueryConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmod+'&mark=<?php echo $vQueryMark; ?>&in_mod=<?php echo $vModName; ?>&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&condition='+vcondition+'&iscondition='+$viscond;
		$("div#query_values").hide();
		$.get(theURL,function(txt){ $("div#query_editval").html(txt); });
		$("div#query_editval").show();
		}
}


function DeleteSelectedQueryConditions()
{
	vmod = document.Form.query_ModOptionsActive.value;
        velement= document.Form.query_fields.value;
        vlevel = 1;
        vfield = document.Form.query_values;
        vcondition = vfield.selectedIndex;
        viscond = document.Form.conditions.value;

	if (velement == '')
		{
		velement = '-1';
		}

	if (viscond=='1')
		{
		if ((velement!='-1') && (vfield.selectedIndex+1!=vfield.options.length) && (vcondition > -1))
			{
			for(var j=vfield.selectedIndex; j<vfield.options.length-1; j++) {
				vfield.options[j].value = vfield.options[j+1].value;
				vfield.options[j].text = vfield.options[j+1].text;
				}
			vfield.options.length -= 1;
			theURL = 'jQueryDeleteQueryConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmod+'&mark=<?php echo $vQueryMark; ?>&all=0&in_mod=<?php echo $vModName; ?>&opener=Form&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&condition='+vcondition+'&iscondition='+viscond;
			//window.open(theURL,'','width=10,height=10,dependent=1');
			$.get(theURL,function(txt){
				openWindowGiveMeQueryConditions(document.Form.query_fields.value, 1, document.Form.query_values,0);
				});

			vqueryMark = '<?php echo $vQueryMark; ?>';
			if (vfield.options.length==1)
				{
				vv = document.Form.query_fields.options[document.Form.query_fields.selectedIndex].text;
				if (vv.indexOf(vqueryMark) == 0)
					{
					document.Form.query_fields.options[document.Form.query_fields.selectedIndex].text = vv.substring(vqueryMark.length);
					}
				}

			}
		}
	else	{

		if (vfield.selectedIndex > -1)
			{

			vcondition = vfield.options[vfield.selectedIndex].value;

			for(var j=vfield.selectedIndex; j<vfield.options.length-1; j++)
				{
				vfield.options[j].value = vfield.options[j+1].value;
				vfield.options[j].text = vfield.options[j+1].text;
				}
			if (vfield.selectedIndex == vfield.options.length-1)
				{
				vfield.selectedIndex = vfield.selectedIndex-1;
				}
			vfield.options.length = vfield.options.length-1;
			if (vfield.options.length==0)
				{
				vfield.options.length = 1;
				vfield.options[0].text = "--- <?php echo $query_strings["notFieldSelected"];?> ---";
				vfield.options[0].value = "";
				}


			theURL = 'jQueryDeleteQueryConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmod+'&mark=<?php echo $vQueryMark; ?>&all=0&in_mod=<?php echo $vModName; ?>&opener=Form&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&condition='+vcondition+'&iscondition='+viscond;
			//window.open(theURL,'','width=10,height=10,dependent=1');
			$.get(theURL,function(txt){
			//openWindowGiveMeQueryConditions(document.Form.query_fields.value, 1, document.Form.query_values,0);

			});
			}
		}


}

function ShowAllQueryConditions()
{
}

function DeleteAllQueryConditions()
{
	vmod = document.Form.query_ModOptionsActive.value;
        velement= document.Form.query_fields.value;
        vlevel = 1;
        vfield = document.Form.query_values;
        vcondition = vfield.selectedIndex;
        viscond = document.Form.conditions.value;

	if (velement == '')
		{
		velement = '-1';
		}

	if ((velement!='-1') || (viscond!='1'))
		{

		theURL = 'jQueryDeleteQueryConditions.php?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod='+vmod+'&mark=<?php echo $vQueryMark; ?>&all=1&in_mod=<?php echo $vModName; ?>&opener=Form&field='+vfield.name+'&element='+velement+'&level='+vlevel+'&condition='+vcondition+'&iscondition='+viscond+'&cantrows=<?php echo $vquery_fields_max_rows; ?>';
		//window.open(theURL,'','width=10,height=10,dependent=1');
		$.get(theURL,function(txt){ $("div#query_selectvalues").html(txt); });

		vqueryMark = '<?php echo $vQueryMark; ?>';
		vv = document.Form.query_fields.options[document.Form.query_fields.selectedIndex].text;
		if (vv.indexOf(vqueryMark) == 0)
			{
			document.Form.query_fields.options[document.Form.query_fields.selectedIndex].text = vv.substring(vqueryMark.length);
			}

		}
}


function OpenLink(vvalue)
{
if ((vvalue.indexOf("@") != -1) &&
   (vvalue.indexOf("mailto:") == -1))
	{
	vvalue = 'mailto: '+vvalue;
	}
if (vvalue.length > 0)
	{
	window.open(vvalue);
	}
}

function Today(theField,FieldSave, formatEnter, formatSave, IsEmptyOK, s, vWithAccept)
{
	var mydate=new Date();
	var year=mydate.getYear();
	if (year < 1000)
		year+=1900;
	var month=mydate.getMonth()+1;
	if (month<10)
		month="0"+month;	
	var day=mydate.getDate();
	if (day<10)
		day="0"+day;
	theField.value = year+""+month+""+day;
	CheckDate(theField, FieldSave, 'ymd', formatSave, IsEmptyOK, s, vWithAccept);
}

//The most important function of validation
//-----------------------------------------
function checkField (theField, theFunction, IsEmptyOK, s)
{
    var msg;
    if (checkField.arguments.length < 3) IsEmptyOK = defaultIsEmptyOK;
    if (checkField.arguments.length == 4)
    	{
        msg = s;
        }
    else
    	{
	msg = 'There is a mistake...';
    	}
    if ((IsEmptyOK == true) && (isIsEmpty(theField.value)))
    	{
    	PutChange(theField);
    	return true;
    	}

    if ((IsEmptyOK == false) && (isIsEmpty(theField.value))) return warnIsEmpty(theField);

    if (theFunction(theField.value) == true)
    	{
    	PutChange(theField);
        return true;
        }
    else
        return warnInvalid(theField,msg);

}


//-->
</script>

<?php
//case of is used Textbox_combobox
if ($IsThereTextbox_combobox)  {
?>
<script language="JavaScript" type="text/JavaScript">
<!--

function textbox_comboboxUpdate(name, radio, combo, text)
{
	if (radio[0].checked)  //combobox
		{
		name.value = combo.value;
		combo.disabled = "";
		text.disabled = "disabled";
		}
	else	{	//textbox
		name.value = text.value;
		text.disabled = "";
		combo.disabled = "disabled";
		}
	PutChange(name.value);
}

function textbox_comboboxChange(name, vvalue)
{
	name.value = vvalue.value;
	PutChange(name.value);
}


function textbox_comboboxFromFileUpdate(name, radio, combo, text, vvalue, vloaded, vpathref, buttonGo)
{
	if (vpathref.length>0)
		{
		if (vvalue.length==0)
			{
			return;
			}
		while (vvalue.indexOf("\\") != -1)
			{
			vvalue = vvalue.substring(vvalue.indexOf("\\")+1, vvalue.length);
			}
		vvalue = vpathref+vvalue;
		// in this case is necessary to load the file
		vloaded.value = "2";
		buttonGo.disabled = "disabled";
		}
	else	{
		// The file was loaded and this function was called by the onload method in the body....
		vloaded.value = "1";
		buttonGo.disabled = "";
		}
	if (vvalue.length>0)
		{
		name.value = vvalue;
		radio[1].checked = 1;
		radio[0].checked = 0;
		text.disabled = "";
		combo.disabled = "disabled";
		text.value = vvalue;
		PutChange(name.value);

		}

}

//-->
</script>

<?php
	}  //case of is used Textbox_combobox


////case of is used CheckBox_Multi
if ($IsThereCheckbox_multi)  {
?>
<script language="JavaScript" type="text/JavaScript">
<!--

//CheckBoxMultiUpdate(this, document.Form.'.$vName.', '."'".$vDelimitedChar."'".')
//CheckBoxMultiSummary(document.Form.'.$vSummaryFieldTable.'__'.$vSummaryFieldField.', document.Form.'.$vName.', '."'".$vDelimitedChar."'".')
//$IsThereCheckbox_multi  = 1;

function CheckBoxMultiUpdate(vcheckbox, vformsave, vdelimiter)
{
	vsave = vformsave.value;
	vtemp = vdelimiter+vsave+vdelimiter;
	if (vcheckbox.checked)
		{

		if (vtemp.indexOf(vdelimiter+vcheckbox.value+vdelimiter) == -1)
			{
			if (vsave.length==0)
				{
				vsave = vcheckbox.value;
				}
			else	{
				vsave += vdelimiter+vcheckbox.value;
				}
			}
		}
	else 	{

		if (vtemp.indexOf(vdelimiter+vcheckbox.value+vdelimiter) > -1)
			{
			vtemp1 = vdelimiter+vcheckbox.value+vdelimiter;
			vsave =  vtemp.substring(0,vtemp.indexOf(vdelimiter+vcheckbox.value+vdelimiter))+vdelimiter;
			vsave += vtemp.substring(vtemp.indexOf(vdelimiter+vcheckbox.value+vdelimiter)+vtemp1.length);
			vsave = vsave.substring(vdelimiter.length);
			vsave = vsave.substring(0, vsave.length-vdelimiter.length);
			}

		}
	vformsave.value = vsave;
	PutChange(vcheckbox);
}

function CheckBoxMultiSummary(vsummary, vsave, vdelimiter)
{
	vsummary.value = vsave.value;
	PutChange(vsummary);
}

//-->
</script>

<?php
	}  //case of is used CheckBox_Multi


//case of is used Listbox_*
if ($IsThereListbox)  {
?>
<script language="JavaScript" type="text/JavaScript">
<!--

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
	PutChange(vsave);
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

function listboxClear(list, vsave, vselected, vdelimiter)
{
	list.options.length = 0;
	listboxUpdate(list, vsave, vselected, vdelimiter);
}

//-->
</script>

<?php
	}  //case of is used Listbox_*

//case of is used Checkbox
if ($IsThereCheckbox)  {
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function UpdateChecked(vcheckbox, vName, vChecked, vUnckecked)  {
  	if (vcheckbox.checked) {
  		vcheckbox.value = vChecked;
  		vName.value = vChecked;
		}
	else {
		vcheckbox.value = vUnckecked;
		vName.value = vUnckecked;
		}
	PutChange(vcheckbox);
}

//-->
</script>

<?php
	}  //case of is used Checkbox

//case of is used textbox_list
if ($IsThereTextbox_list)  {
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function openWindowTextbox_lst(theURL,FieldName,features,vbutton, vcombo, vcombost,vid,vdesc,vsql,vlinked,vfieldst,vfield) {
	var vdefault = vcombost.value;
	if (vbutton.value == '<?php echo $vLinked; ?>')
		{
		vbutton.value = '<?php echo $vFind; ?>';
		vcombo.value = '';
		}
		else
		{
		if (vdefault.length>0)
				{
				theURL += '?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&filter='+vdefault+'&mod=<?php echo $vModName; ?>&id='+vid+'&desc='+vdesc+'&sql='+vsql+'&linked='+vlinked+'&fieldst='+vfieldst+'&field='+vfield+'&Opener=Form&FieldName='+FieldName;
				window.open(theURL,'',features);
				}
		}
}

//-->
</script>
<?php
	}  //case of is used textbox_list

//case of is used textbox_add
if ($IsThereTextbox_add)  {
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function openWindowTextbox_add(theURL,winName,features,vcombo,vid,vdesc,vsql,vfield, vdelimited)
{
	theURL += '?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&filter='+vcombo.value+'&mod=<?php echo $vModName; ?>&id='+vid+'&desc='+vdesc+'&sql='+vsql+'&field='+vfield+'&Opener=Form'+'&delimitedchar='+vdelimited;
	window.open(theURL,winName,features);

}

<!--
function openWindow_buttonUpdate(theURL,winName,features,vaction,vsql)
{
	theURL += '?s_id=<?php echo $HTTP_PARAM_VARS['s_id']; ?>&mod=<?php echo $vModName; ?>&sql='+escape(vsql)+'&action='+escape(vaction)+'&Opener=Form';
	window.open(theURL,winName,features);

}

//-->
</script>
<?php
	}  //case of is used textbox_add

?>
<script language="JavaScript" type="text/JavaScript">
<!--

function listbox_textboxlist_Add(vvalue, vdescript, list, vsave, vselected, vdelimiter)
{
	if ((vvalue.value != "") && (vdescript.value != ""))
		{
		for(var i=0; i<list.options.length; i++)
			{
			if (list.options[i].value==vvalue.value) {
				return;
				}
			}
		var no = new Option();
		no.value = vvalue.value;
		no.text = vdescript.value;
		list.options[list.options.length] = no;
		listboxUpdate(list, vsave, vselected, vdelimiter);
		}

}

function listbox_textbox_Add(vvalue, list, vsave, vselected, vdelimiter)
{
	if (vvalue.value != "")
		{
		for(var i=0; i<list.options.length; i++)
			{
			if (list.options[i].value==vvalue.value)
				{
				return;
				}
			}
		var no = new Option();
		no.value = vvalue.value;
		no.text = vvalue.value;
		list.options[list.options.length] = no;
		listboxUpdate(list, vsave, vselected, vdelimiter);
		}

}

function memo_combobox_Add(list, vsave, vdelimiter)
{
	if (list.selectedIndex > -1)
		{
		if (vsave.value == "")
			{
			vsave.value = list.options[list.selectedIndex].text;
			}
		else	{
			vsave.value += vdelimiter+list.options[list.selectedIndex].text;
			}
		PutChange(vsave);
		}

}

function memo_textboxlist_Add(theURL,FieldName,features,vbutton, vcombo, vcombost,vid,vdesc,vsql,vlinked,vfieldst,vfield, vsave, vdelimiter, vfind, vadd)

{
//openWindowTextbox_lst(theURL,FieldName,features,vbutton, vcombo, vcombost,vid,vdesc,vsql,vlinked,vfieldst,vfield, vsave, vdelimiter) {
//openWindowTextbox_lst';
//$vHTML .=  "('GiveMeLst.php','".urlencode($vContent)."','height=150,dependent=1',this, document.Form.".$vNamelst.", document.Form.".$vTable."__".$vtFieldst.",     '".$vId."','".$vDesc."','".htmlentities(urlencode($vSql))."','".$button_strings['Add']."','".$vTable.'__'.$vtFieldst."','".$vNamelst."')";
//$vHTML .=  '	memo_textboxlist_Add(document.Form.'.$vNamelst.', document.Form.'.$vTable."__".$vtFieldst.',
//$vHTML .=  ' value="'.$button_strings['Add'].'">'."\n";

	if (vlinked==vadd)
		{
		vvalue = vcombo;
		vdescript = vcombost;

		if ((vvalue.value != "") && (vdescript.value != ""))
			{
			if (vsave.value == "")
				{
				vsave.value = vvalue.value;
				}
			else	{
				vsave.value += vdelimiter+vvalue.value;
				}
			vbutton.value = vfind;
			vdescript.value = '';
			PutChange(vsave);

			}
		}
	else	{
		vlinked = vadd;
		openWindowTextbox_lst(theURL,FieldName,features,vbutton, vcombo, vcombost,vid,vdesc,vsql,vlinked,vfieldst,vfield, vsave, vdelimiter)
		}

}

function JS_OnLoad()
{


}

$(document).ready(function()
{


<?php echo $vScriptIni; ?>

});

<?php print $vScript; ?>
//-->
</script>

<script language="JavaScript" src="lib/js/scripts_inc.js" type="text/javascript"></script>

<?php

	$vLink .= '<hr />';

	if ($vShowForm)
		{
		print '<form action="run.php#markGo" method="post" enctype="multipart/form-data" name="Form">';
		
		if (!isset($HTTP_PARAM_VARS['toiframe']))
			{
			echo "<p align='right'>".$Confs["UserIco"].$s_connection['user_register']. " (<a href='javascript:Redirect(".rawurlencode('"?s_id='.$HTTP_PARAM_VARS['s_id'].'&mod='.$vModName.'&logout"').");'>".$button_strings["Logout"]."</a>)"."</p>";
			
			if (file_exists ($xmlConfDirectory."head.php"))
					{
					require($xmlConfDirectory."head.php");
					}

			//-------------Head Section--------------------------------------------------------------
			print "<div id='head'>$vHead";
			if (isset($s_xml_conf['searchs'][0]['quickquery']))
					{
					$HTTP_PARAM_VARS['searchquick'] = (isset($HTTP_PARAM_VARS['searchquick'])) ? $HTTP_PARAM_VARS['searchquick'] : "";
					echo "<p align='right'>".$Confs["SearchIco"]."<input name='searchquick' type='text' value='".$HTTP_PARAM_VARS['searchquick']."' /><input name='quickaction' type='submit' value='".$button_strings["Find"]."' />";	
					if (isset($s_xml_conf['searchs'][0]['helpquickquery'] ))
						{
						$vHelpContent = $s_xml_conf['searchs'][0]['helpquickquery'];

						if ($vHelpOpenNewWindow)
							{
							echo  '&nbsp;<a href="showcontent.php?content='.htmlentities(nl2br($vHelpContent).'&keepThis=true&TB_iframe=true&height=250&width=700').'" title="'.$message_strings['Help'].'" class="thickbox">'.$Confs["HelpIco"].'</a>';
							}
						else	{
							echo '&nbsp;<a href="javascript:Redirect('."'".$vHelpContent."'".')">'.$Confs["HelpIco"].'</a>';
							}
						}
					echo "</p>";
					}
			print "</div>";
			
			//-------------Search Section--------------------------------------------------------------
			print "<div id='search'>$vSearch</div>";
						
			//-------------Link Section--------------------------------------------------------------
			print "<div id='link'>$vLink</div>";
			
			
			//-------------Panel Section--------------------------------------------------------------
			print "<div id='panel'>";
			//print $vButton.'<br /><br />' ;
			if (isset($s_xml_conf['configuration']))
					{
					for ($vaa=0; $vaa<count($s_xml_conf['configuration']); $vaa++)
						{
						if (get_value($s_xml_conf['configuration'][$vaa],'tagname')=='info')
							{
							echo "\n".'<table width="'.$vFormPercent.'%" border="0" cellpadding="4" cellspacing="0" class="TableField">';
							echo "\n".'<tr><td class="TableBar" >';
							echo stripcslashes(urldecode(get_value($s_xml_conf['configuration'][$vaa],'content')));
							echo "\n</td></tr></table>\n";
							}
						}
				}

			print "$panel</div>";
			}
			
		//if ($vShowFieldSection)
		//-------------Form Section--------------------------------------------------------------
		print "<div id='form'>$vHTML</div>";
		
		//-------------Button Section--------------------------------------------------------------
		print "<div id='button'>$vButton</div>";
				
		print "\n</form>\n";
		
		
		//-------------Foot Section--------------------------------------------------------------
		print "<div id='foot'>";
		if (!isset($HTTP_PARAM_VARS['toiframe']))
			{		
			print $vHTMLAfterForm;
			print $vComments;
			}
		
		require('./inc/script_end.inc.php');
		
		if (file_exists ($xmlConfDirectory."foot.php"))
				{
				require($xmlConfDirectory."foot.php");
				}
		print "</div>";  //for Foot section
		
		
		if (!isset($HTTP_PARAM_VARS['toiframe']))
			{
			echo "<div style=\"position:absolute; width:10px; height:800px;\"></div>";  //to leave extra emty space at the end of the page...
			//<div id="s" style="position:absolute; top:50px; left:400px; width:100px; height:100px; background-color:green; text-align:center;">
			}
		
		}

//print "<pre>"; print_r($HTTP_PARAM_VARS); print "</pre>";
//print "<pre>"; print_r($_GET); print "</pre>";



?>

</body>
</html>






