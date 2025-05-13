<?php

namespace App\Tests;

use App\Tests\Utils\ApiResponse;
use App\Tests\Utils\UserBuilder;
use App\Tests\Utils\UserTestUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiTestCase extends WebTestCase
{
    use TestServiceContainer;

    protected KernelBrowser $client;
    protected ContainerInterface $container;
    protected ?EntityManagerInterface $entityManager = null;
    protected ?string $jwtToken = null;
    protected ?array $currentUser = null;
    protected ?UserTestUtils $userUtils = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = static::getContainer();
        
        // Disable reboot between requests to maintain the database connection
        $this->client->disableReboot();
        
        // Get the entity manager if it's available
        if ($this->container->has(EntityManagerInterface::class)) {
            $this->entityManager = $this->container->get(EntityManagerInterface::class);
            
            // Set up the database schema for testing
            $this->setupDatabaseSchema();
        }
        
        // Initialize the user utils
        $this->userUtils = new UserTestUtils($this);
        
        // Create standard test users (Alice and Bob)
        $this->createStandardTestUsers();
        
    }

    /**
     * Create standard test users (Alice and Bob) that can be used in all tests
     */
    protected function createStandardTestUsers(): void
    {
        try {
            // Try to create Alice
            $alice = UserBuilder::Alice();
            $this->userUtils->registerAs($alice);
            
            // Try to create Bob
            $bob = UserBuilder::Bob();
            $this->userUtils->registerAs($bob);
        } catch (\Exception $e) {
            // Users might already exist, which is fine
            // We'll just continue with the test
        }
    }
    
    protected function tearDown(): void
    {
        // Rollback the transaction to clean up after the test
        if ($this->entityManager && $this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->rollBack();
        }
        
        // If we have an entity manager, close it to avoid memory leaks
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }
        
        $this->jwtToken = null;
        $this->currentUser = null;
        $this->userUtils = null;
        
        parent::tearDown();
    }
    
    /**
     * Set up the database schema for testing
     */
    protected function setupDatabaseSchema(): void
    {
        if (!$this->entityManager) {
            return;
        }
        
        // Create database schema if it doesn't exist
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        
        // Drop and recreate schema to ensure a clean state
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
        
        // Explicitly begin a transaction to keep the connection alive
        $this->entityManager->getConnection()->beginTransaction();
    }

    /**
     * Make a JSON request to the API
     */
    public function jsonRequest(string $method, string $uri, array $parameters = [], array $files = [], array $server = [], ?string $content = null, bool $changeHistory = true): ApiResponse
    {
        $server = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $server);

        // Add JWT token if available
        if ($this->jwtToken !== null) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer ' . $this->jwtToken;
        }

        if ($content === null && !empty($parameters)) {
            $content = json_encode($parameters);
        }

        $this->client->request($method, $uri, [], $files, $server, $content, $changeHistory);
        $response = $this->client->getResponse();
        
        // Automatically extract JWT token from response if present
        $this->extractJwtTokenFromResponse($response);
        
        // Return an ApiResponse object instead of the raw Symfony Response
        return new ApiResponse($response);
    }
    
    /**
     * Extract JWT token from response if present
     */
    private function extractJwtTokenFromResponse(Response $response): void
    {
        if ($response->getStatusCode() === Response::HTTP_OK || $response->getStatusCode() === Response::HTTP_CREATED) {
            $data = json_decode($response->getContent(), true);
            
            if (isset($data['accessToken'])) {
                $this->jwtToken = $data['accessToken'];
            }
            
            if (isset($data['user'])) {
                $this->currentUser = $data['user'];
            }
        }
        
        $requestUri = $this->client->getRequest()->getUri();
        if (strpos($requestUri, '/api/logout') !== false && $response->getStatusCode() === Response::HTTP_OK) {
            $this->jwtToken = null;
        }
    }

    /**
     * Make a GET request with JSON headers
     */
    public function jsonGet(string $uri, array $parameters = [], array $server = []): ApiResponse
    {
        return $this->jsonRequest('GET', $uri, $parameters, [], $server);
    }

    /**
     * Make a POST request with JSON headers
     */
    public function jsonPost(string $uri, array $parameters = [], array $server = []): ApiResponse
    {
        return $this->jsonRequest('POST', $uri, $parameters, [], $server);
    }

    /**
     * Make a PUT request with JSON headers
     */
    public function jsonPut(string $uri, array $parameters = [], array $server = []): ApiResponse
    {
        return $this->jsonRequest('PUT', $uri, $parameters, [], $server);
    }

    /**
     * Make a DELETE request with JSON headers
     */
    public function jsonDelete(string $uri, array $parameters = [], array $server = []): ApiResponse
    {
        return $this->jsonRequest('DELETE', $uri, $parameters, [], $server);
    }

    /**
     * Get the user utils
     */
    public function getUserUtils(): UserTestUtils
    {
        return $this->userUtils;
    }
}
