/**
 * Authentication Plugin
 * Handles authentication state, token refreshing, and route protection
 */

import { useAuthStore } from '@/stores/auth'
import type { Router as VueRouter } from 'vue-router'

/**
 * Setup authentication
 * @param router Vue Router instance
 */
export function setupAuth(router: VueRouter) {
  const authStore = useAuthStore()

  // Check authentication status on app startup
  authStore.checkAuth()

  // Setup navigation guards for protected routes
  router.beforeEach(async (to, from, next) => {
    // Check if the route requires authentication
    if (to.meta.requiresAuth) {
      // Verify authentication status
      const isAuthenticated = await authStore.checkAuth()
      
      if (!isAuthenticated) {
        // Redirect to login page with return URL
        next({ 
          name: 'login', 
          query: { redirect: to.fullPath } 
        })
        return
      }
    }
    
    // If the route is for guests only (like login/register) and user is authenticated
    if (to.meta.guestOnly && authStore.isAuthenticated) {
      // Redirect to home or dashboard
      next({ name: 'home' })
      return
    }
    
    // Continue navigation
    next()
  })

  // Set up automatic token refresh
  setInterval(() => {
    if (authStore.isAuthenticated && authStore.shouldRefreshToken) {
      authStore.refreshAccessToken()
    }
  }, 60000) // Check every minute
}
