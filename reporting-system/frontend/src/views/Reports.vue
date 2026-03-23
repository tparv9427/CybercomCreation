<template>
  <div class="reports-page">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Reports</h1>
        <p class="page-sub">Dynamic reporting powered by Solr</p>
      </div>
      <div class="header-actions">
        <button
          class="btn-tab"
          :class="{ active: activeTab === 'table' }"
          @click="activeTab = 'table'"
        >📋 Data Table</button>
        <button
          class="btn-tab"
          :class="{ active: activeTab === 'chart' }"
          @click="activeTab = 'chart'"
        >📊 Charts</button>
        <button
          class="btn-tab"
          :class="{ active: activeTab === 'compare' }"
          @click="activeTab = 'compare'"
        >⇌ Compare</button>
      </div>
    </div>

    <!-- Loading banner for fields -->
    <div v-if="initLoading" class="init-loading">
      <div class="spinner"></div>
      <span>Connecting to Solr and loading fields…</span>
    </div>

    <template v-else>
      <!-- Filter Bar (always visible) -->
      <section class="section-card">
        <div class="section-header" @click="filtersOpen = !filtersOpen">
          <div class="section-title">
            <span>🔍 Filters</span>
            <span class="filter-badge" v-if="store.filters.rules.length">
              {{ store.filters.rules.length }}
            </span>
          </div>
          <span class="toggle-icon">{{ filtersOpen ? '▲' : '▼' }}</span>
        </div>
        <div v-show="filtersOpen">
          <FilterBuilder />
        </div>
      </section>

      <!-- Saved Views sidebar strip -->
      <section class="section-card">
        <div class="section-header" @click="viewsOpen = !viewsOpen">
          <div class="section-title">
            <span>💾 Saved Views</span>
            <span class="filter-badge" v-if="store.savedViews.length">
              {{ store.savedViews.length }}
            </span>
          </div>
          <span class="toggle-icon">{{ viewsOpen ? '▲' : '▼' }}</span>
        </div>
        <div v-show="viewsOpen">
          <SavedViews />
        </div>
      </section>

      <!-- Main Tab Content -->
      <div class="tab-content">
        <!-- Data Table -->
        <transition name="fade" mode="out-in">
          <div v-if="activeTab === 'table'" key="table">
            <DataTable />
          </div>

          <!-- Charts -->
          <div v-else-if="activeTab === 'chart'" key="chart">
            <ChartRenderer />
          </div>

          <!-- Period Comparison -->
          <div v-else-if="activeTab === 'compare'" key="compare">
            <ComparisonTool />
          </div>
        </transition>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useReportStore } from '../stores/reportStore'
import FilterBuilder from '../components/FilterBuilder.vue'
import SavedViews from '../components/SavedViews.vue'
import DataTable from '../components/DataTable.vue'
import ChartRenderer from '../components/ChartRenderer.vue'
import ComparisonTool from '../components/ComparisonTool.vue'

const store = useReportStore()

const activeTab   = ref<'table' | 'chart' | 'compare'>('table')
const filtersOpen = ref(true)
const viewsOpen   = ref(false)
const initLoading = ref(true)

onMounted(async () => {
  await store.fetchFields()
  store.fetchData()
  initLoading.value = false
})
</script>

<style scoped>
.reports-page {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
}

.page-sub {
  font-size: 0.875rem;
  color: #9ca3af;
  margin-top: 0.25rem;
}

.header-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-tab {
  padding: 0.5rem 1.1rem;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
  background: white;
  transition: all 0.15s;
}
.btn-tab.active {
  background: #4f46e5;
  border-color: #4f46e5;
  color: white;
  font-weight: 600;
}

.section-card {
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.875rem 1.5rem;
  cursor: pointer;
  user-select: none;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.15s;
}
.section-header:hover { background: #fafafa; }

.section-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  font-size: 0.9rem;
  color: #111827;
}

.filter-badge {
  background: #eef2ff;
  color: #4f46e5;
  border-radius: 9999px;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.1rem 0.5rem;
}

.toggle-icon {
  font-size: 0.7rem;
  color: #9ca3af;
}

.tab-content { min-height: 300px; }

/* init loading */
.init-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 4rem;
  color: #6b7280;
  font-size: 0.9rem;
}

.spinner {
  width: 22px;
  height: 22px;
  border: 2px solid #e5e7eb;
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* tab transitions */
.fade-enter-active,
.fade-leave-active   { transition: opacity 0.2s ease; }
.fade-enter-from,
.fade-leave-to       { opacity: 0; }
</style>
