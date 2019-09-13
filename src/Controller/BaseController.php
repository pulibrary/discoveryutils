<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

abstract class BaseController extends AbstractController
{

    abstract protected function gather_data( Request $request, $index_type, $query);

    public function page(LoggerInterface $logger, Request $request, $index_type)
    {
        
        if (empty($request->query->get('query'))) {
            return new Response("No Query Supplied");
        }
        $query = htmlspecialchars($request->query->get('query'));

        if($request->server->get('HTTP_REFERER')) { 
          $referer = $request->server->get('HTTP_REFERER');
        } else {
          $referer = "Direct Query";
        }

        $response_data =  $this->gather_data($request, $index_type, $query);
        
        $logger->info( get_class($this) . ':' . $query . "\tREFERER:" . $referer);

        $response_data->headers->set('Access-Control-Allow-Origin', "*");
        $response_data->headers->set("Access-Control-Allow-Headers","Content-Type, x-requested-with");
        return $response_data;
    }
}
