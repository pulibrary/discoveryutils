<?php
namespace PrimoServices;

/*
 * @Searchlink
 * Uses Primo "deep" linking services to return a bookmarkable URL for a primo basic search
 * 
 * sample http://searchit.princeton.edu/primo_library/libweb/action/dlSearch.do?institution=PRN&vid=PRINCETON&onCampus=false&indx=1&bulkSize=150&vl(freeText0)=dogs&vl(89332482UI0)=any&query=any,contains,dogs
 * 
 * onCampus must be present or an error comes 
 * 
 * scopes do not seem to work 
 */

class SearchLink 
{
  
  private $query;
  
  public function __construct($query) {
    $this->query = $query;
        
  }
}