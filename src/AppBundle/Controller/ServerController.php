<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Server;

class ServerController extends Controller
{

    public function listAction(UserInterface $user)
    {

      $servers = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findAllByUID($user->getId());

        if (!$servers) {
        throw $this->createNotFoundException(
            'No active servers'
        );

        }
        // replace this example code with whatever you need
        return $this->render('server/list.html.twig', [
            'page_title' => 'My Servers',
            'servers' => $servers
        ]);
    }

    public function viewAction($page, UserInterface $user)
    {

      $server = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findByID($page);

        if (!$server) {
          throw $this->createNotFoundException(
            'No server found'
          );
        } elseif($user->getId() != $server->getUID()) {
          throw $this->createNotFoundException(
            'Server does not belong to the user'
          );
        }
        // replace this example code with whatever you need
        return $this->render('server/server.html.twig', [
            'page_title' => 'Manage Server',
            'server' => $server
        ]);
    }

}
