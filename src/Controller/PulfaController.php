<?php
namespace App\Controller;

use Pulfa\Pulfa, 
    Pulfa\Response as PulfaResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class PulfaController extends AbstractController
{
    public function page(LoggerInterface $logger, Request $request, $index_type)
    {
        $host = "https://findingaids.princeton.edu";
        $base = "/collections.xml";
        $num_records_brief_display = 3;
            

        if (empty($request->query->get('query'))) {
            return "No Query Supplied";
        }
        $query = $request->query->get('query');

        if($request->server->get('HTTP_REFERER')) { //should not be repeated moved out to utilities class
          $referer = $request->server->get('HTTP_REFERER');
        } else {
          $referer = "Direct Query";
        }
      
        if($request->query->get('number')) {
          $result_size = $request->query->get('number');
        } else {
          $result_size = $num_records_brief_display;
        }
      
      
        $pulfa = new \Pulfa\Pulfa($host, $base);
        $pulfa_response_data = $pulfa->query($query, 0, $result_size);
        $pulfa_response = new PulfaResponse($pulfa_response_data, $query);
        $brief_response = $pulfa_response->getBriefResponse();
        $brief_response['query'] = $query;
      
        $logger->info("Pulfa Query:" . $query . "\tREFERER:" . $referer);
        return new Response(json_encode($brief_response), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
  }
}