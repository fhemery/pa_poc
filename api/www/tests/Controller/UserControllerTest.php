<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    public function testUsersMeEndpointWithoutAuthentication(): void
    {
        $response = $this->jsonGet('/api/users/me');
        
        $this->assertEquals(
            Response::HTTP_UNAUTHORIZED, 
            $response->getStatusCode()
        );
    }
    
    public function testAuthenticationFlow(): void
    {
        $email = 'test' . uniqid() . '@example.com';
        $password = 'Test123!';
        
        $loginResponse = $this->getUserUtils()->registerAndLoginUser($email, $password);
        $this->assertArrayHasKey('token', $loginResponse);
        
        $response = $this->jsonGet('/api/users/me');
        
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );
        
        $this->assertEquals($email, $response->get('email'));
        
        $this->getUserUtils()->logoutUser();
        
        $response = $this->jsonGet('/api/users/me');
        $this->assertEquals(
            Response::HTTP_UNAUTHORIZED,
            $response->getStatusCode(),
            'The response status code should be 401 after logout'
        );
    }
}
