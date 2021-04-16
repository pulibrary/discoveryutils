<?php

namespace FAQ;
use \FAQ\Parser as FAQParser,
    \FAQ\Link as Link;

    /*
     * Response
     *
     * Response from a Query to the LibAnswers API
     */

    class Response
    {

      protected $records = array(); // Records attached to current response
      public $hits;
      public $qString;


      function __construct($faq_api_response = array() ) {
        $this->hits = 0;
        $this->more_link = new Link('https://faq.library.princeton.edu/');
        if(isset($faq_api_response['search']['numFound'])){
          $this->hits = $faq_api_response['search']['numFound'];
        }
        $this->records = Parser::convertToFaqRecords($faq_api_response);
        if(isset($faq_api_response['search']['query'])){
          $this->more_link = new Link($faq_api_response['search']['query']);
        }
      }


      public function getBriefResponse() {
        $brief_result_set = array();

        foreach($this->records as $record) {
          $brief_result = array(
            'url' => $record->url,
            'question' => trim($record->question),
            'group_id' => $record->g,
            'topics' => $record->topics,
          );
          array_push($brief_result_set, $brief_result);
        }

        return $brief_result_set;
      }

    }
