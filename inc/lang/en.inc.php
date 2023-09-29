<?php

require('es.inc.php');

$charset = "iso-8859-1";

$info_strings["Connected"] = 			'connected to Database';
$info_strings["ExtResult"] = 			'Result from extern command';
$info_strings["IBError"] = 			'InterBase Error';
$info_strings["ExtError"] = 			'Error from extern command';
$info_strings["Error"] = 			'Error';
$info_strings["Warning"] = 			'Warning';
$info_strings["Message"] = 			'Message';
$info_strings["ComCall"] = 			'Command call';
$info_strings["Debug"] = 			'Debug output';
$info_strings["PHPError"] = 			'PHP Error';
$info_strings["empty"] = 			'empty';

$MESSAGES["DATA_DONT_FOUND"] = 			'The data were not found...';
$MESSAGES["COOKIES_NEEDED"] = 			'You have to enable cookies in your browser settings if you want to use the customizing feature!';

$message_strings["AskDelete"] = 			'Are you sure that you want to delete the active record?';
$message_strings["AskSave"] = 			'There is changes in the form, do you want to save the changes before load the new data?';
$message_strings["E_Mandatory_Empty"] = 			'Error: the data "%s" in the table "%s" is mandatory and is empty.';
$message_strings["No_Data_Exist"] = 			'There is not data...';
$message_strings["Searching"] = 			'Search in progress... ';
$message_strings["Inserting"] = 			'Preparing to insert...';
$message_strings["Saving"] = 			'Saving...';
$message_strings["Updating"] = 			'Updating...';
$message_strings["DuplicateField"] = 			'Error: there is two fields with the same name...';
$message_strings["Help"] = 			'Help';
$message_strings["NotConditionsDefined"] =	'Not conditions defined';

$message_strings["UserNotFound"] =	'Error: The user was not found in the database...';
$message_strings["CookiesEnabled"] =	'Cookies must be enabled in your browser.';
$message_strings["LoginForgotten"] =	'Forgotten your username or password?';
$button_strings["LoginAsGuest"] = 		'Login as a guest';
$button_strings["SendMessagAdmin"] = 	'Send Message to the Admin';

$button_strings["Login"] = 			'Login';
$button_strings["Logout"] = 			'Logout';
$button_strings["Create"] = 			'Create';
$button_strings["Delete"] = 			'Delete';
$button_strings["Select"] = 			'Select';
$button_strings["Save"] = 			'Save';
$button_strings["Reset"] = 			'Reset';
$button_strings["Cancel"] = 			'Cancel';
$button_strings["Add"] = 			'Add';
$button_strings["Modify"] = 			'Modify';
$button_strings["Ready"] = 			'Ready';
$button_strings["Yes"] = 			'Yes';
$button_strings["No"] = 			'No';
$button_strings["DoQuery"] = 			'Perform Query';
$button_strings["QueryPlan"] = 			'Query Plan';
$button_strings["Go"] = 			'Go';
$button_strings["DisplAll"] = 			'Display All';
$button_strings["Insert"] = 			'Insert';
$button_strings["Export"] = 			'Export';
$button_strings["Import"] = 			'Import';
$button_strings["Remove"] = 			'Remove';
$button_strings["Drop"] = 			'Drop';
$button_strings["Set"] = 			'Set';
$button_strings["Clear"] = 			'Clear';
$button_strings["SweepNow"] = 			'Sweep Now';
$button_strings["Execute"] = 			'Execute';
$button_strings["Backup"] = 			'Backup';
$button_strings["Restore"] = 			'Restore';
$button_strings["Reload"] = 			'Reload';
$button_strings["OpenAll"] = 			'Open All';
$button_strings["CloseAll"] = 			'Close All';
$button_strings["Defaults"] = 			'Set Defaults';
$button_strings["Load"] = 			'Load';
$button_strings["Refresh"] = 			'Refresh';
$button_strings["Find"] = 			'Find';
$button_strings["Linked"] = 			'Linked';
$button_strings["Selected"] = 			'Selected';
$button_strings["Prev"] = 			'Prev';
$button_strings["Next"] = 			'Next';
$button_strings["LeaveQuery"] = 			'Leave query';
$button_strings["Start"] = 			'Start';
$button_strings["Open"] = 			'open';
$button_strings["Close"] = 			'close';
$button_strings["Up"] = 			'up';
$button_strings["Top"] = 			'top';
$button_strings["Bottom"] = 			'bottom';
$button_strings["Down"] = 			'down';
$button_strings["HidePanel"] = 			'Hide panel';
$button_strings["ShowPanel"] = 			'Show panel';
$button_strings["Language"] = 			'Language';
$button_strings["workwith"] = 			'Work with';
$button_strings["Query"] = 			'Query';
$button_strings["Fields"] = 			'Fields';
$button_strings["FieldName"] = 			'Field name:';
$button_strings["Conditions"] = 			'Conditions';
$button_strings["OutputTo"] = 			'Output to';
$button_strings["SaveQuery"] = 			'Save query';
$button_strings["LoadQuery"] = 			'Load query';
$button_strings["InitQuery"] = 			'Init query';
$button_strings["DeleteAll"] = 			'Delete all';
$button_strings["Change"] = 			'Change';
$button_strings["QueryResult"] = 			'Query result: ';
$button_strings["Username"] = 			'User:';
$button_strings["Password"] = 			'Password:';
$button_strings["level"] = 				'Level';

$OutputType_strings["form"] = 			'Edit form';
$OutputType_strings["datagrid"] = 			'Data grid';
$OutputType_strings["text"] = 			'Text file ASCII';
$OutputType_strings["html"] = 			'Web page (HTML)';
$OutputType_strings["pdf"] = 			'PDF document';
$OutputType_strings["doc"] = 			'Word document';
$OutputType_strings["csv"] = 			'CSV document';
$OutputType_strings["xml"] = 			'XML file';
$OutputType_strings["xmlABCD"] = 			'XML ABCD';
$OutputType_strings["xmlDarwinCore"] = 			'XML Darwin Core';
$OutputType_strings["HISPID3"] = 			'HISPID 3';
$OutputType_strings["ITF2"] = 			'ITF 2';

$query_strings["isnull"] = 			'is null';
$query_strings["isnotnull"] = 			'is not null';
$query_strings["equals"] = 			'equals';
$query_strings["notequals"] = 			'not equals';
$query_strings["like"] = 			'like';
$query_strings["lessthan"] = 			'less than';
$query_strings["lessthanorequals"] = 			'less than or equals';
$query_strings["greaterthan"] = 			'greater than';
$query_strings["greaterthanorequals"] = 			'greater than or equals';
$query_strings["not"] = 			'no follow all the conditions';
$query_strings["and"] = 			'follow all the conditions';
$query_strings["or"] = 			'follow at least one condition';
$query_strings["notConditions"] = 			'there is not conditions';
$query_strings["insertCondition"] = 			'insert a new condition';
$query_strings["notFieldSelected"] = 			'there is not a field selected';
$query_strings["UseLink"] = 			'Use query with link';


 //Report from the base file 


 //Last report from 'en.inc.php' 


 //Report for this conversion 
?>