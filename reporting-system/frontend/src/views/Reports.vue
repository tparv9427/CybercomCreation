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
          :class="{ active: activeTab === 'compare' }"
          @click="activeTab = 'compare'"
        >⇌ Compare</button>
        <button
          class="btn-tab"
          :class="{ active: activeTab === 'pivot' }"
          @click="activeTab = 'pivot'"
        >📉 Pivot Table</button>
      </div>
    </div>

    <!-- Loading banner for fields -->
    <div v-if="store.error" class="error-banner">
      <span>⚠️ {{ store.error }}</span>
      <button @click="store.error = null" class="btn-clear-error">✕</button>
    </div>

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
        <transition name="fade" mode="out-in">
          <!-- Data Table + Bar/Pie Charts -->
          <div v-if="activeTab === 'table'" key="table">
            <DataTable />
            <!-- Bar & Pie charts appear only after data is loaded -->
            <div v-if="store.docs.length > 0" class="inline-chart-section">
              <div class="inline-chart-header">
                <span class="inline-chart-title">📊 Data Visualisation</span>
                <span class="inline-chart-hint">Based on current report results</span>
              </div>
              <ChartRenderer mode="barPie" />
            </div>
          </div>

          <!-- Period Comparison -->
          <div v-else-if="activeTab === 'compare'" key="compare">
            <ComparisonTool />
          </div>

          <!-- Pivot Table -->
          <div v-else-if="activeTab === 'pivot'" key="pivot">
            <PivotTable />
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
import PivotTable from '../components/PivotTable.vue'

const store = useReportStore()

const activeTab   = ref<'table' | 'compare' | 'pivot'>('table')
const filtersOpen = ref(true)
const viewsOpen   = ref(false)
const initLoading = ref(true)

onMounted(async () => {
  await store.fetchFields()
  if (store.user?.default_view_id) {
    await store.applyDefaultView()
  } else {
    store.fetchData()
  }
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
  animation: pulse 1s ease-in-out infinite;
}

@keyframes pulse {
  0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
  70% { transform: scale(1.05); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
}

.toggle-icon {
  font-size: 0.7rem;
  color: #9ca3af;
}

.tab-content { min-height: 300px; }

/* Error banner */
.error-banner {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #991b1b;
  padding: 0.75rem 1.25rem;
  border-radius: 0.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
  font-weight: 500;
  animation: slideDown 0.3s ease-out;
}
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.btn-clear-error {
  background: transparent;
  color: #991b1b;
  font-size: 1.1rem;
  padding: 0.2rem 0.5rem;
  border-radius: 0.25rem;
  transition: background 0.15s;
}
.btn-clear-error:hover { background: #fee2e2; }

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
.fade-leave-active   { transition: all 0.25s ease; }
.fade-enter-from     { opacity: 0; transform: translateY(10px); }
.fade-leave-to       { opacity: 0; transform: translateY(-10px); }

/* Inline chart section (below data table) */
.inline-chart-section {
  margin-top: 1.5rem;
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  padding: 1.25rem 1.5rem;
}

.inline-chart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
  padding-bottom: 0.875rem;
  border-bottom: 1px solid #f3f4f6;
}

.inline-chart-title {
  font-size: 0.9375rem;
  font-weight: 700;
  color: #111827;
}

.inline-chart-hint {
  font-size: 0.78rem;
  color: #9ca3af;
}
</style>
