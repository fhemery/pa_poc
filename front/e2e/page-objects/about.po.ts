import { Page, Locator, expect } from '@playwright/test';

/**
 * About Page Object
 * Encapsulates interactions with the about page
 */
export class AboutPage {
  readonly page: Page;
  readonly heading: Locator;

  /**
   * Constructor
   * @param page Playwright page
   */
  constructor(page: Page) {
    this.page = page;
    this.heading = page.locator('h1');
  }

  /**
   * Navigate to the about page
   */
  async goto(): Promise<void> {
    await this.page.goto('/about');
  }

  /**
   * Wait for the page to be loaded
   */
  async waitForLoaded(): Promise<void> {
    await this.heading.waitFor({ state: 'visible' });
    await expect(this.heading).toHaveText('About');
  }

  /**
   * Check if we're on the about page
   */
  async isCurrentPage(): Promise<boolean> {
    return this.page.url().includes('/about');
  }
}

export default AboutPage;
