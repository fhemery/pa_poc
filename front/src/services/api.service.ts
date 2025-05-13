/**
 * API Service
 * Handles all API calls to the backend
 */

const API_BASE_URL = 'http://localhost';

/**
 * Generic API error class
 */
export class ApiError extends Error {
  status: number;
  
  constructor(message: string, status: number) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
  }
}

/**
 * API Service
 */
export const apiService = {
 
  /**
   * Generic GET request
   * @param endpoint API endpoint
   * @returns Promise with the response data
   */
  async get<T>(endpoint: string): Promise<T> {
    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`);
      
      if (!response.ok) {
        throw new ApiError(`API returned status ${response.status}`, response.status);
      }
      
      return await response.json();
    } catch (error) {
      console.error(`Error fetching ${endpoint}:`, error);
      throw error;
    }
  },
  
  /**
   * Generic POST request
   * @param endpoint API endpoint
   * @param data Request payload
   * @returns Promise with the response data
   */
  async post<T>(endpoint: string, data: any): Promise<T> {
    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      });
      
      if (!response.ok) {
        throw new ApiError(`API returned status ${response.status}`, response.status);
      }
      
      return await response.json();
    } catch (error) {
      console.error(`Error posting to ${endpoint}:`, error);
      throw error;
    }
  }
};

export default apiService;
