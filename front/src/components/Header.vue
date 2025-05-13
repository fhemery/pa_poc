<script setup lang="ts">
import { RouterLink } from 'vue-router';
import ServiceStatusIndicator from './ServiceStatusIndicator.vue';
</script>

<template>
  <header class="app-header">
    <div class="logo-container">
      <img
        alt="Vue logo"
        class="logo"
        src="@/assets/logo.svg"
        width="40"
        height="40"
      />
      <h1 class="app-title">Novel Reviews</h1>
    </div>

    <nav class="main-nav">
      <RouterLink to="/">Home</RouterLink>
      <RouterLink to="/about">About</RouterLink>
    </nav>

    <ServiceStatusIndicator />
  </header>
</template>

<style scoped>
.app-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  background-color: #f8f9fa;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo-container {
  display: flex;
  align-items: center;
}

.app-title {
  margin: 0 0 0 1rem;
  font-size: 1.5rem;
  font-weight: 600;
}

.logo {
  height: 40px;
  width: 40px;
}

.main-nav {
  display: flex;
  gap: 1.5rem;
}

.main-nav a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  padding: 0.5rem 0;
  position: relative;
}

.main-nav a.router-link-active {
  color: #3498db;
}

.main-nav a.router-link-active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #3498db;
}
</style>
