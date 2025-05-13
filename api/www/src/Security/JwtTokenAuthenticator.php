<?php

namespace App\Security;

use App\Service\TokenBlacklistService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class JwtTokenAuthenticator extends JWTAuthenticator
{
    private TokenBlacklistService $tokenBlacklistService;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $eventDispatcher,
        TokenExtractorInterface $tokenExtractor,
        TokenBlacklistService $tokenBlacklistService
    ) {
        parent::__construct($jwtManager, $eventDispatcher, $tokenExtractor);
        $this->tokenBlacklistService = $tokenBlacklistService;
    }

    public function doAuthenticate(Request $request): Passport
    {
        $token = $this->getTokenExtractor()->extract($request);

        if ($token === false) {
            throw new AuthenticationException('No JWT token found');
        }

        // Check if token is blacklisted
        if ($this->tokenBlacklistService->isBlacklisted($token)) {
            throw new InvalidTokenException('JWT token is blacklisted');
        }

        return parent::doAuthenticate($request);
    }
}
