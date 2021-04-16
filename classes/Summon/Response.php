<?php

namespace Summon;
use Summon\Parser;

/*
 * Response
 *
 * Response from a Query to the Summon API
 */

class Response
{

  protected $records = array(); // Records attached to current response
  protected $db_recommendations = array(); //recommended databases
  public $hits;
  public $queryString;
  public $deep_search_link;

  function __construct($summon_api_response = array() ) {
    $this->hits = $summon_api_response['recordCount'];
    $this->queryString = $summon_api_response['query']['queryString'];
    $this->query_details = $summon_api_response['query'];
    $this->records = Parser::convertToSummonRecords($summon_api_response);
    $this->db_recommendations = Parser::getDatabaseRecommendations($summon_api_response);
    $this->buildDeepSearchLink();
  }

  public function getBriefResults() {
    $brief_result_set = array();
          //print_r($this->records);
    foreach($this->records as $record) {
      $publication_title = "";
      if(isset($record->PublicationTitle)) {
         $publication_title = $record->PublicationTitle[0];
      }
      $abstract = "";
      if(isset($record->Abstract)) {
         $abstract = $record->Abstract[0];
      }

      $brief_result = array(
        'url' => $record->link,
        'title' => trim($this->handleNull($record->Title[0])),
        'holdings' => $record->hasFullText,
        'is_full_text' => $record->isFullTextHit,
        'in_holdings' => $record->inHoldings,
        'format' => $this->handleNull($record->ContentType),
        'abstract' => htmlspecialchars($abstract), //FIXME should probably do this
        'fulltextavail' => $record->hasFullText,
        'publication_date' => $this->handleNull($record->PublicationDate),
        'snippet' => $record->Snippet,
        'publication_title' => $publication_title,
        'publication_year' => $this->handleNull($record->PublicationYear),
        'formatted_pub_date' => $record->getFormattedDate(),
        'author' => $record->getFormattedAuthor(),
        'isxn' => $record->getISXN(),
      );
      array_push($brief_result_set, $brief_result);
    }

    return $brief_result_set;
  }

  public function getRecommendations() {
    if(count($this->db_recommendations) > 0) {
      return $this->db_recommendations;
    } else {
      return array();
    }
  }

  public function buildDeepSearchLink() {
    $this->deep_search_link = $this->queryString;
  }

  public function handleNull($a) {
    if(is_null($a)){
      return null;
    } else {
      return $a[0];
    }
  }

}
