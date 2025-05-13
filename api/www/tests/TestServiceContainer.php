<?php

namespace App\Tests;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Helper trait for mocking services in tests
 */
trait TestServiceContainer
{
    /**
     * Replace a service in the container with a mock
     *
     * @param string $id The service ID to replace
     * @param object $mock The mock object to use as a replacement
     * @return void
     */
    protected function mockService(string $id, object $mock): void
    {
        if (!property_exists($this, 'container') || !$this->container instanceof ContainerInterface) {
            throw new \LogicException('The TestServiceContainer trait requires a "container" property that implements ContainerInterface');
        }

        $container = $this->container;
        
        // Get the private container services property using reflection
        $servicesReflection = new \ReflectionProperty($container, 'services');
        $servicesReflection->setAccessible(true);
        
        // Get the current services
        $services = $servicesReflection->getValue($container);
        
        // Replace the service with our mock
        $services[$id] = $mock;
        
        // Update the services property
        $servicesReflection->setValue($container, $services);
    }
}
