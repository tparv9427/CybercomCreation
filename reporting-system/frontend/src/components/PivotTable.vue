<template>
  <div class="pivot-container">
    <div class="pivot-controls">
      <div class="ctrl">
        <label>Metric</label>
        <select v-model="metric">
          <option v-for="f in numericFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <div class="ctrl">
        <label>Level 1 (Row)</label>
        <select v-model="groupBy1">
          <option v-for="f in textFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <div class="ctrl">
        <label>Level 2 (Sub-Row)</label>
        <select v-model="groupBy2">
          <option value="">(None)</option>
          <option v-for="f in textFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <button class="btn-run" @click="fetchData" :disabled="loading">
        {{ loading ? 'Calculating...' : '🚀 Run Pivot' }}
      </button>
    </div>

    <div class="pivot-results" :class="{ loading }">
      <table class="pivot-table">
        <thead>
          <tr>
            <th>Dimension Hierarchy</th>
            <th class="text-right">Aggregation (AVG)</th>
          </tr>
        </thead>
        <tbody>
          <template v-if="data.length === 0 && !loading">
            <tr>
              <td colspan="2" class="empty-state">No pivot data. Select dimensions and run.</td>
            </tr>
          </template>
          <template v-for="row in data" :key="row.label">
            <!-- Level 1 Row -->
            <tr class="level-1">
              <td>
                <span class="expander">▼</span>
                <strong>{{ row.label }}</strong>
              </td>
              <td class="text-right">{{ formatValue(row.value) }}</td>
            </tr>
            <!-- Level 2 Rows -->
            <tr v-for="sub in row.subs" :key="sub.label" class="level-2">
              <td class="indent">
                <span class="dot-sep"></span>
                {{ sub.label }}
              </td>
              <td class="text-right">{{ formatValue(sub.value) }}</td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useReportStore } from '../stores/reportStore'

const store = useReportStore()
const loading = ref(false)
const data = ref<any[]>([])

const metric = ref('')
const groupBy1 = ref('')
const groupBy2 = ref('')

const numericFields = computed(() => store.fields.filter(f => f.type === 'number'))
const textFields    = computed(() => store.fields.filter(f => f.type === 'text'))

async function fetchData() {
  if (!metric.value || !groupBy1.value) return
  
  loading.value = true
  const groups = [groupBy1.value]
  if (groupBy2.value) groups.push(groupBy2.value)
  
  try {
    data.value = await store.fetchFacets(metric.value, groups)
  } finally {
    loading.value = false
  }
}

function formatValue(v: number) {
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v)
}

onMounted(() => {
  if (store.fields.length) {
    metric.value = numericFields.value[0]?.name || ''
    groupBy1.value = textFields.value[0]?.name || ''
    groupBy2.value = textFields.value[1]?.name || ''
  }
})
</script>

<style scoped>
.pivot-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.pivot-controls {
  display: flex;
  align-items: flex-end;
  gap: 1rem;
  padding: 1.25rem;
  background: #f8fafc;
  border-radius: 0.75rem;
  border: 1px solid #f1f5f9;
}

.ctrl {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.ctrl label {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #64748b;
  letter-spacing: 0.05em;
}

.ctrl select {
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #e2e8f0;
  font-size: 0.85rem;
  background: white;
}

.btn-run {
  background: #4f46e5;
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  height: 38px;
}

.pivot-results {
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  transition: opacity 0.2s;
}
.pivot-results.loading { opacity: 0.6; pointer-events: none; }

.pivot-table {
  width: 100%;
  border-collapse: collapse;
}

.pivot-table th {
  background: #f9fafb;
  padding: 0.875rem 1.25rem;
  text-align: left;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6b7280;
  border-bottom: 1px solid #e5e7eb;
}

.pivot-table td {
  padding: 0.75rem 1.25rem;
  border-bottom: 1px solid #f3f4f6;
  font-size: 0.9rem;
}

.text-right { text-align: right; }

.level-1 { background: #fafafa; }
.level-1 td { color: #111827; }

.level-2 td {
  color: #4b5563;
  font-size: 0.85rem;
}

.indent {
  padding-left: 2.5rem !important;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.expander {
  display: inline-block;
  width: 16px;
  font-size: 0.6rem;
  color: #94a3b8;
}

.dot-sep {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #e2e8f0;
}

.empty-state {
  padding: 3rem;
  text-align: center;
  color: #9ca3af;
  font-style: italic;
}
</style>
