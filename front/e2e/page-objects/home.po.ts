import { Page, Locator, expect } from '@playwright/test';

/**
 * Home Page Object
 * Encapsulates interactions with the home page
 */
export class HomePage {
  readonly page: Page;
  readonly heading: Locator;
  readonly serviceStatusIndicator: Locator;

  /**
   * Constructor
   * @param page Playwright page
   */
  constructor(page: Page) {
    this.page = page;
    this.heading = page.locator('h2');
    this.serviceStatusIndicator = page.getByTestId('service-status-indicator');
  }

  /**
   * Navigate to the home page
   */
  async goto(): Promise<void> {
    await this.page.goto('/');
  }

  /**
   * Wait for the page to be loaded
   */
  async waitForLoaded(): Promise<void> {
    await this.heading.waitFor({ state: 'visible' });
    await expect(this.heading).toHaveText('Home');
  }

  /**
   * Get the service status
   * @returns The current service status (online, offline, or unknown)
   */
  async getServiceStatus(): Promise<'online' | 'offline' | 'unknown'> {
    await this.serviceStatusIndicator.waitFor({ state: 'visible' });
    
    const classes = await this.serviceStatusIndicator.getAttribute('class') || '';
    
    if (classes.includes('status-online')) {
      return 'online';
    } else if (classes.includes('status-offline')) {
      return 'offline';
    } else {
      return 'unknown';
    }
  }

  /**
   * Wait for the service status to be a specific value
   * @param status The expected status
   * @param options Options for waiting
   */
  async waitForServiceStatus(
    status: 'online' | 'offline' | 'unknown', 
    options?: { timeout?: number }
  ): Promise<void> {
    const statusClass = `status-${status}`;
    await expect(this.serviceStatusIndicator).toHaveClass(
      new RegExp(statusClass), 
      { timeout: options?.timeout || 5000 }
    );
  }

  /**
   * Get the service status tooltip text
   * @returns The tooltip text
   */
  async getServiceStatusTooltip(): Promise<string> {
    return this.serviceStatusIndicator.getAttribute('title') || '';
  }
}

export default HomePage;
