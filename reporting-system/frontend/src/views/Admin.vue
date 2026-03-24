<template>
  <div class="admin-page">
    <div class="page-header">
      <div>
        <h1 class="page-title">Admin Monitoring</h1>
        <p class="page-sub">Security & Usage Analytics Overview</p>
      </div>
    </div>

    <div v-if="!store.adminStats" class="loading-full">
      <div class="spinner"></div>
      Fetching administrative data...
    </div>

    <div v-else class="admin-grid">
      <!-- Quick Stats -->
      <section class="stats-row">
        <div class="stat-card">
          <div class="stat-label">Total Users</div>
          <div class="stat-value">{{ store.adminStats.counts.totalUsers }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Lifetime Audit Events</div>
          <div class="stat-value">{{ store.adminStats.counts.totalLogs.toLocaleString() }}</div>
        </div>
      </section>

      <!-- Users & Actions -->
      <div class="charts-grid">
        <section class="admin-card">
          <h3 class="card-title">Top Active Users</h3>
          <div class="user-list">
            <div v-for="u in store.adminStats.topUsers" :key="u.email" class="user-item">
              <div class="u-info">
                <span class="u-name">{{ u.name }}</span>
                <span class="u-email">{{ u.email }}</span>
              </div>
              <span class="u-total">{{ u.total }} events</span>
            </div>
          </div>
        </section>

        <section class="admin-card">
          <h3 class="card-title">Feature Popularity</h3>
          <div class="action-chart">
             <div v-for="a in store.adminStats.actions" :key="a.action" class="bar-row">
                <span class="bar-label">{{ a.action.replace(/_/g, ' ') }}</span>
                <div class="bar-wrap">
                  <div class="bar-fill" :style="{ width: (a.total / maxAction * 100) + '%' }"></div>
                </div>
                <span class="bar-val">{{ a.total }}</span>
             </div>
          </div>
        </section>
      </div>

      <!-- Automation History -->
      <section class="admin-card full-width">
        <h3 class="card-title">Automated Report Delivery (Last 30 Days)</h3>
        <div class="history-plot" v-if="store.adminStats.sentReports.length">
           <div v-for="day in store.adminStats.sentReports" :key="day.date" class="plot-col">
              <div class="plot-bar" :style="{ height: (day.total / maxSent * 100) + '%' }" :title="day.date + ': ' + day.total">
                <span class="plot-hint">{{ day.total }}</span>
              </div>
              <span class="plot-lbl">{{ day.date.split('-').slice(1).join('/') }}</span>
           </div>
        </div>
        <div v-else class="empty-hint">No automated reports sent in this period.</div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useReportStore } from '../stores/reportStore'

const store = useReportStore()

onMounted(() => {
  store.fetchAdminStats()
})

const maxAction = computed(() => {
  if (!store.adminStats?.actions?.length) return 1
  return Math.max(...store.adminStats.actions.map((a: any) => a.total))
})

const maxSent = computed(() => {
  if (!store.adminStats?.sentReports?.length) return 1
  return Math.max(...store.adminStats.sentReports.map((d: any) => d.total))
})
</script>

<style scoped>
.admin-page {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.page-header { margin-bottom: 0.5rem; }
.page-title { font-size: 1.5rem; font-weight: 700; color: #111827; }
.page-sub { font-size: 0.875rem; color: #9ca3af; margin-top: 0.25rem; }

.loading-full {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 8rem;
  gap: 1rem;
  color: #64748b;
}

.admin-grid {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.25rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 0.5rem; }
.stat-value { font-size: 1.75rem; font-weight: 800; color: #4f46e5; }

.charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.admin-card {
  background: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
}

.full-width { grid-column: span 2; }

.card-title {
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.user-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.user-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 0.5rem;
  border: 1px solid #f1f5f9;
}

.u-info { display: flex; flex-direction: column; }
.u-name { font-weight: 600; font-size: 0.85rem; color: #334155; }
.u-email { font-size: 0.75rem; color: #94a3b8; }
.u-total { font-weight: 700; color: #4f46e5; font-size: 0.85rem; }

/* Bar chart */
.action-chart {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}
.bar-row {
  display: grid;
  grid-template-columns: 120px 1fr 40px;
  align-items: center;
  gap: 1rem;
}
.bar-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: capitalize; }
.bar-wrap { height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
.bar-fill { height: 100%; background: #6366f1; border-radius: 4px; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
.bar-val { font-size: 0.75rem; font-weight: 700; color: #334155; text-align: right; }

/* History Plot */
.history-plot {
  display: flex;
  align-items: flex-end;
  gap: 0.5rem;
  height: 200px;
  padding-top: 2rem;
}
.plot-col {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}
.plot-bar {
  width: 100%;
  background: #c7d2fe;
  border-radius: 4px 4px 0 0;
  position: relative;
  transition: all 0.3s;
  cursor: pointer;
}
.plot-bar:hover { background: #4f46e5; }
.plot-bar:hover .plot-hint { opacity: 1; transform: translateY(-24px); }
.plot-hint {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%) translateY(-18px);
  background: #1e293b;
  color: white;
  font-size: 0.65rem;
  padding: 0.15rem 0.35rem;
  border-radius: 4px;
  opacity: 0;
  transition: all 0.2s;
  pointer-events: none;
}
.plot-lbl { font-size: 0.65rem; color: #94a3b8; transform: rotate(-45deg); white-space: nowrap; margin-top: 0.5rem; }

.empty-hint {
  text-align: center;
  padding: 4rem;
  color: #94a3b8;
  font-style: italic;
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
</style>
