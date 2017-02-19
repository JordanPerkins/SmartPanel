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
use AppBundle\Entity\Server;

class DefaultController extends Controller
{
    // Applies to the / route
    public function indexAction(Request $request, UserInterface $user)
    {

      $settings = $this->get('app.settings')->get();

      // Uses the Server entity to fetch server count for display on dashboard.
      $server_count = count($this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findAllByUID($user->getId()));

        // Render the page, passing on that information.
      return $this->render('default/index.html.twig', [
                            'page_title' => 'Dashboard',
                            'server_count' => $server_count,
                            'settings' => $settings,
                            ]);
    }

    // Applies to the /profile route
    public function profileAction(Request $request, UserInterface $user)
    {

      $settings = $this->get('app.settings')->get();

      // Render the form. Error bubbling enabled so errors display at the top, not by the input.
      $form = $this->createFormBuilder($user)
          ->add('firstname', TextType::class, array('error_bubbling' => true))
          ->add('surname', TextType::class, array('error_bubbling' => true))
          ->add('email', EmailType::class, array('error_bubbling' => true))
          ->add('save', SubmitType::class, array('label' => 'Save Profile'))
          ->getForm();

      $form->handleRequest($request);

      // Check for submission and validate it using Validator.
      if ($form->isSubmitted() && $form->isValid()) {
        // Update user entity
        $user = $form->getData();

        // Write to database
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

      }

      // Render the page. A submitted variable is used for error handling.
      return $this->render('default/profile.html.twig', [
                          'page_title' => 'Profile',
                          'form' => $form->createView(),
                          'submitted' => $form->isSubmitted(),
                          'settings' => $settings
                          ]);

    }

    // Used by the /password route.
    public function passwordAction(Request $request, UserInterface $user)
    {

      $settings = $this->get('app.settings')->get();

      // Create a new Form Model object.
      $password = new Credentials();

      // Build the form, once again using error handling.
      $form = $this->createFormBuilder($password)
          ->add('password', PasswordType::class, array('error_bubbling' => true))
          ->add('newpassword', PasswordType::class, array('error_bubbling' => true))
          ->add('verifypassword', PasswordType::class, array('error_bubbling' => true))
          ->add('save', SubmitType::class, array('label' => 'Change Password'))
          ->getForm();

      $form->handleRequest($request);

      /* Validating the form is esepcially important.
       * It checks password strength, the current password and that they match. */
      if ($form->isSubmitted() && $form->isValid()) {

        // Get the new password and encrypt it.
        $password = $form->getData();
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password->getNewPassword());
        $user->setPassword($encoded);

        // Write to the database.
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

      }

      return $this->render('default/password.html.twig', [
                          'page_title' => 'Change Password',
                          'form' => $form->createView(),
                          'submitted' => $form->isSubmitted(),
                          'settings' => $settings
                          ]);

    }

}
