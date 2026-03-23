<template>
  <div class="filter-builder">
    <div class="date-presets">
      <span class="preset-label">📅 Quick Ranges:</span>
      <button 
        v-for="p in presets" 
        :key="p.label" 
        class="preset-btn"
        @click="applyPreset(p.days)"
      >
        {{ p.label }}
      </button>
    </div>

    <FilterRow :group="store.filters" :is-root="true" />
    
    <div class="fb-footer">
      <button class="btn-clear" @click="clearAll" v-if="store.filters.rules.length">Clear All Filters</button>
    </div>
    <div v-if="!store.filters.rules.length" class="no-filters">
       Displaying all records — add filters above to narrow your results.
    </div>
  </div>
</template>

<script setup lang="ts">
import { useReportStore } from '../stores/reportStore'
import FilterRow from './FilterRow.vue'

const store = useReportStore()

const presets = [
  { label: '7 Days', days: 7 },
  { label: '15 Days', days: 15 },
  { label: '30 Days', days: 30 },
  { label: '150 Days', days: 150 },
  { label: '6 Months', days: 180 },
  { label: '1 Year', days: 365 },
]

function applyPreset(days: number) {
  const to = new Date()
  const from = new Date()
  from.setDate(to.getDate() - days)
  
  // Format as Solr ISO: YYYY-MM-DDTHH:mm:ssZ
  store.dateTo = to.toISOString().split('.')[0] + 'Z'
  store.dateFrom = from.toISOString().split('.')[0] + 'Z'
  
  store.start = 0
  store.fetchData()
}

function apply() {
  store.start = 0
  store.fetchData()
}

function clearAll() {
  store.filters.rules = []
  store.start = 0
  store.fetchData()
}
</script>

<style scoped>
.filter-builder {
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  padding: 1.25rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.fb-actions {
  display: flex;
  gap: 0.75rem;
  padding-top: 1rem;
  border-top: 1px dashed #f3f4f6;
}

.btn-apply {
  background: #4f46e5;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 0.5rem 1.25rem;
  border-radius: 0.375rem;
}

.btn-clear {
  color: #6b7280;
  font-size: 0.875rem;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
}

.no-filters {
  font-size: 0.875rem;
  color: #9ca3af;
  text-align: center;
  padding: 1rem 0;
}

.date-presets {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px dashed #e5e7eb;
  flex-wrap: wrap;
}

.preset-label {
  font-size: 0.72rem;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  margin-right: 0.4rem;
}

.preset-btn {
  background: white;
  border: 1px solid #e5e7eb;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #4b5563;
  transition: all 0.15s;
  cursor: pointer;
}
.preset-btn:hover {
  background: #eef2ff;
  border-color: #6366f1;
  color: #4f46e5;
  transform: translateY(-1px);
}
.preset-btn:active { transform: translateY(0); }
</style>
