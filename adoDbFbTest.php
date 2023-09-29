<?php
require('./lib/adodb/adodb-exceptions.inc.php');
include_once('lib/adodb/toexport.inc.php');
include_once('lib/adodb/rsfilter.inc.php');
include('lib/adodb/adodb.inc.php');
//include('lib/adodb/tohtml.inc.php'); //ya está declarado en toexport.inc.php
include_once('lib/adodb/adodb-pager.inc.php');

	$vtypeSource = 'firebird';
	$vhostnameSource = '127.0.0.1';
	$vDBSource = 'C:\xampp\databases\COLLMAN.GDB';
	$vuserSource = 'SYSDBA';
	$vpswdSource = 'masterkey';
	$vlocaleSource = '';


	$ADODB_COUNTRECS=false; //no se puede usar la funcion $rec->RecordCount().
	$dbSource = &ADONewConnection($vtypeSource);
	$dbSource->debug = false;
	if($vtypeSource == "odbc")
		{

		if(PERSISTANT_CONNECTIONS)
			{
			$dbSource->PConnect($vDBSource, $vuserSource,$vpswdSource, $vlocaleSource);
			}
		else 	$dbSource->Connect($vDBSource, $vuserSource,$vpswdSource, $vlocaleSource);
		}
	if($vtypeSource == "access")
		{

		if(PERSISTANT_CONNECTIONS)
			{
			//$dbSource->PConnect($s_connection['database'], $s_connection['user'],$s_connection['pswd'], $s_connection['locale']);
			$dbSource->PConnect("Driver={Microsoft Access Driver (*.mdb)};Dbq=".$vDBSource.";Uid=".$vuserSource.";Pwd=".$vpswdSource.";");
			}
		else 	
			{
			//$dbSource->Connect($s_connection['database'], $s_connection['user'],$s_connection['pswd'], $s_connection['locale']);
			$dbSource->Connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=".$vDBSource.";Uid=".$vuserSource.";Pwd=".$vpswdSource.";");
			}
		}
	else if (($vtypeSource == "ibase") or ($vtypeSource == "firebird"))
		{
		if(PERSISTANT_CONNECTIONS)
			{
			$dbSource->PConnect($vhostnameSource.":".$vDBSource,$vuserSource,$vpswdSource);
			}
		else 	{
			$dbSource->Connect($vhostnameSource.":".$vDBSource,$vuserSource,$vpswdSource);
			}
		}
	else 	{
		if(PERSISTANT_CONNECTIONS)
			{
			$dbSource->PConnect($vhostnameSource,$vuserSource,$vpswdSource, $vDBSource,$vlocaleSource);
			}
		else $dbSource->Connect($vhostnameSource,$vuserSource,$vpswdSource,$vDBSource,$vlocaleSource);
		}


$sql = 'SELECT first 10 * FROM "tblImages"';
$dbSource->SetFetchMode(ADODB_FETCH_ASSOC);
$rec = &$dbSource->Execute($sql);
if (!$rec) 
	print $dbSource->ErrorMsg();
else
while (!$rec->EOF) {
	//print $rec->fields['Folder'].' '.$rec->fields['FileName'].'<BR>';
	$rec->MoveNext();
}

$rec->Close(); # opcional

exit;





$ADODB_COUNTRECS=false; //no se puede usar la funcion $rec->RecordCount(). 
$dbSource = &ADONewConnection('firebird'); 
$dbSource->Connect('localhost:C:\xampp\databases\COLLMAN.GDB','SYSDBA','masterkey');
$dbSource->debug = true;

//$id = $dbSource->GenID('"GEN_tblContacts_ContactId"');
//echo ($id);
//echo $dbSource->qstr("tblTmpImages sdsd");


$sql = 'SELECT * FROM "tblITF" WHERE "ITFId"= -1';
# Selecciona un registro en blanco de la base de datos

$rs = $dbSource->Execute($sql); # Ejecuta la busqueda y obtiene el recordset vacio

$record = array(); # Inicializa el arreglo que contiene los datos a insertar

# Asignar el valor de los campos en el registro
# Observa que el nombre de los campos pueden ser mayusculas o minusculas
$record["InsidFk"] = "HAJB";
$record["AccId"] = "19990003";
$record["PlantFk"] = "199";

