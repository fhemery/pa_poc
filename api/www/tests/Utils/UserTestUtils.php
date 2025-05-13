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
    public function loginAs(TestUser $user): ApiResponse
    {
        $response = $this->testCase->jsonPost('/api/login', $user->toLoginArray());
        
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
     */
    public function logoutUser(): void
    {
        $this->testCase->jsonPost('/api/logout');
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
