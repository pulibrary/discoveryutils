<?php

namespace Primo\Requests;

use Primo\Record as Record;
use Primo\Holdings\Archives as Holding;
use Primo\Items\Archives as Item;

Class Aeon
{
  
  /* Requests must supply the following parameters
   * 
   * # Required Parameters
   * @site Library Code (Marquand, RBSC, etc.) from holding
   * @location Library Code Again?? from holding
   * @sublocation - Shelf Location Code from holdings
   * @callnumber - Call Number from holdings
   * @itemtitle - title from bib
   * @restrictions - item restrictions on access from holdings
   * 
   * #Optional Parameters
   * @itemvolume - item enumeration
   * @itemnumber - barcode or other ID
   * @referencenumber - Bib Number/MFHD - Would PNX Work? from bib
   * @itemauthor - Author from bib
   * @itemdate - Pubdate from bib
   * @itemcitation - Pub Information from bib
   * @collectionextent - 300 $a - is this even relevant?
   * @itemedition - Maybe important for finding Aids from holdings?
   */
   
   public static function createRequest(Record $record, Holding $holding, Item $item) {
     $request_params = array();
     return $request_params;
   }
   
   private function getBibData($record) {
     $bibdata = array();
     return $bibdata;
   }
   
   private function getHoldingsData($holding) {
     $holdingdata = array();
     return $holdingdata;
   }
  
  private function getItemData($item) {
    $itemdata = array();
    return $itemdata;
  }
}
