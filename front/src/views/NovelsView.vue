<template>
  <div class="novels-container">
    <h1>Browse Novels</h1>
    <p class="placeholder-message">Novel listing will be implemented in a future update.</p>
    
    <!-- Placeholder for novel list -->
    <div class="novel-grid">
      <div v-for="i in 6" :key="i" class="novel-card placeholder">
        <div class="novel-cover"></div>
        <div class="novel-info">
          <div class="novel-title"></div>
          <div class="novel-author"></div>
          <div class="novel-rating"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// This is a placeholder component for the novels listing page
// It will be implemented with actual data in a future update
</script>

<style scoped>
.novels-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  margin-bottom: 20px;
  color: #333;
}

.placeholder-message {
  background-color: #e3f2fd;
  padding: 15px;
  border-radius: 4px;
  margin-bottom: 30px;
  color: #0d47a1;
}

.novel-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 25px;
}

.novel-card {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  background-color: white;
}

.novel-card:hover {
  transform: translateY(-5px);
}

.novel-cover {
  height: 200px;
  background-color: #e0e0e0;
}

.novel-info {
  padding: 15px;
}

.novel-title {
  height: 20px;
  background-color: #e0e0e0;
  margin-bottom: 10px;
  border-radius: 4px;
}

.novel-author {
  height: 15px;
  background-color: #e0e0e0;
  width: 70%;
  margin-bottom: 10px;
  border-radius: 4px;
}

.novel-rating {
  height: 15px;
  background-color: #e0e0e0;
  width: 50%;
  border-radius: 4px;
}
</style>
