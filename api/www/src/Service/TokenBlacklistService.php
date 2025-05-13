<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class TokenBlacklistService
{
    private FilesystemAdapter $cache;
    
    public function __construct()
    {
        $this->cache = new FilesystemAdapter('jwt_blacklist', 0, '%kernel.project_dir%/var/cache/jwt_blacklist');
    }
    
    public function blacklist(string $token, int $expiresAt): void
    {
        $tokenId = $this->getTokenId($token);
        $ttl = $expiresAt - time();
        
        if ($ttl <= 0) {
            return; // Token already expired, no need to blacklist
        }
        
        $this->cache->get($tokenId, function (ItemInterface $item) use ($ttl) {
            $item->expiresAfter($ttl);
            return true;
        });
    }
    
    public function isBlacklisted(string $token): bool
    {
        $tokenId = $this->getTokenId($token);
        return $this->cache->hasItem($tokenId);
    }
    
    private function getTokenId(string $token): string
    {
        // Create a unique ID for the token
        return 'jwt_' . hash('sha256', $token);
    }
}
