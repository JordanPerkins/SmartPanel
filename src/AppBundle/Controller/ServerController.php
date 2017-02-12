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
use AppBundle\Entity\Log;
use AppBundle\Form\Model\Action;
use Symfony\Component\HttpFoundation\JsonResponse;

class ServerController extends Controller
{

    // Used on route /servers to display user's virtual servers
    public function listAction(UserInterface $user)
    {

      // Get servers using the entity repository using the active user's ID
      $servers = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findAllByUID($user->getId());

      // No active servers
      if (!$servers) {
        throw $this->createNotFoundException('No active servers');
      }

      // Render page returning server list as the servers variable
      return $this->render('server/list.html.twig', [
                            'page_title' => 'My Servers',
                            'servers' => $servers
                          ]);
    }

    // Used on route /servers/<id> to view a specific server.
    public function viewAction($page, UserInterface $user, Request $request)
    {

      // Get the specific server using the entity repository.
      $server = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findByID($page);

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

      $node = $this->getDoctrine()
        ->getRepository('AppBundle:Node')
        ->findByID($server->getNid());

        $action = new Action();
        $result = false;

        $form = $this->createFormBuilder($action)
                ->add('action', HiddenType::class, array('error_bubbling' => true))
                ->add('value', HiddenType::class, array('error_bubbling' => true))
                ->add('save', SubmitType::class)
                ->getForm();

        $form->handleRequest($request);

        // Check for submission and validate it using Validator.
        if ($form->isSubmitted()) {
          if ($form->isValid()) {
            // Update action entity
            $action = $form->getData();
            $result = $action->handle($server, $node);
            //Add to event log
            if ($action->getAction() == "password") {
              $log = new Log($action->getAction(), new \DateTime("now"), $request->getClientIp(), null, $server->getId(), $user->getId(), $result);
            } else {
                $log = new Log($action->getAction(), new \DateTime("now"), $request->getClientIp(), $action->getValue(), $server->getId(), $user->getId(), $result);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($log);
            $em->flush();
            if ($result) {
              if ($action->getAction() == "hostname") {
                $server->setHostname($action->getValue());
                $em = $this->getDoctrine()->getManager();
                $em->persist($server);
                $em->flush();
              }
              if ($action->getAction() == "reinstall") {
                $os = $this->getDoctrine()
                  ->getRepository('AppBundle:Template')
                  ->findByFile($action->getValue());
                $server->setOs($os->getName());
                $em = $this->getDoctrine()->getManager();
                $em->persist($server);
                $em->flush();
              }
              if ($action->getAction() == "tuntap_enable" || $action->getAction() == "tuntap_disable" || $action->getAction() == "fuse_enable" || $action->getAction() == "fuse_disable") {
                $info = explode('_', $action->getAction());
                $method = 'set'.ucfirst($info[0]);
                if ($info[1] == "enable") {
                  $server->$method(true);
                } else {
                  $server->$method(false);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($server);
                $em->flush();
              }
            }
          } else {
            $result = 0;
          }

         return new Response($result);

       } else {

        // Render the page, passing the information as the server variable.
        return $this->render('server/server.html.twig', [
            'page_title' => 'Manage Server',
            'server' => $server,
            'form' => $form->createView(),
            'templates' => $template,
        ]);

      }

    }

    public function jsonAction($page, UserInterface $user) {

      // Get the specific server using the entity repository.
      $server = $this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findByID($page);

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
                            'templates' => $templates
                          ]);

  }

}
