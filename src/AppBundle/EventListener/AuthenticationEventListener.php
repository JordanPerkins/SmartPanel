<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\Log;

class AuthenticationEventListener implements AuthenticationSuccessHandlerInterface
{
    protected $router;
    protected $container;
    protected $em;

    public function __construct(Router $router, $container)
    {
        $this->router = $router;
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getEntityManager();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
      $user = $token->getUser();
      $log = new Log("login", new \DateTime("now"), $request->getClientIp(), null, 0, $user->getId(), true);
      $this->em->persist($log);
      $this->em->flush();
      $response = new Response();
      $response = new RedirectResponse('/');
      return $response;
    }

}

?>
