<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class LogoutResponseListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->getPathInfo() === '/api/logout') {
            $response->headers->clearCookie('refresh_token', '/', null, true, true, 'None');
            $response->headers->clearCookie('BEARER', '/', null, true, true, 'None');
        }
    }
}
