# Novel Reviewing Site

A web application for reviewing novels built with Symfony 7.2 and Vue.js.

## Project Structure

This project is organized into two main components:

- **API Backend**: A Symfony 7.2 application with MySQL database
- **Frontend**: A Vue.js application with service status monitoring

## Development

### Prerequisites

- Docker and Docker Compose (for backend)
- Node.js v18+ (for frontend)
- pnpm v8+ (for frontend package management)

### Getting Started

1. Clone the repository

2. Setup back-end, using [API README](api/README.md).

3. Setup front-end, using [Frontend README](front/README.md).

4. Access the applications:
   - Backend API: http://localhost
   - Frontend: http://localhost:5173

### Testing

#### Backend Testing

The project includes comprehensive testing for the API backend. See the [API README](api/README.md#testing) for details on running tests.

#### Frontend Testing

The frontend includes E2E tests using Playwright with a Page Object Model pattern. See the [Frontend README](front/README.md#testing) for details on running tests.
