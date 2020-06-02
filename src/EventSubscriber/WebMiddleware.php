<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use App\Controller\Web\WebController;
use App\Controller\Exception\NotPublishedException;

/**
 *
 * This middleware intercept each controller calls in order to
 * determine if the current request is a published instance or not.
 *
 * The first step is verify if the current request must be checked,
 * because the validation only have sense for App\Entity\Publishable instances
 *
 * The second step is verify if the instance is published or not.
 *
 * After knowing the publishing status of the instance, the last step is
 * redirect the user to the appropriate destination or offer other published alternatives.
 *
 */
class WebMiddleware implements EventSubscriberInterface
{
    private $router;
    private $requestStack;

    // The default action name used to determine if the controller call
    // must be intercepted or not
    const DEFAULT_ACTION = 'detail';

    public function __construct(RequestStack $requestStack, RequestContextAwareInterface $router = null)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $this->setRouterContext($request);
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event)
    {

        // STEP 1: the current request must be checked ?
        if ($this->mustBeChecked($event)) {
            $request = $event->getRequest();
            list($language, $region) = $this->collectParams($request);

            // Get a reference to the repository if it is present
            // in the arguments of the controller call
            $repository = $this->getRepositoryFromArguments($event->getArguments());

            // If we have $repository, try to obtain the instance (entity object)
            // managed by the controller
            $instance = $repository ? $repository->getInstanceByRequest($request) : null;

            // STEP 2: the current instance is published?
            if ($instance && !$instance->isPublished($language, $region)) {
                throw new NotPublishedException();
            }
        }
    }

    // Propagate router context to the parent
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        if (null !== $parentRequest = $this->requestStack->getParentRequest()) {
            $this->setRouterContext($parentRequest);
        }
    }

    private function collectParams(Request $request)
    {
        $params = $request->attributes->get('_route_params');
        $language = $params['_locale'] ?? null;
        $region = $params['_region'] ?? null;
        return [$language, $region];
    }

    private function getRepositoryFromArguments($arguments)
    {
        foreach ($arguments as $argument) {
            if ($argument instanceof ServiceEntityRepository) {
                return $argument;
            }
        }
        return null;
    }

    private function mustBeChecked(ControllerArgumentsEvent $event)
    {
        $controller = $event->getController();
        $action = null;

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $action = $controller[1];
            $controller = $controller[0];
        }

        return
            $controller instanceof WebController
            && isset($action)
            && $action == self::DEFAULT_ACTION
        ;
    }

    // Adding the _region parameter into the router context to avoid
    // an explicit reference in every router construction.
    // With this action, the _region will have an intelligent behaviour like the
    // _locale parameter and we will not need to specify it every time.
    // The Router will take it from the context when we don't specify one.
    private function setRouterContext(Request $request)
    {
        list($language, $region) = $this->collectParams($request);
        if (null !== $this->router && $region && $region!='') {
            $this->router->getContext()->setParameter('_region', $region);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 18]]
            ,KernelEvents::CONTROLLER_ARGUMENTS => [['onKernelControllerArguments',0]]
            ,KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest',0]]
        ];
    }
}
