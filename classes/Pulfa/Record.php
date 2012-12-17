<?php

namespace Pulfa;
use Primo\Parser as XmlParser;

/*
 * @ \Pulfa\Record
 *
 * Represents a single Pulfa Document
 * 
 * Class implements PHP magic methods for __get and __isset in order to provide access to record 
 * properties
 * 
 * $record = new Record();
 * echo $record->myroperty
 * 
 * See $record_elements for available properties
 * 
 * The isset method all for boolean testing of property values i.e.
 * 
 * if (isset($record->digital)) {
 *    // do something 
 * }
 */

class Record
{
  
  private $fields = array(); // major elements 
  
  private $record_elements = array(
    "uri",
    "digital",
    "title",
    "creator",
    "lang",
    "date",
    "type",
    "id",
    "extent",
    "hits",
  );
  private $namespaces = array(
    "pulfa" => "http://library.princeton.edu/pulfa",
  );
  private $default_namespace = "pulfa";
  private $dom; //dom representation of record 
  public $breadcrumbs;
  
  function __construct($xml) {
    $this->dom = XmlParser::convertToDOMDocument($xml);  
    $this->xpath = $this->loadXPath($this->dom);
    foreach($this->namespaces as $prefix => $namespace) {
      $this->xpath->registerNamespace($prefix, $namespace);
    } 
    $this->loadFields();
    $this->breadcrumbs = $this->loadBreadCrumbs();
  }
  
  // use the isset and get php magic methods to provide access to hit properties
  
  private function query($path) {
    return $this->xpath->query($path);
  }
  
  
  
  private function getElementText($element) {
    $textContent = "";
    $elementList = $this->query($this->default_namespace.":".$element);
    foreach ($elementList as $node) {
      $textContent .= ' ' . $node->textContent;
    }
    
    return $textContent;
  }
  
  private function loadXPath($dom) {
    try {
      return new \DOMXPath($dom);
    } catch (Exception $e) {
      return "Error Parsing Pulfa Record: " . $e->getMessage();
    }
  }
  
  
  private function loadFields() {
    foreach($this->record_elements as $field) {
      if($field == 'digital') {
        $element_data = $this->getElementText('has-digital-content'); 
      } else {
        $element_data = $this->getElementText($field);
      }
      if ($element_data) {
        $this->fields[$field] = trim($element_data);
      }
    }
  }
  
  public function loadBreadCrumbs() {
    $breadcrumb_data = $this->query('//pulfa:breadcrumbs');
    $breadcrumb_list = array();
    if($breadcrumb_data->length > 0) {
      $breadcrumbs = $breadcrumb_data->item(0);
      if($breadcrumbs->hasChildNodes()){
        $crumbs = $breadcrumbs->getElementsByTagName('crumb');
        foreach($crumbs as $node) {
          $item_crumb_data = array(
            'text' => $node->nodeValue,
            'uri' => $node->getAttribute('uri'),
            'level' => $node->getAttribute('level'),
            );
          array_push($breadcrumb_list, $item_crumb_data);
        }
      }
    } 
    
    return $breadcrumb_list;
    
  }
  
  public function __toString() {
    return $this->dom->saveXML();
  }
  
  
  public function __get($name) {
    if (array_key_exists($name, $this->fields)) {
      return $this->fields[$name];
    }
  }
  
   public function __isset($name)
   {
      return isset($this->fields[$name]);
   }
}
