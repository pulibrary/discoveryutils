<?php
namespace App\Controller;

use Summon\Summon,
    Summon\Query as SummonQuery,
    Summon\Response as SummonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ArticlesController extends BaseController
{
    protected function config_data()
    {}

      protected function gather_data( Request $request, $index_type, $query)
      //public function page(LoggerInterface $logger, Request $request, $index_type)
      {
        $client_id = 'princeton';
        $base_url = 'https://princeton.summon.serialssolutions.com/search?';
        $num_records_brief_display = 5;
        $authcode = $_ENV['SUMMON_AUTHCODE'];
            
      
        if($request->query->get('number')) {
          $result_size = $request->query->get('number');
        } else {
          $result_size = $num_records_brief_display;
        }
      
      
        $summon_client = new Summon($client_id, $authcode);
        $summon_client->limitToHoldings(); // only bring back Princeton results
      
        if($index_type == 'guide') {
          $summon_client->addFilter('ContentType, Research Guide');
          $summon_data = new SummonResponse($summon_client->query($query, 1, 3));
          $summon_full_search_link = new SummonQuery($query, array(
            "s.fvf" => 'ContentType,Research Guide',
            "keep_r" => "true",
            "s.dym" => "t",
            "s.ho" => "t"
          ));
          $response_data = array(
            'query' => htmlspecialchars($query),
            'number' => $summon_data->hits,
            'more' => $summon_full_search_link->getLink(),
            'records' => $summon_data->getBriefResults(),
          );
        } elseif ($index_type == "spelling") {
          if($summon_client->checkSpelling($query, 1, 1)) {
            $suggestion = $summon_client->checkSpelling($query, 1, 1);
          }
          if(isset($suggestion)) {
            $response_data = array($suggestion);
          } else {
            $response_data = array();
          }
        } elseif($index_type == "recommendations") {
          $summon_data = new SummonResponse($summon_client->query($query, 1, 3));
          $response_data['recommendations'] = $summon_data->getRecommendations();
          $response_data['number'] = count($response_data['recommendations']);
        } else {
          $summon_client->addCommandFilter("addFacetValueFilters(ContentType,Newspaper+Article:t,Book+Review:t)");
          //$summon_client->addFilter("IsScholarly,true");
          $summon_data = new SummonResponse($summon_client->query($query, 1, $result_size));
          //print_r($summon_data->query_details);
          $summon_full_search_link = new SummonQuery($query, array(
            //"s.cmd" => "addFacetValueFilters(ContentType,Newspaper+Article,t,Book+Review:t)",
            //"s.fvf" => "IsScholarly,true",
            "fvf" => "ContentType,Newspaper+Article,t",
            "keep_r" => "true",
            "s.dym" => "t",
            "s.ho" => "t"
            )
          );
          $response_data = array(
            'query' => htmlspecialchars($query),
            'number' => $summon_data->hits,
            'more' => $summon_full_search_link->getLink(),
            'records' => $summon_data->getBriefResults(),
          );
        }
      
        return new Response(json_encode($response_data), 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
  }
}
