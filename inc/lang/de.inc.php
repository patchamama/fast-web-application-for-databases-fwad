<?php

require('es.inc.php');
// charset encoding  for html output
$charset = 'iso-8859-1';


// strings for the info-panel
$info_strings = array('Connected' => 'verbunden mit Datenbank',
                      'ExtResult' => 'Ergebnis von externem Befehl',
                      'IBError'   => 'InterBase-Fehler',
                      'ExtError'  => 'Fehler von externem Befehl',
                      'Error'     => 'Fehler',
                      'Warning'   => 'Warnmeldung',
                      'Message'   => 'Meldung',
                      'ComCall'   => 'Befehlszeile',
                      'Debug'     => 'Debug-Ausgabe',
                      'PHPError'  => 'PHP-Fehler',
                      'empty' => 'leer'
                      );

$MESSAGES = array(	'DATA_DONT_FOUND'	  => 'Daten haben nicht gefunden...',
			'COOKIES_NEEDED'        => 'Die Benutzereinstellungen können nur verwendet werden, wenn in den Browser-Einstellungen die Annahme von Cookies erlaubt ist!'
                  );

$message_strings = array('AskDelete'    => 'Are you sure that you want to delete the active record?',
			 'AskSave'	=> 'There is changes in the form, do you want to save the changes before load the new data?',
			 'E_Mandatory_Empty' => 'Error: the data "%s" in the table "%s" is mandatory and is empty.',
			 'No_Data_Exist' => 'There is not data...',
			 'Searching' => 'Search in progress... ',
			 'Inserting' => 'Preparing to insert...',
			 'Saving' => 'Saving...',
			 'Updating' => 'Updating...',
			 'DuplicateField' => 'Error: there is two fields with the same name...'
                  	);



// strings to inscribe buttons
$button_strings = array('Login'    => 'Anmeldung',
                        'Logout'   => 'Abmelden',
                        'Create'   => 'Erstellen',
                        'Delete'   => 'Löschen',
                        'Select'   => 'Auswählen',
                        'Save'     => 'Speichern',
                        'Reset'    => 'Zurücksetzen',
                        'Cancel'   => 'Abbrechen',
                        'Add'      => 'Hinzufügen',
                        'Modify'   => 'Bearbeiten',
                        'Ready'    => 'Fertig',
                        'Yes'      => 'Ja',
                        'No'       => 'Nein',
                        'DoQuery'  => 'Abfrage ausführen',
                        'QueryPlan'=> 'Query Plan',
                        'Go'       => 'Go',
                        'DisplAll' => 'Alles anzeigen',
                        'Insert'   => 'Einfügen',
                        'Export'   => 'Eportieren',
                        'Import'   => 'Importieren',
                        'Remove'   => 'Entfernen',
                        'Drop'     => 'Löschen',
                        'Set'      => 'Setzen',
                        'Clear'    => 'Leeren',
                        'SweepNow' => 'Jetzt aufräumen',
                        'Execute'  => 'Ausführen',
                        'Backup'   => 'Sichern',
                        'Restore'  => 'Wiederherstellen',
                        'Reload'   => 'neu laden',
                        'OpenAll'  => 'alle öffnen',
			'CloseAll' => 'alles schliessen',
			'Defaults' => 'Voreinstellungen setzen',
                        'Load'     => 'Laden',
                        'Insert'   => 'Einfügen',
                        'Refresh'  => 'Auffrischen',
                        'Find'	   => 'Suchen',
			'Linked'   => 'verbunden',
			'Selected' => 'Ausgewählt',
                        'Prev'     => 'Zurück',
			'Next'     => 'Nächste',
                     	'LeaveQuery'=> 'Query verlassen',
                     	'Start'    => 'Start',
                     	'Open'        => '&ouml;ffnen',
			'Close'       => 'Schliessen',
			'Up'          => 'nach oben',
			'Top'         => 'oben',
			'Bottom'      => 'unten',
                        'Down'        => 'nach unten',
			'HidePanel'=> 'Feld ausblenden',
			'ShowPanel'=> 'Feld zeigen',
                        'Language'  => 'Sprache',
                        'workwith' => 'Arbeiten mit',
			'Query' => 'Query',
                        'Fields'    => 'Felder',
			'FieldName'=> 'Feldname:',
			'Conditions'=>'Bedingung',
			'OutputTo' =>'Ausgabe an',
			'SaveQuery'=> 'Speichern query',
			'LoadQuery'=> 'Laden query',
			'InitQuery'=> 'Start query',
		        'DeleteAll'=> 'Löschen alles',
			'Change'   => 'Bechseln',
			'QueryResult'=>'Resultats Query: '
                        );


//Types of outputs....
$OutputType_strings = array('form'=> 'Edit form',
			'datagrid'=> 'Data grid',
                        'text'=> 'Text file ASCII',
                        'html'=> 'Web seite (HTML)',
                        'pdf'=> 'PDF dokument',
                        'doc'=> 'Word dokument',
                        'csv'=> 'CSV dokument',
                        'xml'=> 'XML dokument',
                        'xmlABCD'=> 'XML ABCD',
                        'xmlDarwinCore'=> 'XML Darwin Core',
                        'HISPID3'=> 'HISPID 3',
                        'ITF2'=> 'ITF 2'
                        );

$query_strings = array( 'isnull' 		=> 'is null',
                        'isnotnull'		=> 'is not null',
                        'equals'		=> "equals",
                        'notequals'		=> "not equals",
                        'like'			=> "like",
                        'lessthan'		=> "less than",
                        'lessthanorequals'	=> "less than or equals",
                        'greaterthan'		=> "greater than",
                        'greaterthanorequals'	=> "greater than or equals",
                        "not" 			=> "no follow all the conditions",
			"and" 			=> "follow all the conditions",
			"or" 			=> "follow at least one condition",
			"notConditions"		=> "there is not conditions",
			"insertCondition"	=> "insert a new condition",
			"notFieldSelected"	=> "there is not a field selected",
			"UseLink"		=> "Use query with link"
                      );

?>