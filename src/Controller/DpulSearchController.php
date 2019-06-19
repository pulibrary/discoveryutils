<?php
namespace App\Controller;

use Blacklight\Blacklight as Blacklight,
    Blacklight\DpulResponse as DpulResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DpulSearchController extends AbstractController
{
    public function page(Request $request, $index_type)
    {
        $dpul_host = "https://dpul.princeton.edu";

        if (empty($request->query->get('query'))) {
            return new Response("No Query Supplied");
        }
        $query = $request->query->get('query');

        if ($index_type == 'isbn') {
          $index_type = 'isbn';
        } elseif ($index_type == 'issn') {
          $index_type = 'issn';
        } elseif ($index_type == 'title') {
          $index_type= 'left_anchor';
        } else {
          $index_type = 'all_fields';
        }
        $client = new Blacklight($dpul_host, 'catalog');
        $response = $client->query($query, $index_type);
        $blacklight_response = DpulResponse::getResponse($response, $dpul_host);
        $blacklight_response["more"] = $dpul_host . "/catalog?" . "search_field=" . $index_type . "&q=" . urlencode($query) . "&utf8=%E2%9C%93";
        return new JSONResponse($blacklight_response, 200, array('Content-Type' => 'application/json', 'Cache-Control' => 's-maxage=3600, public'));
  }

}