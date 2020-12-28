<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Services\RequestDataParser;
use App\Services\ThirdPartyConnector;
use App\Structures\UserData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class UsersMiddleware implements EventSubscriberInterface
{
    private const USER_ROLE_ENDPOINTS = [
        '/api/users/show',
        '/api/users/update',
        '/api/users/delete',
    ];

    /** @var ThirdPartyConnector */
    private $thirdPartyConnector;

    public function __construct(ThirdPartyConnector $thirdPartyConnector)
    {
        $this->thirdPartyConnector = $thirdPartyConnector;
    }

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        /** @var AbstractController|AbstractController[] $controller */
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedController) {
            $request = $event->getRequest();
            $userId = $this->thirdPartyConnector->validateToken($request);

            if (!$userId) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
            }

            $user = $this->thirdPartyConnector->getUser($userId);

            if (!$user) {
                throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found');
            }

            if (!$user->isAdmin) {
                $defaultUserEndpoints = $this->getDefaultUserEndpoints($user);
                $requestUri = $request->getRequestUri();

                if (!in_array($requestUri, $defaultUserEndpoints)) {
                    throw new HttpException(Response::HTTP_FORBIDDEN, 'Access denied');
                }

                $request = RequestDataParser::transformJsonBody($request);

                if ((int)$request->get('role_id', 0)) {
                    throw new HttpException(Response::HTTP_FORBIDDEN, 'Access denied');
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * @param UserData $userData
     * @return string[]
     */
    private function getDefaultUserEndpoints(UserData $userData): array
    {
        $list = self::USER_ROLE_ENDPOINTS;

        foreach ($list as &$endpoint) {
            $endpoint .= '/' . $userData->userId;
        }

        return $list;
    }
}