$insertSQL = $dbSource->GetInsertSQL($rs, $record, 0);
$updateSQL = $dbSource->GetUpdateSQL($rs, $record, 1);
echo $insertSQL.'<br>';
echo $updateSQL.'<br>';
$dbSource->$fmtDate = "'d-m-Y'";
echo $dbSource->DBDate('2003/12/31');

//-----------------------------------------
//Select
$sql = 'SELECT first 10 * FROM "tblImages"';
$dbSource->SetFetchMode(ADODB_FETCH_ASSOC);
$rec = &$dbSource->Execute($sql);
if (!$rec) 
	print $dbSource->ErrorMsg();
else
while (!$rec->EOF) {
	//print $rec->fields['Folder'].' '.$rec->fields['FileName'].'<BR>';
	$rec->MoveNext();
}

$rec->Close(); # opcional

//---------------------------------------------------------------
//Probando cursores Siguiente y anterior, debe de estar declarado include_once('lib/adodb/adodb-pager.inc.php');
$sql = 'SELECT "MDate" "Fecha", "MInfo" "InformaciOn", "LabelInfo" "Etiqueta" FROM "tblITF_ClonCheck" where "ClonCheckId"=3';
$pager = new ADODB_Pager($dbSource,$sql, 'edit_db', true);
$pager->Render($rows_per_page=5);


//--------------------------------------------
//Exportando a html, es necesario que esté declarado  include('lib/adodb/tohtml.inc.php');
$sql = 'SELECT "ClonFk", "MDate" "Fecha", "MInfo" "InformaciOn", "LabelInfo" "Etiqueta" FROM "tblITF_ClonCheck"';
print "<pre>";
$rec = &$dbSource->Execute($sql);  //no se puede ejectutar $rec->MoveFirst();
print rs2html($rec); # obtenemos un texto en formato HTML
print '<hr>';
exit;
//----------------------------------------------
//Exportando a formatos delimitados por tabulador o comas
//$rec = &$dbSource->Execute($sql);
//print "<pre>";
//print rs2csv($rec); # obtenemos un texto en formao CSV
//print '<hr>';

//$rec = &$dbSource->Execute($sql);
//print rs2tab($rec,false); # obtenemos el texto delimitado por tabuladores
			 # false == omite el nombre de los campos en el primer renglon
//$rec = &$dbSource->Execute($sql);
//print '<hr>';
//$rec->MoveFirst();
//rs2tabout($rec); # manda a la salida estandar (stdout) (tambien existe la funcion rs2csvout)
//print "</pre>";

//$rec = &$dbSource->Execute($sql);
//$fp = fopen($path, "w");
//if ($fp) {
//   rs2csvfile($rec, $fp); # Escribe a un archivo (tambien existe la funcion rs2tabfile)
//   fclose($fp);
//}
//Todas las funciones anteriores tienen un ultimo parametro opcional, $addtitles que tiene como valor por omision true. Cuando se le manda false se omite el nombre de los campos en la primera linea.

//-----------------------------------------------------
//Insert
$sql = 'insert into "tblImages"("Folder","FileName") ';
$sql .= "values ('imagenes','prueba.jpg')";

if ($dbSource->Execute($sql) === false) {
	print 'error al insertar: '.$dbSource->ErrorMsg().'<BR>';
}
else print 'El dato fue insertado bien <br>';


//---------------------------------------------------------
//ver como funciona GetUpdateSQL( ) y GetInsertSQL( ). 

//----------------------------------------------------
//Filtros a la medida
// Procesar con ucwords() cada elemento en un rec
function do_ucwords(&$arr,$rs)
{
	foreach($arr as $k => $v) {
		$arr[$k] = ucwords($v);
	}
}
$rs = &$dbSource->Execute($sql);
$rs = RSFilter($rs,'do_ucwords');
//Regresa el rec apuntando nuevamente al primer registro. 


//---------------------------------------------------
//Transacciones Inteligentes
//$dbSource->StartTrans();
//$dbSource->Execute($sql);
//$dbSource->Execute($Sql2);
//$dbSource->CompleteTrans();
//El metodo CompleteTrans() detecta cuando ocurrio un error SQL, y procesara Rollback o Commit segun sea necesario
//Para forzar especificamente un evento rollback si no ha ocurrido un error, usa el metodo FailTrans() entre el StartTrans() y el CompleteTrans(). 


//-------------------------------------------------
//ver Usando manejadores de errores a la medida y PEAR_Error

//---------------------------------------------------
//ver Tablas Pivote



$dbSource->Close(); # opcional

?>
