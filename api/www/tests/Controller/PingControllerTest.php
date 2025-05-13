<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class PingControllerTest extends ApiTestCase
{
    public function testPingEndpoint(): void
    {
        // Make a GET request to the ping endpoint
        $this->jsonRequest('GET', '/api/ping');
        
        // Assert that the response status code is 200
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // Assert that the response is in JSON format
        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json'),
            'The response content type is not application/json'
        );
        
        // Get the JSON response
        $jsonContent = $this->getJsonResponse();
        
        // Assert that the response contains the expected data
        $this->assertIsArray($jsonContent);
        $this->assertArrayHasKey('status', $jsonContent);
        $this->assertEquals('ok', $jsonContent['status']);
    }
}
