<?php
namespace App\Controller;

use FAQ\FAQ,
    FAQ\Response as FAQResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class FaqController extends AbstractController
{
    public function page(LoggerInterface $logger, Request $request, $index_type)
    {
        $host = "https://api2.libanswers.com";
        $base = "/1.0/search";
        $num_records_brief_display = 3;
    

        $query = $index_type;
        $qString = array();
        if($request->query->get('group_id')) {
          $qString['group_id'] = $request->query->get('group_id');
        }
    
        if($request->query->get('topics')) {
          $qString['topics'] = $request->query->get('topics');
        }
    
        if($request->query->get('sort')) {
          $qString['sort'] = $request->query->get('sort');
        }
    
        if($request->query->get('sort_dir')) {
          $qString['sort_dir'] = $request->query->get('sort_dir');
        }
    
        if($request->query->get('page')) {
          $qString['page'] = $request->query->get('page');
        }
    
        if($request->query->get('callback')) {
          $qString['callback'] = $request->query->get('callback');
        }
    
        if($request->query->get('limit')) {
          $qString['limit'] = $request->query->get('limit');
        } else {
          $qString['limit'] = $num_records_brief_display;
        }
 
        if($request->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
          $referer = $request->server->get('HTTP_REFERER');
        } else {
          $referer = "Direct Query";
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
    
        $logger->info("FAQ Query:" . $query . "\tREFERER:" . $referer);
    
        $response = new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
        $response->headers->set('Access-Control-Allow-Origin', "*");
        $response->headers->set("Access-Control-Allow-Headers","Content-Type");
    
        return $response;
    }
}
