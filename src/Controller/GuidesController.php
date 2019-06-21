<?php
namespace App\Controller;

use Guides\Guides,
    Guides\Response as GuidesResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class GuidesController extends AbstractController
{
    public function page(LoggerInterface $logger, Request $request, $index_type)
    {
        $guide_vars = array(
          'host' => "https://lgapi.libapps.com",
          'base' => "/1.1/guides",
          'num.records.brief.display' => 3,
          'site_id' => '77',
          'key' => $_ENV['LIB_GUIDES_KEY'],
          'status' => '1',
          'external_link_base' => 'http://libguides.princeton.edu/srch.php?',
        );
      
        $host = "https://findingaids.princeton.edu";
        $base = "/collections.xml";
        $num_records_brief_display = 3;
            

        if (empty($request->query->get('query'))) {
            return "No Query Supplied";
        }
        $query = htmlspecialchars($request->query->get('query'));

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

        $qString = array();
      
        $guides = new \Guides\Guides($guide_vars);
        $guides_response_data = $guides->query($query, 0, $qString);
        $guides_response = new GuidesResponse($guides_response_data, $query);
      
        $response_data = array(
             'query' => $guides_response->query,
             'number' => count($guides_response->getBriefResponse()),
             'more' => $guides_response->more_link,
             'records' => $guides_response->getBriefResponse(),
           );
      
        $logger->info("Guides Query:" . $query . "\tREFERER:" . $referer);
        return new Response(json_encode($response_data), 200, 
                            array('Content-Type' => 'application/json', 
                                  'Cache-Control' => 's-maxage=3600', 'public'));
  }
}
