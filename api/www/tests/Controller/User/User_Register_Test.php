<?php

namespace App\Tests\Controller\User;

use App\Tests\ApiTestCase;
use App\Tests\Utils\UserBuilder;
use Symfony\Component\HttpFoundation\Response;

class User_Register_Test extends ApiTestCase
{
    public function test_Register_shouldReturn400_whenEmailIsEmpty(): void
    {
        $invalidUser = UserBuilder::Random()
            ->withEmail('')
            ->build();
        
        $response = $this->userUtils->registerAs($invalidUser);
        
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('email', strtolower($response->get('message')));
    }
    
    public function test_Register_shouldReturn400_whenEmailIsNotValid(): void
    {
        $invalidUser = UserBuilder::Random()
            ->withEmail('not-an-email')
            ->build();
        
        $response = $this->userUtils->registerAs($invalidUser);
        
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('email', strtolower($response->get('message')));
    }
    
    public function test_Register_shouldReturn400_whenPasswordIsEmpty(): void
    {
        $invalidUser = UserBuilder::Random()
            ->withPassword('short')
            ->build();
        
        $response = $this->userUtils->registerAs($invalidUser);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('password', strtolower($response->get('message')));
    }
    
    public function test_Register_shouldReturn400_whenPasswordIsTooShort(): void
    {
        $invalidUser = UserBuilder::Random()
            ->withPassword('12345')
            ->build();
        
        $response = $this->userUtils->registerAs($invalidUser);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('password', strtolower($response->get('message')));
    }
    
    public function test_Register_shouldReturn400_whenUserAlreadyExists(): void
    {
        $user = UserBuilder::Random()->build();
        $firstResponse = $this->userUtils->registerAs($user);
        $this->assertEquals(Response::HTTP_CREATED, $firstResponse->getStatusCode());
        
        $secondResponse = $this->userUtils->registerAs($user);
        $this->assertEquals(Response::HTTP_CONFLICT, $secondResponse->getStatusCode());
        $this->assertStringContainsString('already exists', strtolower($secondResponse->get('message')));
    }
    
    public function test_Register_shouldReturn201_whenUserIsValid(): void
    {
        $user = UserBuilder::Random()->build();
        $registerResponse = $this->userUtils->registerAs($user);
        $this->assertEquals(Response::HTTP_CREATED, $registerResponse->getStatusCode());
        
        $loginResponse = $this->userUtils->loginAs($user);
        $this->assertEquals(Response::HTTP_OK, $loginResponse->getStatusCode());
        $this->assertNotNull($loginResponse->get('accessToken'), 'Login should return an access token');
    }
}