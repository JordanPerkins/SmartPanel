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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Server;
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

      $node = $this->getDoctrine()
        ->getRepository('AppBundle:Node')
        ->findByID($server->getNid());

        $action = new Action();
        $result = false;

        $form = $this->createFormBuilder($action)
                ->add('action', HiddenType::class, array('error_bubbling' => true))
                ->add('value', TextType::class, array('error_bubbling' => true))
                ->add('save', SubmitType::class, array('label' => "Start Server"))
                ->getForm();

        $form->handleRequest($request);

        // Check for submission and validate it using Validator.
        if ($form->isSubmitted()) {
          if ($form->isValid()) {
            // Update action entity
            $action = $form->getData();
            $result = $action->handle($server, $node);
            if ($action->getAction() == "hostname") {
              $server->setHostname($action->getValue());
              $em = $this->getDoctrine()->getManager();
              $em->persist($server);
              $em->flush();
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

}
