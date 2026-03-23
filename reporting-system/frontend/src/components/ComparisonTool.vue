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
        <label>Metric</label>
        <select v-model="field">
          <option v-for="f in numericFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <div class="input-group">
        <label>Group by</label>
        <select v-model="groupBy">
          <option v-for="f in textFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <div class="input-group">
        <label>Date field</label>
        <select v-model="dateField">
          <option v-for="f in dateFields" :key="f.name" :value="f.name">{{ f.label }}</option>
        </select>
      </div>
      <button class="btn-compare" @click="compare" :disabled="store.comparing">
        {{ store.comparing ? 'Comparing…' : '⇌ Compare' }}
      </button>
    </div>

    <!-- Shortcuts row -->
    <div class="shortcuts-row">
      <button class="shortcut" @click="setRelative('prev_month')">Prev Month vs This Month</button>
      <button class="shortcut" @click="setRelative('prev_year')">Same Period Last Year</button>
    </div>

    <!-- Results Table -->
    <div v-if="store.comparisonData.length" class="results-table-wrap">
      <table class="results-table">
        <thead>
          <tr>
            <th>Group</th>
            <th>Period A</th>
            <th>Period B</th>
            <th>Δ Absolute</th>
            <th>Δ %</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in store.comparisonData" :key="row.group">
            <td class="td-group">{{ row.group }}</td>
            <td>{{ row.period_a }}</td>
            <td>{{ row.period_b }}</td>
            <td :class="row.diff >= 0 ? 'positive' : 'negative'">
              {{ row.diff >= 0 ? '+' : '' }}{{ row.diff }}
            </td>
            <td :class="row.pct_change >= 0 ? 'positive' : 'negative'">
              {{ row.pct_change >= 0 ? '+' : '' }}{{ row.pct_change }}%
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else-if="!store.comparing" class="no-results">
      Select two date ranges and click Compare to see results.
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useReportStore } from '../stores/reportStore'

const store = useReportStore()

const dateFromA = ref('')
const dateToA   = ref('')
const dateFromB = ref('')
const dateToB   = ref('')
const field     = ref(store.chartField)
const groupBy   = ref(store.chartGroupBy)
const dateField = ref('Date_dt')

const numericFields = computed(() => store.fields.filter(f => f.type === 'number'))
const textFields    = computed(() => store.fields.filter(f => f.type === 'text'))
const dateFields    = computed(() => store.fields.filter(f => f.type === 'date'))

function compare() {
  store.compareRanges({
    field: field.value,
    groupBy: groupBy.value,
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
.results-table-wrap {
  overflow-x: auto;
}

.results-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.8125rem;
}

.results-table thead th {
  padding: 0.6rem 1rem;
  text-align: left;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #374151;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 700;
}

.results-table tbody td {
  padding: 0.6rem 1rem;
  border-bottom: 1px solid #f3f4f6;
  color: #374151;
}

.results-table tbody tr:hover td {
  background: #f0f4ff;
}

.td-group {
  font-weight: 600;
  color: #111827;
}

.positive { color: #16a34a; font-weight: 600; }
.negative { color: #dc2626; font-weight: 600; }

.no-results {
  text-align: center;
  padding: 2rem;
  color: #9ca3af;
  font-size: 0.875rem;
}
</style>
