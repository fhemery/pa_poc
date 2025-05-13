<?php

namespace App\Service;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class RefreshTokenService
{
    // Refresh tokens will be valid for 90 days (3 months)
    // TODO : Put this in configuration
    private const REFRESH_TOKEN_TTL = 90 * 24 * 60 * 60; // 90 days in seconds

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RefreshTokenRepository $refreshTokenRepository
    ) {
    }

    public function createRefreshToken(User $user): RefreshToken
    {
        // Create a new refresh token
        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user);
        
        // Generate a secure random token
        $token = bin2hex(random_bytes(32));
        $refreshToken->setToken($token);
        
        // Set expiration date (90 days from now)
        $expiresAt = new \DateTimeImmutable('+' . self::REFRESH_TOKEN_TTL . ' seconds');
        $refreshToken->setExpiresAt($expiresAt);
        
        // Save the refresh token
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();
        
        return $refreshToken;
    }

    public function validateRefreshToken(string $token): RefreshToken
    {
        // Find a valid (non-expired) token
        $refreshToken = $this->refreshTokenRepository->findValidToken($token);
        
        if (!$refreshToken) {
            throw new AuthenticationException('Invalid or expired refresh token');
        }
        
        return $refreshToken;
    }

    public function revokeRefreshToken(string $token): void
    {
        $refreshToken = $this->refreshTokenRepository->findOneBy(['token' => $token]);
        
        if ($refreshToken) {
            $this->entityManager->remove($refreshToken);
            $this->entityManager->flush();
        }
    }

    public function revokeAllUserRefreshTokens(User $user): void
    {
        $tokens = $this->refreshTokenRepository->findBy(['user' => $user]);
        
        foreach ($tokens as $token) {
            $this->entityManager->remove($token);
        }
        
        $this->entityManager->flush();
    }

    public function cleanupExpiredTokens(): int
    {
        return $this->refreshTokenRepository->removeExpiredTokens();
    }
}
