<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\ApiTestCase;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create the SQLite schema for testing
        if ($this->entityManager) {
            // Create database schema if it doesn't exist
            $schemaTool = new SchemaTool($this->entityManager);
            $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
            
            // Drop and recreate schema to ensure a clean state
            $schemaTool->dropSchema($metadata);
            $schemaTool->createSchema($metadata);
            
            // Explicitly begin a transaction to keep the connection alive
            $this->entityManager->getConnection()->beginTransaction();
        }
    }
    
    protected function tearDown(): void
    {
        // Rollback the transaction to clean up after the test
        if ($this->entityManager && $this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->rollBack();
        }
        
        parent::tearDown();
    }
    public function testUsersMeEndpointWithoutAuthentication(): void
    {
        // Make a GET request to the users/me endpoint without authentication
        // We need to catch exceptions in the client to get the response
        $this->client->catchExceptions(true);
        $this->jsonRequest('GET', '/api/users/me');
        
        // Get the response status code
        $statusCode = $this->client->getResponse()->getStatusCode();
        
        // Assert that the response status code is 401 (Unauthorized)
        $this->assertEquals(
            Response::HTTP_UNAUTHORIZED, 
            $statusCode,
            'The response status code should be 401 when accessing /api/users/me without authentication'
        );
        
        // Assert that the response is in JSON format
        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json'),
            'The response content type is not application/json'
        );
    }
    
    public function testAuthenticationFlow(): void
    {
        // Use the same client for all requests to maintain the database connection
        $client = $this->client;
        
        // 1. Register a new user
        $email = 'test' . uniqid() . '@example.com';
        $password = 'Test123!';
        $firstName = 'Test';
        $lastName = 'User';
        
        $userData = [
            'email' => $email,
            'password' => $password,
            'firstName' => $firstName,
            'lastName' => $lastName
        ];
        
        // Disable reboot between requests to maintain the database connection
        $client->disableReboot();
        
        $this->jsonRequest('POST', '/api/public/register', $userData);
        
        // Assert registration was successful
        $this->assertEquals(
            Response::HTTP_CREATED,
            $this->client->getResponse()->getStatusCode(),
            'User registration should return a 201 Created response'
        );
        
        $registerResponse = $this->getJsonResponse();
        $this->assertArrayHasKey('message', $registerResponse);
        $this->assertArrayHasKey('user', $registerResponse);
        $this->assertEquals($email, $registerResponse['user']['email']);
        
        // 2. Login with the registered user
        $loginData = [
            'username' => $email,
            'password' => $password
        ];
        
        $this->jsonRequest('POST', '/api/login', $loginData);
        
        // Assert login was successful
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            'Login should return a 200 OK response'
        );
        
        $loginResponse = $this->getJsonResponse();
        $this->assertArrayHasKey('token', $loginResponse);
        
        // Get the JWT token
        $token = $loginResponse['token'];
        
        // 3. Access /users/me with the JWT token
        $this->jsonRequest('GET', '/api/users/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token
        ]);
        
        // Assert that the response status code is 200 (OK)
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            'The response status code should be 200 when accessing /api/users/me with valid authentication'
        );
        
        // Assert that the response contains the user's email
        $meResponse = $this->getJsonResponse();
        $this->assertArrayHasKey('email', $meResponse);
        $this->assertEquals($email, $meResponse['email']);
    }
}
