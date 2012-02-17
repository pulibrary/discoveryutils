<?php

namespace PrimoServices;

Class PrimoQuery
{
  
   /*
   * xservice URL like http://searchit.princeton.edu/PrimoWebServices/xservice/search/brief?institution=PRN&onCampus=false&query=any,exact,lok&indx=1&bulkSize=50&dym=true&highlight=true&lang=eng&firsthit=1&lasthit=50
   * 
   * send item a primo query object 
   * should I have a primo results objects 
   */
  
  private $query_string;
  private $query_value; //the actual text of the query "cat"/
  private $index_field;
  private $precision_operator;
  private $query_params = array(
    "institution" => "PRN",
    "onCampus" => "false",
    "indx" => "1",
    "bulkSize" => "10",
    "dym" => "true",
    "highlight" => "true",
    "lang" => "eng",
    "firsthit" => "1",
    "lasthit" => "10"
    // should tabs be included?
    );
  
  private $allowed_index_params = array( //FIXME Should come from Config structure many more parameters
    "any",
    "isbn",
    "issn",
    "title"
  ); 
  
  private $allowed_precision_operators = array(
    "exact",
    "contains",
    "begins_with"
  );
  
  private $available_local_scopes = array( //See http://searchit.princeton.edu/PrimoWebServices/xservice/getscopesofview?viewId=PRINCETON for list of scopes
    "PRN",
    "LEWIS",
    "ARCH"
  );

  private $available_remote_scopes = array(
    "SummonThirdNode"
  );
  
  function __construct($query_value, $index_field = "any", $precision_operator = "exact", $scope = "PRN") {
    $this->query_value = $query_value;
    $this->index_field = $index_field;
    $this->precision_operator = $precision_operator;
    $this->scope = $scope;
    $this->query_params['query'] = $this->buildQuery(); 
    $this->query_params['loc'] = $this->buildScope(); //Query can also be an array
    $this->query_string = $this->buildQueryString($this->query_params);
    
  }
  
  public function getQueryString() {
    if($this->isValidQuery()) {
      return $this->query_string;
    } else {
      return "Invalid Query" . $this->query_string;
    }
  }
  
  private function buildQuery() {
    return $this->index_field . "," . $this->precision_operator . "," . $this->query_value; //FIXME check for valid operators 
  }
  
  private function buildScope() {
   return "local,scope:(" . $this->scope . ")"; //FIXME Check for valid scope
  }

  private function buildRemoteScope($remote_scope) {
    return "remote,adaptor," . $remote_scope;
  }

  private function buildScopes() {
    //construct the loc parameter if needed//Bug in primo a web service query can accept only a single scope
  }
  
  private function buildQueryString($query_params) {
     return http_build_query($query_params);
  }

  private function isValidScope() {
    if (in_array($this->scope, $this->available_local_scopes)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  private function isValidQueryValue() {
    if(preg_match('/\w+/', $this->query_value)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  private function isValidQuery() {
    if($this->isValidScope() && $this->isValidQueryValue() && $this->isValidIndexType() && $this->isValidPrecisionOperator()) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  
  private function isValidPrecisionOperator() {
    if(in_array($this->index_field, $this->allowed_index_params)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  private function isValidIndexType() {
     if(in_array($this->precision_operator, $this->allowed_precision_operators)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function __toString() {
    return $this->query_string;
  }
}
  
