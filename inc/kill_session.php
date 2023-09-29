<?php

//require('configuration.inc.php');

session_name($HTTP_GET_VARS['s_name']);
session_id($HTTP_GET_VARS['s_id']);
session_start();

session_destroy();

?>		
		<script language="JavaScript" type="text/JavaScript">
		<!-- 
		history.back();
		//-->
		</script>
<?php 
//header('Location: ../main.php?ini_session=1');

?>