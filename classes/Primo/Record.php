<?php
namespace Primo;
use Primo\Document as PrimoDocument;
use Primo\Parser as XmlParser;
use Primo\PermaLink as Permalink;
use Primo\SearchDeepLink as SearchDeepLink;
use Primo\Holding as PrimoHolding;

Class Record 
{
  
  private $xpath_base = "//sear:DOC[1]//";
  private $xpath_doc_root = "//sear:DOC[1]";
  private $def_ns = "def";
  private $xpath;
  private $primo_server_connection;
  private $institutionID = "PRN_VOYAGER";
  private $holdings = array();
  
  private $namespaces = array(
    "sear" => "http://www.exlibrisgroup.com/xsd/jaguar/search",
    "def" => "http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib",
  );

  function __construct($xml,$primo_server_connection) {
    $this->primo_server_connection = $primo_server_connection;
    $dom = XmlParser::convertToDOMDocument($xml);
    $this->xpath = $this->loadXPath($dom);
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
  
  private function loadXPath($dom) {
    
    return new \DOMXPath($dom);
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
  
  /* @return full-text link  
   */ 
  public function getFullTextLinktoSrc() {
      
    $full_text_present = false;
    $available_links = $this->getAllLinks();
    foreach($available_links as $linktype) {
      if($linktype[0] == "sear:linktorsrc") {
        $full_text_link = $linktype[1];
        $full_text_present = true;  
      }
    }
    if($full_text_present) {
      return $full_text_link;
    } else {
      return false;
    }
  }
  
  public function getFullTextOpenURL() {
    
    $full_text_present = false;
    $available_links = $this->getAllLinks();
    foreach($available_links as $linktype) {
      if($linktype[0] == "sear:openurlfulltext") {
        $full_text_link = $linktype[1];
        $full_text_present = true;  
      }
    }
    if($full_text_present) {
      return $full_text_link;
    } else {
      return false;
    }
  }
  
  
  public function getGetItLinks() {
    $getit_links = array();
    $source_ids = $this->getSourceIDs();
    $id_count = count($source_ids); 
    if ($id_count == 1) {
      $source_path = "sear:GETIT[1]"; // take only the first item
    } else {
      $source_path = "sear:GETIT";
    }
    $nodeList = $this->query($source_path); // throw exception when empty
    $record_counter = 0;
    foreach ($nodeList as $node) {
      //link_group = array();
      $node_link_properties = array();
      if(($node->getAttribute('deliveryCategory'))) {
        $node_link_properties['deliveryCategory'] = $node->getAttribute('deliveryCategory');
      }
      if(($full_text_link_value = $node->getAttribute('GetIt1'))) { //FIXME - is GetIt1 comprised of only full-text links?
        if(strstr($full_text_link_value, 'http' )) {
          $node_link_properties['fulltext'] = $node->getAttribute('GetIt1');
          }
      }
      //if(($node->getAttribute('GetIt2'))) {
      //  $node_link_properties['openurl'] = $node->getAttribute('GetIt2');
      //}
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
   * 
   * returns multiple source records if the PNX record in question is a 
   * dedupped title 
   */
  public function getBriefInfo() {
    $getit_links = $this->getGetItLinks();
    $available_libraries = $this->getAvailableLibraries();
    $brief_info_data = array();
    foreach($getit_links as $voyager_key => $getit_data) {
      $voyager_key_available_libraries = array();
      $voyager_key_available_libraries['locations'] = $available_libraries[$voyager_key];
      $locator_links = array();
      foreach($available_libraries[$voyager_key] as $location_code) {
        $locator_link = new \Primo\LocatorLink($this->split_voyager_id($voyager_key), $location_code); //FIXME perhaps splitting should be moved to locater class
        array_push($locator_links, $locator_link->getLink());
      }
      $brief_info_data[$voyager_key] = array_merge($voyager_key_available_libraries, $getit_links[$voyager_key], array('voyager_id' => $this->split_voyager_id($voyager_key)), array('locator_links' => $locator_links));
      // build a permalink for each
      /*
       * Support both Primo deep linking styles. deep links got to the full record view
       * deep search links go to a search for the source PRNVOYAGER ID of the title. 
       * 
       * A dedup title deep link uses Primos "dedup" prefix ID when present, but deep search links always use the source voyuager ID.
       * A deep search link for the voyager id of a merged record will take you the merged Primo record.
       */
      $deep_link = new PermaLink($this->getRecordID(), $this->primo_server_connection);
      $brief_info_data[$voyager_key]['permalink'] = $deep_link->getLink();
      
      $deep_search = new SearchDeepLink($voyager_key, "any", "contains", $this->primo_server_connection);
      $brief_info_data[$voyager_key]['deep_search_id_link'] = $deep_search->getLink();


    }
    
    return $brief_info_data;
  }
  
  private function split_voyager_id($id) {
    $pnx_id_components = preg_split('/PRN_VOYAGER/', $id);
    
    return $pnx_id_components[1];
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
   *  Merged Record Example Avalllibrary Text
   *  have to split this <availlibrary>$$IPRN$$LFIRE$$1(F)$$2D810.C696 P644 2000$$Savailable$$31$$40$$5N$$619$$Xprincetondb$$Yf$$OPRN_VOYAGER3216675</availlibrary>
   *  
   */
  
  public function getAvailableLibraries() {
    $available_ids = array();
    $available_path = "def:PrimoNMBib/def:record/def:display/def:availlibrary";
    // need to check for another test 
    $availableList = $this->query($available_path);
    $num_avail_items = count($this->getSourceIDs());
    $location_code_delimiter = '/\$\$Y/';
    $id_delimeter = '/\$\$O/';
    foreach ($availableList as $node) { //FIXME - This code block should probably be rethought
      if($num_avail_items == 1) {
        $loc_code = preg_split($location_code_delimiter, $node->textContent);
        if (array_key_exists($this->getRecordID(), $available_ids)) {
          array_push($available_ids[$this->getRecordID()], $loc_code[1]);
        } else {
          $available_ids[$this->getRecordID()] = array($loc_code[1]);
        }
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

  private function buildHoldings() {
    $available_path = "def:PrimoNMBib/def:record/def:display/def:availlibrary";
    // need to check for another test 
    $availableList = $this->query($available_path);
    //$num_avail_items = count($this->getSourceIDs());
    return $availableList;
  }

  /* return an array of "PrimoHolding" objects for all composite records */

  public function getHoldings() {
    $holdings_list = $this->buildHoldings();
    foreach($holdings_list as $holding) {
      array_push($this->holdings, new PrimoHolding($holding->textContent)); 
    }
    
    return $this->holdings;
  }
  
  public function getBriefHoldings() {
    $holdings_list = $this->buildHoldings();
    $holdings_locations = array();
    foreach($holdings_list as $holding) {
      $current_holding = new PrimoHolding($holding->textContent); 
      array_push( $holdings_locations, $current_holding->primo_library);
    }
    
    return $holdings_locations;
  }

  private function getElements($tag, $namespace_prefix = "def") {
    $queryString = $namespace_prefix . ":" . $tag;
    $nodeList = $this->query($queryString);
    
    return $nodeList;
  }

  /*
   * Return Primo Metadata from the "display" and "addata" 
   * sections of the PNX record.
   */
  public function getPrimoDocumentData() {
    $display_data = $this->getElements("display");
    $display_values = $this->getSectionFields($display_data);
    $add_data_section = $this->getElements("addata");
    $add_data_section_values = $this->getSectionFields($add_data_section);
    $record_metadata = array_merge($display_values, $add_data_section_values); //, $enrichment_section_values); //, $search_data_values);//$display_values, );

    return $record_metadata;
  }
  
  public function getSubjects() {
    //FIXME return subject headings associated with the documents
  }
  
  public function getNotes() {
    //FIXME get notes attached to document 
  }
  
  public function getRisType() {
    return $this->getElements("ristype");
  }
  
  public function getCallNumber() {
    return $this->getText("lsr05");
  }
  
  public function getFormatType() {
    $search_data = $this->getElements("search");
    $fields = $this->getSectionFields($search_data);
    
    return $fields['rsrctype'][0];
  }
  
  public function getTitle() {
    $display_data = $this->getElements("display");
    $fields = $this->getSectionFields($display_data);
    
    return $fields['title'][0];
  }
  
  // this should be refactored method is way toooo long
  public function getCitation($type = "RIS") {
    $primo_document = new PrimoDocument();
    $metadata = $this->getPrimoDocumentData();
    //print_r($metadata);
    $format_mappings = array();
    if ($type == "RIS") {
      $ris_mapping = $primo_document::getPNXtoRISmappings();
      $ris_type = "TY - ";
      $ris_title = "TI - "; //FIXME is this just for needed for monographs
      foreach($metadata as $key => $field_values) {
        $ris_label = $ris_mapping[$key];
        $ris_key = $ris_label[0];
        $value = $field_values[0];
        //foreach($field_values as $value) { //FIXME take only the first value for publishers, jtitles, and btitles 
          if($key == "ristype") {
            $ris_type .= $value;
          } elseif($key == "title" || $key == "btitle" || $key == "jtitle") {
            $short_title = preg_split("/\s(\/|:)\s/", $value); //FIXME kludge to split on delimiters
            $ris_title .= $short_title[0];
            array_push($format_mappings, $ris_title);
          } elseif($key == "subject") {
            $subjects = preg_split("/;/", $value); // split field along values
            foreach($subjects as $subject) {
              $ris_subject = "KW - " . trim($subject);
              //echo $ris_subject;
              array_push($format_mappings, $ris_subject);
            }
          } elseif($key == "seriestitle") {
            $series_split = preg_split("/;/", $value);
            $series_title = "T3 - " . trim($series_split[0]);
            array_push($format_mappings, $series_title);
            $number_series = "M1 - " . $series_split[1];
            if($series_split[1]) {
              array_push($format_mappings, $number_series);
            }
          } elseif($key == "addau") {
            if(is_array($field_values)) {
              foreach($field_values as $add_author) {
                array_push($format_mappings, "A2 - " . $add_author);
              }
            } else {
              array_push($format_mappings, "A2 - " . $value);
            }
          } elseif($key == "language") {
            if($value != "und;und") {
              array_push($format_mappings, "LA - ". $value);
            }
          } else {
            $ris_value = $ris_key . " - " . $value;
            array_push($format_mappings, $ris_value);
          }
      }
      array_unshift($format_mappings, $ris_type); //Make sure RIS type is first element. 
    }
    
    $resource_link = $this->getResourceLink();
    
    if($this->getCallNumber()) {
      array_push($format_mappings, "CN - ". trim($this->getCallNumber()));
    }
    array_push($format_mappings, "UR - ". $resource_link);
    array_push($format_mappings, "ER - "); //push the RIS last reference marker on stack

    return implode("\n", $format_mappings);
  }
  
  /* is this more efficeint that getRecordID
  public function getId() {
    $nodeList = $this->getElements("recordid");
    //echo $nodeList->length;
    $record_id = $nodeList->item(0); //FIXME do we need to check for more than one?
    return $record_id->textContent;
  }
  */
  /* 
   * 
   * returns an @array
   * [pnx_data_field] => array_of_values()
   * in practice mosts fields will have one value, but some will have repeats
   */
  private function getSectionFields(\DOMNodeList $nodeList) {
    $section_values = array();
    $primo_document = new PrimoDocument();
    if($nodeList->length == 1) {
      $data_elements = $nodeList->item(0);
      if($data_elements->hasChildNodes()){ //FIXME should this block be it's own function
        foreach($primo_document::getPNXFields() as $field) { 
          $pnx_data_elements = $data_elements->getElementsByTagName($field);
          foreach($pnx_data_elements as $element) {
            $value = $element->textContent;
            if(array_key_exists($field, $section_values)) {
              array_push($section_values[$field], $value);
            } else {
              $section_values[$field] = array($value);
            }
          }
        }
      }
    }

    return $section_values;
  }
  
  public function getResourceLink() {
    if($this->getFullTextLinktoSrc()) {
      $resource_link = $this->getFullTextLinktoSrc();
    } else {
      $deep_link = new PermaLink($this->getRecordID(), $this->primo_server_connection);
      $resource_link = $deep_link->getLink();
    }
    
    return $resource_link;
    
  }
  
  public function getStdNums() {
    //ADDME returns standard numbers (ISSN/ISBN associated with the record)
  }
  
  /*
   * Check out Magic Methods Implementation for access to important properties
   */  
}
?>