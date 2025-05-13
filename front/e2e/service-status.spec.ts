import { test, expect } from '@playwright/test';
import { HomePage } from './pages/home.po';

// Test the service status indicator
test.describe('Service Status Indicator', () => {
  let homePage: HomePage;

  // Before each test, create a new HomePage instance
  test.beforeEach(({ page }) => {
    homePage = new HomePage(page);
  });

  // Test that the indicator shows green when API is available
  test('shows green when API is available', async ({ request }) => {
    // First, verify that the backend is actually running
    // by making a direct request to the ping endpoint
    const apiResponse = await request.get('http://localhost/api/ping');
    
    // Skip the test if the backend is not available
    test.skip(!apiResponse.ok(), 'Backend API is not available, skipping test');
    
    // Navigate to the home page
    await homePage.goto();
    
    // Wait for the page to be loaded
    await homePage.waitForLoaded();

    // Wait for the service status to be online
    await homePage.waitForServiceStatus('online', { timeout: 5000 });
    
    // Check the tooltip
    const tooltipText = await homePage.getServiceStatusTooltip();
    expect(tooltipText).toBe('Service is online');
  });
});
