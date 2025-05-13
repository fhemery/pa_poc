# Novel Review Frontend

Vue.js frontend for the Novel Review application. This application communicates with the Symfony backend API.

## Prerequisites

- Node.js (v18 or higher)
- pnpm (v8 or higher)
- Running backend API (see [API README](../api/README.md))

## Quick Start

1. Install dependencies:
   ```sh
   pnpm install
   ```

2. Start the development server:
   ```sh
   pnpm dev
   ```

3. Access the application at http://localhost:5173

## Development Workflow

### Project Structure

```
src/
├── assets/       # Static assets (CSS, images)
├── components/   # Vue components
├── services/     # API services
├── views/        # Page components
└── App.vue       # Root component
```

### API Communication

The application communicates with the backend API at http://localhost. API services are located in `src/services/`.

## Testing

### E2E Testing with Playwright

The application uses Playwright for E2E testing with a Page Object Model pattern.

1. Install Playwright browsers (first time only):
   ```sh
   pnpm test:e2e:install
   ```

2. Make sure the backend API is running:
   ```sh
   cd ../api
   docker compose up -d
   ```

3. Start the development server (if not already running):
   ```sh
   pnpm dev
   ```

4. Run the E2E tests:
   ```sh
   pnpm test:e2e
   ```

5. Run tests with UI mode (for debugging):
   ```sh
   pnpm test:e2e:ui
   ```

6. View the test report:
   ```sh
   pnpm test:e2e:report
   ```

### Test Structure

```
e2e/
├── pages/         # Page Object Model classes
│   ├── home.po.ts        # Home page interactions
│   └── about.po.ts       # About page interactions
├── service-status.spec.ts # Tests for service status indicator
└── test-utils.ts         # Test utilities and helpers
```

## Building for Production

```sh
pnpm build
```

This creates a `dist` directory with production-ready files.
