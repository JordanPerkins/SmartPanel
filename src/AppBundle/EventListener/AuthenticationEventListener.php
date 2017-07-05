<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\AuthenticationLog;

class AuthenticationEventListener implements AuthenticationSuccessHandlerInterface
{
    protected $router;
    protected $container;
    protected $em;

    public function __construct(Router $router, $container)
    {
        $this->router = $router;
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
      $user = $token->getUser();
      if ($user->getIsActive()) {
        $log = new AuthenticationLog($user->getId(), new \DateTime("now"), $request->getClientIp(), true);
        $this->em->persist($log);
        $this->em->flush();
        $response = new Response();
        $response = new RedirectResponse('/');
      // User is not active - log them out.
      } else {
        $response = new RedirectResponse('/logout');
      }
      return $response;
    }

}

?>
