<?php

namespace App\Tests\Utils;

use Symfony\Component\HttpFoundation\Response;

/**
 * ApiResponse class that wraps a Symfony Response with convenient methods for testing
 */
#[\AllowDynamicProperties] // Allow dynamic properties for PHP 8.2+ compatibility
class ApiResponse
{
    private int $statusCode;
    private array $jsonData;
    private array $headers;
    private Response $originalResponse;

    public function __construct(Response $response)
    {
        $this->originalResponse = $response;
        $this->statusCode = $response->getStatusCode();
        $this->jsonData = json_decode($response->getContent(), true) ?? [];
        
        // Convert headers to a simple associative array
        $this->headers = [];
        foreach ($response->headers->all() as $key => $values) {
            $this->headers[$key] = count($values) === 1 ? $values[0] : $values;
        }
    }

    /**
     * Get the HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the decoded JSON data
     */
    public function json(): array
    {
        return $this->jsonData;
    }

    /**
     * Get a specific value from the JSON data using dot notation
     * 
     * @param string $key The key to retrieve (supports dot notation for nested data)
     * @param mixed $default The default value to return if the key doesn't exist
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $data = $this->jsonData;
        
        if (strpos($key, '.') === false) {
            return $data[$key] ?? $default;
        }
        
        foreach (explode('.', $key) as $segment) {
            if (!is_array($data) || !array_key_exists($segment, $data)) {
                return $default;
            }
            
            $data = $data[$segment];
        }
        
        return $data;
    }

    /**
     * Check if the JSON data has a specific key
     */
    public function has(string $key): bool
    {
        return $this->get($key, $this) !== $this;
    }

    /**
     * Get all response headers
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific header value
     */
    public function header(string $name, mixed $default = null): mixed
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? $default;
    }

    /**
     * Check if the response has a specific header
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }
    
    /**
     * Check if the response is successful (2xx status code)
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Check if the response is a redirect (3xx status code)
     */
    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Check if the response is a client error (4xx status code)
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Check if the response is a server error (5xx status code)
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }

    /**
     * Get the original Symfony response object
     */
    public function getOriginalResponse(): Response
    {
        return $this->originalResponse;
    }
}
