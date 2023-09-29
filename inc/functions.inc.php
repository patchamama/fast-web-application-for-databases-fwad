<?php

//
// create a temporary file which contains '$sql_string'
// return the filename
//

function build_sql_file($sql){

    $sql = str_replace("\r\n", "\n", $sql);
    $sql .= "\n";
    $tmp_name = $Confs["TMPPATH"].uniqid('').'.sql';

    if ($fp = fopen ($tmp_name, 'a')) {
        fwrite($fp, $sql);
        fclose($fp); 
    }

    return $tmp_name;
}




//
// send http-headers to prevent browser-caching
// and set the charset for the content
//
function send_http_headers() {

    $now = gmdate('D, d M Y H:i:s') . ' GMT';
    header('Expires: 0');
    header('Last-Modified: '.$now);
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: pre-check=0, post-check=0, max-age=0');
    header('Pragma: no-cache');

    header('Content-Type: text/html; charset='.$GLOBALS['charset']);
}

// boddy tag for all html pages
function html_body() {

    return "<body>\n";
          //."  <center>\n";
}

//
// redirect the client to $url
//
function redirect($url) {

    if ($Confs["META_REDIRECT"] === TRUE) {
        echo "<head>\n"
            .'  <meta http-equiv="refresh" content="0; URL='.$url."\">\n"
            ."</head>\n";
    }
    else {
        header('Location: '.$url);
    }

    exit;
}


//
// print $errorstring and stop the script
//
function db_error($file='', $line='', $sql='') {

    echo '<b>Database Error</b><br>'
        .'file: '.$file.', line: '.$line.'<br>'
        .'statement: '.$sql.'<br>'
        .'db_errmsg: '.$errorstring;
    exit;
}


//
// examine the version of the running php interpreter
//
function get_php_version() {

    preg_match('/^([0-9]+).([0-9]+).([0-9]+)/', phpversion(), $matches);

    $vinfo = array ('VER' => $matches[1],
                    'SUB' => ((strlen($matches[2]) > 0) ? $matches[2] : 0),
                    'MIN' => ((strlen($matches[3]) > 0) ? $matches[3] : 0)
                    );
    return $vinfo;
}


//
// replacement for php < 4.2.0
//
if (!function_exists('array_fill')) {

    function array_fill($start_index, $num, $value) {

        $arr = array();
        while ($num > 0) {
            $arr[$start_index] = $value;
            $start_index++;
            $num--;
        }

        return $arr;
    }
}



//
// handler for php errors, $php_error is displayed on the info-panel
//
function error_handler($errno, $errmsg, $file, $line, $errstack) 
{
    global $php_error, $s_connection, $HTTP_PARAM_VARS, $s_lasterrors;

    //if (strpos($errmsg, 'ibase') !== 0) 
    {
    	        
    if ($errno<>2048)
    	{
    	if (empty($s_connection['user_register']))
	    		{
	    		$s_connection['user_register'] = 'indefined';
	    		}
	if (empty($s_connection['user_type']))
		{
		$s_connection['user_type'] = 'not defined';
		}
    	$t = date('Y-m-d H:i:s')."<br>".$s_connection['user_register'].' as '.$s_connection['user_type'];
    	$vparams = $_SERVER["REQUEST_URI"]."?";
    	foreach ($HTTP_PARAM_VARS as $vvparam => $vvpval)
    		{
    		$vparams .= (empty($vparams)) ? "" : "&";
		$vparams .= ($vvparam."=".$vvpval);
    		}
    	$tterror = "$file $line $errno";
	
	$s_lasterrors = (isset($s_lasterrors)) ? $s_lasterrors : array();
	$s_lasterrors = (!is_array($s_lasterrors)) ? array() : $s_lasterrors; 

    	if (!in_array($tterror,$s_lasterrors))
    		{
		$s_lasterrors[] = "$file $line $errno";
		$php_error .= "($t) $errmsg<br>\n"
		    ."in file: $file, line $line, error no. $errno, error_stack $errstack<br>\n";
		error_log("<tr><td>Ver.".VERSION."</td><td>$t</td><td>$errmsg</td><td>$file</td><td>line $line</td><td>error no. $errno, error_stack $errstack</td><td><a href='http://localhost".($vparams)."'>$vparams</a></td></tr>\n", 3, "support/errorlog.htm");
		}
    	}

    //if ($num_err == E_USER_ERROR) {
    //    mail("phpdev@example.com", "Error Cr&iacute;tico de Usuario", $err);
    //	}

	
    }
}


