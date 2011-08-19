<?php
require_once('primo_record.php');
header('Content-type: application/json');

$primo_base_url = "http://searchit.princeton.edu/PrimoWebServices/xservice/getit";
$primo_institution = "PRN";

if(isset($_GET['doc_id'])) {
  $pn_xid = $_GET['doc_id'];
}

$xml = file_get_contents(urlencode($primo_base_url."?instiution=".$primo_institution."&doc_id=".$pnx_id));

$record = new PrimoRecord($xml);
$source_ids = $record->getSourceIDs();

echo json_encode($source_ids);



?>

