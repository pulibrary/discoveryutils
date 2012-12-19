<?php

namespace Primo;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Scope 
{
    
  private $primo_scope_name;
  private $primo_scope_description;
  private $primo_scope_param;
  
  function __construct(\DOMElement $scope) {
    $this->crawler = new DomCrawler($scope);
    $this->buildScopeValues();
  }
  
  private function buildScopeValues() {
    $this->primo_scope_name = $this->crawler->filter('Scope Name')->text();
    $this->primo_scope_description = $this->crawler->filter('Scope Description')->text();
    $this->primo_scope_param = $this->crawler->filter('Scope searchLocParam')->text();
  }
   
  public function getScopeValues() {
     
    return array(
       'name' => $this->primo_scope_name,
       'label' => $this->primo_scope_description,
       'param' => $this->primo_scope_param,
    );
  }
  
}
