<?php

Class PrimoRecord 
{
  
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
       $primo_record_string .= "[". $element->nodeName. "]";
        $nodes = $element->childNodes;
        foreach ($nodes as $node) {
          $primo_record_string .= "[" . $node->nodeName . "] = " . $node->nodeValue. "\n";
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
  private function getText($tag) {
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
      $textContent .= $node->textContent;
    }
    
    return $textContent;
  }
  
  /*
   * Returns all links from the sear:Links segment by category
   * 
   */
  public function getAllLinks() {
    $links = array();
    $source_path = "sear:LINKS//*";
    $nodeList = $this->query($source_path);
    foreach ($nodeList as $node) {
      //$node->tagName; 
      array_push($links, array($node->tagName, $node->textContent));
    }
    
    return $links;
  }
  
  public function getGetItLinks() {
    $getit_links = array();
    $source_path = "sear:GETIT";
    $nodeList = $this->query($source_path); // throw exception when empty
    $source_ids = $this->getSourceIDs();
    $record_counter = 0;
    foreach ($nodeList as $node) {
      $node_link_properties = array();
      if(($node->getAttribute('deliveryCategory'))) {
        $node_link_properties['deliveryCategory'] = $node->getAttribute('deliveryCategory');
      }
      if(($node->getAttribute('GetIt1'))) {
        $node_link_properties['GetIt1'] = $node->getAttribute('GetIt1');
      }
      if(($node->getAttribute('GetIt2'))) {
        $node_link_properties['fulltext'] = $node->getAttribute('GetIt2');
      }
      $getit_links[$source_ids[$record_counter]] = $node_link_properties;
      $record_counter = $record_counter + 1;
    }
    
    return $getit_links;
  }
  
  /* returns a brief representation of resource
   * 
   * fulltext openurl
   * non-fulltext openurl
   * location code info
   * voyager id
   */
  public function getBriefInfo() {
    $getit_links = $this->getGetItLinks();
    $available_libraries = $this->getAvailabilbleLibraries();
    $brief_info_data = array();
    foreach($getit_links as $voyager_key => $getit_data) {
      $voyager_key_available_libraries = array();
      $voyager_key_available_libraries['locations'] = $available_libraries[$voyager_key];
      $brief_info_data[$voyager_key] = array_merge($getit_links[$voyager_key], $voyager_key_available_libraries);
    }
    
    return $brief_info_data;
  }
  
  public function getOpenURL() {
    return;
  }
  
  /*
   *  getSourceIDs
   * 
   *  extracts record source ID from PNX record
   *  
   *  Example Text
   *  looks like $$V6109368$$OPRN_VOYAGER6109368  
   */
  
  public function getSourceIDs() {
    $source_ids = array();
    $source_path = "def:PrimoNMBib/def:record/def:control/def:sourcerecordid";
    $id_delimiter = '/\$\$O/';
    $sourceList = $this->query($source_path);
    $id_count = $sourceList->length; 
    foreach ($sourceList as $node) {    
      if ($id_count == 1) {
        array_push($source_ids, $this->institutionID.$node->textContent);
      } else {
        $id = preg_split($id_delimiter,$node->textContent);
        array_push($source_ids,$id[1]);
      }
      
    }
    
    return $source_ids;
  }
  
  /*
   * getAvailableLibraries
   * 
   * Looks at PNX Records and extracts PNX stored Locations for single 
   * source and merged records. From the "availlibrary" element
   * 
   *  Single Source Example Availibrary Text
   *  have to split this $$IPRN$$LFIRE$$1(F)$$2PS3618.A914 B36 2010$$Savailable$$31$$40$$5N$$60$$Xprincetondb$$Yf
   *  
   *  Merged Record Example Aviallibrary Text
   *  have to split this <aviallibrary>$$IPRN$$LFIRE$$1(F)$$2D810.C696 P644 2000$$Savailable$$31$$40$$5N$$619$$Xprincetondb$$Yf$$OPRN_VOYAGER3216675</availlibrary>
   *  
   */
  
  public function getAvailabilbleLibraries() {
    $available_ids = array();
    $available_path = "def:PrimoNMBib/def:record/def:display/def:availlibrary";
    $availableList = $this->query($available_path);
    $num_avail_items = $availableList->length;
    $location_code_delimiter = '/\$\$Y/';
    $id_delimeter = '/\$\$O/';
    foreach ($availableList as $node) {
      if($num_avail_items == 1) {
        $loc_code = preg_split($location_code_delimiter, $node->textContent);
        $available_ids[$this->getRecordID()] = array($loc_code[1]);
      } else {
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
