# API Backend (Symfony 7.2)

## Setup and Installation

### Starting the Docker Environment

To start the Docker environment, run the following command from the `api` directory:

```bash
docker compose up -d
```

### Running Composer Commands

To run Composer commands after setup:

```bash
docker compose exec php-fpm composer require some/package
```

## Testing

The project uses PHPUnit for testing and includes a custom test runner script that simplifies running tests and watching for changes.

### Running Tests

From the `api` directory, you can use the test runner script:

```bash
# Run all tests
./run-tests.sh

# Run a specific test file
./run-tests.sh tests/Controller/PingControllerTest.php

# Run tests with a specific method name filter
./run-tests.sh -f testPingEndpoint
```

### Watch Mode

The test runner includes a watch mode that automatically re-runs tests when files change:

```bash
# Watch all tests
./run-tests.sh -w

# Watch a specific test file
./run-tests.sh -w tests/Controller/PingControllerTest.php
```

> **Note:** Watch mode requires `inotify-tools` to be installed. If it's not installed, the script will prompt you to install it with `sudo apt-get install inotify-tools`.

### Test Structure

Tests are organized in the `tests/` directory:

- `ApiTestCase.php`: Base class for API tests with helper methods
- `TestServiceContainer.php`: Trait for mocking services in tests
- `Controller/`: Controller tests

### Writing Tests

Extend the `ApiTestCase` class for API tests:

```php
namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class YourControllerTest extends ApiTestCase
{
    public function testYourEndpoint(): void
    {
        // Make a request
        $this->jsonRequest('GET', '/api/your-endpoint');
        
        // Assert response
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // Get JSON response
        $jsonContent = $this->getJsonResponse();
        
        // Make assertions
        $this->assertIsArray($jsonContent);
    }
}
```