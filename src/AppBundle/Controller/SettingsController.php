<?php

/* The controller used for the index, profile and change password pages.
   Created by Jordan Perkins. */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Settings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SettingsController extends Controller
{

    private $em;

    public function __construct($entityManager = null) {
      if (!empty($entityManager)) {
        $this->em = $entityManager;
      }
    }

    public function get($name = null)
    {

      if ($name) {
        // Fetch particular setting entity.
        $setting = $this->em->getRepository('AppBundle:Settings')
          ->findBySetting($name);

        // Return it's value
        return $setting->getValue();

      } else {

        // Fetch all entities
        $settings = $this->em->getRepository('AppBundle:Settings')
          ->findAll();

        $array = array();
        foreach ($settings as $setting) {
          $value = str_replace('%time%', date("m/d/Y H:i:s"), $setting->getValue());
          $array[$setting->getSetting()] = $value;
        }

        return $array;

      }

    }

    public function set($name, $value)
    {

      // Fetch particular setting entity.
      $setting = $this->em->getRepository('AppBundle:Settings')->findBySetting($name);

      // Set new value
      $setting->setValue($value);

      // Write to database
      $this->em->persist($setting);
      $this->em->flush();

    }

    public function formAction(UserInterface $user, Request $request)
    {

      // User is not admin, redirect to dashboard.
      if (!$user->getIsAdmin()) {
        return new RedirectResponse('/');
      }

      /* Get the settings into an array where the key is the value name.
      Also complete the creation on the form. */

      $form = $this->createFormBuilder();

      $settings = array();
      foreach ($this->getDoctrine()->getRepository('AppBundle:Settings')->findAll() as $setting) {
        if ($setting->getNumerical()) {
          $form->add($setting->getSetting(), IntegerType::class, array('data' => $setting->getValue(), 'error_bubbling' => true));
        } else {
          $form->add($setting->getSetting(), TextType::class, array('data' => $setting->getValue(), 'error_bubbling' => true));
        }
        $settings[$setting->getSetting()] = $setting->getValue();
      }

      $form->add('save', SubmitType::class, array('label' => 'Save'));
      $form = $form->getForm();

      $form->handleRequest($request);

      // Check for submission and validate it using Validator.
      if ($form->isSubmitted() && $form->isValid() && $user->getIsAdmin()) {
        // Update user entity
        foreach ($form->getData() as $setting => $value) {

          $setting = $this->getDoctrine()->getRepository('AppBundle:Settings')->findBySetting($setting);

          // Set new value
          $setting->setValue($value);

          // Write to database
          $em = $this->getDoctrine()->getManager();
          $em->persist($setting);
          $em->flush();

        }

      }

      return $this->render('admin/settings.html.twig', [
                            'page_title' => 'Settings',
                            'form' => $form->createView(),
                            'settings' => $settings,
                            'submitted' => $form->isSubmitted(),
                          ]);

    }

}
