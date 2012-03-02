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
    "displayField" => "title",
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
    "ARCH",
    "COURSE",
    "OTHERS",
    "FIRE",
    "MUSIC",
    "ENG",
  );

  private $available_remote_scopes = array(
    "SummonThirdNode"
  );
  
  function __construct($query_value, $index_field = "any", $precision_operator = "exact", $scopes = array()) {
    $this->query_value = $query_value;
    $this->index_field = $index_field;
    $this->precision_operator = $precision_operator;
    $this->scopes = $scopes;
    $this->query_params['query'] = $this->buildQuery(); 
    //$this->query_params['loc'] = $this->buildScopes(); //Query can also be an array
    $this->query_string = $this->buildQueryString($this->query_params);
    $this->query_string .= $this->buildScopes();
  
  }
  
  public function getQueryString() {
    return $this->query_string;
  }
  
  private function buildQuery() {
    $this->normalizeQuery($this->query_value);
    return $this->index_field . "," . $this->precision_operator . "," . $this->normalizeQuery($this->query_value); //FIXME check for valid operators 
  }
  
  private function normalizeQuery($query_string) {
    return str_replace(",", "", $query_string);
  }
  
  /* local and remote scopes must be treated differently */
  private function buildScopes() {
   $scopes = array();
   foreach($this->scopes as $scope) {
     if($this->isRemoteScope($scope)) {
       if (count($scopes) == 0) {
         array_push($scopes, "adaptor," . $scope); 
       }
       else {
         array_push($scopes, $scope); 
       }
     } else {
       if (count($scopes) == 0) {
        array_push($scopes, "local,scope:(" . $scope . ")"); //FIXME Check for valid scope
       } else {
         array_push($scopes, "scope:(" . $scope . ")");
       }
     }
   }
   //print_r($scopes);
   $scope_string = "&loc=" .implode(",", $scopes); //HACK b/c ex libris can't deal params consistently
   return $scope_string;
  }

  /*
  private function buildRemoteScope($remote_scope) {
    return "remote,adaptor," . $remote_scope;
  }
  */
 
  private function buildQueryString($query_params) {
    return http_build_query($query_params);
    /*
    $query_array = array();
    foreach( $query_params as $key => $key_value ){
      $query_array[] = $key . '=' . urlencode($key_value);
    }

    return implode( '&', $query_array );
     */  
  }

  private function isRemoteScope($scope) {
    if (in_array($scope, $this->available_remote_scopes)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  private function isValidScope($scope) {
    if (in_array($scope, $this->available_local_scopes)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  private function isValidQueryValue() {
    if(preg_match('/.+/', $this->query_value)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  private function isValidQuery() {
    if($this->isValidQueryValue() && $this->isValidIndexType() && $this->isValidPrecisionOperator()) {
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
  
