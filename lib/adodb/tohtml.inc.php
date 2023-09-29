<?php 
/*
  V4.64 20 June 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  
  Some pretty-printing by Chris Oxenreider <oxenreid@state.net>
*/ 
  
// specific code for tohtml
GLOBAL $gSQLMaxRows,$gSQLBlockRows;
	 
$gSQLMaxRows = 100; // max no of rows to download
$gSQLBlockRows=100; // max no of rows per table block

// RecordSet to HTML Table
//------------------------------------------------------------
// Convert a recordset to a html table. Multiple tables are generated
// if the number of rows is > $gSQLBlockRows. This is because
// web browsers normally require the whole table to be downloaded
// before it can be rendered, so we break the output into several
// smaller faster rendering tables.
//
// $rs: the recordset
// $ztabhtml: the table tag attributes (optional)
// $zheaderarray: contains the replacement strings for the headers (optional)
//
//  USAGE:
//	include('adodb.inc.php');
//	$db = ADONewConnection('mysql');
//	$db->Connect('mysql','userid','password','database');
//	$rs = $db->Execute('select col1,col2,col3 from table');
//	rs2html($rs, 'BORDER=2', array('Title1', 'Title2', 'Title3'));
//	$rs->Close();
//
// RETURNS: number of rows displayed
function rs2html(&$rs,$ztabhtml=false,$zheaderarray=false,$htmlspecialchars=true,$echo = false, $vLinks = array(),$vShowFieldsinLink = false)
{
$s ='';$rows=0;$docnt = false;
GLOBAL $gSQLMaxRows,$gSQLBlockRows;

	if (!$rs) {
		printf(ADODB_BAD_RS,'rs2html');
		return false;
	}
	if ($rs->EOF) 
		{
		if (isset($message_strings['No_Data_Exist']))
			{
			echo $message_strings['No_Data_Exist'];
			}
		return false;
		}
	
	
	if (! $ztabhtml) $ztabhtml = "BORDER='1' WIDTH='98%'";
	//else $docnt = true;
	$typearr = array();
	$ncols = $rs->FieldCount();
	$vTemp = array();
		
	$vFieldsDontShow = array();
	if (!$vShowFieldsinLink)
		{
		foreach ($vLinks as $vkey => $vvalue)
			{
			while ((@preg_match("|.*(__(\w*)__).*|U", $vvalue, $match)) )
				{
				$vvalue = ereg_replace($match[1],"*" , $vvalue);
				$vFieldsDontShow[] = $match[2];
				}
			}
		}	
	
	//$hdr = "<TABLE COLS=$ncols $ztabhtml><tr>\n\n";
	$hdr = "<TABLE $ztabhtml><tr>\n\n";
	$vfieldNr = -1;
	
	for ($i=0; $i < $ncols; $i++) 
  	  	{
		$field = $rs->FetchField($i);
		if ($zheaderarray) $fname = $zheaderarray[$i];
		else $fname = (htmlspecialchars($field->name));
		
		if (!(in_array($fname,$vFieldsDontShow)))
			{
			$vTemp[] = $fname;
			$typearr[++$vfieldNr] = $rs->MetaType($field->type,$field->max_length);
			//print " $field->name $field->type $typearr[$i] ";
	
			//if (strlen($fname)==0) $fname = '&nbsp;';
			//$fname = utf8_decode($fname);
			if ( ($fname=="null") || (strlen($fname)==0)  )
				$hdr .= "<TH>&nbsp;</TH>";
			else
				$hdr .= "<TH>".htmlentities($fname)."</TH>";
			}
		}
	$ncols = count($vTemp);
	
	$hdr .= "\n</tr>";
	if ($echo) print $hdr."\n\n";
	else $html = $hdr;
	
	// smart algorithm - handles ADODB_FETCH_MODE's correctly by probing...
	$numoffset = isset($rs->fields[0]) ||isset($rs->fields[1]) || isset($rs->fields[2]);
		

	while (!$rs->EOF) {
		
		$s .= "<TR valign=top>\n";

		for ($i=0; $i < $ncols; $i++) 
		  if (!in_array($vTemp[$i],$vFieldsDontShow))
			{
			
			$match[1] = '';
			$match[2] = '';
			if ($i===0) $v=($numoffset) ? $rs->fields[0] : reset($rs->fields);
			else $v = ($numoffset) ? $rs->fields[$i] : next($rs->fields);
			 
			$vtt = FALSE;
			if ($i<count($vTemp))
				{
				if (array_key_exists($vTemp[$i], $vLinks))
					{
					$vtt = $vLinks[$vTemp[$i]];

					//Processing the values to change the value of the fields...that can exist in format __fielname__
					while ((@preg_match("|.*(__(\w*)__).*|U", $vtt, $match)) )
						{
						 
						if ((array_key_exists($match[2], $rs->fields)) )
							{
							$vtt = ereg_replace($match[1],sprintf ("%s", trim($rs->fields[$match[2]])) , $vtt);
							}
						else	{
							$vtt = False;
							}
						}
					$match[1] ="<A HREF='".urldecode($vtt)."'>";
					//$match[1] ='<A href="javascript:void(0)" onClick="javascript: window.open('."'".urldecode($vtt)."','','');".'">';
					$match[2] ="</A>";
					}
				}
			
			$type = $typearr[$i];
			switch($type) {
			case 'D':
				if (empty($v)) $s .= "<TD> &nbsp; </TD>\n";
				else if (!strpos($v,':')) {
					$s .= "	<TD>".$match[1].$rs->UserDate($v,"D d, M Y") .$match[2]."&nbsp;</TD>\n";
				}
				break;
			case 'T':
				if (empty($v)) $s .= "<TD> &nbsp; </TD>\n";
				else $s .= "	<TD>".$match[1].$rs->UserTimeStamp($v,"D d, M Y, h:i:s") .$match[2]."&nbsp;</TD>\n";
			break;
			case 'I':
			case 'N':
				$s .= "	<TD align=right>".$match[1].stripslashes((trim($v))).$match[2] ."&nbsp;</TD>\n";
			   	
			break;
			/*
			case 'B':
				if (substr($v,8,2)=="BM" ) $v = substr($v,8);
				$mtime = substr(str_replace(' ','_',microtime()),2);
				$tmpname = "tmp/".uniqid($mtime).getmypid();
				$fd = @fopen($tmpname,'a');
				@ftruncate($fd,0);
				@fwrite($fd,$v);
				@fclose($fd);
				if (!function_exists ("mime_content_type")) {
				  function mime_content_type ($file) {
				    return exec("file -bi ".escapeshellarg($file));
				  }
				}
				$t = mime_content_type($tmpname);
				$s .= (substr($t,0,5)=="image") ? " <td><img src='$tmpname' alt='$t'></td>\\n" : " <td><a
				href='$tmpname'>$t</a></td>\\n";
				break;
			*/

			default:
				if ($htmlspecialchars) $v = htmlspecialchars(trim($v));
				$v = trim($v);
				if (strlen($v) == 0) $v = '&nbsp;';
				$s .= "	<TD>".$match[1]. str_replace("\n",'<br>',$v).$match[2] ."</TD>\n";
		
			}
		} // for
		$s .= "</TR>\n\n";
			  
		$rows += 1;
		if ($rows >= $gSQLMaxRows) {
			$rows = "<p>Truncated at $gSQLMaxRows</p>";
			break;
		} // switch

		$rs->MoveNext();
	
	// additional EOF check to prevent a widow header
		if (!$rs->EOF && $rows % $gSQLBlockRows == 0) {
	
		//if (connection_aborted()) break;// not needed as PHP aborts script, unlike ASP
			if ($echo) print $s . "</TABLE>\n\n";
			else $html .= $s ."</TABLE>\n\n";
			$s = $hdr;
		}
	} // while

	if ($echo) print $s."</TABLE>\n\n";
	else $html .= $s."</TABLE>\n\n";
	
	if ($docnt) if ($echo) print "<H2>".$rows." Rows</H2>";
	
	return ($echo) ? $rows : $html;
 }
 
