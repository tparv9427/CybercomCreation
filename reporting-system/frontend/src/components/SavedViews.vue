<template>
  <div class="saved-views">
    <!-- Save new view -->
    <div class="save-row">
      <input
        v-model="viewName"
        class="view-name-input"
        placeholder="Name this view…"
        @keyup.enter="save"
      />
      <button class="btn-save" :disabled="!viewName.trim() || saving" @click="save">
        {{ saving ? 'Saving…' : '💾 Save View' }}
      </button>
    </div>

    <!-- Saved list -->
    <div v-if="store.savedViews.length === 0" class="no-views">
      No saved views yet. Configure filters & columns, then save.
    </div>

    <ul class="views-list" v-else>
      <li v-for="view in store.savedViews" :key="view.id" class="view-item">
        <div class="view-meta">
          <span class="view-name">{{ view.name }}</span>
          <span class="view-sub">
            {{ (view.config?.selectedColumns ?? []).length }} columns ·
            {{ (view.config?.filters?.rules ?? []).length }} filters
          </span>
        </div>
        <div class="view-btns">
          <button class="btn-load" @click="store.applyView(view)">Load</button>
          <button class="btn-delete" @click="del(view.id)">✕</button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useReportStore } from '../stores/reportStore'

const store  = useReportStore()
const viewName = ref('')
const saving   = ref(false)

onMounted(() => store.fetchSavedViews())

async function save() {
  if (!viewName.value.trim()) return
  saving.value = true
  await store.saveView(viewName.value.trim())
  viewName.value = ''
  saving.value = false
}

async function del(id: number) {
  await store.deleteView(id)
}
</script>

<style scoped>
.saved-views {
  background: white;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  padding: 1.25rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.save-row {
  display: flex;
  gap: 0.75rem;
}

.view-name-input {
  flex: 1;
  padding: 0.5rem 0.875rem;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
  font-size: 0.875rem;
  outline: none;
  transition: border-color 0.15s;
}
.view-name-input:focus { border-color: #6366f1; }

.btn-save {
  background: #4f46e5;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 0.5rem 1.25rem;
  border-radius: 0.375rem;
  white-space: nowrap;
  transition: background 0.15s;
}
.btn-save:hover:not(:disabled) { background: #4338ca; }
.btn-save:disabled { opacity: 0.5; cursor: not-allowed; }

.no-views {
  font-size: 0.875rem;
  color: #9ca3af;
  text-align: center;
  padding: 1rem 0;
}

.views-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  max-height: 260px;
  overflow-y: auto;
}

.view-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  border: 1px solid #f3f4f6;
  background: #f9fafb;
  transition: border-color 0.15s;
}
.view-item:hover { border-color: #c7d2fe; }

.view-meta {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.view-name {
  font-weight: 600;
  font-size: 0.875rem;
  color: #111827;
}

.view-sub {
  font-size: 0.75rem;
  color: #9ca3af;
}

.view-btns {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-load {
  background: #eef2ff;
  color: #4f46e5;
  font-weight: 600;
  font-size: 0.8rem;
  padding: 0.3rem 0.85rem;
  border-radius: 0.375rem;
  border: 1px solid #c7d2fe;
  transition: background 0.15s;
}
.btn-load:hover { background: #e0e7ff; }

.btn-delete {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #fee2e2;
  color: #ef4444;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s;
}
.btn-delete:hover { background: #fca5a5; }
</style>
