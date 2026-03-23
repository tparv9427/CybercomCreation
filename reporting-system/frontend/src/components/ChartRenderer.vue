<template>
  <div class="analytics-card">

    <!-- ── Chart Type Selector ─────────────────────────────────────────── -->
    <div class="chart-type-bar">
      <button
        v-for="t in types"
        :key="t.id"
        :class="['type-btn', { active: chartType === t.id }]"
        @click="chartType = t.id"
      >
        {{ t.icon }} {{ t.label }}
      </button>
    </div>

    <!-- ── Controls (Dynamic per Chart Type) ─────────────────────────── -->
    <div class="chart-controls">

      <!-- SHARED: Metric + Group By (Bar & Pie) -->
      <template v-if="chartType === 'bar' || chartType === 'pie'">
        <div class="ctrl">
          <label>Metric</label>
          <select v-model="store.chartField" @change="refresh">
            <option v-for="f in numericFields" :key="f.name" :value="f.name">{{ f.label }}</option>
          </select>
        </div>

        <div class="ctrl" v-if="chartType === 'bar'">
          <label>Group By</label>
          <select v-model="store.chartGroupBy" @change="refresh">
            <option v-for="f in textFields" :key="f.name" :value="f.name">{{ f.label }}</option>
          </select>
        </div>

        <!-- Pie: Column Distribution selector -->
        <div class="ctrl" v-if="chartType === 'pie'">
          <label>Distribution Column</label>
          <select v-model="store.chartGroupBy" @change="refresh">
            <option v-for="f in textFields" :key="f.name" :value="f.name">{{ f.label }}</option>
          </select>
        </div>
      </template>

      <!-- BAR CHART: Range vs Limit mode -->
      <template v-if="chartType === 'bar'">
        <div class="ctrl range-mode-ctrl">
          <label>Display Mode</label>
          <div class="radio-group">
            <label class="radio-label">
              <input type="radio" value="limit" v-model="barMode" @change="refresh" />
              Top Limit
            </label>
            <label class="radio-label">
              <input type="radio" value="range" v-model="barMode" @change="refresh" />
              Row Range
            </label>
          </div>
        </div>

        <!-- Limit Mode -->
        <div class="ctrl" v-if="barMode === 'limit'">
          <label>Top N Results</label>
          <input
            id="bar-limit"
            type="number"
            v-model.number="barLimit"
            min="1"
            max="500"
            @change="refresh"
            class="num-input"
          />
        </div>

        <!-- Range Mode -->
        <template v-if="barMode === 'range'">
          <div class="ctrl">
            <label>From Rank</label>
            <input
              id="bar-range-from"
              type="number"
              v-model.number="barRangeFrom"
              min="1"
              @change="onRangeChange"
              class="num-input"
            />
          </div>
          <div class="ctrl">
            <label>To Rank</label>
            <input
              id="bar-range-to"
              type="number"
              v-model.number="barRangeTo"
              :min="barRangeFrom + 1"
              @change="onRangeChange"
              class="num-input"
            />
          </div>
        </template>
      </template>

      <!-- LINE CHART: Column selector (bound to comparisonData) -->
      <template v-if="chartType === 'line'">
        <div class="ctrl">
          <label>Compare Metric</label>
          <select v-model="lineMetric" @change="renderLine">
            <option v-for="f in numericFields" :key="f.name" :value="f.name">{{ f.label }}</option>
          </select>
        </div>
        <div v-if="!hasComparisonData" class="no-comparison-hint">
          ⚠ Run a Period Comparison first to populate this chart.
        </div>
      </template>

      <button class="btn-refresh" :disabled="loading" @click="refresh" v-if="chartType !== 'line'">
        {{ loading ? '...' : '↺ Refresh' }}
      </button>
    </div>

    <!-- ── Visualization Area ─────────────────────────────────────────── -->
    <div class="canvas-container">
      <canvas ref="canvasRef"></canvas>

      <div v-if="loading" class="chart-overlay">
        <div class="spinner"></div>
        <span>Consolidating metrics...</span>
      </div>

      <div v-if="!loading && !hasData" class="chart-overlay empty">
        <span v-if="chartType === 'line' && !hasComparisonData">
          No comparison data yet. Use the <strong>Comparison Panel</strong> to select two date ranges.
        </span>
        <span v-else>No data found for this period/filter.</span>
      </div>
    </div>

    <!-- ── Context Footer ─────────────────────────────────────────────── -->
    <div class="chart-footer" v-if="hasData">
      <div class="legend-chip">
        <span class="dot"></span>
        <strong>{{ currentMetricLabel }}</strong>
      </div>
      <span class="hint" v-if="chartType === 'bar'">
        <template v-if="barMode === 'limit'">Top {{ barLimit }} results</template>
        <template v-else>Showing ranks {{ barRangeFrom }} – {{ barRangeTo }}</template>
      </span>
      <span class="hint" v-else-if="chartType === 'line'">
        Comparing two periods across {{ chartLabels.length }} intervals
      </span>
      <span class="hint" v-else>
        Distribution across {{ chartLabels.length }} categories
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { Chart, registerables } from 'chart.js'
import axios from 'axios'
import { useReportStore } from '../stores/reportStore'

