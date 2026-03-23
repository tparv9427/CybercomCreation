<template>
  <div class="dashboard">

    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Live Analytics Overview 🚀</h1>
        <p class="page-sub">Real-time Solr data insights & project metrics</p>
      </div>
      <router-link to="/reports" class="btn-go-reports">
        Open Reports →
      </router-link>
    </div>

    <!-- Loading -->
    <div class="init-loading" v-if="loading">
      <div class="spinner"></div>
      Fetching system stats…
    </div>

    <template v-else>

      <!-- Stat Cards -->
      <div class="stats-grid">
        <div class="stat-card indigo">
          <div class="stat-icon">🗄️</div>
          <div class="stat-body">
            <div class="stat-val">{{ stats.total_docs.toLocaleString() }}</div>
            <div class="stat-lbl">Indexed Documents (Solr)</div>
          </div>
        </div>
        <div class="stat-card violet">
          <div class="stat-icon">🧩</div>
          <div class="stat-body">
            <div class="stat-val">{{ stats.total_fields }}</div>
            <div class="stat-lbl">Available Fields</div>
          </div>
        </div>
        <div class="stat-card teal">
          <div class="stat-icon">💾</div>
          <div class="stat-body">
            <div class="stat-val">{{ stats.saved_views }}</div>
            <div class="stat-lbl">Saved Views</div>
          </div>
        </div>
        <div class="stat-card amber">
          <div class="stat-icon">⬇️</div>
          <div class="stat-body">
            <div class="stat-val">{{ stats.total_exports }}</div>
            <div class="stat-lbl">CSV Exports</div>
          </div>
        </div>
      </div>

      <!-- Middle row -->
      <div class="mid-grid">

        <!-- Field type breakdown -->
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">Field Type Breakdown</h2>
            <span class="card-badge">{{ stats.total_fields }} total</span>
          </div>
          <div class="card-body">
            <div
              v-for="(count, type) in stats.field_types"
              :key="type"
              class="field-row"
            >
              <div class="field-label">
                <span class="field-dot" :class="type"></span>
                {{ type }}
              </div>
              <div class="field-bar-wrap">
                <div
                  class="field-bar"
                  :class="type"
                  :style="{ width: barPct(count) + '%' }"
                ></div>
              </div>
              <div class="field-count">{{ count }}</div>
            </div>

            <div class="empty-note" v-if="!stats.total_fields">
              No documents indexed yet. Run the Kafka producer to ingest data.
            </div>
          </div>
        </div>

        <!-- Feature shortcuts -->
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">Quick Launch</h2>
          </div>
          <div class="card-body shortcuts-grid">
            <router-link to="/reports" class="shortcut-card" @click="setTab('table')">
              <div class="sc-icon table">📋</div>
              <div class="sc-label">Data Table</div>
              <div class="sc-desc">Browse &amp; filter all indexed records</div>
            </router-link>
            <router-link to="/reports" class="shortcut-card" @click="setTab('chart')">
              <div class="sc-icon chart">📊</div>
              <div class="sc-label">Charts</div>
              <div class="sc-desc">Visualise data with bar, line &amp; pie</div>
            </router-link>
            <router-link to="/reports" class="shortcut-card" @click="setTab('compare')">
              <div class="sc-icon compare">⇌</div>
              <div class="sc-label">Compare Periods</div>
              <div class="sc-desc">A vs B period metric comparison</div>
            </router-link>
            <router-link to="/reports" class="shortcut-card">
              <div class="sc-icon filter">🔍</div>
              <div class="sc-label">Filter Builder</div>
              <div class="sc-desc">Advanced AND/OR query rules</div>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Recent Audit Activity</h2>
        </div>
        <div class="card-body">
          <div v-if="stats.recent_activity.length === 0" class="empty-note">
            No activity logged yet. Actions like saving views and exporting appear here.
          </div>
          <table class="activity-table" v-else>
            <thead>
              <tr>
                <th>Action</th>
                <th>Details</th>
                <th>IP Address</th>
                <th>When</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(log, i) in stats.recent_activity" :key="i">
                <td>
                  <span class="action-badge" :class="actionClass(log.action)">
                    {{ actionLabel(log.action) }}
                  </span>
                </td>
                <td class="detail-cell">{{ detailText(log.details) }}</td>
                <td class="mono">{{ log.ip_address ?? '—' }}</td>
                <td class="time-cell">{{ log.time }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const API = 'http://localhost:9006/api'

interface Stats {
  total_docs: number
  total_fields: number
  saved_views: number
  total_exports: number
  recent_activity: { action: string; details: any; ip_address: string; time: string }[]
  field_types: Record<string, number>
}

const loading = ref(true)
const stats = ref<Stats>({
  total_docs: 0,
  total_fields: 0,
  saved_views: 0,
  total_exports: 0,
  recent_activity: [],
  field_types: {},
})

onMounted(async () => {
  try {
    const { data } = await axios.get(`${API}/stats`)
    stats.value = data
  } catch {
    // Solr may not be running — show zeros
  } finally {
    loading.value = false
  }
})

function barPct(count: number) {
  const max = Math.max(...Object.values(stats.value.field_types))
  return max ? Math.round((count / max) * 100) : 0
}

function actionLabel(action: string) {
  const m: Record<string, string> = {
    export_report: 'Export CSV',
    save_view: 'Save View',
    send_scheduled_report: 'Scheduled Report',
  }
  return m[action] ?? action.replace(/_/g, ' ')
}

function actionClass(action: string) {
  if (action === 'export_report') return 'amber'
  if (action === 'save_view') return 'teal'
  if (action === 'send_scheduled_report') return 'violet'
  return 'indigo'
}

function detailText(details: any) {
  if (!details) return '—'
  return Object.values(details).join(', ')
}

function setTab(_tab: string) {
  // The Reports page reads this from localStorage on mount
  localStorage.setItem('reports_tab', _tab)
}
</script>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
}

