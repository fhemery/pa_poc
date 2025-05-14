import "./assets/main.css";

import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";
import { setupAuth } from "./plugins/auth";

const app = createApp(App);

// Create and use Pinia
const pinia = createPinia();
app.use(pinia);
app.use(router);

// Initialize app
app.mount("#app");

// Setup authentication after app is mounted
setupAuth(router);
