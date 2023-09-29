<?php

  $vRegister = false;
  if (isset($HTTP_GET_VARS['register']))
  	$vRegister = true;
  $vRegister = true;
  if ((!isset($_SERVER['PHP_AUTH_USER'])) or ($vRegister)) {
  	header('WWW-Authenticate: Basic realm="fwa"');
   	if (ereg("Microsoft", $SERVER_SOFTWARE))
   	     header("Status: 401 Unauthorized");
         else
   	     header("HTTP/1.0 401 Unauthorized");
    echo "<HTML><HEAD></HEAD><BODY><H1>Is not possible to work without authentication...</H1></BODY></HTML>\n";
    exit;
  } else {
    //$_SERVER['PHP_AUTH_USER']
    //$_SERVER['PHP_AUTH_PW']
    //Redirect to $HTTP_GET_VARS['redirect']
    if (isset($HTTP_GET_VARS['redirect']))
	     echo "<head>\n"
		  .'  <meta http-equiv="refresh" content="0; URL='.$HTTP_GET_VARS['redirect']."\">\n"
		  ."</head>\n";
  }
?>
