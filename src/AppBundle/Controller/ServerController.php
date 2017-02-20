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
use AppBundle\Form\Model\OpenVZAction;
use Symfony\Component\HttpFoundation\JsonResponse;

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
          throw $this->createNotFoundException('No server found');
      // Server does not belong to the user
      } elseif($user->getId() != $server->getUID()) {
          throw $this->createNotFoundException('Server does not belong to the user');
      }

      $template = $this->getDoctrine()
        ->getRepository('AppBundle:Template')
        ->findByType($server->getType());

      $ipv4 = $this->getDoctrine()
          ->getRepository('AppBundle:IP')
          ->findBySID($server->getId(), 4);

      $node = $this->getDoctrine()
        ->getRepository('AppBundle:Node')
        ->findByID($server->getNid());

      if ($server->getType() == "openvz") {
        $action = new OpenVZAction($server, $node, $template, $user, $request);
        $page = 'server/openvz.html.twig';
      }

        $form = $this->createFormBuilder($action)
                ->add('action', HiddenType::class, array('error_bubbling' => true))
                ->add('value', HiddenType::class, array('error_bubbling' => true))
                ->add('save', SubmitType::class)
                ->getForm();

        $form->handleRequest($request);

        // Check for submission and validate it using Validator.
        if ($form->isSubmitted()) {
          if ($form->isValid() && $this->getDoctrine()->getRepository('AppBundle:Log')->checkRateLimit($user->getId(), $settings["ratelimit_limit"], $settings["ratelimit_time"])) {
            // Update action entity
            $action = $form->getData();
            $result = $action->handle();

            $em = $this->getDoctrine()->getManager();
            $em->persist($result[1]);
            $em->flush();
            if ($result[0] != null) {
              $em = $this->getDoctrine()->getManager();
              $em->persist($result[0]);
              $em->flush();
              return new Response(1);
            } else {
              return new Response(0);
          }
        } else {
          return new Response(0);
        }

       } else {

        // Render the page, passing the information as the server variable.
        return $this->render($page, [
            'page_title' => 'Manage Server',
            'server' => $server,
            'form' => $form->createView(),
            'templates' => $template,
            'ipv4' => $ipv4,
            'settings' => $settings,
        ]);

      }

    }

    public function jsonAction($sid, UserInterface $user) {

      // Get the specific server using the entity repository.
      $server = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findByID($sid);

      // Server does not exist
      if (!$server) {
          throw $this->createNotFoundException('No server found');
      // Server does not belong to the user
      } elseif($user->getId() != $server->getUID()) {
          throw $this->createNotFoundException('Server does not belong to the user');
      }

      $node = $this->getDoctrine()
        ->getRepository('AppBundle:Node')
        ->findByID($server->getNid());

      $data = $server->getStatus($node);

      $response = new JsonResponse();
      $response->setData($data);

      return $response;

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
        throw $this->createNotFoundException('No active servers');
      }

      // Render page returning logs
      return $this->render('server/logs.html.twig', [
                            'page_title' => 'Event Log',
                            'events' => $logs,
                            'templates' => $templates,
                            'settings' => $settings,
                          ]);

  }

}
