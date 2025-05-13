<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { pingApiService } from '@/services/ping.api.service';

// Status indicator states
const STATUS_UNKNOWN = 'unknown';
const STATUS_ONLINE = 'online';
const STATUS_OFFLINE = 'offline';

// Service status
const serviceStatus = ref(STATUS_UNKNOWN);
let pingInterval: number | null = null;

// Function to check API availability
const checkApiStatus = async () => {
  try {
    const data = await pingApiService.checkStatus();
    serviceStatus.value = data.status === 'ok' ? STATUS_ONLINE : STATUS_OFFLINE;
  } catch (error) {
    serviceStatus.value = STATUS_OFFLINE;
    console.error('Error checking API status:', error);
  }
};

// Start periodic checking on component mount
onMounted(() => {
  // Check immediately
  checkApiStatus();
  
  // Then check every 30 seconds
  pingInterval = window.setInterval(checkApiStatus, 30000);
});

// Clean up interval on component unmount
onUnmounted(() => {
  if (pingInterval !== null) {
    clearInterval(pingInterval);
  }
});

// Compute status indicator color
const statusColor = computed(() => {
  switch (serviceStatus.value) {
    case STATUS_ONLINE:
      return '#4CAF50'; // Green
    case STATUS_OFFLINE:
      return '#F44336'; // Red
    default:
      return '#9E9E9E'; // Grey
  }
});

// Compute status text for tooltip
const statusText = computed(() => {
  switch (serviceStatus.value) {
    case STATUS_ONLINE:
      return 'Service is online';
    case STATUS_OFFLINE:
      return 'Service is offline';
    default:
      return 'Checking service status...';
  }
});
</script>

<template>
  <div class="status-indicator-container">
    <div 
      :class="[
        'status-indicator', 
        `status-${serviceStatus}`
      ]" 
      :style="{ backgroundColor: statusColor }" 
      :title="statusText"
      data-testid="service-status-indicator"
    ></div>
  </div>
</template>

<style scoped>
.status-indicator-container {
  display: flex;
  align-items: center;
}

.status-indicator {
  width: 1rem;
  height: 1rem;
  border-radius: 50%;
  transition: background-color 0.3s ease;
}

/* Status-specific classes */
.status-online {
  background-color: #4CAF50 !important; /* Green */
}

.status-offline {
  background-color: #F44336 !important; /* Red */
}

.status-unknown {
  background-color: #9E9E9E !important; /* Grey */
}
</style>
