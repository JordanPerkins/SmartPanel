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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormError;
use AppBundle\Entity\Plan;

class PlansController extends Controller
{
    // Applies to the /admin/clients route.
    public function listAction(UserInterface $user, Request $request)
    {

      // User is not admin, redirect to dashboard.
      if (!$user->getIsAdmin()) {
        return new RedirectResponse('/');
      }

      $settings = $this->get('app.settings')->get();

        // Render the form to be used. Takes input of id only.
        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, array('error_bubbling' => true))
            ->add('save', SubmitType::class, array('label' => 'Reset'))
            ->getForm();

        $form->handleRequest($request);

        // Check for submissions
        if ($form->isSubmitted() && $form->isValid() && $user->getIsAdmin()) {

          // Get information on the user with the id submitted.
          $usr = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findByID($form->get('id')->getData());

          // User does not exist
          if (!$usr) {
            $form->get('id')->addError(new FormError("User was not found."));
          } else if ($this->getDoctrine()->getRepository('AppBundle:Server')->countByID($usr->getId()) > 0) {
            $form->get('id')->addError(new FormError("This client has active servers."));
          } else {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($usr);
            $em->flush();
          }

        }

        // Fetch all users.
        $users = $this->getDoctrine()
          ->getRepository('AppBundle:User')
          ->findAll();

      // Render the page, passing on the user list for displaying.
      return $this->render('admin/clients.html.twig', [
                            'page_title' => 'List Clients',
                            'clients' => $users,
                            'settings' => $settings,
                            'form' => $form->createView(),
                            'submitted' => $form->isSubmitted(),
                            ]);
    }

    public function newAction(UserInterface $user, Request $request, $type)
    {

        // User is not admin, redirect to dashboard.
        if (!$user->getIsAdmin()) {
          return new RedirectResponse('/');
        }

        $settings = $this->get('app.settings')->get();

        // Create new user
        $plan = new Plan();

        if ($type == "lxc") {
          // Render the form to be used.
          $form = $this->createFormBuilder($plan)
              ->add('name', TextType::class, array('error_bubbling' => true))
              ->add('type', HiddenType::class, array('error_bubbling' => true, 'data'  => 'lxc'))
              ->add('disk', TextType::class, array('error_bubbling' => true))
              ->add('ram', TextType::class, array('error_bubbling' => true))
              ->add('swap', TextType::class, array('error_bubbling' => true))
              ->add('console', CheckboxType::class, array('error_bubbling' => true, 'required' => false))
              ->add('cmode', ChoiceType::class, array('error_bubbling' => true, 'choices'  => array('/dev/tty' => 'tty', '/dev/console' => 'console', 'shell mode' => 'shell')))
              ->add('cpu', TextType::class, array('error_bubbling' => true))
              ->add('cpulimit', TextType::class, array('error_bubbling' => true))
              ->add('cpuunits', TextType::class, array('error_bubbling' => true))
              ->add('ipv4', TextType::class, array('error_bubbling' => true))
              ->add('ipv6', TextType::class, array('error_bubbling' => true))
              ->add('tty', TextType::class, array('error_bubbling' => true))
              ->add('unprivileged', CheckboxType::class, array('error_bubbling' => true, 'required' => false))
              ->add('onboot', CheckboxType::class, array('error_bubbling' => true, 'required' => false))
              ->add('storage', TextType::class, array('error_bubbling' => true))
              ->add('searchdomain', TextType::class, array('error_bubbling' => true))
              ->add('save', SubmitType::class, array('label' => 'Save'))
              ->getForm();
          }

          $form->handleRequest($request);

          // Check for submissions
          if ($form->isSubmitted() && $form->isValid() && $user->getIsAdmin()) {

            // Get the information and persist it to the DB.
            $plan = $form->getData();

            // Write to database
            $em = $this->getDoctrine()->getManager();
            $em->persist($plan);
            $em->flush();
         }

        return $this->render('admin/plannew.html.twig', [
                            'page_title' => 'New Plan',
                            'form' => $form->createView(),
                            'submitted' => $form->isSubmitted(),
                            'settings' => $settings,
                            ]);

    }

}
