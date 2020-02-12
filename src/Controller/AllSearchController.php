<?php
namespace App\Controller;
use Utilities\CoreSearchLink as CoreSearchLink;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Psr\Log\LoggerInterface;

class AllSearchController extends AbstractController
{
    public function page(LoggerInterface $logger, Request $request, $index_type)
    {
        $query = htmlspecialchars($request->query->get('query'));
        $all_search_host = 'https://library.princeton.edu';
        $deep_search_link = new CoreSearchLink($all_search_host, 'find/all', $query);
        return $this->redirect($deep_search_link->getLink());
  }
}