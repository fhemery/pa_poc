/**
 * API Service
 * Handles all API calls to the backend
 */

const API_BASE_URL = 'http://localhost';

// Token storage keys
const ACCESS_TOKEN_KEY = 'accessToken';
const REFRESH_TOKEN_KEY = 'refreshToken';
const TOKEN_EXPIRY_KEY = 'tokenExpiry';

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
 * Get the stored access token
 * @returns The access token or null if not found
 */
function getAccessToken(): string | null {
  return localStorage.getItem(ACCESS_TOKEN_KEY);
}

/**
 * Get the stored refresh token
 * @returns The refresh token or null if not found
 */
function getRefreshToken(): string | null {
  return localStorage.getItem(REFRESH_TOKEN_KEY);
}

/**
 * Check if the access token is expired or about to expire
 * @returns True if the token is expired or will expire in less than 5 minutes
 */
function isTokenExpired(): boolean {
  const expiryString = localStorage.getItem(TOKEN_EXPIRY_KEY);
  if (!expiryString) return true;
  
  const expiryTime = new Date(expiryString).getTime();
  const currentTime = new Date().getTime();
  
  // Token is considered expired if it expires in less than 5 minutes (300000 ms)
  return expiryTime - currentTime < 300000;
}

/**
 * API Service
 */
export const apiService = {
 
  /**
   * Generic GET request
   * @param endpoint API endpoint
   * @param requiresAuth Whether the request requires authentication
   * @returns Promise with the response data
   */
  async get<T>(endpoint: string, requiresAuth: boolean = false): Promise<T> {
    try {
      const headers: HeadersInit = {
        'Content-Type': 'application/json',
      };
      
      if (requiresAuth) {
        const token = getAccessToken();
        if (token) {
          headers['Authorization'] = `Bearer ${token}`;
        }
      }
      
      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        headers
      });
      
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
   * @param requiresAuth Whether the request requires authentication
   * @returns Promise with the response data
   */
  async post<T>(endpoint: string, data: any, requiresAuth: boolean = false): Promise<T> {
    try {
      const headers: HeadersInit = {
        'Content-Type': 'application/json',
      };
      
      if (requiresAuth) {
        const token = getAccessToken();
        if (token) {
          headers['Authorization'] = `Bearer ${token}`;
        }
      }
      
      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        method: 'POST',
        headers,
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
  },
  
  /**
   * Set authentication tokens in local storage
   * @param accessToken The JWT access token
   * @param refreshToken The refresh token
   * @param expiresIn Token expiration time in seconds
   */
  setAuthTokens(accessToken: string, refreshToken: string, expiresIn: number): void {
    localStorage.setItem(ACCESS_TOKEN_KEY, accessToken);
    localStorage.setItem(REFRESH_TOKEN_KEY, refreshToken);
    
    // Calculate and store expiry time
    const expiryTime = new Date(Date.now() + expiresIn * 1000);
    localStorage.setItem(TOKEN_EXPIRY_KEY, expiryTime.toISOString());
  },
  
  /**
   * Clear authentication tokens from local storage
   */
  clearAuthTokens(): void {
    localStorage.removeItem(ACCESS_TOKEN_KEY);
    localStorage.removeItem(REFRESH_TOKEN_KEY);
    localStorage.removeItem(TOKEN_EXPIRY_KEY);
  },
  
  /**
   * Check if the user is authenticated
   * @returns True if the user has a valid access token
   */
  isAuthenticated(): boolean {
    return !!getAccessToken() && !isTokenExpired();
  }
};

export default apiService;
