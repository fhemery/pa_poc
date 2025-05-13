<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ApiTestCase extends WebTestCase
{
    use TestServiceContainer;

    protected KernelBrowser $client;
    protected ContainerInterface $container;
    protected ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = static::getContainer();
        
        // Get the entity manager if it's available
        if ($this->container->has(EntityManagerInterface::class)) {
            $this->entityManager = $this->container->get(EntityManagerInterface::class);
        }
    }

    protected function tearDown(): void
    {
        // If we have an entity manager, close it to avoid memory leaks
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }
        
        parent::tearDown();
    }

    /**
     * Make a JSON request to the API
     */
    protected function jsonRequest(string $method, string $uri, array $parameters = [], array $files = [], array $server = [], string $content = null, bool $changeHistory = true): void
    {
        $server = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $server);

        if ($content === null && !empty($parameters)) {
            $content = json_encode($parameters);
        }

        $this->client->request($method, $uri, [], $files, $server, $content, $changeHistory);
    }

    /**
     * Get the JSON response from the client
     */
    protected function getJsonResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
