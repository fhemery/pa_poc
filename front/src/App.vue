<script setup lang="ts">
import { RouterView } from "vue-router";
import Header from "./components/Header.vue";
</script>

<template>
  <Header />
  <main class="content">
    <RouterView />
  </main>
</template>

<style scoped>
.content {
  padding: 2rem;
}
</style>
