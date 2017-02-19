<?php

/* The controller used for the index, profile and change password pages.
   Created by Jordan Perkins. */
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
use AppBundle\Form\Model\Credentials;
use AppBundle\Entity\User;

class ClientsController extends Controller
{
    // Applies to the /admin/clients route.
    public function listAction(UserInterface $user)
    {

      $settings = $this->get('app.settings')->get();

      // Fetch all users.
      $users = $this->getDoctrine()
        ->getRepository('AppBundle:User')
        ->findAll();

        // Render the page, passing on the user list for displaying.
      return $this->render('admin/clients.html.twig', [
                            'page_title' => 'List Clients',
                            'clients' => $users,
                            'settings' => $settings,
                            ]);
    }

  }
