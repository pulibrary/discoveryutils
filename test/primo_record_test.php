<?php
require_once('../primo_record.php');

$record = new PrimoRecord(file_get_contents('getit-response.xml'));

//print $record;

//echo '[', $record->getText('recordid'), "]\n";
//echo '[', $record->getText('openurl'), "]\n";
//echo '[', $record->getText('sear:openurl'), "]\n";
print_r($record->getAvailabilbleLibraries());
//echo '[', $record->getText('availlibrary'), "]\n";
print_r($record->getAllLinks());
//print_r($record->getSourceIDs());
//echo $record->getRecordID();

$single_source = new PrimoRecord(file_get_contents('single_voyager_source.xml'));
//print_r($single_source->getSourceIDs());
//echo $single_source->getRecordID();
print_r($single_source->getAvailabilbleLibraries());

$dedup = new PrimoRecord(file_get_contents('dedup_response.xml'));
print_r($dedup->getAvailabilbleLibraries());

?>
