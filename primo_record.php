<?php

Class PrimoRecord {
  
  private $xpath_base = "//sear:DOC[1]//";
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
  
  private function loadXPath($xml) {
    $dom = DOMDocument::loadXML($xml);
    return new DOMXPath($dom);
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
    $source_ids = array();
    $source_path = "def:PrimoNMBib/def:record/def:control/def:sourcerecordid";
    $sourceList = $this->query($source_path);
    foreach ($sourceList as $node) {
      array_push($source_ids,$node->textContent);
    }
    return $source_ids;
  }
  
}

?>