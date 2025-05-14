<template>
  <div class="profile-container">
    <h1>My Profile</h1>
    
    <div v-if="isLoading" class="loading">
      Loading profile...
    </div>
    
    <div v-else-if="error" class="error-message">
      {{ error }}
    </div>
    
    <div v-else class="profile-content">
      <div class="profile-header">
        <div class="profile-avatar">
          {{ initials }}
        </div>
        <div class="profile-name">
          <h2>{{ authStore.fullName }}</h2>
          <p class="email">{{ authStore.user?.email }}</p>
        </div>
      </div>
      
      <div class="profile-details">
        <div class="detail-item">
          <span class="label">First Name:</span>
          <span class="value">{{ authStore.user?.firstName }}</span>
        </div>
        
        <div class="detail-item">
          <span class="label">Last Name:</span>
          <span class="value">{{ authStore.user?.lastName }}</span>
        </div>
        
        <div class="detail-item">
          <span class="label">Email:</span>
          <span class="value">{{ authStore.user?.email }}</span>
        </div>
        
        <div class="detail-item">
          <span class="label">Account ID:</span>
          <span class="value">{{ authStore.user?.id }}</span>
        </div>
      </div>
      
      <div class="profile-actions">
        <button @click="handleLogout" class="logout-button">
          Logout
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const isLoading = ref(true)
const error = ref('')

// Compute user initials for avatar
const initials = computed(() => {
  if (!authStore.user) return '?'
  
  const first = authStore.user.firstName.charAt(0)
  const last = authStore.user.lastName.charAt(0)
  return `${first}${last}`
})

// Load user profile data
onMounted(async () => {
  try {
    await authStore.fetchUserProfile()
  } catch (err) {
    error.value = 'Failed to load profile data'
    console.error('Profile error:', err)
  } finally {
    isLoading.value = false
  }
})

// Handle logout
const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.profile-container {
  max-width: 600px;
  margin: 40px auto;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  background-color: white;
}

h1 {
  text-align: center;
  margin-bottom: 24px;
  color: #333;
}

.loading {
  text-align: center;
  padding: 20px;
  color: #666;
}

.error-message {
  background-color: #ffebee;
  color: #d32f2f;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 16px;
  font-size: 14px;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #1976d2;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  font-weight: bold;
}

.profile-name {
  flex: 1;
}

.profile-name h2 {
  margin: 0;
  font-size: 24px;
  color: #333;
}

.email {
  margin: 4px 0 0;
  color: #666;
  font-size: 14px;
}

.profile-details {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 30px;
}

.detail-item {
  display: flex;
  border-bottom: 1px solid #eee;
  padding-bottom: 8px;
}

.label {
  width: 120px;
  font-weight: 500;
  color: #666;
}

.value {
  flex: 1;
  color: #333;
}

.profile-actions {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.logout-button {
  background-color: #f44336;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.logout-button:hover {
  background-color: #d32f2f;
}
</style>
