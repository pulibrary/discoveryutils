<?php
namespace App\Controller;

use Pudl\Pudl,
    Pudl\Response as PudlResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class PudlController extends AbstractController
{
    public function page(LoggerInterface $logger, Request $request, $index_type)
    {
        $host = "http://pudl.princeton.edu";
        $base = "/pudl/Objects";
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
 
        if($request->query->get('format')) {
          $format = $request->query->get('format');
        } else {
          $format = "json";
        }
      
      
        $pudl = new \Pudl\Pudl($host, $base);
        $pudl_response_data = $pudl->query($query);
      
        $pudl_response = new PudlResponse($pudl_response_data, $query);
        $brief_response = $pudl_response->getBriefResponse();
      
        $logger->info("Pudl Query:" . $query . "\tREFERER:" . $referer);
        if($format == "html") {
          return $this->render('pudlbrief.html.twig', array(
                                'environment' => $this->getParameter('kernel.environment'),
                                'title' => $this->getParameter('application.title'),
                                'query' => $query,
                                'more' => $brief_response['more'],
                                'number' => $brief_response['number'],
                                'records' => $brief_response['records'],
                              ));
        } else {
          return new Response(json_encode($brief_response), 200, array(
            'charset' => 'utf-8',
            'Content-Type' => 'application/json',
            'Cache-Control' => 's-maxage=3600, public'
            )
          );
        }
  }
}
