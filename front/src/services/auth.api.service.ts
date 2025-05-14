/**
 * Authentication API Service
 * Handles API calls related to authentication
 */

import apiService from './api.service';

/**
 * Login response type
 */
export interface LoginResponse {
  accessToken: string;
  refreshToken: string;
  expiresIn: number;
  refreshExpiresIn: number;
}

/**
 * User profile response type
 */
export interface UserProfileResponse {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
}

/**
 * Registration request type
 */
export interface RegistrationRequest {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
}

/**
 * Authentication API Service
 */
export const authApiService = {
  /**
   * Login with email and password
   * @param email User email
   * @param password User password
   * @returns Promise with login response
   */
  async login(email: string, password: string): Promise<LoginResponse> {
    return await apiService.post<LoginResponse>('/api/login', { email, password });
  },

  /**
   * Register a new user
   * @param userData User registration data
   * @returns Promise with registration response
   */
  async register(userData: RegistrationRequest): Promise<{ message: string }> {
    return await apiService.post<{ message: string }>('/api/public/register', userData);
  },

  /**
   * Logout the current user
   * @param refreshToken The refresh token to invalidate
   * @returns Promise with logout response
   */
  async logout(refreshToken: string): Promise<{ message: string }> {
    return await apiService.post<{ message: string }>('/api/logout', { refreshToken }, true);
  },

  /**
   * Refresh the access token using a refresh token
   * @param refreshToken The refresh token
   * @returns Promise with the new tokens
   */
  async refreshToken(refreshToken: string): Promise<LoginResponse> {
    return await apiService.post<LoginResponse>('/api/refresh-token', { refreshToken });
  },

  /**
   * Get the current user's profile
   * @returns Promise with the user profile
   */
  async getUserProfile(): Promise<UserProfileResponse> {
    return await apiService.get<UserProfileResponse>('/api/users/me', true);
  }
};

export default authApiService;
