/**
 * Ping API Service
 * Handles API calls related to the ping endpoint
 */

import apiService from './api.service';

/**
 * Response type for the ping endpoint
 */
export interface PingResponse {
  status: string;
}

/**
 * Ping API Service
 */
export const pingApiService = {
  /**
   * Check if the API is available by calling the ping endpoint
   * @returns Promise with the ping response
   */
  async checkStatus(): Promise<PingResponse> {
    try {
      return await apiService.get<PingResponse>('/api/ping');
    } catch (error) {
      console.error('Error checking API status:', error);
      throw error;
    }
  }
};

export default pingApiService;
