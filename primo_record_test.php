<?php
require_once('primo_record.php');

$record = new PrimoRecord(file_get_contents('getit-response.xml'));
echo '[', $record->getText('recordid'), "]\n";
//echo '[', $record->getText('openurl'), "]\n";
echo '[', $record->getText('sear:openurl'), "]\n";

echo '[', $record->getText('availlibrary'), "]\n";

print_r($record->getSourceIDs());
?>