<template>
  <div class="create-review-container">
    <h1>Write a Review</h1>
    
    <div class="auth-check" v-if="!authStore.isAuthenticated">
      <p>You need to be logged in to write a review.</p>
      <router-link to="/login" class="login-link">Login</router-link>
    </div>
    
    <div v-else class="review-form-container">
      <div class="placeholder-message">
        Review creation will be implemented in a future update.
      </div>
      
      <form @submit.prevent="handleSubmit" class="review-form">
        <div class="form-group">
          <label for="novel">Novel</label>
          <select id="novel" v-model="selectedNovel" required class="form-control">
            <option value="" disabled>Select a novel</option>
            <option value="placeholder">Placeholder Novel</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="rating">Rating</label>
          <div class="rating-selector">
            <div 
              v-for="star in 5" 
              :key="star" 
              class="star" 
              :class="{ active: star <= rating }"
              @click="rating = star"
            >
              ★
            </div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="title">Review Title</label>
          <input 
            id="title" 
            v-model="title" 
            type="text" 
            required 
            placeholder="Give your review a title"
            class="form-control"
          />
        </div>
        
        <div class="form-group">
          <label for="content">Review Content</label>
          <textarea 
            id="content" 
            v-model="content" 
            required 
            placeholder="Write your review here..."
            class="form-control"
            rows="8"
          ></textarea>
        </div>
        
        <div class="form-actions">
          <button type="submit" class="submit-button">Submit Review</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

// Form state
const selectedNovel = ref('');
const rating = ref(0);
const title = ref('');
const content = ref('');

// Handle form submission
const handleSubmit = () => {
  // This is a placeholder - in a real implementation, we would submit the review
  alert('Review submission will be implemented in a future update.');
  
  // Reset form
  selectedNovel.value = '';
  rating.value = 0;
  title.value = '';
  content.value = '';
  
  // Navigate back to novels
  router.push('/novels');
};
</script>

<style scoped>
.create-review-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  margin-bottom: 30px;
  color: #333;
}

.auth-check {
  background-color: #fff3e0;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  margin-bottom: 30px;
}

.login-link {
  display: inline-block;
  margin-top: 10px;
  background-color: #42b883;
  color: white;
  padding: 8px 20px;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 500;
}

.placeholder-message {
  background-color: #e3f2fd;
  padding: 15px;
  border-radius: 4px;
  margin-bottom: 30px;
  color: #0d47a1;
}

.review-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

label {
  font-weight: 500;
  color: #333;
}

.form-control {
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

.rating-selector {
  display: flex;
  gap: 10px;
}

.star {
  font-size: 30px;
  color: #ddd;
  cursor: pointer;
  transition: color 0.2s;
}

.star:hover, .star.active {
  color: #ffc107;
}

.form-actions {
  margin-top: 10px;
}

.submit-button {
  background-color: #42b883;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 12px 24px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.submit-button:hover {
  background-color: #3aa876;
}
</style>
