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
     * Register a new user for testing
     */
    public function registerUser(string $email, string $password, string $firstName = 'Test', string $lastName = 'User'): array
    {
        $userData = [
            'email' => $email,
            'password' => $password,
            'firstName' => $firstName,
            'lastName' => $lastName
        ];
        
        $response = $this->testCase->jsonPost('/api/public/register', $userData);
        
        $statusCode = $response->getStatusCode();
        if ($statusCode !== Response::HTTP_CREATED) {
            throw new \RuntimeException(sprintf('Failed to register user: %s', json_encode($response->json())));
        }
        
        return $response->json();
    }
    
    /**
     * Login a user and store the JWT token
     */
    public function loginUser(string $email, string $password): array
    {
        $loginData = [
            'username' => $email,
            'password' => $password
        ];
        
        $response = $this->testCase->jsonPost('/api/login', $loginData);
        
        $statusCode = $response->getStatusCode();
        if ($statusCode !== Response::HTTP_OK) {
            throw new \RuntimeException(sprintf('Failed to login user: %s', json_encode($response->json())));
        }
        
        $responseData = $response->json();
        if (!isset($responseData['token'])) {
            throw new \RuntimeException('No token returned from login');
        }
        
        return $responseData;
    }
    
    /**
     * Register and login a user in one step
     */
    public function registerAndLoginUser(?string $email = null, string $password = 'Test123!'): array
    {
        if ($email === null) {
            $email = 'test' . uniqid() . '@example.com';
        }
        
        $this->registerUser($email, $password);
        return $this->loginUser($email, $password);
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
        
        return $response->json();
    }
}
