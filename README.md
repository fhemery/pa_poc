# Novel Reviewing Site

A web application for reviewing novels built with Symfony 7.2 and Vue.js.

## Project Structure

This project is organized into two main components:

- **API Backend**: A Symfony 7.2 application with MySQL database
- **Frontend**: A Vue.js application (planned)

## Components

### API Backend

The API backend is a Symfony 7.2 application that provides the REST API endpoints for the novel reviewing functionality. It uses MySQL for data storage and is containerized with Docker.

For detailed information about the API backend, including setup instructions and testing procedures, see the [API README](api/README.md).

### Frontend (Planned)

The frontend will be a Vue.js application that consumes the API endpoints provided by the backend.

## Development

### Prerequisites

- Docker and Docker Compose
- Node.js and npm (for frontend development)

### Getting Started

1. Clone the repository
2. Start the API backend:
   ```bash
   cd api
   docker compose up -d
   ```
3. Access the API at http://localhost

### Testing

The project includes comprehensive testing for the API backend. See the [API README](api/README.md#testing) for details on running tests.
