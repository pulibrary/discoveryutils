<?php 
namespace Primo;
use Primo\Document as PrimoDocument;
use Primo\Items\Archives as Archives;
use Utilities\Parser as XmlParser;
use Primo\PermaLink as Permalink;
use Primo\SearchDeepLink as SearchDeepLink;
use Primo\Holdings\Holding as PrimoHolding;
use Primo\Holdings\Archives as ArchivalHolding;

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
  
  // get the contents of one specific tag this should probably go
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
      array_push($links, array($node->tagName => $node->textContent));
    }
    return $links;
  }
  
  /* @return full-text link  
   */ 
  public function getFullTextLinktoSrc() {
      
    $full_text_present = false;
    $available_links = $this->getAllLinks();
    foreach($available_links as $linktype) {
      if(array_key_exists("sear:linktorsrc", $linktype)) {
        $full_text_link = $linktype["sear:linktorsrc"];
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
      if(array_key_exists("sear:openurlfulltext", $linktype)) {
        $full_text_link = $linktype["sear:openurlfulltext"];
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
	  // HACK FOR MARCIT LINKS
          if(strstr($full_text_link_value, 'sfx.princeton.edu')) {
          	$node_link_properties['fulltext'] = $this->getFullTextLinktoSrc();
	  } else {
          	$node_link_properties['fulltext'] = $node->getAttribute('GetIt1');
          }
        }
      }
      //if(($node->getAttribute('GetIt2'))) {
      //  $node_link_properties['openurl'] = $node->getAttribute('GetIt2');
      //}
      $getit_links[$source_ids[$record_counter]] = $node_link_properties;
      $record_counter = $record_counter + 1;
    }
    // hack for records without their own "getit" link
    if($record_counter < $id_count) {
       foreach($source_ids as $id) {
         if(!array_key_exists($getit_links, $id)) {
           $getit_links[$id] = array("deliveryCategory" => "Physical Item");
         }
      }      	
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


  public function getArchivalItems() {
    $items = array();
    $item_list = $this->getElements("lds48");
    $holding = $this->getArchivalHoldings();
    if($item_list->length > 0) {
      foreach($item_list as $item) {
        array_push($items, new \Primo\Items\Archives($item->textContent, $holding, $this));
      }
    }
    return $items;
  }
 
  private function getFindingAidPath() {
    $finding_aid_base = "http://findingaids.princeton.edu/collections/";
    $ead_id = str_replace('EAD', '', $this->getRecordID());
    //$ead_id = $this->getRecordID();
    $ead_path = str_replace('_', '/', $ead_id);
    return $finding_aid_base . $ead_path;
  }

  private function buildArchivalHoldings() {
    $this->buildHoldings();
    $holdings= $this->getHoldings();
    $holding_params = array();
    $holding_params['library'] = $holdings[0]->primo_library;
    
    $source = $this->getSourceID();
    $aeon_params = array();
    if ($source == 'EAD') {
      $holding_params['add_information'] = $this->getArchivalAddedDescriptions();
      $holding_params['call_number'] = $this->getArchivalCallNumber();
      $holding_params['summary_statement'] = $this->getSummaryArchivesStatement();
      $holding_params['access'] = $this->getAccessStatement();
      $holding_params['request_url'] = $this->getFindingAidPath();
      $holding_params['request_label'] = "Request Via Finding Aid";
      $holding_params['link_to_finding_aid'] = $this->getArchivesLinks();
      $holding_params['collection_title'] = $this->getArchivalCollectionTitle();
      $holding_params['collection_description'] = $this->getArchivalCollectionDescription();
      $aeon_params = array(
        'ReferenceNumber' => $this->getRecordID(),
        'Site' => $holding_params['library'],
        'CallNumber' => $this->getArchivalCallNumber(),
        'Location' => $this->getArchivalCollectionTitle(),
        'Action' => '10',
        'Form' => '21',
        'ItemTitle' => $this->getTitle(),
        'ItemAuthor' => $this->getCreator(),
        'ItemDate' => $this->getCreationDate(),
        'ItemInfo1' => 'Reading Room Access Only',
      );

    } elseif ($source == 'Theses') {
      $holding_params['call_number'] = $this->getOtherCallNum();
      $aeon_params = array(
        'ReferenceNumber' => $this->getRecordID(),
        'Site' => 'MUDD',
        'CallNumber' => $this->getOtherCallNum(),
        'Location' => 'mudd',
        'Action' => '10',
        'Form' => '21',
        'ItemTitle' => $this->getTitle(),
        'ItemAuthor' => $this->getCreator(),
        'ItemDate' => $this->getCreationDate(),
        'ItemInfo1' => 'Reading Room Access Only',
      );

    } elseif ($source == 'Visuals') {
      $holding_params['call_number'] = $this->getOtherCallNum();
      $aeon_params = array(
        'ReferenceNumber' => $this->getRecordID(),
        'Site' => 'RBSC',
        'CallNumber' => $this->getOtherCallNum(),
        'Location' => 'ga',
        'Action' => '10',
        'Form' => '21',
        'ItemTitle' => $this->getTitle() . " [" . $this->getGenre() . "]",
        'ItemVolume' => $this->getOtherSubTitle(),
        'SubLocation' => $this->getOtherItemInfoFour(),
        'ItemInfo1' => 'Reading Room Access Only',
        'ItemAuthor' => $this->getCreator()
      );
    }
    $holding_params['request_url'] = "https://libweb10.princeton.edu/aeon/aeon.dll?" . http_build_query($aeon_params);
    $holding_params['request_label'] = "Reading Room Request";

    return $holding_params;
  }
  
  private function getAccessStatement() {
    $access = $this->getElements("lds23");
    return $access->item(0)->textContent;
  }
 
   public function getOtherCallNum() {
    $element_data = $this->getElements("lds29");
    return $element_data->item(0)->textContent;
  }

  public function getOtherItemInfoFour() {
    $element_data = $this->getElements("lds42");
    return $element_data->item(0)->textContent;
  }

  public function getOtherSubTitle() {
    $element_data = $this->getElements("lds43");
    if($element_data->length > 0) {
        return $element_data->item(0)->textContent;
     } else {
        return null;
     }
  }
 
  public function getGenre() {
    $summary = $this->getElements("genre");
    return $summary->item(0)->textContent;
  }
  

  private function getSummaryArchivesStatement() {
    $summary = $this->getElements("lds05");
    if($summary->length > 0) {
        return $summary->item(0)->textContent;
     } else {
        return null;
     }
  }
  
  
  private function getArchivalAddedDescriptions() {
    $added_info = $this->getElements("lds40");
    return $added_info->item(0)->textContent;
  }

  private function getArchivalCollectionDescription() {
    $added_info = $this->getElements("lds44");
     if($added_info->length > 0) {
        return $added_info->item(0)->textContent;
     } else {
        return null;
     }
  }

  private function getArchivalCollectionTitle() {
    $added_info = $this->getElements("lds43");
    if($added_info->length > 0) {
      return $added_info->item(0)->textContent;
    } else {
      return null;
    }
  }

  private function getArchivalCallNumber() {
    $call_num = $this->getElements("lds28");
    if($call_num->length > 0) {
      return $call_num->item(0)->textContent;
    } else {
      return null;
    }
  }
  
  /* gets link to finding aid 
   * 
   * Stored in LINKS/linktofa
   * 
   * */
  
  private function getArchivesLinks() {
   
    $links = $this->getAllLinks();
    $link_to_finding_aid = NULL;
    foreach ($links as $link) {
      if(array_key_exists('sear:linktofa', $link)) {
        $link_to_finding_aid = $link['sear:linktofa'];
      }
    }
    return $link_to_finding_aid;
  }
  
  public function getArchivalHoldings() {
    // return an archival holdings object 
    $holdings_info = $this->buildArchivalHoldings();
    $archival_holdings = new ArchivalHolding($holdings_info);
    
    return $archival_holdings;
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
      $request_link = $this->primo_server_connection['record.request.base'] . "?" . "bib=" . $this->getRecordID() . "&loc=" . $current_holding->location_code;
      if(isset($this->primo_server_connection['available.scopes'][$current_holding->primo_library]['name'])) {
        $library_label = $this->primo_server_connection['available.scopes'][$current_holding->primo_library]['name'];
      } else {
        $library_label = "can't find unknown";
      }
      array_push( $holdings_locations, array($current_holding->primo_library => array(
        'location_code' => $current_holding->location_code,
        'library_label' => $library_label,
        'request_link' =>  $request_link,
          ),
        )
      );
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
  
  public function getSourceType() {
    $control_information = $this->getElements('control');
    $fields = $this->getControlFields($control_information);
    
    return $fields['sourceformat'];
  }
  
  public function getSourceSystem() {
    $control_information = $this->getElements('control');
    $fields = $this->getControlFields($control_information);
    
    return $fields['sourcesystem'];
  }
 
  public function getSourceID() {
    $control_information = $this->getElements('control');
    $fields = $this->getControlFields($control_information);
    
    return $fields['sourceid'];
  }
 
  /* strip punctuation of the end of titles
   * 
   */
  public function getNormalizedTitle() {
    $title = $this->getTitle();
    $this->processTitles();
    return rtrim($title, ".;/,");
  }
  
  private function processTitles() {
    // test for format type....
    $display_data = $this->getElements("display");
    $fields = $this->getSectionFields($display_data);
    //print_r($fields);
    $search_data = $this->getElements("search");
    $normalized_fields = $this->getSectionFields($search_data);
    //return $fields['title'][0];
    //print_r($normalized_fields);
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
  
  private function getControlFields(\DOMNodeList $nodeList) {
    $control_fields = array(
      "sourceid",
      "recordid",
      "sourcesystem",
      "sourceformat"
    );
    $control_values = array();
    if($nodeList->length == 1) {
      $data_elements = $nodeList->item(0);
      if($data_elements->hasChildNodes()) {
        foreach($control_fields as $field) {
          $pnx_conrol_elements = $data_elements->getElementsByTagName($field); 
          $control_values[$field] = $pnx_conrol_elements->item(0)->textContent;
        }
      }
    }
    
    return $control_values;
  }
  
  public function getResourceLink() {
    //if($this->getFullTextLinktoSrc()) {
    //  $resource_link = $this->getFullTextLinktoSrc();
    //} else {
    $resource_link = "";
    //if($this->isDedup()) { //FIXME Should get First Source ID 
      $deep_link = new PermaLink($this->getRecordID(), $this->primo_server_connection);
      $resource_link = $deep_link->getLink();
    //} else {
    //  $deep_search = new SearchDeepLink($this->getRecordID(), "any", "contains", $this->primo_server_connection);
    //  $resource_link = $deep_search->getLink();
    //}

    return $resource_link;
    
  }
  
  
  public function isDedup() {
    if(strstr('dedup', $this->getRecordID())) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  public function hasFullText() {
    
    $fulltext = "N"; 
 
    if($this->getFullTextLinktoSrc()) {
      $fulltext = "Y";
    }
    
    return $fulltext;
  }  
  
  public function getCreationDate() {
    $creation_date = $this->getElements('creationdate');

    if($creation_date->length > 0) {
      $date = $creation_date->item(0);
      return $date->nodeValue;
    } else {
      return NULL;
    }
    
  }
  
  public function getCreator() {
    $creator = $this->getElements('creator');

    if($creator->length > 0) {
      $creator_string = $creator->item(0);
      return $creator_string->nodeValue;
    } else {
      return NULL;
    }
  }
  
  public function getToc() {
    $toc = $this->getElements('toc');
    
    if($toc->length > 0) {
      $toc_string = $toc->item(0);
      return $toc_string->nodeValue;
    } else {
      return NULL;
    }
  }
  
  public function getDescription() {
    $description = $this->getElements('description');

    if($description->length > 0) {
      $desc_string = $description->item(0);
      return $desc_string->nodeValue;
    } else {
      return NULL;
    }
  }
  
   public function getNotes() {
    $notes = $this->getElements('notes');

    if($notes->length > 0) {
      $notes_string = $notes->item(0);
      return $notes_string->nodeValue;
    } else {
      return NULL;
    }
  }
  
  public function getPublisher() {
    $publisher = $this->getElements('publisher');

    if($publisher->length > 0) {
      $publisher_string = $publisher->item(0);
      return $publisher_string->nodeValue;
    } else {
      return NULL;
    }
  }
  
  public function getISXN() {
    //ADDME returns standard numbers (ISSN/ISBN associated with the record)
    //$isbn = $this->getElements('isbn');
    //$issn = $this->getElements('issn');
    
    
  }
  
  
  /*
   * Accepts a string that represents a record format type
   * and returns true or false based on the return value
   */
  public function isA($type) {
    if($type == $this->getFormatType()) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  /*
   * Test if record source is XML
   * 
   */
  public function isXmlSource() {
    $source_type = $this->getSourceType();
    if($source_type == "XML") {
      return TRUE;
    } else {
      return FALSE;
    }
  }

}
?>
