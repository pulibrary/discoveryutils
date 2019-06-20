<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function homepage()
    {
        $env = $this->getParameter('kernel.environment');
        $title = $this->getParameter('application.title');
        return $this->render('home.html.twig', array(
          'environment' => $env,
          'title' => $title
        ));
    }
}