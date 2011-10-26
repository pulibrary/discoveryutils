<?php

Class PrimoRecord {
  
  private $xpath_base = "//sear:DOC[1]//";
  private $xpath_doc_root = "//sear:DOC[1]";
  private $def_ns = "def";
  private $xpath;
  private $institutionID = "PRN_VOYAGER";
  
  private $namespaces = array(
    "sear" => "http://www.exlibrisgroup.com/xsd/jaguar/search",
    "def" => "http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib",
  );

  function __construct($xml) {
    $this->xpath = $this->loadXPath($xml);
    // Set Namespaces in Constructor
    foreach($this->namespaces as $prefix => $namespace) {
      $this->xpath->registerNamespace($prefix, $namespace);
    }  
  }
  
  public function __toString() {
    $full_primo_record = $this->get_record_root();
    //echo $full_primo_record;
    $primo_record_string = ""; //new DOMDocument;
    if (!is_null($full_primo_record)) {
      //echo $full_primo_record->saveXML();
      foreach ($full_primo_record as $element) {
       $primo_record_string .= "<br/>[". $element->nodeName. "]";
        $nodes = $element->childNodes;
        foreach ($nodes as $node) {
          $primo_record_string .= "<br/>[" . $node->nodeName . "] = " . $node->nodeValue. "\n";
        }
      }
    } else {
      $primo_record_string .= "[No Record Found]";
    }
    return $primo_record_string;
  }
  
  private function loadXPath($xml) {
    $dom = DOMDocument::loadXML($xml);
    return new DOMXPath($dom);
  }
  
  private function get_record_root() {
    return $this->xpath->query($this->xpath_doc_root);
  }
  
  private function query($path) {
    //echo $this->xpath_base.$path."\n";
    return $this->xpath->query($this->xpath_base.$path);
  }
  
  // get the contents of one specific tag
  public function getText($tag) {
    $textContent = '';
    $is_namespace = '/\w+:\w+/';
    if(!(preg_match($is_namespace,$tag))) {
      $tag = $this->def_ns.":".$tag;
    }
    $nodeList = $this->query($tag);
    foreach ($nodeList as $node) {
      $textContent .= ' ' . $node->textContent;
    }
    return $textContent;
  }
  
  public function getRecordID(){
    $source_path = "def:PrimoNMBib/def:record/def:control/def:recordid";
    $nodeList = $this->query($source_path);
    $textContent = "";
    foreach ($nodeList as $node) {
      $textContent .= ' ' . $node->textContent;
    }
    return $textContent;
  }
  
  public function getSourceIDs() {
    //$source_path = 
    $source_ids = array();
    $source_path = "def:PrimoNMBib/def:record/def:control/def:sourcerecordid";
    $sourceList = $this->query($source_path);
    $id_count = $sourceList->length; // how many sources did this rec have?
    foreach ($sourceList as $node) {
      //looks like $$V6109368$$OPRN_VOYAGER6109368     
      if ($id_count == 1) {
        array_push($source_ids, $this->institutionID.$node->textContent);
      } else {
        $id_delimiter = '/\$\$O/';
        $id = preg_split($id_delimiter,$node->textContent);
        array_push($source_ids,$id[1]);
      }
      
    }
    return $source_ids;
  }
  
  public function getAvailabilbleLibraries() {
    //$source_path = 
    $available_ids = array();
    $available_path = "def:PrimoNMBib/def:record/def:display/def:availlibrary";
    $availableList = $this->query($available_path);
    $num_avail_items = $availableList->length;
    $location_code_delimiter = '/\$\$Y/';
    $id_delimeter = '/\$\$O/';
    foreach ($availableList as $node) {
      if($num_avail_items == 1) {
        //echo $node->textContent;
        // have to split this $$IPRN$$LFIRE$$1(F)$$2PS3618.A914 B36 2010$$Savailable$$31$$40$$5N$$60$$Xprincetondb$$Yf
        $loc_code = preg_split($location_code_delimiter, $node->textContent);
        //echo $this->getRecordID();
        //echo $loc_code[1];
        $available_ids[$this->getRecordID()] = array($loc_code[1]);
         // only one to send back
      }
      else {
        // have to split this <aviallibrary>$$IPRN$$LFIRE$$1(F)$$2D810.C696 P644 2000$$Savailable$$31$$40$$5N$$619$$Xprincetondb$$Yf$$OPRN_VOYAGER3216675</availlibrary>
        $location_and_sourceid = preg_split($location_code_delimiter, $node->textContent);
        $available_string = preg_split($id_delimeter, $location_and_sourceid[1]);
        if (array_key_exists($available_string[1], $available_ids)) {
          array_push($available_ids[$available_string[1]], $available_string[0]);
        } else {
          $available_ids[$available_string[1]] = array($available_string[0]);
        }
      }
    }
    return $available_ids;
  }
  
}

?>
