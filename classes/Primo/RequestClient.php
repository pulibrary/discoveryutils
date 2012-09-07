<?php
namespace Primo;

class RequestClient
{
  
  private $request_base = "http://libserv5.princeton.edu/requests_test/service.php?bib=";
  private $record_id;
  
  function __construct($record_id) {
    $this->record_id = $record_id;
  }
  
  public function doLookup() { //FIXME  
    return file_get_contents($this->request_base . $this->record_id);
  }
  
  public function __toString() {
    return $this->request_base . $this->record_id;
  }
}
  