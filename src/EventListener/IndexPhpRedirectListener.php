<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 64)]
final class IndexPhpRedirectListener
{
    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->isMethodSafe() || $request->getBaseUrl() === $request->getBasePath()) {
            return;
        }

        $queryString = $request->getQueryString();
        $target = $request->getBasePath() . $request->getPathInfo() . ($queryString === null ? '' : '?' . $queryString);

        $event->setResponse(new RedirectResponse($target, Response::HTTP_MOVED_PERMANENTLY));
    }
}
