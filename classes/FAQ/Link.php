<?php

namespace FAQ;

class Link
{

  private $faq_base = "http://faq.library.princeton.edu/search/?";
  //private $faq_params = array();

  function __construct() {
    //$qString['q'] = $search_terms;
    //$this->faq_params = $qString;
  }

  public function getLink($qString, $search_terms) {
    $qString['q'] = $search_terms;
    $qString['g'] = $qString['group_id']; // LibAnswers API is not consistent with their search page
    unset($qString['group_id']);

    //$this->faq_params = $qString;
    return $this->faq_base . urldecode(http_build_query($qString));
  }

}
