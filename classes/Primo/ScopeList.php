<?php

namespace Primo;
use Primo\Scope as Scope;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\Yaml\Dumper as YamlDumper;

class ScopeList
{

  private $crawler;
    
  function __construct($scope_response) {
    
    $this->crawler = new DomCrawler($scope_response);
    
  }
  
  public function getScopes() {
    $scopes = $this->crawler->filter('Scopes Scope')->each(function ($node, $i) {
      $scope = new Scope($node);  
      return $scope->getScopeValues();
    });
  
    return $scopes;
      
  }
  
  public function asYaml() {
      
    $scopes = $this->getScopes();
    $sorted_scopes = array();
    foreach ($scopes as $scope) {
      
    }
    $yaml_dumper = new YamlDumper();
    return $yaml_dumper->dump($scopes,2);
    
  }
  
   
    
}
