<?php
require_once('../classes/primo_record.php');
require_once('../classes/primo_parser.php');

$record = new PrimoRecord(file_get_contents('./support/getit-response.xml'));

//print $record;

//echo '[', $record->getText('recordid'), "]\n";
//echo '[', $record->getText('openurl'), "]\n";
//echo '[', $record->getText('sear:openurl'), "]\n";
//print_r($record->getAvailabilbleLibraries());
//echo '[', $record->getText('availlibrary'), "]\n";
print_r($record->getBriefInfo());
//print_r($record->getSourceIDs());
//echo $record->getRecordID();

$single_source = new PrimoRecord(file_get_contents('./support/single_voyager_source.xml'));
//print_r($single_source->getSourceIDs());
//echo $single_source->getRecordID();
//print_r($single_source->getAvailabilbleLibraries());

$dedup = new PrimoRecord(file_get_contents('./support/dedup_response.xml'));
//print_r($dedup->getAvailabilbleLibraries());
//print_r($dedup->getAllLinks());
//print_r($dedup->getGetItLinks());
print_r($dedup->getBriefInfo());
$PNXloader = new PNXLoader();
$xml = file_get_contents('./support/single_voyager_source.xml');
$pnx_rec = $PNXloader->loadPNX($xml);

//print_r($pnx_rec->getAllLinks());
//print_r($pnx_rec->getGetItLinks());
print_r($pnx_rec->getAvailabilbleLibraries());
print_r($pnx_rec->getBriefInfo());
//print_r($pnx_rec->getFullText());

?>
