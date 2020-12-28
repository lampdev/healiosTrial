<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();

//$serverIp = $request->server->get('SERVER_ADDR');
//$lastDote = strrpos($serverIp, '.');
//$serverMask = substr($serverIp, 0, $lastDote);
//$clientIp = $request->getClientIp();
//$lastDote = strrpos($clientIp, '.');
//$clientMask = substr($clientIp, 0, $lastDote);
//$serverEnd = str_replace($serverMask . '.', '', $serverIp);
//$clientEnd = str_replace($clientMask . '.', '', $clientIp);
//
//echo json_encode([$serverIp, $clientIp, $serverMask, $clientMask, $serverEnd, $clientEnd]);
//die();

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
