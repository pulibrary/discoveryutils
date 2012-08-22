<?php
namespace PrimoServices;

/*
 * Returns primo bibliographic elements
 * based on the "RIS" Type Mapping 
 * see the "docs" directory for an example mapping 
 */

class PrimoDocument {
  
  // mapped from RIS fields Defined at: http://en.wikipedia.org/wiki/RIS_(file_format)#Tags
  
  private static $primo_fields_to_map = array(
    "isbn" =>             array("SN","isbn"),
    //"creationdate" =>     array("Y1","creationdate"),
    "risdate" =>          array("Y1", "RIS Date Field"),
    "ristype" =>          array("TY","Type"),
    "recordid" =>         array("ID","Source Record ID"),
    "title" =>            array("TI","title"),  
    "seconderytitle" =>   array("T2","secondary title"),
    "seriestitle" =>      array("T3","series title"),
    "unititle" =>         array("T3", "tertiary title"),
    //"creator" =>          array("AU","creator"), stick with the fields from addata for author values 
    //"contributor" =>      array("A2","contributor"),
    "aucorp" =>           array("A2", "Corporate Author"),
    "notes" =>            array("N1","notes"),
    "abstract" =>         array("AB","abstract"),
    "subject" =>          array("KW","subject"),
    "periodicalfull" =>   array("JF","periodical full title"),
    "periodicalabbrev" => array("JA","periodical abbrev title"),
    //"jtitle" =>           array("JO", "Journal Title"),
    "volume" =>           array("VL","volume"),
    "issue" =>            array("IS","issue"),
    "startpage" =>        array("SP","starting page"),
    "number" =>           array("M1", "number"),
    "otherpages" =>       array("EP","other pages"),
    "cop" =>              array("CY","City of Publication"),
    "issn" =>             array("SN","issn"),
    "pub" =>              array("PB", "Publisher"),
    "addau" =>            array("A2","additional authors"),
    //"lds04" =>            array("PB","?"),
    'lsr05' =>            array('CN', "Call Number"),
    //'classificationlcc' =>array('CN', "Call Number"),
    'alttitle' =>         array('J2', "Alternative Title"),
    //"date" =>             array("DA", "Date"), 
    "au" =>               array("AU", "Author"),
    //"aufirst" =>          array("", "Author First"),
    //"aulast" =>           array("", "Author Last"),
    "language" =>         array("LA", "Language"),
    "format" =>           array("M3", "Type of Work"),
    "genre" =>            array("KW", "Genre"),
    //"btitle" =>           array("TI", "btitle"),
    "stitle" =>           array("ST", "Short Title"),
    "rsrctype" =>         array("EX", "Extra Field"),
    );
  
  
  public static function getPNXFields() {
    return array_keys(self::$primo_fields_to_map);
  }
  
  public static function getPNXtoRISmappings() {
    return self::$primo_fields_to_map;
  }
}
