<?php
namespace App\Controller;

use Pulfa\Pulfa, 
    Pulfa\Response as PulfaResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PulfaController extends BaseController
{
    protected function gather_data( Request $request, $index_type, $query)
    {
        $host = "https://findingaids.princeton.edu";
        $base = "/collections.xml";
        $num_records_brief_display = 3;
            

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
      
        return new Response(json_encode($brief_response), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
  }
}