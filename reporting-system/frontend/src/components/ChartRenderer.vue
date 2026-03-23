<template>
  <div class="analytics-card">
    <!-- Configuration -->
    <div class="chart-controls">
      <div class="ctrl">
        <label>Metric</label>
        <select v-model="store.chartField" @change="refresh">
          <option v-for="f in numericFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <div class="ctrl">
        <label>Group By</label>
        <select v-model="store.chartGroupBy" @change="refresh">
          <option v-for="f in textFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <div class="ctrl">
        <label>View</label>
        <div class="type-selector">
          <button v-for="t in types" :key="t.id" :class="{ active: chartType === t.id }" @click="chartType = t.id">{{ t.icon }}</button>
        </div>
      </div>
      <button class="btn-refresh" :disabled="loading" @click="refresh">
        {{ loading ? '...' : '↺ Refresh' }}
      </button>
    </div>

    <!-- Visualization Area -->
    <div class="canvas-container">
      <canvas ref="canvasRef"></canvas>
      
      <div v-if="loading" class="chart-overlay">
        <div class="spinner"></div>
        <span>Consolidating metrics...</span>
      </div>

      <div v-if="!loading && !hasData" class="chart-overlay empty">
         No data found for this period/filter.
      </div>
    </div>

    <!-- Context Footer -->
    <div class="chart-footer" v-if="hasData">
      <div class="legend-chip">
        <span class="dot"></span>
        <strong>{{ currentMetricLabel }}</strong>
      </div>
      <span class="hint">Analyzed across {{ chartLabels.length }} categories</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { Chart, registerables } from 'chart.js'
import axios from 'axios'
import { useReportStore } from '../stores/reportStore'

Chart.register(...registerables)

const API = 'http://localhost:9006/api'
const store = useReportStore()

const canvasRef = ref<HTMLCanvasElement | null>(null)
const chartType = ref<'bar' | 'line' | 'pie'>('bar')
const loading = ref(false)
let chartInstance: Chart | null = null

const chartLabels = ref<string[]>([])
const chartValues = ref<number[]>([])

const types = [
  { id: 'bar', icon: '📊' },
  { id: 'line', icon: '📈' },
  { id: 'pie', icon: '🥧' },
] as const

const numericFields = computed(() => store.fields.filter(f => f.type === 'number'))
const textFields = computed(() => store.fields.filter(f => f.type === 'text'))
const hasData = computed(() => chartLabels.value.length > 0)
const currentMetricLabel = computed(() => store.fields.find(f => f.name === store.chartField)?.label || 'Value')

async function refresh() {
  if (!store.chartField || !store.chartGroupBy) return
  
  loading.value = true
  try {
    const { data } = await axios.get(`${API}/report/facets`, {
      params: {
        metric: store.chartField,
        group_by: store.chartGroupBy,
        filters: store.filters.rules.length ? JSON.stringify(store.filters) : undefined,
        date_from: store.dateFrom || undefined,
        date_to: store.dateTo || undefined,
      }
    })

    chartLabels.value = data.map((d: any) => d.label)
    chartValues.value = data.map((d: any) => d.value)

    await nextTick()
    render()
  } catch (e) {
    console.error('Chart Load Error:', e)
  } finally {
    loading.value = false
  }
}

function render() {
  if (!canvasRef.value) return
  if (chartInstance) chartInstance.destroy()

  const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#14b8a6']

  chartInstance = new Chart(canvasRef.value, {
    type: chartType.value as any,
    data: {
      labels: chartLabels.value,
      datasets: [{
        label: currentMetricLabel.value,
        data: chartValues.value,
        backgroundColor: chartType.value === 'pie' ? colors : colors[0],
        borderColor: colors[0],
        borderWidth: chartType.value === 'line' ? 3 : 0,
        borderRadius: 6,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: chartType.value === 'pie' },
        tooltip: {
          padding: 12,
          backgroundColor: '#1e293b',
          titleFont: { size: 14, weight: 'bold' }
        }
      },
      scales: chartType.value !== 'pie' ? {
        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
        x: { grid: { display: false } }
      } : {}
    }
  })
}

watch(chartType, render)
watch(() => store.dateFrom, refresh)

onMounted(() => {
  if (store.fields.length) {
    // Initial load if fields are ready
    if (!store.chartField) store.chartField = numericFields.value[0]?.name
    if (!store.chartGroupBy) store.chartGroupBy = textFields.value[0]?.name
    refresh()
  }
})

// Deep watch for field availability
watch(() => store.fields, (newFields) => {
  if (newFields.length && !chartLabels.value.length) {
    if (!store.chartField) store.chartField = numericFields.value[0]?.name
    if (!store.chartGroupBy) store.chartGroupBy = textFields.value[0]?.name
    refresh()
  }
}, { immediate: true })

onUnmounted(() => {
  if (chartInstance) chartInstance.destroy()
})
</script>

<style scoped>
.analytics-card {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  background: white;
  min-height: 520px;
}

.chart-controls {
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
  color: #1e293b;
  background: white;
}

.type-selector {
  display: flex;
  gap: 2px;
  background: #e2e8f0;
  padding: 2px;
  border-radius: 0.5rem;
}

.type-selector button {
  padding: 0.4rem 0.75rem;
  border-radius: 0.4rem;
  font-size: 1rem;
  background: transparent;
  transition: all 0.2s;
}

.type-selector button.active {
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.btn-refresh {
  margin-left: auto;
  background: #6366f1;
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.85rem;
}

.canvas-container {
  flex: 1;
  position: relative;
  min-height: 400px;
}

.chart-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  background: rgba(255,255,255,0.8);
  backdrop-filter: blur(2px);
  z-index: 10;
  color: #64748b;
  font-size: 0.95rem;
}

.chart-overlay.empty {
  background: #f8fafc;
  border: 2px dashed #e2e8f0;
  border-radius: 1rem;
}

.spinner {
  width: 28px;
  height: 28px;
  border: 3px solid #e2e8f0;
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.chart-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 0;
  border-top: 1px solid #f1f5f9;
}

.legend-chip {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.9rem;
}

.dot {
  width: 10px;
  height: 10px;
  background: #6366f1;
  border-radius: 3px;
}

.hint {
  font-size: 0.75rem;
  color: #94a3b8;
}
</style>
