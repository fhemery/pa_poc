<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class PingControllerTest extends ApiTestCase
{
    public function testPingEndpoint(): void
    {
        // Make a GET request to the ping endpoint
        $response = $this->jsonGet('/api/ping');
        
        // Assert that the response status code is 200
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());    
        
        // Assert that the response contains the expected data
        $this->assertEquals('ok', $response->get('status'));
    }
}
