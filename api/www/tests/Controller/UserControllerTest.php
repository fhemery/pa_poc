<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use App\Tests\Utils\UserBuilder;
use App\Tests\Utils\UserTestUtils;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->userUtils = new UserTestUtils($this);
    }
    
    public function test_GetUserMe_shouldReturn401WhenUserIsNotLoggedIn(): void
    {
        $response = $this->jsonGet('/api/users/me');
        
        $this->assertEquals(
            Response::HTTP_UNAUTHORIZED, 
            $response->getStatusCode()
        );
    }
    
    public function test_GetUserMe_shouldReturn200WhenUserIsLogged(): void
    {
        // Use the predefined Alice user
        $alice = UserBuilder::Alice();
        
        // Register and login Alice
        $this->userUtils->loginAs($alice);
        
        // Get Alice's details
        $response = $this->jsonGet('/api/users/me');
        
        // Assert the response contains the correct user data
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($alice->getEmail(), $response->get('email'));
    }
}
