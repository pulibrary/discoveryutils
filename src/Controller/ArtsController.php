<?php
namespace App\Controller;

use Arts\Query,
    Arts\Link,
    Arts\Response as ArtsResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ArtsController extends BaseController
{
    protected function config_data()
    {
      return array(
        'host' => "https://data.artmuseum.princeton.edu",
        'base' => "/search",
      );
    }

    protected function gather_data( Request $request, $index_type, $query)
    {   
        $num_records_brief_display = 3;
        $external_link_base = 'https://artmuseum.princeton.edu';

        $query_runner = new Query($this->config_data());
        $arts_json_data = $query_runner->query($query, $index_type);
        $arts_response  = ArtsResponse::getResponse($arts_json_data, $external_link_base);
        $more_link = 'https://artmuseum.princeton.edu/search/collections/beta?query='.$query;
      
        $response_data = array(
             'query' => $query,
             'number' => $arts_response["number"],
             'more' => $more_link,
             'records' => $arts_response["records"],
           );
        return new Response(json_encode($response_data), 200, 
                            array('Content-Type' => 'application/json', 
                                  'Cache-Control' => 's-maxage=3600', 'public'));
  }
}