//
// process the tag specified in father_name and store the content in the global var s_xml_conf
//
function process_xml_path($xpath, $path, $tag_name, $attrFilter='', $attrFilterval='')
{
global $s_xml_conf;

	//$vTagTmp = $path->get_elements_by_tagname($father_name);
	$path = $path.'/'.$tag_name;
	$vTagTmp = $xpath->evaluate($path.'/*');
	$vChild  = false;
	if (count($vTagTmp)>0)
		{
		$vChild = true;
		if (!empty($attrFilter))
			{
			$vTagTmp1 = $xpath->evaluate($path."[@".$attrFilter."='".$attrFilterval."']/*");
			if (count($vTagTmp1)>0)
				{
				$path = $path."[@".$attrFilter."='".$attrFilterval."']";
				$vTagTmp = $vTagTmp1;
				}
			}
		}
	$i = -1;
	
	if (!$vChild)
		{
		$s_xml_conf[$tag_name] = array();
		}
	else	
		{
		for ($i = 0; $i < count($vTagTmp); $i++) 
			{
			$s_xml_conf[$tag_name][$i]['content'] = urldecode($xpath->getData($vTagTmp[$i]));
			$s_xml_conf[$tag_name][$i]['tagname'] = urldecode($xpath->nodeName($vTagTmp[$i]));
			//$vattrs = $vChild->attributes();
			$vattrs = $xpath->getAttributes($vTagTmp[$i]);
			if (count($vattrs)>0) 
				{
				foreach ($vattrs as $vv => $vr) 
					{
					$s_xml_conf[$tag_name][$i][$vv] = urldecode($vr);
					}
				}
			}
		}
}

// This function run fine with php4
function process_xml_tag($root, $father_name, $filter='', $filterval='')
{
global $s_xml_conf;

	$vTagTmp = $root->get_elements_by_tagname($father_name);
	$vChild  = false;
	if (count($vTagTmp)==1)
		{
		$vChild = $vTagTmp[0]->first_child();
		}
	else	{
		if (!empty($filter))
			{
			for ($i = 0; $i < count($vTagTmp); $i++) 
				{
				if ($vTagTmp[$i]->get_attribute($filter) == $filterval)
					{
					$vChild = $vTagTmp[$i]->first_child();
					$i = count($vTagTmp);
					}
				}
			}
		else	{
			for ($i = 0; $i < count($vTagTmp); $i++) 
				{
				$vChild = $vTagTmp[$i]->first_child();
				$i = count($vTagTmp);
				}
			}
		}
	$i = -1;
	
	if (!$vChild)
		{
		$s_xml_conf[$father_name] = array();
		}
		
	while ($vChild)
		{
		if (!empty($vChild->tagname))
			{
			$s_xml_conf[$father_name][++$i]['content'] = urldecode($vChild->get_content());
			$s_xml_conf[$father_name][$i]['tagname'] = urldecode($vChild->tagname);
			$vattrs = $vChild->attributes();
			if (count($vattrs)>0) 
				{
				foreach ($vattrs as $vv => $vr) 
					{
					$s_xml_conf[$father_name][$i][$vr->name()] = urldecode($vr->value());
					}
				}
			}
		$vChild = $vChild->next_sibling();
		}
}

//
//  Return a false value if not exist the variable of if it is empty....
//
function get_value($var, $ind)
{
	if (isset($var[$ind]))
		{
		return $var[$ind];
		//if (empty($var[$ind]))
		//	{
		//	return false;
		//	}
		//else	{
		//	return $var[$ind];
		//	}
		}
	else	{
		return false;
		}
}

//
//Check if the are equals the mark and the name of the component and if is true return the value of the component focused that will be initialized in the onLoad section of the html
//
function CheckFocus($vMark, $vCompName, $vCompGo)
{
		
	if ($vMark==$vCompName)
		{
		
		return "\n document.Form.".$vCompGo.".focus();";
		//return '<A name="markGo"></A>'
		}
	else	{
		return "";
		}
}

//
//As the function empty return true if a variable has the value '0' we go to create other function the fix it...
//
function IsEmpty($value)
{
	if (isset($value))
		{
		if (is_string($value))
			{
			return (empty($value) and ($value!='0'));
			}
		else 	{
			return (empty($value));
			}
		}
	else	{
		return false;
		}
}

?>