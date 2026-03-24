<template>
  <div class="data-table-wrapper">
    <!-- Column Selector bar -->
    <div class="table-toolbar">
      <div class="col-selector">
        <div class="dropdown" ref="colDropdown">
          <button class="btn-dropdown" @click="showColDropdown = !showColDropdown">
            📊 Columns ({{ store.selectedColumns.length }}) ▾
          </button>
          <div class="dropdown-menu" v-if="showColDropdown">
            <div class="dropdown-search">
              <input type="text" v-model="colSearch" placeholder="Filter columns..." />
            </div>
            <div class="dropdown-items">
              <label 
                class="dropdown-item" 
                v-for="f in filteredFields" 
                :key="f.name"
              >
                <input 
                  type="checkbox" 
                  :value="f.name" 
                  v-model="store.selectedColumns"
                />
                {{ f.label }}
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="table-actions">
        <button class="btn-search" @click="reload" :disabled="store.loading">
          {{ store.loading ? '...' : '🔍 Search' }}
        </button>
        <span class="count-badge">{{ store.total.toLocaleString() }} rows</span>
        <select v-model.number="store.rows" class="rows-select" @change="store.resetPaging()">
          <option :value="25">25 / page</option>
          <option :value="50">50 / page</option>
          <option :value="100">100 / page</option>
        </select>
        <button class="btn-export" @click="store.exportCsv()">⬇ Export CSV</button>
        <button class="btn-export print-hide" @click="printReport">🖨️ Print PDF</button>
      </div>
    </div>

    <!-- Loading / Error states -->
    <div class="table-state" v-if="store.loading">
      <div class="spinner"></div> Loading data…
    </div>
    <div class="table-state error" v-else-if="store.error">⚠ {{ store.error }}</div>

    <!-- Table -->
    <div class="table-scroll" v-else>
      <table class="report-table">
        <thead>
          <tr>
            <th
              v-for="(col, index) in store.selectedColumns"
              :key="col"
              :draggable="true"
              @dragstart="startDragCol($event, index)"
              @dragover.prevent="onDragOverCol($event, index)"
              @drop="dropCol($event, index)"
              @click="setSort(col)"
              :style="{ minWidth: (store.columnWidths[col] ?? 140) + 'px' }"
              :class="{ 'th-sortable': true, 'drag-over': dragOverIdx === index }"
            >
              <div class="th-inner">
                <span>{{ labelFor(col) }}</span>
                <span class="sort-icon" v-if="sortField === col">
                  {{ sortDir === 'asc' ? '▲' : '▼' }}
                </span>
                <span class="sort-icon muted" v-else>⇅</span>
              </div>
              <!-- Resize handle -->
              <div
                class="resize-handle"
                @mousedown.stop="startResize($event, col)"
              ></div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="store.docs.length === 0">
            <td :colspan="store.selectedColumns.length" class="empty-row">
              No records found.
            </td>
          </tr>
          <tr
            v-else
            v-for="(doc, i) in store.docs"
            :key="i"
            :class="{ 'row-alt': i % 2 === 1 }"
          >
            <td v-for="col in store.selectedColumns" :key="col">
              <span v-html="formatCell(doc[col])"></span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination" v-if="!store.loading && store.total > 0">
      <button class="page-btn-wide" :disabled="store.cursor === '*'" @click="store.resetPaging()">« Start</button>
      <button class="page-btn-wide" :disabled="store.cursorHistory.length === 0" @click="store.prevPage()">‹ Prev</button>
      <span class="page-info">
        Paging: {{ store.cursorHistory.length + 1 }}
      </span>
      <button class="page-btn-wide" :disabled="!store.nextCursor || store.nextCursor === store.cursor" @click="store.nextPage()">Next ›</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useReportStore } from '../stores/reportStore'

const store = useReportStore()
const colDropdown = ref<HTMLElement | null>(null)
const showColDropdown = ref(false)
const colSearch = ref('')

const filteredFields = computed(() => {
  if (!colSearch.value) return store.fields
  const s = colSearch.value.toLowerCase()
  return store.fields.filter(f => f.label.toLowerCase().includes(s))
})

// Close dropdown on outside click
function handleClickOutside(e: MouseEvent) {
  if (colDropdown.value && !colDropdown.value.contains(e.target as Node)) {
    showColDropdown.value = false
  }
}