Chart.register(...registerables)

const API   = 'http://localhost:9006/api'
const store = useReportStore()

// ── Refs ────────────────────────────────────────────────────────────────────
const canvasRef  = ref<HTMLCanvasElement | null>(null)
const chartType  = ref<'bar' | 'line' | 'pie'>('bar')
const loading    = ref(false)
let chartInstance: Chart | null = null

const chartLabels = ref<string[]>([])
const chartValues = ref<number[]>([])

// ── Bar chart options ────────────────────────────────────────────────────────
const barMode      = ref<'limit' | 'range'>('limit')
const barLimit     = ref(20)
const barRangeFrom = ref(1)
const barRangeTo   = ref(20)

// ── Line chart option ────────────────────────────────────────────────────────
const lineMetric = ref('')

// ── Chart type definitions ───────────────────────────────────────────────────
const types = [
  { id: 'bar',  icon: '📊', label: 'Bar Chart'  },
  { id: 'pie',  icon: '🥧', label: 'Pie Chart'  },
  { id: 'line', icon: '📈', label: 'Line Chart' },
] as const

// ── Computed ─────────────────────────────────────────────────────────────────
const numericFields      = computed(() => store.fields.filter(f => f.type === 'number'))
const textFields         = computed(() => store.fields.filter(f => f.type === 'text'))
const hasData            = computed(() => chartLabels.value.length > 0)
const hasComparisonData  = computed(() => store.comparisonData && store.comparisonData.length > 0)
const currentMetricLabel = computed(() => store.fields.find(f => f.name === store.chartField)?.label || 'Value')

// ── Bar: computed rows/start params ─────────────────────────────────────────
const barParams = computed(() => {
  if (barMode.value === 'limit') {
    return { limit: barLimit.value, start: 0 }
  }
  const from  = Math.max(1, barRangeFrom.value)
  const to    = Math.max(from + 1, barRangeTo.value)
  return { limit: to - from + 1, start: from - 1 }
})

// ── Range validation ─────────────────────────────────────────────────────────
function onRangeChange() {
  if (barRangeTo.value <= barRangeFrom.value) {
    barRangeTo.value = barRangeFrom.value + 1
  }
  refresh()
}

// ── Main refresh (Bar & Pie) ─────────────────────────────────────────────────
async function refresh() {
  if (chartType.value === 'line') {
    renderLine()
    return
  }
  if (!store.chartField || !store.chartGroupBy) return

  loading.value = true
  try {
    const { data } = await axios.get(`${API}/report/facets`, {
      params: {
        metric:   store.chartField,
        group_by: store.chartGroupBy,
        limit:    barParams.value.limit,
        start:    barParams.value.start,
        filters:  store.filters.rules.length ? JSON.stringify(store.filters) : undefined,
        date_from: store.dateFrom || undefined,
        date_to:   store.dateTo   || undefined,
      }
    })

    chartLabels.value = data.map((d: any) => d.label)
    chartValues.value = data.map((d: any) => d.value)

    await nextTick()
    renderBarOrPie()
  } catch (e) {
    console.error('Chart Load Error:', e)
  } finally {
    loading.value = false
  }
}

// ── Render: Bar or Pie ───────────────────────────────────────────────────────
function renderBarOrPie() {
  if (!canvasRef.value) return
  if (chartInstance) chartInstance.destroy()

  const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#14b8a6']

  chartInstance = new Chart(canvasRef.value, {
    type: chartType.value as any,
    data: {
      labels: chartLabels.value,
      datasets: [{
        label: currentMetricLabel.value,
        data:  chartValues.value,
        backgroundColor: chartType.value === 'pie' ? colors : colors[0],
        borderColor:     colors[0],
        borderWidth:     0,
        borderRadius:    6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: chartType.value === 'pie' },
        tooltip: {
          padding:         12,
          backgroundColor: '#1e293b',
          titleFont:       { size: 14, weight: 'bold' }
        }
      },
      scales: chartType.value !== 'pie' ? {
        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
        x: { grid: { display: false } }
      } : {}
    }
  })
}

