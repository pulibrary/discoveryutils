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
  
  /* return a yaml structure than can be written to a conf file
   * 
   */
  public function asYaml() {
      
    $scopes = $this->getScopes();
    $scope_values = array();
    foreach ($scopes as $scope) {
      $scope_values[$scope['name']] = array(
        'name' => $scope['label'],
        'param' => $scope['param'],
        );
    }
    ksort($scope_values);

    $yaml_dumper = new YamlDumper();
    return $yaml_dumper->dump($scope_values,2);
    
  }
  
   
    
}