.page-sub {
  font-size: 0.875rem;
  color: #9ca3af;
  margin-top: 0.2rem;
}

.btn-go-reports {
  background: #4f46e5;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 0.6rem 1.4rem;
  border-radius: 0.5rem;
  transition: background 0.15s;
}
.btn-go-reports:hover { background: #4338ca; }

/* loading */
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

/* stat cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.25rem;
}

.stat-card {
  border-radius: 0.875rem;
  padding: 1.4rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.1rem;
  color: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.stat-card.indigo  { background: linear-gradient(135deg, #6366f1, #4f46e5); }
.stat-card.violet  { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.stat-card.teal    { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.stat-card.amber   { background: linear-gradient(135deg, #f59e0b, #d97706); }

.stat-icon {
  font-size: 2rem;
  opacity: 0.85;
}

.stat-val {
  font-size: 1.6rem;
  font-weight: 800;
  line-height: 1.1;
  letter-spacing: -0.5px;
}

.stat-lbl {
  font-size: 0.78rem;
  font-weight: 500;
  opacity: 0.85;
  margin-top: 0.2rem;
}

/* mid grid */
.mid-grid {
  display: grid;
  grid-template-columns: 1fr 1.4fr;
  gap: 1.25rem;
}

/* cards */
.card {
  background: white;
  border-radius: 0.875rem;
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f3f4f6;
}

.card-title {
  font-size: 0.9375rem;
  font-weight: 700;
  color: #111827;
}

.card-badge {
  font-size: 0.75rem;
  background: #eef2ff;
  color: #4f46e5;
  border-radius: 9999px;
  padding: 0.2rem 0.65rem;
  font-weight: 600;
}

.card-body {
  padding: 1.25rem 1.5rem;
}

/* field bar rows */
.field-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.85rem;
}

.field-label {
  width: 60px;
  font-size: 0.8rem;
  font-weight: 500;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  text-transform: capitalize;
  flex-shrink: 0;
}

.field-dot {
  width: 9px;
  height: 9px;
  border-radius: 2px;
  flex-shrink: 0;
}
.field-dot.text    { background: #6366f1; }
.field-dot.number  { background: #14b8a6; }
.field-dot.date    { background: #f59e0b; }
.field-dot.boolean { background: #ec4899; }

.field-bar-wrap {
  flex: 1;
  height: 8px;
  background: #f3f4f6;
  border-radius: 9999px;
  overflow: hidden;
}

.field-bar {
  height: 100%;
  border-radius: 9999px;
  transition: width 0.6s ease;
}
.field-bar.text    { background: #6366f1; }
.field-bar.number  { background: #14b8a6; }
.field-bar.date    { background: #f59e0b; }
.field-bar.boolean { background: #ec4899; }

.field-count {
  font-size: 0.8rem;
  font-weight: 700;
  color: #374151;
  width: 24px;
  text-align: right;
  flex-shrink: 0;
}

/* shortcuts */
.shortcuts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.shortcut-card {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  padding: 1rem;
  border-radius: 0.625rem;
  border: 1px solid #f3f4f6;
  background: #fafafa;
  cursor: pointer;
  transition: all 0.15s;
  text-decoration: none;
  color: inherit;
}
.shortcut-card:hover {
  border-color: #c7d2fe;
  background: #f5f3ff;
}

.sc-icon {
  width: 36px;
  height: 36px;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}
.sc-icon.table   { background: #eef2ff; }
.sc-icon.chart   { background: #f0fdf4; }
.sc-icon.compare { background: #fff7ed; }
.sc-icon.filter  { background: #fdf2f8; }

.sc-label {
  font-size: 0.85rem;
  font-weight: 700;
  color: #111827;
}
.sc-desc {
  font-size: 0.75rem;
  color: #9ca3af;
  line-height: 1.4;
}

/* activity table */
.activity-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.8125rem;
}

.activity-table thead th {
  padding: 0.5rem 1rem;
  text-align: left;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #6b7280;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 700;
}

.activity-table tbody td {
  padding: 0.65rem 1rem;
  border-bottom: 1px solid #f3f4f6;
  color: #374151;
}

.activity-table tbody tr:last-child td { border-bottom: none; }
.activity-table tbody tr:hover td { background: #f9fafb; }

.action-badge {
  display: inline-block;
  padding: 0.2rem 0.65rem;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: capitalize;
}
.action-badge.amber  { background: #fef3c7; color: #b45309; }
.action-badge.teal   { background: #ccfbf1; color: #0f766e; }
.action-badge.violet { background: #ede9fe; color: #6d28d9; }
.action-badge.indigo { background: #eef2ff; color: #4338ca; }

.detail-cell { color: #6b7280; }
.time-cell   { color: #9ca3af; font-size: 0.78rem; white-space: nowrap; }
.mono        { font-family: monospace; font-size: 0.78rem; color: #6b7280; }

.empty-note {
  color: #9ca3af;
  font-size: 0.875rem;
  text-align: center;
  padding: 1.5rem 0;
  background: #fafafa;
  border-radius: 0.5rem;
  border: 1px dashed #e5e7eb;
}
</style>