onMounted(() => window.addEventListener('click', handleClickOutside))
onUnmounted(() => window.removeEventListener('click', handleClickOutside))

const sortField = ref('')
const sortDir   = ref<'asc' | 'desc'>('asc')

function reload() {
  store.resetPaging()
}

function printReport() {
  window.print()
}

function labelFor(col: string) {
  return store.fields.find(f => f.name === col)?.label ?? col
}

function formatCell(val: any): string {
  if (val === undefined || val === null) return '—'
  if (Array.isArray(val)) return val.join(', ')
  return String(val)
}

function setSort(col: string) {
  if (sortField.value === col) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = col
    sortDir.value = 'asc'
  }
  store.sort = `${col} ${sortDir.value}`
  store.resetPaging()
}

// --- Column resize ---
let resizeStartX = 0
let resizeCol = ''
let resizeStartW = 0

function startResize(e: MouseEvent, col: string) {
  e.preventDefault()
  e.stopPropagation()
  resizeCol    = col
  resizeStartX = e.clientX
  resizeStartW = store.columnWidths[col] ?? 140

  const onMove = (ev: MouseEvent) => {
    const diff = ev.clientX - resizeStartX
    store.setColWidth(resizeCol, Math.max(80, resizeStartW + diff))
  }
  const onUp = () => {
    window.removeEventListener('mousemove', onMove)
    window.removeEventListener('mouseup', onUp)
  }
  window.addEventListener('mousemove', onMove)
  window.addEventListener('mouseup', onUp)
}

// --- Column Reordering (Drag & Drop) ---
const draggedIdx = ref<number | null>(null)
const dragOverIdx = ref<number | null>(null)

function startDragCol(e: DragEvent, index: number) {
  draggedIdx.value = index
  if (e.dataTransfer) {
    e.dataTransfer.effectAllowed = 'move'
    e.dataTransfer.dropEffect = 'move'
  }
}

function onDragOverCol(_e: DragEvent, index: number) {
  dragOverIdx.value = index
}

function dropCol(_e: DragEvent, index: number) {
  if (draggedIdx.value === null || draggedIdx.value === index) {
    dragOverIdx.value = null
    return
  }
  
  const cols = [...store.selectedColumns]
  const [removed] = cols.splice(draggedIdx.value, 1)
  cols.splice(index, 0, removed)
  
  store.selectedColumns = cols
  draggedIdx.value = null
  dragOverIdx.value = null
}
</script>

<style scoped>
.data-table-wrapper {
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

/* toolbar */
.table-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f3f4f6;
  flex-wrap: wrap;
  gap: 1rem;
}

.dropdown { position: relative; }
.btn-dropdown {
  background: white;
  border: 1px solid #d1d5db;
  padding: 0.4rem 0.85rem;
  border-radius: 0.375rem;
  font-size: 0.8rem;
  font-weight: 600;
  color: #374151;
  transition: all 0.15s;
}
.btn-dropdown:hover { border-color: #6366f1; color: #4f46e5; }

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 0.5rem;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  z-index: 100;
  min-width: 240px;
  max-height: 400px;
  display: flex;
  flex-direction: column;
}

