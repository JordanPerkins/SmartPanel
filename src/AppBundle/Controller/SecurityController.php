<?php
/* Security Controller class for handling login/Reset
 * Created by Jordan Perkins */

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
     // Used by the /login route. This was taken from the Symfony documentation.
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

   // Used by the /reset route. Handles password resets.
   public function resetAction(Request $request) {

     // Render the form to be used. Takes input of username only.
     $form = $this->createFormBuilder()
         ->add('username', TextType::class, array('error_bubbling' => true))
         ->add('save', SubmitType::class, array('label' => 'Reset'))
         ->getForm();

     $form->handleRequest($request);

     // Check for submissions
     if ($form->isSubmitted() && $form->isValid()) {

       // Get information on the user with username submitted.
       $user = $this->getDoctrine()
         ->getRepository('AppBundle:User')
         ->findByUsername($form->get('username')->getData());

       // User does not exist
       if (!$user) {
         throw $this->createNotFoundException('User not found');
       }

        // Get the last existing reset record for that user.
       $reset_check = $this->getDoctrine()
         ->getRepository('AppBundle:Reset')
         ->checkReset($user->getId());

      // If a record exists and it was entered less than 3 hours ago, user is submitting too often.
      if ($reset_check && (time() - $reset_check->getTime()->getTimestamp()) < 10800) {
        throw $this->createNotFoundException(
            'You can only request a password reset every 3 hours'
        );
      }

      // The hash key that will be used in the verify link.
      $hash = bin2hex(openssl_random_pseudo_bytes(32));

      // Create a new entity to store all this information.
      $reset = new Reset();
      $reset->setUid($user->getId());
      $reset->setHash($hash);
      $reset->setTime(new \DateTime("now"));
      $reset->setActive(true);

      // Write the object to the database
      $em = $this->getDoctrine()->getManager();
      $em->persist($reset);
      $em->flush();

      // Email out the reset link. Template reset.html.twig
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

     // Render the page. Only the form is passed on.
     return $this->render('security/reset.html.twig', array(
         'form'           => $form->createView(),
         'page_title'     => "Password Reset"
      ));
   }

  // Used by the /reset/<id>/<hash> route
  public function updateAction($id, $hash) {

    // Find the reset based on the information given.
    $reset = $this->getDoctrine()
      ->getRepository('AppBundle:Reset')
      ->findReset($id, $hash);

    // Doesn't exist - info must be wrong.
    if (!$reset) {
      $result = 'Reset information was invalid';
    // Reset link is not active - already in use.
    } elseif (!$reset->getActive()) {
      $result = 'This link has already been used';
    // Link has expired. Generated more than 3 hours ago.
    } elseif ((time() - $reset->getTime()->getTimestamp()) > 10800) {
      $result = 'This link has expired';
    // Link is valid.
    } else {

      // Update the reset entity to set it as inactive and write to database.
      $reset->setActive(false);
      $em = $this->getDoctrine()->getManager();
      $em->persist($reset);
      $em->flush();

      // Fetch the user corresponding to this reset link.
      $user = $this->getDoctrine()
        ->getRepository('AppBundle:User')
        ->findByID($reset->GetUid());

      // Generate a random password, encrypt it and change the entity.
      $randpass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
      $encoder = $this->container->get('security.password_encoder');
      $encoded = $encoder->encodePassword($user, $randpass);
      $user->setPassword($encoded);

      // Write new password to database.
      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();

      // Email new password - template newpassword.html.twig
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

    // Render the page, passing on the result variable.
    return $this->render('security/resetconfirm.html.twig', array(
        'page_title'     => "Password Reset",
        'result'         => $result
     ));

  }

}

?>
