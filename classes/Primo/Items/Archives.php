<?php

namespace Primo\Items;

/*
 * Parse OUT Examples 
 * 
 * $$Ymss$$2C0101$$Ebox 688$$CSeries 5: Book Catalogs and Lists, 1855-1996 (gaps)
 * 
 * $$Yrcpxm$$2C0751$$Ebox 1
 * 
 */


class Archives
{
  
  private $subfield_mappings = array(
    "Y" => "location_code",
    "E" => "box_number",
    "C" => "series_details",
    "2" => "call_number",
  ); 
  
  private $subfields = array();
  private $item_source;
  
  public function __init($item_string) {
    $this->item_source = strval($item_string);   
    $this->buildItem();
  }
  
  private function buildItem() {
    
    $subfield_data_list = explode("$$", $this->item_source);
    array_shift($subfield_data_list); // remove empty first value from explode since all strings start with $$
    $this->extractSubfields($subfield_data_list);
  }
  
  private function extractSubfields($subfield_data_list) {

    foreach($subfield_data_list as $subfield_data) {
   
      $subfield_delimiter = substr($subfield_data, 0, 1);
      $subfield_content = substr($subfield_data, 1);

      if(array_key_exists($subfield_delimiter, $this->subfield_mappings)) {
        $this->subfields[$this->subfield_mappings[$subfield_delimiter]] = $subfield_content;
      }
    }
  }
  
  /*
   * 
   * returns an Array of the Aeon params associated with the request
   * 
   * This array contains a mashup of data from the Record, Archival Holdings, and Archival Item Levels
   * 
   */
  public function getRequestParams() {
    $request_params = array();
    
    return $request_params;
  }
  
  public function __toString() {
    return $this->item_source;
  }
  
  
  public function __get($name) {
    if (array_key_exists($name, $this->subfields)) {
      return $this->subfields[$name];
    }
  }
  
   public function __isset($name) {
      return isset($this->subfields[$name]);
   }
  
}
