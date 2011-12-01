<?php
namespace PrimoServices;

/*
 * Returns primo bibliographic elements
 * based on the "RIS" Type Mapping 
 * see the "docs" directory for an example mapping 
 */

class PrimoDocument {
  
  private $primo_fields_to_map = array(
    "lsr05" => "Call Number",
    "isbn" => "isbn",
    "creationdate" => "creationdate",
    "lds03" => "300 Field",
    "ristype" => "Type",
    "recordid" => "Source Record ID",
    "title" => "title",
    "seconderytitle" => "secondary title",
    "seriestitle" => "series title",
    "creator" => "creator",
    "contributor" => "contributor",
    "notes" => "notes",
    "abstract" => "abstract",
    "subject" => "subject",
    "periodicalfull" => "periodical full title",
    "periodicalabbrev" => "periodical abbrev title",
    "volume" => "volume",
    "issue" => "issue",
    "startpage" => "starting page",
    "otherpages" => "other pages",
    "cop" => "City of Publication",
    "issn" => "issn",
    "addau" => "additional authors",
    "subject" => "subject",
    "lds04" => "?"
    );
  
  protected function map() {
    
  }
}