.dropdown-search {
  padding: 0.75rem;
  border-bottom: 1px solid #f3f4f6;
}
.dropdown-search input {
  width: 100%;
  padding: 0.35rem 0.65rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  outline: none;
}
.dropdown-search input:focus { border-color: #6366f1; }

.dropdown-items {
  overflow-y: auto;
  padding: 0.5rem;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.45rem 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.8rem;
  color: #374151;
  cursor: pointer;
}
.dropdown-item:hover { background: #f9fafb; }
.dropdown-item input { width: 14px; height: 14px; cursor: pointer; }

.table-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.count-badge {
  font-size: 0.75rem;
  color: #6b7280;
  white-space: nowrap;
}

.rows-select {
  padding: 0.35rem 0.6rem;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
  font-size: 0.8rem;
  color: #374151;
  background: #f9fafb;
  outline: none;
}

.btn-export {
  background: #f9fafb;
  border: 1px solid #d1d5db;
  color: #374151;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 0.35rem 0.85rem;
  border-radius: 0.375rem;
  transition: all 0.15s;
}
.btn-export:hover { border-color: #6366f1; color: #4f46e5; }

.btn-search {
  background: #f97316;
  border: 1px solid #ea580c;
  color: white;
  font-size: 0.85rem;
  font-weight: 700;
  padding: 0.45rem 1.25rem;
  border-radius: 0.375rem;
  transition: all 0.15s;
  box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2);
}
.btn-search:hover:not(:disabled) { background: #ea580c; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(249, 115, 22, 0.25); }
.btn-search:active { transform: translateY(0); }
.btn-search:disabled { opacity: 0.6; cursor: not-allowed; }

/* states */
.table-state {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 0.9rem;
  gap: 0.75rem;
}
.table-state.error { color: #ef4444; }

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* table */
.table-scroll {
  overflow-x: auto;
  overflow-y: auto;
  max-height: 480px;
}

.report-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.8125rem;
}

.report-table thead th {
  position: sticky;
  top: 0;
  background: #f8fafc;
  z-index: 1;
  text-align: left;
  font-weight: 600;
  font-size: 0.75rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #e5e7eb;
  white-space: nowrap;
  cursor: pointer;
  user-select: none;
  position: relative;
}

.th-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
}

.sort-icon { font-size: 0.7rem; color: #6366f1; }
.sort-icon.muted { color: #d1d5db; }

.th-sortable:hover .sort-icon.muted { color: #9ca3af; }

.th-sortable[draggable="true"] {
  cursor: grab;
}
.th-sortable[draggable="true"]:active {
  cursor: grabbing;
}
.th-sortable.drag-over {
  border-left: 2px solid #6366f1 !important;
  background: #f0f4ff !important;
}

.resize-handle {
  position: absolute;
  right: 0;
  top: 0;
  width: 5px;
  height: 100%;
  cursor: col-resize;
  z-index: 2;
}
.resize-handle:hover { background: rgba(99, 102, 241, 0.25); }

.report-table tbody td {
  padding: 0.65rem 1rem;
  border-bottom: 1px solid #f3f4f6;
  color: #374151;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 280px;
}

.report-table tbody td mark {
  background: #fef08a;
  color: #854d0e;
  border-radius: 0.25rem;
  padding: 0 0.15rem;
  font-weight: 600;
}

.row-alt td { background: #fafafa; }

.report-table tbody tr:hover td {
  background: #f0f4ff;
}

.empty-row {
  text-align: center;
  padding: 3rem!important;
  color: #9ca3af;
}

/* pagination */
.pagination {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-top: 1px solid #f3f4f6;
}

.page-btn {
  width: 32px;
  height: 32px;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
  background: white;
  font-size: 0.875rem;
  color: #374151;
  transition: all 0.15s;
}
.page-btn:hover:not(:disabled) { border-color: #6366f1; color: #4f46e5; }
.page-btn:disabled { opacity: 0.35; cursor: not-allowed; }

.page-btn-wide {
  padding: 0 0.85rem;
  height: 32px;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
  background: white;
  font-size: 0.8rem;
  font-weight: 600;
  color: #374151;
  transition: all 0.15s;
}
.page-btn-wide:hover:not(:disabled) { border-color: #6366f1; color: #4f46e5; background: #f0f4ff; }
.page-btn-wide:disabled { opacity: 0.35; cursor: not-allowed; }

.page-info {
  font-size: 0.8rem;
  color: #6b7280;
  padding: 0 0.5rem;
  white-space: nowrap;
}
/* Print styles */
@media print {
  .print-hide,
  .table-toolbar,
  .pagination,
  .dropdown,
  .page-header,
  .section-header, /* Hide accordion headers in print */
  .sidebar,
  .topbar {
    display: none !important;
  }

  .data-table-wrapper {
    border: none;
    border-radius: 0;
  }

  .table-scroll {
    max-height: none !important;
    overflow: visible !important;
  }

  .report-table {
    font-size: 10pt;
  }

  .report-table thead th {
    position: static;
    background: #eee !important;
    border-bottom: 2pt solid #000;
  }

  .report-table tbody td {
    border-bottom: 1pt solid #ccc;
    white-space: normal; /* allow wrap in print */
    word-break: break-all;
  }

  /* Force charts onto new page if needed or keep together */
  .inline-chart-section {
    page-break-before: auto;
    page-break-inside: avoid;
    border: 1px solid #ccc;
    padding: 20px;
  }

  body {
    background: white !important;
    color: black !important;
  }
}
</style>
