<?php

namespace App\Tests\Controller\User;

use App\Tests\ApiTestCase;
use App\Tests\Utils\UserBuilder;
use App\Tests\Utils\TestUser;
use Symfony\Component\HttpFoundation\Response;

class User_Login_Test extends ApiTestCase
{
    public function test_Login_shouldReturn401_whenEmailDoesNotExist(): void
    {
        // Create a user that doesn't exist in the database
        $nonExistentUser = new TestUser(
            'nonexistent' . uniqid() . '@example.com',
            'Password123!',
            'Non',
            'Existent'
        );
        
        // Try to login with the non-existent user
        $response = $this->userUtils->loginAs($nonExistentUser);
        
        // Assert response
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertStringContainsString('invalid', strtolower($response->get('message')));
    }
    
    public function test_Login_shouldReturn401_whenPasswordIsInvalid(): void
    {
        $alice = UserBuilder::Alice();
        
        // Create a user with Alice's email but wrong password
        $aliceWithWrongPassword = new TestUser(
            $alice->getEmail(),
            'WrongPassword123!',
            $alice->getFirstName(),
            $alice->getLastName()
        );
        
        // Try to login with wrong password
        $response = $this->userUtils->loginAs($aliceWithWrongPassword);
        
        // Assert response
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertStringContainsString('invalid', strtolower($response->get('message')));
    }
    
    public function test_Login_shouldReturn200_whenCredentialsAreValid(): void
    {
        $alice = UserBuilder::Alice();
        $this->userUtils->registerAs($alice);
        
        // Login with Alice's credentials
        $response = $this->userUtils->loginAs($alice);
        
        // Assert response
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotNull($response->get('token'), 'Login response should contain a JWT token');
    }
}