<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use phpCAS;

class MusicArchiveController extends AbstractController
{
    public function page(Request $request, $course_id)
    {
        $env = $this->getParameter('kernel.environment');
        $title = $this->getParameter('application.title');
        $user = "";
        // PHP CAS incantations
        if(!($env == 'test')) {
          phpCAS::setDebug('cas.log');
          phpCAS::setVerbose(true);
          phpCAS::client(CAS_VERSION_2_0, $_ENV['CAS_URL'], 443, $_ENV['CAS_PATH'], false);
          if ($env == 'prod') {
            phpCAS::setCasServerCACert('/etc/ssl/certs/ssl-cert-snakeoil.pem');
          } else {
            phpCAS::setNoCasServerValidation();
          }
          $service_url = $_ENV['APP_URL'] . '/utils/musicarchive/' . $course_id;
          //phpCAS::getServiceURL();
          // if ($request->query->get('ticket')) {
          //   $request->query->set('ticket') == null;
          // }
          phpCAS::setFixedServiceURL($service_url);
          //phpCAS::setFixedServiceURL($service_url);
          //if (!phpCAS::checkAuthentication()) {
          phpCAS::forceAuthentication();
          //}
        }
        if($env == 'test') {
          $user = "";
        } else {
          $user = phpCAS::getUser();
        }


        return $this->render('redirect.html.twig', array(
          'environment' => $env,
          'title' => $title,
          'course_id' => $course_id,
          'user' => $user,
          'service_url' => $service_url
        ));
    }
}
