<?php
// File           GiveMeLst.php / Phyllacanthus
// Purpose        Show a list result of a sql query and select a value of it that can be update in the father form...
// Author         Armando Urquiola Cabrera (urquiolaf@hotmail.com), has bien created based in the software ibWebAdmin (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner
// Version        Jun 1, 2005
// parameters     mod
//		  sql
//		  action
//		  Opener


require('./inc/script_start.inc.php');

//echo html_head('UpdateValues.php');

	//fields need to work in the sql query and combobox...
	$vSql = urldecode(stripcslashes($HTTP_GET_VARS['sql']));  
	$vAction = urldecode(stripcslashes($HTTP_GET_VARS['action']));  
	$vDateFrmtEnter = 'dmy';
	$vDateFrmtSave  = 'ymd';

	//Query...
	$res = &$dbhandle->Execute($vSql);
	if ($res === FALSE)
		{
		$db_error .= $dbhandle->ErrorMsg();
		}

	$entro = 0;
	$sqlFields = array();
	$sqlFieldsDate = array();
	while ( (!$res->EOF) and ($entro<2) )
		{
		$entro++;
		if ($entro==1)
			{
			for ($i=0, $max=$res->FieldCount(); $i < $max; $i++) 
				{
				$fld = $res->FetchField($i);
				$sqlFields[$fld->name] = $res->fields[$fld->name];
				$type = $res->MetaType($fld->type);
				if ( ($type=='D') or ($type=='T') )
					{
					$sqlFieldsDate[$fld->name] == '';
					}
				
				}
			}
		$res->MoveNext();
		}
	if ($entro == 0)
		{
?>		
		<script language="JavaScript" type="text/JavaScript">
		<!-- 
		//alert('<?php echo $MESSAGES['DATA_DONT_FOUND']?>');
		window.close();
		//-->
		</script>
<?php 
		}
	$listUpdate = split(";", $vAction);
	$vScript = '';

	print_r($sqlFields);
	foreach ($listUpdate as $tt1)  
		{
		if (!empty($tt1))
			{
			if (@ereg ('__(.+)\.(.+)__=(.+)', $tt1, $match)) 
				{
				
				if (array_key_exists($match[3], $sqlFields)) 
					{
					if (!empty($sqlFields[$match[3]]))
						{
						$vScript .= "\n".'	if (window.opener.document.Form.'.$match[1].'__'.$match[2].'.type=="select-one")'."\n";
						$vScript .= '		{'."\n";
						
						$vScript .= 'for (var i = 0; i <window.opener.document.Form.'.$match[1].'__'.$match[2].'.length; i++)'."\n";
						$vScript .= '	{'."\n";
						$vScript .= '	if (window.opener.document.Form.'.$match[1].'__'.$match[2].'.options[i].text=="'.$sqlFields[$match[3]].'")'."\n";
						$vScript .= '		{'."\n";
						$vScript .= '		window.opener.document.Form.'.$match[1].'__'.$match[2].'.selectedIndex= i;'."\n";
						$vScript .= '		}'."\n";
						$vScript .= '	}'."\n";
						
						$vScript .= '		} else';
						$vScript .= '	window.opener.document.Form.'.$match[1].'__'.$match[2].'.value="'.$sqlFields[$match[3]].'";'."\n";
						
						}
					//if ((array_key_exists($match[3], $sqlFieldsDate)) and (!empty($vDateFrmtSave)) )
					//	{
					//	$vScript .= '	UpdateDate(window.opener.document.Form.'.$match[1].'__'.$match[2].', window.opener.document.Form.'.$match[1].'__'.$match[2].'_date,'."'".$vDateFrmtSave."'".' ,'."'".$vDateFrmtEnter."'".'); ';
					//	}
					
					
					}
				else
					{
					echo '<br>The field '.$match[3].' do not exist in the sql sentences...';
					}
				}
			else	{
				echo '<br>the parameter: '.$tt1.' has a invalid format ';
				}
			}
		}
	
?>
<?php //echo htmlentities($vScript);?>

<script language="JavaScript" type="text/JavaScript">
<!-- 
 <?php //echo ($vScript1);?>
 
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

function UpdateDate(theField, FieldSave, formatEnter, formatSave)
{// we go to use here the format of date "dd/mm/aaaa"
	var vfrmt = "";
	var vd = -1;
	var vm = -1;
	var vy = -1;
	vent = 0;
	
	//alert(theField.value);

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
		}
	else	{
		//return warnInvalid(theField,msg);
		}

}

function JS_OnLoad()
{
	
	<?php echo $vScript;?>

	window.close();
}
//-->
</script>

<body ONLOAD='JS_OnLoad()' bgcolor="#ffffff" style="background-color:#ffffff" text="#000000">
<div id="Layer1" style="position:absolute; z-index:1; top: 10; left: 10";>
<img name="fwa" src="images/loading.gif" width="110" height="120" hspace="220">
</div>
<div id="Layer2" style="position:absolute; z-index:2; top: 50px; width: 220px; left: 10; height: 73px"> 
  <?php
	echo "<p>".$message_strings['Updating']."</p>" ;
	echo "</div>" ;
		
	require('./inc/script_end.inc.php');
?>