<?php
namespace App\Controller;

use FAQ\FAQ,
    FAQ\Response as FAQResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class FaqController extends BaseController
{
    protected function gather_data( Request $request, $index_type, $query)
    {
        $host = "https://api2.libanswers.com";
        $base = "/1.0/search";
        $num_records_brief_display = 3;
    

        $qString = array();
        if($request->query->get('group_id')) {
          $qString['group_id'] = htmlspecialchars($request->query->get('group_id'));
        }
    
        if($request->query->get('topics')) {
          $qString['topics'] = htmlspecialchars($request->query->get('topics'));
        }
    
        if($request->query->get('sort')) {
          $qString['sort'] = htmlspecialchars($request->query->get('sort'));
        }
    
        if($request->query->get('sort_dir')) {
          $qString['sort_dir'] = htmlspecialchars($request->query->get('sort_dir'));
        }
    
        if($request->query->get('page')) {
          $qString['page'] = htmlspecialchars($request->query->get('page'));
        }
    
        if($request->query->get('callback')) {
          $qString['callback'] = htmlspecialchars($request->query->get('callback'));
        }
    
        if($request->query->get('limit')) {
          $qString['limit'] = $request->query->get('limit');
        } else {
          $qString['limit'] = $num_records_brief_display;
        }
 
        $faq = new \FAQ\FAQ($host, $base);
        $faq_response_data = $faq->query($query, 0, $qString);
    
        $faq_response = new FAQResponse($faq_response_data, $query);
    
        $response_data = array(
          'query' => $query,
          'number' => $faq_response->hits,
          'more' => $faq_response->more_link->getLink($qString, $query),
          'records' => $faq_response->getBriefResponse(),
        );
    
        $response = new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
    
        return $response;
    }
}
