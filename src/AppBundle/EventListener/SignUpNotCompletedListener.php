<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Listener that prompts male activate account if not activated
 */
class SignUpNotCompletedListener
{
    protected $tokenStorage;
    protected $entityManager;
    protected $router;
    protected $container;
    protected $targetRoutes;

    public function __construct(TokenStorage $tokenStorage, Router $router, EntityManager $entityManager, $targetRoutes, $excludedRoutes)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->targetRoutes = $targetRoutes;
        $this->entityManager = $entityManager;
        $this->excludedRoutes = $excludedRoutes;
        $this->excludedRoutes[] = $this->targetRoutes['male'];
        $this->excludedRoutes[] = $this->targetRoutes['female'];
        $this->excludedRoutes[] = 'sign_up_subscription';
        $this->excludedRoutes[] = 'payment_subscription';
    }

    /**
     * Redirects a male user to Activation page if not activated
     * @param FilterControllerEvent $event
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        // Check that the current request is a "MASTER_REQUEST"
        // Or that the current request is not a target request
        // Ignore any sub-request
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST ||
            in_array($event->getRequest()->get('_route'), $this->excludedRoutes) ||
            !$this->tokenStorage->getToken() ||
            strpos($event->getRequest()->get('_route'), 'api_1_') !== false ||
            strpos($event->getRequest()->get('_route'), 'api_2_') !== false ||
            $event->getRequest()->get('_route') == 'contact'
        )
        {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {

            //if($user->getId() == 445){

            //}

            if($user->getGender()->getId() == 1 && !$user->getIsActivated()){
                $redirectUrl = $this->router->generate($this->targetRoutes['male']);
                //$status = 'not_activated';
            }
            elseif($user->getGender()->getId() == 2 && !$user->hasPhotos()) {
                $redirectUrl = $this->router->generate($this->targetRoutes['female']);
                //$status = 'no_photo';
            }
            elseif($user->getGender()->getId() == 1  /* and !$user->is2D() */ and !$user->isPaying()
                and ($this->entityManager->getRepository('AppBundle:Settings')->find(1)->getIsCharge() or $user->getId() == 455))
            {
                if($event->getRequest()->get('_route')=='messenger'){
                    return;
                }else {
                    if($event->getRequest()->get('_route') == 'user_homepage' || $event->getRequest()->get('_route') == 'homepage' and !$user->is2D()){
                        $redirectUrl = $this->router->generate('messenger');
                    }else {
                        $redirectUrl = $this->router->generate('sign_up_subscription');
                    }
                }
            }
            else{
                return;
            }
            /*
            if(strpos($event->getRequest()->get('_route'), 'api_1_') !== false){
                //$event->setResponse();
                //$event->getRequest()->attributes->set('status', $status);
                //return;// new Response(json_encode(array('status' => $status)));
                $event->getRequest()->attributes->set('status', $status);
                return;// new Response(json_encode(array('status' => $status)));
                //$event->getRequest()->attributes->set('auth_token', $token);


            }
            */
            $event->setController(function() use ($redirectUrl) {
                return new RedirectResponse($redirectUrl);
            });
        }

    }

    public function onKernelResponse(FilterResponseEvent $event)
    {


            if(strpos($event->getRequest()->get('_route'), 'api_1_') !== false){
                $status = $event->getRequest()->attributes->get('status');
                //$event->setResponse();
                //$event->getRequest()->attributes->set('status', $status);
                //return;// new Response(json_encode(array('status' => $status)));
                $event->setResponse(new Response(json_encode(array('status' => $status))));
                //new Response(json_encode(array('status' => $status)));
                //$event->getRequest()->attributes->set('auth_token', $token);


            }
            return;

    }
}