<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Reset;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\DateTime;

class SecurityController extends Controller
{
     public function loginAction(Request $request)
     {
     $authenticationUtils = $this->get('security.authentication_utils');
     // get the login error if there is one
     $error = $authenticationUtils->getLastAuthenticationError();
     // last username entered by the user
     $lastUsername = $authenticationUtils->getLastUsername();

     return $this->render('security/login.html.twig', array(
         'last_username'  => $lastUsername,
         'error'          => $error,
         'page_title'     => "Login"
     ));
   }

   public function resetAction(Request $request) {

     $reset = new Reset();

     $form = $this->createFormBuilder()
         ->add('username', TextType::class, array('error_bubbling' => true))
         ->add('save', SubmitType::class, array('label' => 'Reset'))
         ->getForm();

     $form->handleRequest($request);

     if ($form->isSubmitted()) {
       // $form->getData() holds the submitted values
       // but, the original `$task` variable has also been updated
       $user = $this->getDoctrine()
         ->getRepository('AppBundle:User')
         ->findByUsername($form->get('username')->getData());

       if (!$user) {
         throw $this->createNotFoundException(
             'User not found'
         );
        }

        $reset_check = $this->getDoctrine()
         ->getRepository('AppBundle:Reset')
         ->checkReset($user->getId());

      if ($reset_check && (time() - $reset_check->getTime()->getTimestamp()) < 10800) {
        throw $this->createNotFoundException(
            'You can only request a password reset every 3 hours'
        );
      }

      $hash = bin2hex(openssl_random_pseudo_bytes(32));

      $reset->setUid($user->getId());
      $reset->setHash($hash);
      $reset->setTime(new \DateTime("now"));
      $reset->setActive(true);

      $em = $this->getDoctrine()->getManager();
      $em->persist($reset);
      $em->flush();

      $message = \Swift_Message::newInstance()
            ->setSubject('Reset Password')
            ->setFrom("panel@budgetnode.com")
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                  'Emails/reset.html.twig',
                  array(
                    'username' => $user->getUsername(),
                    'id' => $reset->getId(),
                    'firstname' => $user->getFirstname(),
                    'surname' => $user->getSurname(),
                    'hash' => $hash
                  )
                ),
                'text/html'
            );

      $this->get('mailer')->send($message);

    }

     return $this->render('security/reset.html.twig', array(
         'form'           => $form->createView(),
         'page_title'     => "Password Reset"
      ));
   }

  public function updateAction($id, $hash) {

    $reset = $this->getDoctrine()
      ->getRepository('AppBundle:Reset')
      ->findReset($id, $hash);

    if (!$reset) {
      $result = 'Reset information was invalid';
    } elseif (!$reset->getActive()) {
      $result = 'This link has already been used';
    } elseif ((time() - $reset->getTime()->getTimestamp()) > 10800) {
      $result = 'This link has expired';
    } else {

      $reset->setActive(false);
      $em = $this->getDoctrine()->getManager();
      $em->persist($reset);
      $em->flush();

      $user = $this->getDoctrine()
        ->getRepository('AppBundle:User')
        ->findByID($reset->GetUid());

      $randpass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
      $encoder = $this->container->get('security.password_encoder');
      $encoded = $encoder->encodePassword($user, $randpass);
      $user->setPassword($encoded);

      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();

      $message = \Swift_Message::newInstance()
            ->setSubject('Reset Password')
            ->setFrom("panel@budgetnode.com")
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                  'Emails/newpassword.html.twig',
                  array(
                    'username' => $user->getUsername(),
                    'firstname' => $user->getFirstname(),
                    'surname' => $user->getSurname(),
                    'password' => $randpass
                  )
                ),
                'text/html'
            );

      $this->get('mailer')->send($message);

      $result = 'You will be emailed a new password.';
    }

    return $this->render('security/resetconfirm.html.twig', array(
        'page_title'     => "Password Reset",
        'result'         => $result
     ));

  }

}

?>
