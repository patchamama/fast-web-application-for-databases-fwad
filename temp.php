<?php //PHP ADODB document - made with PHAkt 2.7.3?>
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php
print('http://templo/fotos/__tblImages.Folder__/__tblImages.FileName__<br>');
print_r(split('__','http://templo/fotos/__tblImages.Folder__/__tblImages.FileName__'));
$tt = split('__','http://templo/fotos/__tblImages.Folder__/__tblImages.FileName__');
$vSelected = '';
$vScript_ = '';
foreach ($tt as $tt1)  
	{
	if (strpos($tt1,'.')>0)  
		{
		$ttabl = substr ($tt1, 0,strpos($tt1,'.'));	   
		$tfiel = substr ($tt1, strpos($tt1,'.')+1);	
		if (isset($s_fields_value[$ttabl][$tfiel]))
			{
			$vSelected .= $s_fields_value[$ttabl][$tfiel];
			$vScript_ .= (strlen($vScript_)>0)? '+':'';
			$vScript_ .= 'Form.'.$ttabl.'__'.$tfiel.'.value';
			}
		else 
			{
			$vSelected .= $tt1;
			$vScript_ .= (strlen($vScript_)>0)? '+':'';
			$vScript_ .= '"'.$tt1.'"';			
			}
		}
	else
		{
		$vSelected .= $tt1;
		$vScript_ .= (strlen($vScript_)>0)? '+':'';
		$vScript_ .= '"'.$tt1.'"';			
		}
	}
print '<br>'.$vSelected;
print '<br>'.$vScript_;
?>
</body>
</html>
