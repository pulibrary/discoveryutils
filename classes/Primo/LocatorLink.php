<?php
namespace Primo;

/*
 * @Locatorlink
 * Uses Primo "deep" linking services to return a bookmarkable URL
 * sample http://searchit.princeton.edu/primo_library/libweb/action/dlDisplay.do?institution=PRN&vid=PRINCETON&docId={primo_id}
 * 
 * Sample locatoer link http://library.princeton.edu/catalogs/locator/PRODUCTION/index.php?loc=nec&id=dedupmrg39805310
 * 
 */

class LocatorLink {
  private $source_id;
  private $location_code;
  private $locator_link_base = "/searchit/map";
  private $link;
  
  public function __construct($id,$location_code) {
    $this->source_id = $id;
    $this->location_code = $location_code;
    $this->link = $this->buildLink();
  }
  
  public function getLink() {
    return $this->link;
  }
  
  private function buildLink() {
    return $this->locator_link_base . "?" . "loc=" . $this->location_code . "&id=" . $this->source_id;
  }
  
} 
