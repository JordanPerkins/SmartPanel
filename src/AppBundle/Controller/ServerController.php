<?php
/* The controller for the server pages
 * Created by Jordan Perkins
*/
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Server;
use AppBundle\Form\Model\LXCAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ServerController extends Controller
{

    // Used on route /servers to display user's virtual servers
    public function listAction(UserInterface $user)
    {

      $settings = $this->get('app.settings')->get();

      // Get servers using the entity repository using the active user's ID
      $servers = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findAllByUID($user->getId());

      // Render page returning server list as the servers variable
      return $this->render('server/list.html.twig', [
                            'page_title' => 'My Servers',
                            'settings' => $settings,
                            'servers' => $servers
                          ]);
    }

    // Used on route /servers/<id> to view a specific server.
    public function viewAction($sid, UserInterface $user, Request $request)
    {

      $settings = $this->get('app.settings')->get();

      // Get the specific server using the entity repository.
      $server = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findByID($sid);

      // Server does not exist
      if (!$server) {
          return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Server not found', 'settings' => $settings]);
      // Server does not belong to the user
    } elseif(!$user->getIsAdmin() && $user->getId() != $server->getUID()) {
          return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Server does not belong to you', 'settings' => $settings]);
      }

      $template = $this->getDoctrine()
        ->getRepository('AppBundle:Template')
        ->findByType($server->getType());

      $node = $this->getDoctrine()
        ->getRepository('AppBundle:Node')
        ->findByID($server->getNid());

      $hash = $this->getParameter('secret_hash');

      if ($server->getType() == "lxc") {
        $action = new LXCAction([$server, $node, $template, $user, $request], $hash);
        $page = 'server/lxc.html.twig';
      }

        $form = $this->createFormBuilder($action)
                ->add('action', HiddenType::class, array('error_bubbling' => true))
                ->add('value', HiddenType::class, array('error_bubbling' => true))
                ->add('save', SubmitType::class)
                ->getForm();

        $form->handleRequest($request);

        // Check for submission and validate it using Validator.
        if ($form->isSubmitted()) {
          if (!$form->isValid()) {
            return new Response("Data contains illegal characters");
          }
          if (!$this->getDoctrine()->getRepository('AppBundle:Log')->checkRateLimit($user->getId(), $settings["ratelimit_limit"], $settings["ratelimit_time"])) {
            return new Response("Rate limit exceeded");
          }
          if ($server->getSuspended()) {
            return new Response("Server is suspended");
          }
            // Update action entity
            $action = $form->getData();
            $result = $action->handle();

            $em = $this->getDoctrine()->getManager();

            if ($result[1] != null) {
              $em->persist($result[1]);
              $em->flush();
            }

            if ($result[0][0]) {
              $em->persist($server);
              $em->flush();
              return new Response(1);
            } else {
              if (is_array($result[0][1])) {
                return new Response(0);
              } else {
                return new Response($result[0][1]);
              }
          }

       } else {

        // Render the page, passing the information as the server variable.
        return $this->render($page, [
            'page_title' => 'Manage Server',
            'server' => $server,
            'form' => $form->createView(),
            'templates' => $template,
            'settings' => $settings,
        ]);

      }

    }

    public function jsonAction($sid, UserInterface $user, Request $request) {

      // Get the specific server using the entity repository.
      $server = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findByID($sid);

      $settings = $this->get('app.settings')->get();

      // Server does not exist
      if (!$server) {
          return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Server not found', 'settings' => $settings]);
      // Server does not belong to the user
    } elseif(!$user->getIsAdmin() &&  $user->getId() != $server->getUID()) {
          return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Server does not belong to you', 'settings' => $settings]);
      }

      $type = $request->query->get('type');

      if ($type == "ip") {

      $ipv4 = $this->getDoctrine()
            ->getRepository('AppBundle:IP')
            ->findBySID($server->getId(), 4);

      $data = array();
      foreach ($ipv4 as $ip) {
        $block = $this->getDoctrine()->getRepository('AppBundle:IPBlock')->findByID($ip->getBlock());
        $ip_data = [
          'ip' => $ip->getIp(),
          'interface' => $ip->getInterface(),
          'rdns' => $ip->getRdns(),
          'gateway' => $block->getGateway(),
          'netmask' => $block->getNetmask(),
        ];
        $data[] = $ip_data;
      }

      } elseif ($type == "status") {

        $node = $this->getDoctrine()
          ->getRepository('AppBundle:Node')
          ->findByID($server->getNid());

        $hash = $this->getParameter('secret_hash');

        if ($server->getType() == "lxc") {
          $action = new LXCAction([$server, $node, null, $user, $request], $hash, "status");
          $data = $action->handle()[0];
          if ($data[0] == false) {
            if ($data[1] == "Could not fetch status") {
              $data = "vm-missing";
            } else {
              $data = false;
            }
          }
        }

      }

      $response = new JsonResponse();
      $response->setData($data);

      return $response;

    }

    public function graphAction($sid, UserInterface $user, Request $request) {

      // Get the specific server using the entity repository.
      $server = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findByID($sid);

      $settings = $this->get('app.settings')->get();

      // Server does not exist
      if (!$server) {
          return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Server not found', 'settings' => $settings]);
      // Server does not belong to the user
    } elseif(!$user->getIsAdmin() && $user->getId() != $server->getUID()) {
          return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Server does not belong to you', 'settings' => $settings]);
      }

      $node = $this->getDoctrine()
        ->getRepository('AppBundle:Node')
        ->findByID($server->getNid());

      $type = $request->query->get('type');
      $period = $request->query->get('period');

      if ($type != "cpu" && $type != "netin" && $type != "netout" && $type != "mem" && $type != "disk") {
        return new Response(0);
      }
      if ($period != "hour" && $period != "day" && $period != "week" && $period != "month" && $period != "year") {
        return new Response(0);
      }

      $hash = $this->getParameter('secret_hash');

      if ($server->getType() == "lxc") {
        $action = new LXCAction([$server, $node, null, $user, $request], $hash, "graph", [$type, $period]);
        $data = $action->handle()[0];
      }

      $headers = array(
          'Content-Type'     => 'image/png',
        );

      return new Response($data, 200, $headers);

    }

    public function logAction(UserInterface $user)
    {

      $settings = $this->get('app.settings')->get();

      // Get logs using the entity repository using the active user's ID
      $logs = $this->getDoctrine()
        ->getRepository('AppBundle:Log')
        ->findAllByID($user->getId());

      $templates = $this->getDoctrine()
          ->getRepository('AppBundle:Template')
          ->findAll();

      // No logs found
      if (!$logs) {
        return $this->render('default/error.html.twig', ['page_title' => 'Error', 'error' => 'Event Log is empty', 'settings' => $settings]);
      }

      // Render page returning logs
      return $this->render('server/logs.html.twig', [
                            'page_title' => 'Event Log',
                            'events' => $logs,
                            'templates' => $templates,
                            'settings' => $settings,
                          ]);

  }

  // Used on route /admin/servers to display all servers
  public function listAllAction(UserInterface $user)
  {

    if (!$user->getIsAdmin()) {
      return new RedirectResponse('/');
    }

    $settings = $this->get('app.settings')->get();

    // Get servers using the entity repository using the active user's ID
    $servers = $this->getDoctrine()
      ->getRepository('AppBundle:Server')
      ->findAll();

    // Render page returning server list as the servers variable
    return $this->render('server/listall.html.twig', [
                          'page_title' => 'Virtual Servers',
                          'settings' => $settings,
                          'servers' => $servers
                        ]);
  }

}
