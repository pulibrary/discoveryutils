<?php
namespace PrimoServices;

/*
 * Returns primo bibliographic elements
 * based on the "RIS" Type Mapping 
 * see the "docs" directory for an example mapping 
 */

class PrimoDocument {
  
  private static $primo_fields_to_map = array(
    "lsr05" =>            array("U2","Call Number"),
    "isbn" =>             array("SN","isbn"),
    "creationdate" =>     array("Y1","creationdate"),
    "lds03" =>            array("U1","300 Field"),
    "ristype" =>          array("TY","Type"),
    "recordid" =>         array("ID","Source Record ID"),
    "title" =>            array("TI","title"),  
    "seconderytitle" =>   array("T2","secondary title"),
    "seriestitle" =>      array("T3","series title"),
    "creator" =>          array("AU","creator"),
    "contributor" =>      array("A2","contributor"),
    "notes" =>            array("N1","notes"),
    "abstract" =>         array("AB","abstract"),
    "subject" =>          array("KW","subject"),
    "periodicalfull" =>   array("JF","periodical full title"),
    "periodicalabbrev" => array("JA","periodical abbrev title"),
    "volume" =>           array("VL","volume"),
    "issue" =>            array("IS","issue"),
    "startpage" =>        array("SP","starting page"),
    "otherpages" =>       array("EP","other pages"),
    "cop" =>              array("CY","City of Publication"),
    "issn" =>             array("SN","issn"),
    "addau" =>            array("A2","additional authors"),
    "lds04" =>            array("PB","?"),
    "date" =>             array("DA", "Date"), //FIXME get ris date value
    "au" =>               array("AU", "Author"),
    //"aufirst" =>          array("", "Author First"),
    //"aulast" =>           array("", "Author Last"),
    "format" =>           array("FM", "Format"),
    "genre" =>            array("KW", "Genre"),
    "btitle" =>           array("T1", "btitle"),
    );
  
  protected function map() {
    
  }
  
  public static function getPNXFields() {
    return array_keys(self::$primo_fields_to_map);
  }
  
  public static function getPNXtoRISmappings() {
    return self::$primo_fields_to_map;
  }
}
