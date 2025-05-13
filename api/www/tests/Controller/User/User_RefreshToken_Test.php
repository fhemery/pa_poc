<?php

namespace App\Tests\Controller\User;

use App\Tests\ApiTestCase;
use App\Tests\Utils\UserBuilder;
use Symfony\Component\HttpFoundation\Response;

class User_RefreshToken_Test extends ApiTestCase
{
    public function test_RefreshToken_shouldReturn400_whenNoRefreshTokenProvided(): void
    {
        $response = $this->jsonPost('/api/refresh-token', []);
        
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('refresh token is required', strtolower($response->get('message')));
    }
    
    public function test_RefreshToken_shouldReturn401_whenInvalidRefreshTokenProvided(): void
    {
        $response = $this->jsonPost('/api/refresh-token', [
            'refreshToken' => 'invalid-refresh-token'
        ]);
        
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertStringContainsString('invalid or expired', strtolower($response->get('message')));
    }
    
    public function test_RefreshToken_shouldReturn200_withNewTokens(): void
    {
        // Register a new user
        $user = UserBuilder::Alice();
        $response = $this->userUtils->loginAs($user);
        $refreshToken = $response->get('refreshToken');
        
        // Use refresh token to get new tokens
        $response = $this->userUtils->renewToken($refreshToken);
        
        // Assert response
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotNull($response->get('accessToken'), 'Response should contain a new access token');
        $this->assertNotNull($response->get('refreshToken'), 'Response should contain a new refresh token');
        $this->assertNotEquals($refreshToken, $response->get('refreshToken'), 'New refresh token should be different');
        $this->assertEquals(3600, $response->get('expiresIn'), 'Access token should expire in 1 hour');
        $this->assertEquals(7776000, $response->get('refreshExpiresIn'), 'Refresh token should expire in 90 days');
        
    }

    public function test_RefreshToken_shouldReturn401_whenRefreshTokenIsInvalid(): void
    {
        $user = UserBuilder::Alice();
        $response = $this->userUtils->loginAs($user);
        $refreshToken = $response->get('refreshToken');
        
        // First call will work
        $response = $this->userUtils->renewToken($refreshToken);

        // Second call will fail, token is blacklisted
        $response = $this->userUtils->renewToken($refreshToken, false);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertStringContainsString('invalid or expired', strtolower($response->get('message')));
    }

    public function test_RefreshToken_shouldReturn401_whenUserLogsOut(): void
    {
        $user = UserBuilder::Alice();
        $response = $this->userUtils->loginAs($user);
        $refreshToken = $response->get('refreshToken');
        
        // Pass the refresh token to the logout method to blacklist it
        $this->userUtils->logoutUser($refreshToken);

        // Second call will fail, token is blacklisted
        $response = $this->userUtils->renewToken($refreshToken, false);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertStringContainsString('invalid or expired', strtolower($response->get('message')));
    }
}
