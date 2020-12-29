<?php
/** @noinspection PhpUnused */

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Services\UserRetriever;
use HealiosTrial\Services\JsonRequestDataKeeper;
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

    /** @var UserRetriever */
    private $userInfoRetriever;

    public function __construct(UserRetriever $userInfoRetriever)
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
        $user = $this->userInfoRetriever->getUserByToken($request);

        if (!$user) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        if ($user->isAdmin) {
            return;
        }

        $defaultUserEndpoints = $this->getDefaultUserEndpoints($user->id);
        $requestUri = $request->getRequestUri();

        if (!in_array($requestUri, $defaultUserEndpoints)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Access denied');
        }

        $request = JsonRequestDataKeeper::keepJson($request);

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
