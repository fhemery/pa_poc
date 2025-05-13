import { Page } from '@playwright/test';

/**
 * Helper functions for E2E tests
 */
export const testUtils = {
  /**
   * Mock API responses for testing
   */
  mockResponses: {
    // Mock API response for successful ping
    pingSuccess: {
      status: 200,
      body: JSON.stringify({ status: 'ok' })
    },
    // Mock API response for failed ping
    pingFailure: {
      status: 500,
      body: JSON.stringify({ error: 'Internal Server Error' })
    },
    // Mock API response for network error
    networkError: {
      status: 0,
      body: 'Network error'
    }
  },

  /**
   * Setup API mocks for testing
   * @param page Playwright page
   */
  setupApiMocks: async (page: Page) => {
    // Mock the ping endpoint
    await page.route('**/api/ping', route => {
      route.fulfill(testUtils.mockResponses.pingSuccess);
    });
  },

  /**
   * Get the computed style property of an element
   * @param page Playwright page
   * @param selector CSS selector for the element
   * @param property CSS property to get
   */
  getComputedStyle: async (page: Page, selector: string, property: string): Promise<string> => {
    return page.locator(selector).evaluate((el, prop) => {
      return window.getComputedStyle(el).getPropertyValue(prop);
    }, property);
  }
};

export default testUtils;
