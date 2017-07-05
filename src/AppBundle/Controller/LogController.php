<?php

/* The controller used for the index, profile and change password pages.
   Created by Jordan Perkins. */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\User;

class LogController extends Controller
{
    // Applies to the /admin/clients route.
    public function viewAction(UserInterface $user, Request $request)
    {

      // User is not admin, redirect to dashboard.
      if (!$user->getIsAdmin()) {
        return new RedirectResponse('/');
      }

      $settings = $this->get('app.settings')->get();

      $logs = $this->getDoctrine()
        ->getRepository('AppBundle:AuthenticationLog')
        ->findAll();

      // Render the page, passing on the user list for displaying.
      return $this->render('admin/log.html.twig', [
                            'page_title' => 'Authentication Log',
                            'logs' => $logs,
                            'settings' => $settings,
                            ]);
    }
  }
