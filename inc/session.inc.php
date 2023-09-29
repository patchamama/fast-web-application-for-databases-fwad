<?php
// File           session.inc.php / ibWebAdmin
// Purpose        session and fallback related functions, define all session variables
// Author         Lutz Brueckner <irie@gmx.de>
// Copyright      (c) 2000, 2001, 2002, 2003, 2004 by Lutz Brueckner,
//                published under the terms of the GNU General Public Licence v.2,
//                see file LICENCE for details
// Created        <00/12/16 12:48:45 lb>
//
// $Id: session.inc.php,v 1.59 2004/07/28 10:33:11 lbrueckner Exp $


//
// fallback to get-/post-session-mode if the client accept no cookies
// set $s_cookies = TRUE if the client accept cookies
// 
function fallback_session() {
    global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS;
    global $HTTP_SESSION_VARS;

    // check if we got a valid session-id, redirect if not
    // and force ssl usage if configured

    if ((!isset($HTTP_COOKIE_VARS[SESSION_NAME])  &&
         !isset($HTTP_POST_VARS[SESSION_NAME])  &&
         !isset($HTTP_GET_VARS[SESSION_NAME]) 
         )  
        || ($Confs["PROTOCOL"] == 'https'  &&  !isset($HTTP_SERVER_VARS['HTTPS']))
        ) {
        // this is thought to work around a xitami webserver bug
        $script = !empty($HTTP_SERVER_VARS['PHP_SELF']) ? $HTTP_SERVER_VARS['PHP_SELF'] : $HTTP_SERVER_VARS['SCRIPT_NAME'];

        // take care for non-standard http ports
        $port_str = isset($HTTP_SERVER_VARS['SERVER_PORT']) ? ':' . $HTTP_SERVER_VARS['SERVER_PORT'] : '';

        // no valid id, fallback
        redirect($Confs["PROTOCOL"] . '://' . $HTTP_SERVER_VARS['SERVER_NAME'] . $port_str . $script . '?' . SESSION_NAME . '=' . session_id());
        exit;
    }
   

    $HTTP_SESSION_VARS['s_cookies'] = isset($HTTP_COOKIE_VARS[SESSION_NAME]) ? TRUE : FALSE;
    $GLOBALS['s_cookies'] = $HTTP_SESSION_VARS['s_cookies'];
}


//
// add the session_id to url if necessary
//
function url_session($url) {
    global $s_cookies, $HTTP_SERVER_VARS;

    // peephole optimation, saves up to three function calls per url_session() call
    // and up to 1% script execution time :-)
    static $add_id = FALSE;

    if ($add_id ||
        (!$s_cookies  &&
         !ini_get('session.use_trans_sid') &&
         strstr($url, session_name().'='.session_id()) === FALSE)) {

        $url .= (strchr($url, '?') === FALSE) ? '?' : '&';
        $url .= session_name()."=".session_id();
        $add_id = TRUE;
    }

    return str_replace('&', '&amp;', $url);
}


//
// register all sessionvars and assign default values
//
function initialize_session($vmod) {
    global $HTTP_SESSION_VARS, $HTTP_COOKIE_VARS;
    

    $session_vars = 
         array('s_fields_value' => array(),		  //the values of the fields in Edit are saved here...
              's_fields_newvalue' => array(),		  //values that can be changed when is saved a record, for example: in the autonumerics fields where new values can be stored
              's_fields_depend' => array(),		  //the dependences of the fields in Edit are saved here...	
              's_fields_lastvalue'  => array(),		  //the values of the fields that must be propagated or conserved...	
              's_sql_log' => array(),			  // if DEBUG is TRUE we store all the sql sentences here...
              's_init' => TRUE,  			// indicates that the session is already initialized
              's_ini' => 0,
              's_firstTime'=> 1,
              's_cust' => array('language' 	=> 'es',
				'fontsize'	=> 11,
				'data'		=> ''
				),
              's_connection' => array('database' => '',    // set by the db_login panel
                                 'type'	    => '',
                                 'user'     => '',
                                 'password' => '',
                                 'role'     => '',

								 'register' 	 => '',
				                 'user_register' => '',
								 'user_type'	 => '',
				                 'conected' 	 => FALSE),				
              's_cookies' => 'untested',
              's_POST' => array(),            		// if DEBUG = TRUE the post and get variables are
              's_GET'  => array(),          	 	// stored here for the inc/display_variable.php script
              's_FormChanged' => false,
              's_xml_conf' => array('init' => 0,
              			    'lang' => ''),
              's_active_value' => '',
              's_temporal_vars' => '',
              's_lasterrors' => ''
              );

	session_register($vmod);
    foreach ($session_vars as $key => $val) 
		{
		//$vsesion_name = str_replace('_' , '//', $vsesion_name); // Ruta de las imágenes
        $HTTP_SESSION_VARS[$vmod][$key] = $val;
		}

    localize_session_vars( $vmod );
}


//
// copy all sessionvars from $HTTP_SESSION_VARS[] into the local scope
//
function localize_session_vars($vmod) {
    global $HTTP_SESSION_VARS;
	
	if (isset($HTTP_SESSION_VARS[$vmod]))
		foreach($HTTP_SESSION_VARS[$vmod] as $sname => $svar) 
			{
	        $GLOBALS[$sname] = $svar;
			}
}


//
// store the local vars into the session
//
function globalize_conditions($vmod) 
	{
	global $HTTP_SESSION_VARS;
	
    if (isset($GLOBALS['s_xml_conf'])) 
			{
			$HTTP_SESSION_VARS[$vmod]['s_xml_conf'] = $GLOBALS['s_xml_conf'];
	        } 
	
}


function globalize_session_vars($vmod) 
	{
	global $HTTP_SESSION_VARS;
	
   $session_var_names =
	        array('s_fields_value',
	              's_fields_newvalue',
	              's_fields_depend',
	              's_fields_lastvalue',
	              's_sql_log',
	              's_init',
	              's_ini',
	              's_firstTime',
	              's_useragent',
	              's_cust',
	              's_connection',
	              's_cookies',
	              's_POST',
	              's_GET',
	              's_FormChanged',
	              's_xml_conf',
	              's_active_value',
	              's_temporal_vars',
	              's_lasterrors'
	              );

		
	foreach ($session_var_names as $sname) {
        if (isset($GLOBALS[$sname])) 
			{
			$HTTP_SESSION_VARS[$vmod][$sname] = $GLOBALS[$sname];
	        } 
		else 
			{
	        //unset($HTTP_SESSION_VARS[$vmod][$sname]);
	        }
    }
	
}


//
// reset the session variables which depending on the connected database
//
function cleanup_session() 
{
//echo "limpiando sesion";
    $GLOBALS['s_fields_lastvalue'] = array();
    $GLOBALS['s_fields_value'] = array();
    $GLOBALS['s_fields_depend'] = array();
    $GLOBALS['s_sql_log'] = array();
    $GLOBALS['s_fields_newvalue'] = array();
    $GLOBALS['s_FormChanged'] = '';
    
}

//
// try to identify the users browser;
// the expressions are stolen from pear::Net/UserAgent/Detect.php
// and http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
//
// returns an array with the found informations
//

?>
