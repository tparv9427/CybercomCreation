<template>
  <div class="comparison-tool">
    <!-- Period A -->
    <div class="period-group">
      <div class="period-label">Period A</div>
      <div class="date-row">
        <div class="input-group">
          <label>From</label>
          <input type="date" v-model="dateFromA" />
        </div>
        <div class="input-group">
          <label>To</label>
          <input type="date" v-model="dateToA" />
        </div>
      </div>
    </div>

    <div class="vs-badge">VS</div>

    <!-- Period B -->
    <div class="period-group">
      <div class="period-label">Period B</div>
      <div class="date-row">
        <div class="input-group">
          <label>From</label>
          <input type="date" v-model="dateFromB" />
        </div>
        <div class="input-group">
          <label>To</label>
          <input type="date" v-model="dateToB" />
        </div>
      </div>
    </div>

    <!-- Field selectors -->
    <div class="field-row">
      <div class="input-group">
        <label>Date field</label>
        <select v-model="dateField">
          <option v-for="f in dateFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <button class="btn-compare" @click="compare" :disabled="store.comparing">
        {{ store.comparing ? 'Searching…' : '⇌ Fetch Comparison Data' }}
      </button>
    </div>

    <!-- Shortcuts row -->
    <div class="shortcuts-row">
      <button class="shortcut" @click="setRelative('prev_month')">Prev Month vs This Month</button>
      <button class="shortcut" @click="setRelative('prev_year')">Same Period Last Year</button>
    </div>

    <!-- Two Side-by-Side Results Tables -->
    <div v-if="store.comparisonData.length && (store.docsA.length || store.docsB.length)" class="dual-tables-wrap">
      
      <!-- Table A -->
      <div class="table-section">
        <h4 class="table-title">Period A Data</h4>
        <div class="scroll-table">
          <table class="results-table">
            <thead>
              <tr>
                <th v-for="col in store.selectedColumns" :key="col" class="col-hdr">{{ store.fields.find(f => f.name === col)?.label || col }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(doc, i) in store.docsA" :key="i">
                <td v-for="col in store.selectedColumns" :key="col">{{ Array.isArray(doc[col]) ? doc[col].join(', ') : doc[col] }}</td>
              </tr>
            </tbody>
          </table>
          <div v-if="!store.docsA.length" class="empty-hint">No exact matches for Period A.</div>
        </div>
      </div>

      <!-- Table B -->
      <div class="table-section">
        <h4 class="table-title">Period B Data</h4>
        <div class="scroll-table">
          <table class="results-table">
            <thead>
              <tr>
                <th v-for="col in store.selectedColumns" :key="col" class="col-hdr">{{ store.fields.find(f => f.name === col)?.label || col }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(doc, i) in store.docsB" :key="i">
                <td v-for="col in store.selectedColumns" :key="col">{{ Array.isArray(doc[col]) ? doc[col].join(', ') : doc[col] }}</td>
              </tr>
            </tbody>
          </table>
          <div v-if="!store.docsB.length" class="empty-hint">No exact matches for Period B.</div>
        </div>
      </div>

    </div>

    <div v-if="store.comparisonData.length && !store.docsA.length && !store.docsB.length" class="no-results">
      No data found in either period matching your active filters.
    </div>
    <div v-else-if="!store.comparisonData.length && !store.comparing" class="no-results">
      Select two date ranges and click Fetch Comparison Data to see results.
    </div>

    <!-- Line Chart (appears after comparison results) -->
    <div v-if="store.docsA.length || store.docsB.length" class="inline-chart-section">
      <div class="inline-chart-header">
        <span class="inline-chart-title">📈 Metric Trend Comparison</span>
        <span class="inline-chart-hint">Compare a specific metric row-by-row</span>
      </div>
      <ChartRenderer mode="line" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useReportStore } from '../stores/reportStore'
import ChartRenderer from './ChartRenderer.vue'

const store = useReportStore()

const dateFromA = ref('')
const dateToA   = ref('')
const dateFromB = ref('')
const dateToB   = ref('')
const dateField = ref('Date_dt')

const numericFields = computed(() => store.fields.filter(f => f.type === 'number'))
const textFields    = computed(() => store.fields.filter(f => f.type === 'text'))
const dateFields    = computed(() => store.fields.filter(f => f.type === 'date'))

function compare() {
  store.compareRanges({
    dateField: dateField.value,
    dateFromA: dateFromA.value,
    dateToA: dateToA.value,
    dateFromB: dateFromB.value,
    dateToB: dateToB.value,
  })
}

function setRelative(mode: 'prev_month' | 'prev_year') {
  const now = new Date()
  if (mode === 'prev_month') {
    const thisM1 = new Date(now.getFullYear(), now.getMonth(), 1)
    const prevM1 = new Date(now.getFullYear(), now.getMonth() - 1, 1)
    const prevMLast = new Date(now.getFullYear(), now.getMonth(), 0)
    dateFromA.value = fmt(prevM1)
    dateToA.value   = fmt(prevMLast)
    dateFromB.value = fmt(thisM1)
    dateToB.value   = fmt(now)
  } else {
    const ly1 = new Date(now.getFullYear() - 1, now.getMonth(), 1)
    const lyE = new Date(now.getFullYear() - 1, now.getMonth(), now.getDate())
    const ty1 = new Date(now.getFullYear(), now.getMonth(), 1)
    dateFromA.value = fmt(ly1)
    dateToA.value   = fmt(lyE)
    dateFromB.value = fmt(ty1)
    dateToB.value   = fmt(now)
  }
}

function fmt(d: Date) {
  return d.toISOString().split('T')[0]
}
</script>

<style scoped>
.comparison-tool {
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  padding: 1.25rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.period-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.period-label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6366f1;
}

.date-row, .field-row {
  display: flex;
  align-items: flex-end;
  gap: 1rem;
  flex-wrap: wrap;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.input-group label {
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #9ca3af;
}

.input-group input[type="date"],
.input-group select {
  padding: 0.4rem 0.7rem;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
  background: #f9fafb;
  font-size: 0.875rem;
  color: #111827;
  outline: none;
  transition: border-color 0.15s;
  min-width: 140px;
}

.input-group input:focus,
.input-group select:focus {
  border-color: #6366f1;
}

.vs-badge {
  text-align: center;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  color: #9ca3af;
  background: #f3f4f6;
  border-radius: 9999px;
  padding: 0.3rem 0.8rem;
  width: fit-content;
}

.btn-compare {
  background: #4f46e5;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 0.48rem 1.25rem;
  border-radius: 0.375rem;
  transition: background 0.15s;
  margin-left: auto;
}
.btn-compare:hover:not(:disabled) { background: #4338ca; }
.btn-compare:disabled { opacity: 0.5; cursor: not-allowed; }

.shortcuts-row {
  display: flex;
  gap: 0.6rem;
  flex-wrap: wrap;
}

.shortcut {
  background: #f3f4f6;
  color: #374151;
  font-size: 0.78rem;
  font-weight: 500;
  padding: 0.3rem 0.85rem;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
  transition: all 0.15s;
}
.shortcut:hover { border-color: #6366f1; color: #4f46e5; }

/* results */
.dual-tables-wrap {
  display: flex;
  gap: 2rem;
  overflow-x: auto;
  align-items: flex-start;
}

.table-section {
  flex: 1;
  min-width: 45%;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  overflow: hidden;
}

.table-title {
  padding: 0.75rem 1rem;
  margin: 0;
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #6366f1;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.scroll-table {
  max-height: 400px;
  overflow: auto;
}

.results-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.8rem;
}

.col-hdr {
  padding: 0.6rem 1rem;
  text-align: left;
  color: #374151;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 600;
  position: sticky;
  top: 0;
  z-index: 10;
}

.results-table tbody td {
  padding: 0.5rem 1rem;
  border-bottom: 1px solid #f3f4f6;
  color: #4b5563;
  white-space: nowrap;
}

.results-table tbody tr:hover td {
  background: #f0f4ff;
}

.empty-hint {
  padding: 1.5rem;
  text-align: center;
  color: #9ca3af;
  font-size: 0.85rem;
}

.no-results {
  text-align: center;
  padding: 2rem;
  color: #9ca3af;
  font-size: 0.875rem;
}

/* Inline line chart section */
.inline-chart-section {
  margin-top: 1.5rem;
  padding-top: 1.25rem;
  border-top: 2px dashed #e5e7eb;
}

.inline-chart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
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