// ── Render: Line (from Comparison Panel data) ────────────────────────────────
function renderLine() {
  if (!canvasRef.value || !hasComparisonData.value) return
  if (chartInstance) chartInstance.destroy()

  const metric   = lineMetric.value || numericFields.value[0]?.name
  const compData = store.comparisonData

  // comparisonData shape: [{ label, a_value, b_value, ... }]
  const labels  = compData.map((d: any) => d.label ?? d.name ?? '')
  const valuesA = compData.map((d: any) => d[`${metric}_a`] ?? d.a_value ?? 0)
  const valuesB = compData.map((d: any) => d[`${metric}_b`] ?? d.b_value ?? 0)

  chartLabels.value = labels

  chartInstance = new Chart(canvasRef.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label:       'Period A',
          data:        valuesA,
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99,102,241,0.1)',
          borderWidth: 3,
          tension:     0.35,
          fill:        true,
          pointRadius: 4,
        },
        {
          label:       'Period B',
          data:        valuesB,
          borderColor: '#ec4899',
          backgroundColor: 'rgba(236,72,153,0.1)',
          borderWidth: 3,
          tension:     0.35,
          fill:        true,
          pointRadius: 4,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true },
        tooltip: {
          padding:         12,
          backgroundColor: '#1e293b',
          titleFont:       { size: 14, weight: 'bold' }
        }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
        x: { grid: { display: false } }
      }
    }
  })
}

// ── Watchers ─────────────────────────────────────────────────────────────────
watch(chartType, () => {
  chartLabels.value = []
  chartValues.value = []
  if (chartType.value === 'line') renderLine()
  else refresh()
})

watch(() => store.dateFrom, refresh)

// When comparison data changes → re-render line chart if active
watch(() => store.comparisonData, () => {
  if (chartType.value === 'line') renderLine()
}, { deep: true })

// ── Mounted ──────────────────────────────────────────────────────────────────
onMounted(() => {
  if (store.fields.length) {
    if (!store.chartField)   store.chartField   = numericFields.value[0]?.name
    if (!store.chartGroupBy) store.chartGroupBy = textFields.value[0]?.name
    if (!lineMetric.value)   lineMetric.value   = numericFields.value[0]?.name
    refresh()
  }
})

watch(() => store.fields, (newFields) => {
  if (newFields.length && !chartLabels.value.length) {
    if (!store.chartField)   store.chartField   = numericFields.value[0]?.name
    if (!store.chartGroupBy) store.chartGroupBy = textFields.value[0]?.name
    if (!lineMetric.value)   lineMetric.value   = numericFields.value[0]?.name
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
  gap: 1.25rem;
  background: white;
  min-height: 520px;
}

/* ── Chart Type Bar ───────────────────────────────────────────────────────── */
.chart-type-bar {
  display: flex;
  gap: 0.5rem;
  background: #f1f5f9;
  padding: 4px;
  border-radius: 0.75rem;
  width: fit-content;
}

.type-btn {
  padding: 0.45rem 1rem;
  border-radius: 0.6rem;
  font-size: 0.82rem;
  font-weight: 600;
  color: #64748b;
  background: transparent;
  transition: all 0.2s;
  cursor: pointer;
  border: none;
}

.type-btn.active {
  background: white;
  color: #1e293b;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.08);
}

/* ── Chart Controls ───────────────────────────────────────────────────────── */
.chart-controls {
  display: flex;
  align-items: flex-end;
  flex-wrap: wrap;
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

.ctrl select,
.num-input {
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #e2e8f0;
  font-size: 0.85rem;
  color: #1e293b;
  background: white;
  min-width: 80px;
}

.num-input {
  width: 90px;
}

/* ── Radio Group ──────────────────────────────────────────────────────────── */
.radio-group {
  display: flex;
  gap: 0.75rem;
  padding: 0.45rem 0;
}

.radio-label {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.84rem;
  color: #1e293b;
  cursor: pointer;
  font-weight: 500;
}

.radio-label input[type="radio"] {
  accent-color: #6366f1;
}

.range-mode-ctrl {
  border-right: 1px solid #e2e8f0;
  padding-right: 1rem;
  margin-right: 0.25rem;
}

/* ── Line Chart hint ──────────────────────────────────────────────────────── */
.no-comparison-hint {
  font-size: 0.82rem;
  color: #f59e0b;
  background: #fffbeb;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #fef3c7;
  align-self: flex-end;
}

/* ── Refresh Button ───────────────────────────────────────────────────────── */
.btn-refresh {
  margin-left: auto;
  background: #6366f1;
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.85rem;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-refresh:hover { background: #4f46e5; }
.btn-refresh:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Canvas ───────────────────────────────────────────────────────────────── */
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
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(2px);
  z-index: 10;
  color: #64748b;
  font-size: 0.95rem;
  text-align: center;
  padding: 2rem;
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

/* ── Footer ───────────────────────────────────────────────────────────────── */
.chart-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-top: 1px solid #f1f5f9;
}

.legend-chip {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.88rem;
  color: #1e293b;
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
