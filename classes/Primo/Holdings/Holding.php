<?php
namespace Primo;

/* 
 * 
 * $$IPRN$$LRARE$$1Cotsen Children's Library (CTSN)$$288378$$Scheck_holdings$$30$$41$$5N$$60$$Xprincetondb$$Yctsn
 * $$IPRN$$LRECAP$$1(RCPPA)$$27500.503$$Savailable$$384$$40$$5Y$$636$$Xprincetondb$$Yrcppa$$OPRN_VOYAGER490930
 */

class Holding
{
  private $subfield_mappings = array(
    "I" => "institution",
    "L" => "primo_library",
    "1" => "location_label",
    "2" => "call_number",
    "S" => "service",
    "X" => "source_db",
    "Y" => "location_code",
    "O" => "source_id",
    "3" => "three",
    "4" => "four",
    "5" => "five",
    "6" => "six",
  ); 
  /*
  private $avail_library_pieces;
  private $callnum;
  private $source_id;
  private $location_code;
  private $primo_library_code;
  private $location_label;
  private $raw_source;
   * 
   */
  private $subfields = array();
  
  public function __construct($holdings_statement) {
    $this->holdings_source = strval($holdings_statement);   
    $this->buildHolding();
  }
  
  private function buildHolding() {

    $subfield_data_list = explode("$$", $this->holdings_source);
    array_shift($subfield_data_list); // remove empty first value from explode since all strings start with $$
    $this->extractSubfields($subfield_data_list);
  }
  
  private function extractSubfields($subfield_data_list) {

    foreach($subfield_data_list as $subfield_data) {
      //echo "one" . $subfield_data;
      //echo $subfield_data;
      $subfield_delimiter = substr($subfield_data, 0, 1);
      $subfield_content = substr($subfield_data, 1);
      //echo $subfield_delimiter . " = " . $subfield_content . "\n";
      if(array_key_exists($subfield_delimiter, $this->subfield_mappings)) {
        $this->subfields[$this->subfield_mappings[$subfield_delimiter]] = $subfield_content;
      }
    }
  }
  
  public function getSubfields() {
    return $this->subfields;
  }
  
  public function __toString() {
    return $this->holdings_source;
  }
  
  
  public function __get($name) {
    if (array_key_exists($name, $this->subfields)) {
      return $this->subfields[$name];
    }
  }
  
   public function __isset($name)
   {
      return isset($this->subfields[$name]);
   }
}
