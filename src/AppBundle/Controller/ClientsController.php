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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormError;
use AppBundle\Form\Model\AdminPasswordChange;
use AppBundle\Entity\User;

class ClientsController extends Controller
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

    public function editAction($cid, UserInterface $user, Request $request)
    {

        // User is not admin, redirect to dashboard.
        if (!$user->getIsAdmin()) {
          return new RedirectResponse('/');
        }

        $settings = $this->get('app.settings')->get();

        // Fetch the user
        $usr = $this->getDoctrine()
          ->getRepository('AppBundle:User')
          ->findByID($cid);

        if (!$usr) {
          $form->get('email')->addError(new FormError("User was not found."));
        } else {

        $servers = $this->getDoctrine()
            ->getRepository('AppBundle:Server')
            ->findAllByUID($usr->getId());

          // Render the form to be used.
          $form = $this->createFormBuilder($usr)
              ->add('firstname', TextType::class, array('error_bubbling' => true))
              ->add('surname', TextType::class, array('error_bubbling' => true))
              ->add('email', EmailType::class, array('error_bubbling' => true))
              ->add('isActive', CheckboxType::class, array('error_bubbling' => true))
              ->add('isAdmin', CheckboxType::class, array('error_bubbling' => true))
              ->add('save', SubmitType::class, array('label' => 'Save'))
              ->getForm();

          $form->handleRequest($request);

          // Check for submissions
          if ($form->isSubmitted() && $form->isValid() && $user->getIsAdmin()) {

            // Get the information and persist it to the DB.
            $usr = $form->getData();

            // Write to database
            $em = $this->getDoctrine()->getManager();
            $em->persist($usr);
            $em->flush();

          }

        }

        return $this->render('admin/clientedit.html.twig', [
                            'page_title' => 'Edit Client',
                            'form' => $form->createView(),
                            'submitted' => $form->isSubmitted(),
                            'settings' => $settings,
                            'usr' => $usr,
                            'servers' => $servers,
                            ]);

    }

    public function passwordAction($cid, Request $request, UserInterface $user)
    {

      // User is not admin, redirect to dashboard.
      if (!$user->getIsAdmin()) {
        return new RedirectResponse('/');
      }

      $settings = $this->get('app.settings')->get();

      // Fetch the user
      $usr = $this->getDoctrine()
        ->getRepository('AppBundle:User')
        ->findByID($cid);

      // Create a new Form Model object.
      $password = new AdminPasswordChange();

      // Build the form, once again using error handling.
      $form = $this->createFormBuilder($password)
          ->add('newpassword', PasswordType::class, array('error_bubbling' => true))
          ->add('verifypassword', PasswordType::class, array('error_bubbling' => true))
          ->add('save', SubmitType::class, array('label' => 'Change Password'))
          ->getForm();

      $form->handleRequest($request);


      /* Validating the form is esepcially important.
       * It checks password strength, the current password and that they match. */
      if ($form->isSubmitted() && $form->isValid() && $user->getIsAdmin()) {

        // Get the new password and encrypt it.
        $password = $form->getData();
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($usr, $password->getNewPassword());
        $user->setPassword($usr);

        // Write to the database.
        $em = $this->getDoctrine()->getManager();
        $em->persist($usr);
        $em->flush();

      }

      return $this->render('admin/clientpassword.html.twig', [
                          'page_title' => 'Change Password',
                          'form' => $form->createView(),
                          'submitted' => $form->isSubmitted(),
                          'settings' => $settings,
                          'usr' => $usr->getId()
                          ]);

    }

    public function newAction(UserInterface $user, Request $request)
    {

        // User is not admin, redirect to dashboard.
        if (!$user->getIsAdmin()) {
          return new RedirectResponse('/');
        }

        $settings = $this->get('app.settings')->get();

        // Create new user
        $usr = new User();

          // Render the form to be used.
          $form = $this->createFormBuilder($usr)
              ->add('username', TextType::class, array('error_bubbling' => true))
              ->add('firstname', TextType::class, array('error_bubbling' => true))
              ->add('surname', TextType::class, array('error_bubbling' => true))
              ->add('email', EmailType::class, array('error_bubbling' => true))
              ->add('password', PasswordType::class, array('error_bubbling' => true))
              ->add('isActive', CheckboxType::class, array('error_bubbling' => true))
              ->add('isAdmin', CheckboxType::class, array('error_bubbling' => true))
              ->add('save', SubmitType::class, array('label' => 'Save'))
              ->getForm();

          $form->handleRequest($request);

          // Check for submissions
          if ($form->isSubmitted() && $form->isValid() && $user->getIsAdmin()) {

            // Get the information and persist it to the DB.
            $usr = $form->getData();

            if (!preg_match('@[A-Z]@', $usr->getPassword())) {
              $form->get('password')->addError(new FormError("Password must contain uppercase characters."));
            } else if (!preg_match('@[a-z]@', $usr->getPassword())) {
              $form->get('password')->addError(new FormError("Password must contain lowercase characters."));
            } else if (!preg_match('@[0-9]@', $usr->getPassword())) {
              $form->get('password')->addError(new FormError("Password must contain numbers."));
            } else if (strlen($usr->getPassword()) < 8) {
              $form->get('password')->addError(new FormError("Password must contain 8 characters or more."));
            } else {

              $encoder = $this->container->get('security.password_encoder');
              $encoded = $encoder->encodePassword($usr, $usr->getPassword());
              $usr->setPassword($encoded);

              // Write to database
              $em = $this->getDoctrine()->getManager();
              $em->persist($usr);
              $em->flush();

            }
          }

        return $this->render('admin/clientnew.html.twig', [
                            'page_title' => 'New Client',
                            'form' => $form->createView(),
                            'submitted' => $form->isSubmitted(),
                            'settings' => $settings,
                            ]);

    }






  }
