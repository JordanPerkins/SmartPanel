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
    public function viewAction($type = null, UserInterface $user)
    {

      $actions = [
        'boot' => 'Start VM',
        'shutdown' => 'Shutdown VM',
        'restart' => 'Reboot VM',
        'hostname' => 'Set Hostname',
        'nameserver' => 'Set Nameservers',
        'password' => 'Change Root Password',
        'reinstall' => 'Reinstall OS',
        'resize' => 'Resize Disk',
      ];

      $settings = $this->get('app.settings')->get();

      $page = 'server/logs.html.twig';

      // Is Event Log
      if ($type == null) {

        // Get logs using the entity repository using the active user's ID
        $logs = $this->getDoctrine()->getRepository('AppBundle:Log')->findAllByID($user->getId());

        $title = "Event Log";
        $error = false;

      } else if ($user->getIsAdmin()) {
        $error = true;
        if ($type == "client") {
          $logs = $this->getDoctrine()->getRepository('AppBundle:Log')->findAll();
          $title = "Client Log";
        } else if ($type == "admin") {
          $logs = $this->getDoctrine()->getRepository('AppBundle:AdminLog')->findAll();
          $title = "Admin Log";
        } else if ($type == "auth") {
          $logs = $this->getDoctrine()->getRepository('AppBundle:AuthenticationLog')->findAll();
          $title = "Authentication Log";
          $page = 'admin/authlog.html.twig';
        }
      } else {
        return new RedirectResponse('/');
      }

      if ($type != "auth") {
        foreach ($logs as $log) {
          if ($log->getAction() == "reinstall") {
            $os = $this->getDoctrine()->getRepository('AppBundle:Template')->findByFile($log->getValue());
            if ($os != null) {
              $log->setValue($os->getName());
            }
          }
          $log->setAction($actions[$log->getAction()]);
        }
      }

      return $this->render($page, ['page_title' => $title, 'logs' => $logs, 'settings' => $settings, 'error' => $error]);

    }

  }