// pass in 2 dimensional array
function arr2html(&$arr,$ztabhtml='',$zheaderarray='')
{
	if (!$ztabhtml) $ztabhtml = 'BORDER=1';
	
	$s = "<TABLE $ztabhtml>";//';print_r($arr);

	if ($zheaderarray) {
		$s .= '<TR>';
		for ($i=0; $i<sizeof($zheaderarray); $i++) {
			$s .= "	<TH>{$zheaderarray[$i]}</TH>\n";
		}
		$s .= "\n</TR>";
	}
	
	for ($i=0; $i<sizeof($arr); $i++) {
		$s .= '<TR>';
		$a = &$arr[$i];
		if (is_array($a)) 
			for ($j=0; $j<sizeof($a); $j++) {
				$val = $a[$j];
				if (empty($val)) $val = '&nbsp;';
				$s .= "	<TD>$val</TD>\n";
			}
		else if ($a) {
			$s .=  '	<TD>'.$a."</TD>\n";
		} else $s .= "	<TD>&nbsp;</TD>\n";
		$s .= "\n</TR>\n";
	}
	$s .= '</TABLE>';
	print $s;
}

// pass in 2 dimensional array
function arrTohtml(&$arr,$ztabhtml='', $NumberCols=-1, $zheaderarray='')
{
	if (!$ztabhtml) $ztabhtml = 'BORDER=1';
	
	$s = "<TABLE $ztabhtml>";//';print_r($arr);

	if ($zheaderarray) {
		$s .= '<TR>';
		for ($i=0; $i<sizeof($zheaderarray); $i++) {
			$s .= "	<TH>{$zheaderarray[$i]}</TH>\n";
		}
		$s .= "\n</TR>";
	}
	
	for ($i=0; $i<sizeof($arr); $i++) {
		$s .= '<TR>';
		$a = &$arr[$i];
		for ($j=0; $j<$NumberCols; $j++) 
			if (isset($a[$j]))
				{
				$val = $a[$j];
				if (empty($val)) $val = '&nbsp;';
				$s .= "	<TD>$val</TD>\n";
				}
			else	{
				$s .= "	<TD>&nbsp;</TD>\n";
				}
		$s .= "\n</TR>\n";
	}
	$s .= '</TABLE>';
	print $s;
}

?>
