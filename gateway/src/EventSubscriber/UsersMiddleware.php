<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Services\RequestDataParser;
use App\Services\UserInfoRetriever;
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

    /** @var UserInfoRetriever */
    private $userInfoRetriever;

    public function __construct(UserInfoRetriever $userInfoRetriever)
    {
        $this->userInfoRetriever = $userInfoRetriever;
    }

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event): void
    {
        /** @var AbstractController|AbstractController[] $controller */
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!$controller instanceof TokenAuthenticatedController) {
            return;
        }

        $request = $event->getRequest();
        $userId = $this->userInfoRetriever->getUserIdByToken($request);

        if (!$userId) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        $isAdmin = $this->userInfoRetriever->isAdmin($userId);

        if (is_null($isAdmin)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found');
        }

        if ($isAdmin) {
            return;
        }

        $defaultUserEndpoints = $this->getDefaultUserEndpoints($userId);
        $requestUri = $request->getRequestUri();

        if (!in_array($requestUri, $defaultUserEndpoints)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Access denied');
        }

        $request = RequestDataParser::transformJsonBody($request);

        if ((int)$request->get('role_id', 0)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Role updating forbidden');
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
     * @param int $userId
     * @return string[]
     */
    private function getDefaultUserEndpoints(int $userId): array
    {
        $list = self::USER_ROLE_ENDPOINTS;

        foreach ($list as &$endpoint) {
            $endpoint .= '/' . $userId;
        }

        return $list;
    }
}
