<template>
  <header class="topbar">
    <div class="breadcrumb">
      <span class="breadcrumb-root">ReportEngine</span>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-current">{{ pageTitle }}</span>
    </div>
    <div class="topbar-right">
      <div class="solr-pill">
        <span class="dot"></span>
        Solr · port 9007
      </div>
      <div class="kafka-pill">
        <span class="dot orange"></span>
        Kafka · port 9009
      </div>
      <div class="user-chip">
        <div class="avatar-circle">A</div>
        <span>Admin</span>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const pageTitle = computed(() => {
  const map: Record<string, string> = {
    dashboard: 'Dashboard',
    reports: 'Reports & Data',
  }
  return map[String(route.name)] ?? 'Page'
})
</script>

<style scoped>
.topbar {
  height: 70px;
  background: white;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.75rem;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.04);
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.breadcrumb-root {
  color: #9ca3af;
  font-weight: 500;
}

.breadcrumb-sep {
  color: #d1d5db;
}

.breadcrumb-current {
  color: #111827;
  font-weight: 700;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.solr-pill,
.kafka-pill {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #374151;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 9999px;
  padding: 0.28rem 0.75rem;
}

.dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #4ade80;
  box-shadow: 0 0 5px #4ade80;
}

.dot.orange {
  background: #fb923c;
  box-shadow: 0 0 5px #fb923c;
}

.user-chip {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
  cursor: pointer;
}

.avatar-circle {
  width: 34px;
  height: 34px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-radius: 50%;
  color: white;
  font-weight: 700;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
