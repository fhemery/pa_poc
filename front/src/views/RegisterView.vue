<template>
  <div class="register-container">
    <h1>Create an Account</h1>
    
    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>
    
    <form @submit.prevent="handleRegister" class="register-form">
      <div class="form-group">
        <label for="firstName">First Name</label>
        <input 
          id="firstName"
          v-model="firstName"
          type="text"
          required
          placeholder="Enter your first name"
        />
      </div>
      
      <div class="form-group">
        <label for="lastName">Last Name</label>
        <input 
          id="lastName"
          v-model="lastName"
          type="text"
          required
          placeholder="Enter your last name"
        />
      </div>
      
      <div class="form-group">
        <label for="email">Email</label>
        <input 
          id="email"
          v-model="email"
          type="email"
          required
          placeholder="Enter your email"
        />
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input 
          id="password"
          v-model="password"
          type="password"
          required
          placeholder="Create a password"
          minlength="8"
        />
        <div class="password-hint">Password must be at least 8 characters</div>
      </div>
      
      <div class="form-actions">
        <button 
          type="submit" 
          class="register-button"
          :disabled="isLoading"
        >
          {{ isLoading ? 'Creating Account...' : 'Create Account' }}
        </button>
        
        <div class="login-link">
          Already have an account? 
          <router-link :to="{ name: 'login' }">Login</router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Form state
const firstName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

// Handle registration form submission
const handleRegister = async () => {
  errorMessage.value = ''
  isLoading.value = true
  
  try {
    const success = await authStore.register({
      firstName: firstName.value,
      lastName: lastName.value,
      email: email.value,
      password: password.value
    })
    
    if (success) {
      // Registration successful and user logged in automatically
      router.push('/')
    } else {
      errorMessage.value = 'Registration failed. Please try again.'
    }
  } catch (error: any) {
    console.error('Registration error:', error)
    if (error.status === 409) {
      errorMessage.value = 'Email already in use. Please use a different email.'
    } else {
      errorMessage.value = 'An error occurred during registration. Please try again.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.register-container {
  max-width: 400px;
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

.error-message {
  background-color: #ffebee;
  color: #d32f2f;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 16px;
  font-size: 14px;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

label {
  font-weight: 500;
  font-size: 14px;
}

input {
  padding: 10px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

.password-hint {
  font-size: 12px;
  color: #666;
  margin-top: 4px;
}

.form-actions {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-top: 8px;
}

.register-button {
  background-color: #1976d2;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 12px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.register-button:hover {
  background-color: #1565c0;
}

.register-button:disabled {
  background-color: #90caf9;
  cursor: not-allowed;
}

.login-link {
  text-align: center;
  font-size: 14px;
}

a {
  color: #1976d2;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}
</style>
