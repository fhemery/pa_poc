import { defineStore } from 'pinia'
import apiService from '@/services/api.service'
import authApiService from '@/services/auth.api.service'

interface User {
  id: number
  email: string
  firstName: string
  lastName: string
}

interface AuthState {
  user: User | null
  accessToken: string | null
  refreshToken: string | null
  tokenExpiry: string | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    accessToken: localStorage.getItem('accessToken') || null,
    refreshToken: localStorage.getItem('refreshToken') || null,
    tokenExpiry: localStorage.getItem('tokenExpiry') || null
  }),

  getters: {
    isAuthenticated: (state) => !!state.accessToken,
    tokenExpiresIn: (state): number => {
      if (!state.tokenExpiry) return 0
      return Math.max(0, (new Date(state.tokenExpiry).getTime() - new Date().getTime()) / 1000)
    },
    shouldRefreshToken: (state): boolean => {
      // Refresh if less than 5 minutes remaining (300 seconds)
      return state.tokenExpiry !== null && (new Date(state.tokenExpiry).getTime() - new Date().getTime()) / 1000 < 300
    },
    fullName: (state): string | null => {
      if (!state.user) return null
      return `${state.user.firstName} ${state.user.lastName}`
    }
  },

  actions: {
    setTokens(accessToken: string, refreshToken: string, expiresIn: number) {
      this.accessToken = accessToken
      this.refreshToken = refreshToken
      this.tokenExpiry = new Date(Date.now() + expiresIn * 1000).toISOString()

      // Use the API service to store tokens
      apiService.setAuthTokens(accessToken, refreshToken, expiresIn)
    },

    clearTokens() {
      this.accessToken = null
      this.refreshToken = null
      this.tokenExpiry = null
      this.user = null

      // Use the API service to clear tokens
      apiService.clearAuthTokens()
    },

    async login(email: string, password: string): Promise<boolean> {
      try {
        const response = await authApiService.login(email, password)
        const { accessToken, refreshToken, expiresIn } = response
        this.setTokens(accessToken, refreshToken, expiresIn)
        await this.fetchUserProfile()
        return true
      } catch (error) {
        console.error('Login failed:', error)
        return false
      }
    },

    async register(userData: {
      email: string
      password: string
      firstName: string
      lastName: string
    }): Promise<boolean> {
      try {
        await authApiService.register(userData)
        // Automatically log in after successful registration
        return await this.login(userData.email, userData.password)
      } catch (error) {
        console.error('Registration failed:', error)
        return false
      }
    },

    async logout(): Promise<void> {
      try {
        if (this.refreshToken) {
          await authApiService.logout(this.refreshToken)
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearTokens()
      }
    },

    async refreshAccessToken(): Promise<boolean> {
      if (!this.refreshToken) return false

      try {
        const response = await authApiService.refreshToken(this.refreshToken)
        const { accessToken, refreshToken, expiresIn } = response
        this.setTokens(accessToken, refreshToken, expiresIn)
        return true
      } catch (error) {
        console.error('Token refresh failed:', error)
        this.clearTokens()
        return false
      }
    },

    async fetchUserProfile(): Promise<boolean> {
      if (!this.accessToken) return false

      try {
        const userProfile = await authApiService.getUserProfile()
        this.user = userProfile
        return true
      } catch (error) {
        console.error('Failed to fetch user profile:', error)
        return false
      }
    },

    async checkAuth(): Promise<boolean> {
      // If we have a token but it's close to expiring, refresh it
      if (this.isAuthenticated && this.shouldRefreshToken) {
        await this.refreshAccessToken()
      }

      // If we have a token but no user data, fetch the profile
      if (this.isAuthenticated && !this.user) {
        return await this.fetchUserProfile()
      }

      return this.isAuthenticated
    }
  }
})
