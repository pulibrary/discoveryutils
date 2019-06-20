<?php
namespace App\Controller;
use Hours\Hours as Hours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HoursController extends AbstractController
{
    public function page()
    {
        $hours_base =  $this->getParameter('library.base.url');
        $hours_locations = 'services/voyager/libraries.json';
        $hours_weekly = 'services/voyager/hours.json';

        $hours_client = new Hours($hours_base, $hours_locations, $hours_weekly);
        $response = new Response($this->renderView('locations.xml.twig', array(
            'libraries' => $hours_client->getCurrentHoursByLocation(),
            'base_url' => $hours_base,
            'cur_month' => $hours_client->getCurrentMonth(),
        )));
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
}