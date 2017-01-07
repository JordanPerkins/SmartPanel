<?php

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

    public function indexAction(Request $request, UserInterface $user)
    {

      $server_count = count($this->getDoctrine()
        ->getRepository('AppBundle:Server')
        ->findAllByUID($user->getId()));

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'page_title' => 'Dashboard',
            'server_count' => $server_count
        ]);
    }

    public function profileAction(Request $request, UserInterface $user)
    {

      $user = $this->get('security.token_storage')->getToken()->getUser();

      $form = $this->createFormBuilder($user)
          ->add('firstname', TextType::class, array('error_bubbling' => true))
          ->add('surname', TextType::class, array('error_bubbling' => true))
          ->add('email', EmailType::class, array('error_bubbling' => true))
          ->add('save', SubmitType::class, array('label' => 'Save Profile'))
          ->getForm();

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
      $user = $form->getData();

    // ... perform some action, such as saving the task to the database
    // for example, if Task is a Doctrine entity, save it!
    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

}

return $this->render('default/profile.html.twig', [
  'page_title' => 'Profile',
  'form' => $form->createView(),
  'submitted' => $form->isSubmitted()
  ]);

    }

    public function passwordAction(Request $request, UserInterface $user)
    {

      $password = new Credentials();
      $user = $this->get('security.token_storage')->getToken()->getUser();

      $form = $this->createFormBuilder($password)
          ->add('password', PasswordType::class, array('error_bubbling' => true))
          ->add('newpassword', PasswordType::class, array('error_bubbling' => true))
          ->add('verifypassword', PasswordType::class, array('error_bubbling' => true))
          ->add('save', SubmitType::class, array('label' => 'Change Password'))
          ->getForm();

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
        $password = $form->getData();
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password->getNewPassword());
        $user->setPassword($encoded);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

      }

return $this->render('default/password.html.twig', [
  'page_title' => 'Change Password',
  'form' => $form->createView(),
  'submitted' => $form->isSubmitted()
  ]);

    }



}
