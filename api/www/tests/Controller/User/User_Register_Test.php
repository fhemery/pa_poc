<?php

namespace App\Tests\Controller\User;

use App\Tests\ApiTestCase;
use App\Tests\Utils\UserBuilder;
use Symfony\Component\HttpFoundation\Response;

class User_Register_Test extends ApiTestCase
{
    public function test_Register_shouldReturn400_whenEmailIsEmpty(): void
    {
        // Create a user with empty email
        $invalidUser = UserBuilder::Random()
            ->withEmail('')
            ->build();
        
        // Try to register the user
        $response = $this->userUtils->registerAs($invalidUser);
        
        // Assert response
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('email', strtolower($response->get('message')));
    }
    
    public function test_Register_shouldReturn400_whenEmailIsNotValid(): void
    {
        // Create a user with invalid email
        $invalidUser = UserBuilder::Random()
            ->withEmail('not-an-email')
            ->build();
        
        // Try to register the user
        $response = $this->userUtils->registerAs($invalidUser);
        
        // Assert response
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('email', strtolower($response->get('message')));
    }
    
    public function test_Register_shouldReturn400_whenPasswordIsEmpty(): void
    {
        // Create a user with empty password
        $invalidUser = UserBuilder::Random()
            ->withPassword('short')
            ->build();
        
        // Try to register the user
        $response = $this->userUtils->registerAs($invalidUser);
        
        // Assert response
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('password', strtolower($response->get('message')));
    }
    
    public function test_Register_shouldReturn400_whenPasswordIsTooShort(): void
    {
        // Create a user with short password
        $invalidUser = UserBuilder::Random()
            ->withPassword('12345')
            ->build();
        
        // Try to register the user
        $response = $this->userUtils->registerAs($invalidUser);
        
        // Assert response
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('password', strtolower($response->get('message')));
    }
    
    public function test_Register_shouldReturn400_whenUserAlreadyExists(): void
    {
        // Create a valid user
        $user = UserBuilder::Random()->build();
        
        // Register the user first time (should succeed)
        $firstResponse = $this->userUtils->registerAs($user);
        $this->assertEquals(Response::HTTP_CREATED, $firstResponse->getStatusCode());
        
        // Try to register the same user again
        $secondResponse = $this->userUtils->registerAs($user);
        
        // Assert response
        $this->assertEquals(Response::HTTP_CONFLICT, $secondResponse->getStatusCode());
        $this->assertStringContainsString('already exists', strtolower($secondResponse->get('message')));
    }
    
    public function test_Register_shouldReturn201_whenUserIsValid(): void
    {
        // Create a valid user
        $user = UserBuilder::Random()->build();
        
        // Register the user
        $registerResponse = $this->userUtils->registerAs($user);
        
        // Assert registration response
        $this->assertEquals(Response::HTTP_CREATED, $registerResponse->getStatusCode());
        
        // Try to login with the registered user
        $loginResponse = $this->userUtils->loginAs($user);
        
        // Assert login response
        $this->assertEquals(Response::HTTP_OK, $loginResponse->getStatusCode());
        $this->assertNotNull($loginResponse->get('token'), 'Login should return a JWT token');
    }
}