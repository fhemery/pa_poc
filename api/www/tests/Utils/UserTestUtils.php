<?php

namespace App\Tests\Utils;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserTestUtils
{
    private ApiTestCase $testCase;
    
    public function __construct(ApiTestCase $testCase)
    {
        $this->testCase = $testCase;
    }
    
    /**
     * Register a user from a TestUser object
     */
    public function registerAs(TestUser $user): ApiResponse
    {
        $response = $this->testCase->jsonPost('/api/public/register', $user->toRegistrationArray());

        return $response;
    }

    /**
     * Login a user from a TestUser object and store the JWT token
     */
    public function loginAs(TestUser $user, bool $assertOk = true): ApiResponse
    {
        $response = $this->testCase->jsonPost('/api/login', $user->toLoginArray());
        if ($assertOk) {
            $this->testCase->assertEquals(Response::HTTP_OK, $response->getStatusCode(), "User login is failing");
        }
        
        return $response;
    }
    
    /**
     * Register and login a user from a TestUser object in one step
     */
    public function registerAndLoginAs(TestUser $user)
    {
        $this->registerAs($user);
        $this->loginAs($user);
    }
    
    /**
     * Logout the current user
     * 
     * @param string|null $refreshToken The refresh token to invalidate (optional)
     */
    public function logoutUser(?string $refreshToken = null): void
    {
        $data = [];
        if ($refreshToken) {
            $data['refreshToken'] = $refreshToken;
        }
        
        $this->testCase->jsonPost('/api/logout', $data);
    }
    
    /**
     * Refresh the token using a refresh token
     * 
     * @param string $refreshToken The refresh token to use
     * @param bool $assertOk Whether to assert that the response status is OK
     * @return ApiResponse The API response
     */
    public function renewToken(string $refreshToken, bool $assertOk = true): ApiResponse
    {
        $response = $this->testCase->jsonPost('/api/refresh-token', [
            'refreshToken' => $refreshToken
        ]);
        
        if ($assertOk) {
            $this->testCase->assertEquals(Response::HTTP_OK, $response->getStatusCode(), "Token refresh is failing");
        }
        
        return $response;
    }
    
    /**
     * Get the current user's details from the /api/users/me endpoint
     */
    public function getCurrentUserDetails(): array
    {
        $response = $this->testCase->jsonGet('/api/users/me');
        
        $statusCode = $response->getStatusCode();
        if ($statusCode !== Response::HTTP_OK) {
            throw new \RuntimeException(sprintf('Failed to get user details: %s', json_encode($response->json())));
        }
        
        return $response->body();
    }
    
}
