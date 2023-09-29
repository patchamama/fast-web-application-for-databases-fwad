<?php
	$conf_file = 'configuration.inc.php';
	require($conf_file);

	if (isset($_GET["version"]))
		{
		$Confs["fwa_version"] = date("y.m.d");
		}


	$vfile = getcwd()."\$conf_file";
	if ($fh = fopen($vfile, 'w') )
		{	
		fwrite($fh, chr(60).chr(63)."php\n" );
		fwrite($fh, "	global $Confs; \n");
		foreach ($Confs as $vval => $vvv)
			{
			fwrite($fh, '	$Confs["'.$vval.'"] = "'.$Confs[$vval].'";'."\n");
			}
		fwrite($fh, chr(63).chr(62) );
		fclose($fh);
		echo "Actualización hecha en: ".$vfile."..."; 
		echo "<br /> Versión del programa FWAD: ".date("y.m.d");
		}
	else	{
		echo "Error al actualizar la actualización en: ".$vfile."...";
		}
    //print_r($Confs);
?>