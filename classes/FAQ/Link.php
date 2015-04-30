<?php

namespace FAQ;

class Link
{

  private $faq_base = "http://faq.library.princeton.edu/search/?";

  function __construct() {

  }

  public function getLink($qString, $search_terms) {
    $qString['q'] = $search_terms;
    $qString['g'] = $qString['group_id']; // LibAnswers API is not consistent with their search page
    unset($qString['group_id']);

    return $this->faq_base . urldecode(http_build_query($qString));
  }

}
