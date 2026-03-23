<template>
  <div v-if="!isLoggedIn">
    <Login />
  </div>
  <div v-else class="app-container">
    <Sidebar />
    <div class="main-wrapper">
      <Topbar />
      <main class="content-area">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Sidebar from './components/Sidebar.vue';
import Topbar from './components/Topbar.vue';
import Login from './components/Login.vue';
import { useReportStore } from './stores/reportStore';

const store = useReportStore();
const isLoggedIn = computed(() => !!store.token);
</script>

<style scoped>
.app-container {
  display: flex;
  height: 100vh;
  width: 100vw;
  overflow: hidden;
}

.main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  background-color: #f3f4f6;
}

.content-area {
  flex: 1;
  padding: 1.5rem 2rem;
  overflow-y: auto;
}
</style>
