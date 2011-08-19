<?php
require_once('primo_record.php');
header('Content-type: application/json');

$primo_base_url = "http://searchit.princeton.edu/PrimoWebServices/xservice/getit";
$primo_institution = "PRN";

if(isset($_GET['doc_id'])) {
  $pnx_id = $_GET['doc_id'];
}

$xml = file_get_contents($primo_base_url."?instiution=".$primo_institution."&docId=".$pnx_id);

$record = new PrimoRecord($xml);
$source_ids = $record->getAvailabilbleLibraries();

echo json_encode($source_ids);



?>

