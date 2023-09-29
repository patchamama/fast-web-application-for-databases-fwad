<?php 
// In PHP 4.1.0 or later, $_FILES should be used instead of $HTTP_POST_FILES.
require('inc/script_start.inc.php');
echo html_head('Edit');
echo html_body();

$vPathCopy = "C:/www/site/documents/";
if (is_uploaded_file($HTTP_POST_FILES['userfile']['tmp_name'])) {
	if (file_exists($vPathCopy.$HTTP_POST_FILES['userfile']['name']))
		{
		$vFileName = substr ($HTTP_POST_FILES['userfile']['name'], 0,strpos($HTTP_POST_FILES['userfile']['name'],'.'));
		$vFileExt = substr ($HTTP_POST_FILES['userfile']['name'], strpos($HTTP_POST_FILES['userfile']['name'],'.')+1);
		$vI = 0;
		while (file_exists($vPathCopy.$vFileName.sprintf("%d", ++$vI).'.'.$vFileExt)) ;
    		copy($HTTP_POST_FILES['userfile']['tmp_name'], $vPathCopy.$vFileName.sprintf("%d", $vI).'.'.$vFileExt);
    		echo "File copied to: ".$vPathCopy.$vFileName.sprintf("%d", $vI).'.'.$vFileExt;
    		}
    	else	{
    		if (!copy($HTTP_POST_FILES['userfile']['tmp_name'], $vPathCopy.$HTTP_POST_FILES['userfile']['name']))
    			{
			echo "File was not copied to: ".$vPathCopy.$HTTP_POST_FILES['userfile']['name'];    			
    			}
		else	{  			
	    		echo "File copied to: ".$vPathCopy.$HTTP_POST_FILES['userfile']['name']."\n";
	    		}
    		}
} else {
    echo "Possible file upload attack. Filename: " . $HTTP_POST_FILES['userfile']['name'];
    echo "<br>Temporal Filename: " . $HTTP_POST_FILES['userfile']['tmp_name'];
}
//move_uploaded_file($HTTP_POST_FILES['userfile']['tmp_name'], "C:\www\site\documents/");
phpinfo();

require('./inc/script_end.inc.php');
?> 	