<?php

namespace App\EventListener;

use App\Service\TokenBlacklistService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTTokenListener implements EventSubscriberInterface
{
    private TokenBlacklistService $tokenBlacklistService;
    private RequestStack $requestStack;

    public function __construct(TokenBlacklistService $tokenBlacklistService, RequestStack $requestStack)
    {
        $this->tokenBlacklistService = $tokenBlacklistService;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_DECODED => 'onJWTDecoded',
        ];
    }

    public function onJWTDecoded(JWTDecodedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $token = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
        
        // Check if token is blacklisted
        if ($this->tokenBlacklistService->isBlacklisted($token)) {
            $event->markAsInvalid();
        }
    }
}
