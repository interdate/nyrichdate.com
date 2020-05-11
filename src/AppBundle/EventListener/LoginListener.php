<?php
namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;


/**
 * Listener that updates the last activity of the authenticated user
 */
class LoginListener
{
    protected $tokenStorage;
    protected $entityManager;

    public function __construct(TokenStorage $tokenStorage, EntityManager $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * Update the user "lastLogin"
     * @param  InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Check token authentication availability
        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof User) {
                $user->setLastloginAt(new \DateTime());
                $user->setIsFrozen(0);
                $this->entityManager->flush($user);
            }

        }
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $response = new Response('hi');
        $response->headers->set('X-Status-Code', 200);
        $event->setResponse($response);
    }
}