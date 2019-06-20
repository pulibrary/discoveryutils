<?php
namespace App\Controller;
use Hours\Day as Day;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HoursRbscController extends AbstractController
{
    public function page()
    {
        $hours_base =  $this->getParameter('library.base.url');
        $hours_daily = 'hours';

        $day_client = new Day($hours_base, $hours_daily );
        $daily_hours = $day_client->getDailyHoursByLocation();
        return new Response(json_encode($daily_hours), 200, array('Content-Type' => 'application/json'));
    }
}