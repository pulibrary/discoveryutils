<?php
namespace App\Controller;

use Guides\Guides,
    Guides\Response as GuidesResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class GuidesController extends BaseController
{
    protected function config_data()
    {
        return array(
          'host' => "https://lgapi.libapps.com",
          'base' => "/1.1/guides",
          'num.records.brief.display' => 3,
          'site_id' => '77',
          'key' => $_ENV['LIB_GUIDES_KEY'],
          'status' => '1',
          'external_link_base' => 'http://libguides.princeton.edu/srch.php?',
        );
    }

    protected function gather_data( Request $request, $index_type, $query)
    {
      
        $num_records_brief_display = 3;
        if($request->query->get('number')) {//should not be repeated moved out to utilities class
          $result_size = $request->query->get('number');
        } else {
          $result_size = $num_records_brief_display;
        }

        $qString = array();
      
        $guides = new \Guides\Guides($this->config_data());
        $guides_response_data = $guides->query($query, 0, $qString);
        $guides_response = new GuidesResponse($guides_response_data, $query);
      
        $response_data = array(
             'query' => $guides_response->query,
             'number' => count($guides_response->getBriefResponse()),
             'more' => $guides_response->more_link,
             'records' => $guides_response->getBriefResponse(),
           );
      
        return new Response(json_encode($response_data), 200, 
                            array('Content-Type' => 'application/json', 
                                  'Cache-Control' => 's-maxage=3600', 'public'));
  }
}
