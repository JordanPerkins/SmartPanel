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

    public function listAction(Request $request, UserInterface $user)
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

}
