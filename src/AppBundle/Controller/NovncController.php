<?php

/* The controller used for the index, profile and change password pages.
   Created by Jordan Perkins. */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;

class NovncController extends Controller
{
    public function viewAction(UserInterface $user, Request $request)
    {

      // Render the page
      return $this->render('novnc/novnc.html.twig', [
                            ]);
    }

    public function requestAction($sid, UserInterface $user, Request $request)
    {

      // Get the specific server using the entity repository.
      $server = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findByID($sid);

      $settings = $this->get('app.settings')->get();

      // Server does not exist
      if (!$server) {
          return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Server not found', 'settings' => $settings]);
      // Server does not belong to the user
      } elseif($user->getId() != $server->getUID()) {
          return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Server does not belong to the user', 'settings' => $settings]);
      }

      $node = $this->getDoctrine()
        ->getRepository('AppBundle:Node')
        ->findByID($server->getNid());

      $hash = $this->getParameter('secret_hash');

      $path = "nodes/".$node->getIdentifier()."/".$server->getType()."/".$server->getCtid();
      $result = $node->command("create", $path."/vncproxy", $hash, ['websocket' => 1]);

      if ($result[0]) {
        $data = $result[1];
        $data["path"] = "api2/json/".$path."/vncwebsocket";
        $data["host"] = $node->getIp();
        $data["node_port"] = $node->getPort();

        $response = new JsonResponse();
        $response->setData($data);

        return $response;
      } else {
        return 0;
      }

    }

}
