<template>
  <div class="saved-views">
    <!-- Save new view -->
    <div class="save-row">
      <input
        v-model="viewName"
        class="view-name-input"
        placeholder="Name this view…"
        @keyup.enter="save(false)"
      />
      <div class="save-options">
        <label class="public-checkbox">
          <input type="checkbox" v-model="isPublic" />
          <span>🌐 Share with team (Public)</span>
        </label>
        <div class="save-btns">
          <button v-if="store.currentViewId" class="btn-save-version" :disabled="saving" @click="save(true)">
            Save v{{ store.savedViews.find(v => v.id === store.currentViewId)?.version ? store.savedViews.find(v => v.id === store.currentViewId)!.version + 1 : 2 }}
          </button>
          <button class="btn-save" :disabled="!viewName.trim() || saving" @click="save(false)">
            {{ saving ? 'Saving…' : (store.currentViewId ? 'Save as New' : '💾 Save View') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Saved list -->
    <div v-if="store.savedViews.length === 0" class="no-views">
      No saved views yet. Configure filters & columns, then save.
    </div>

    <ul v-else class="views-list">
      <transition-group name="list">
        <li 
          v-for="view in store.savedViews" 
          :key="view.id"
          class="view-item"
          :class="{ 'view-active': store.currentViewId === view.id }"
        >
          <div class="view-meta">
            <span class="view-name">
              <span v-if="store.user?.default_view_id === view.id" class="default-star">⭐</span>
              {{ view.name }}
              <span v-if="view.version > 1" class="version-badge">v{{ view.version }}</span>
              <span v-if="view.is_public" class="public-badge">🌐 PUBLIC</span>
            </span>
            <span class="view-sub">
              By {{ view.user?.name || 'Unknown' }} • {{ new Date(view.created_at).toLocaleDateString() }}
            </span>
          </div>
          <div class="view-btns">
            <div class="schedule-control">
              <select v-model="view.tempFrequency" class="select-freq">
                <option value="">No Schedule</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
              <button class="btn-schedule" @click="store.setSchedule(view.id, view.tempFrequency || '')">
                ⏲️
              </button>
            </div>
            <button 
              v-if="store.user?.default_view_id !== view.id"
              class="btn-default" 
              @click="store.setDefaultView(view.id)"
              title="Set as Default"
            >
              Make Default
            </button>
            <button class="btn-load" @click="store.applyView(view)">Load</button>
            <button 
              v-if="view.user_id === store.user?.id || (store.user?.roles as any[])?.some(r => r.name === 'Admin')"
              class="btn-delete" 
              @click="del(view.id)"
            >
              ✕
            </button>
          </div>
        </li>
      </transition-group>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useReportStore } from '../stores/reportStore'

const store    = useReportStore()
const viewName = ref('')
const isPublic = ref(false)
const saving   = ref(false)

onMounted(async () => {
  await store.fetchSavedViews()
  // Add temp property for UI
  store.savedViews.forEach((v: any) => {
    v.tempFrequency = v.schedule?.frequency || ''
  })
})

async function save(asVersion: boolean) {
  const name = viewName.value.trim() || store.savedViews.find(v => v.id === store.currentViewId)?.name
  if (!name) return

  saving.value = true
  await store.saveView(name, asVersion && store.currentViewId ? store.currentViewId : undefined, isPublic.value)
  viewName.value = ''
  isPublic.value = false
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
  flex-direction: column;
  gap: 0.75rem;
}

.save-options {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.public-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: #4b5563;
  cursor: pointer;
  user-select: none;
}
.public-checkbox input {
  width: 15px;
  height: 15px;
  cursor: pointer;
}

.save-btns {
  display: flex;
  gap: 0.5rem;
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
  flex: 1;
  background: #4f46e5;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 0.5rem 1.25rem;
  border-radius: 0.375rem;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-save:hover:not(:disabled) { 
  background: #4338ca; 
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

.btn-save-version {
  flex: 1;
  background: #10b981;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  padding: 0.5rem 1.25rem;
  border-radius: 0.375rem;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-save-version:hover { 
  background: #059669; 
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

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
  position: relative; /* For transition-group absolute positioning */
}

.view-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  border: 1px solid #f3f4f6;
  background: #f9fafb;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.view-item:hover { 
  border-color: #c7d2fe; 
  transform: scale(1.01);
}
.view-item.view-active {
  background: #eff6ff;
  border-color: #3b82f6;
  box-shadow: 0 0 0 1px #3b82f6;
}

.view-meta {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.view-name {
  font-weight: 600;
  font-size: 0.875rem;
  color: #111827;
  display: flex;
  align-items: center;
  gap: 0.35rem;
  flex-wrap: wrap;
}

.version-badge {
  font-size: 0.65rem;
  background: #e2e8f0;
  color: #475569;
  padding: 0.1rem 0.35rem;
  border-radius: 9999px;
  font-weight: 700;
}

.public-badge {
  font-size: 0.6rem;
  background: #dcfce7;
  color: #166534;
  padding: 0.1rem 0.4rem;
  border-radius: 0.25rem;
  font-weight: 800;
  letter-spacing: 0.02em;
}

.view-sub {
  font-size: 0.75rem;
  color: #9ca3af;
}

.view-btns {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.schedule-control {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  background: #f1f5f9;
  padding: 0.2rem;
  border-radius: 0.5rem;
  border: 1px solid #e2e8f0;
}

.select-freq {
  font-size: 0.75rem;
  padding: 0.2rem 0.4rem;
  border-radius: 0.35rem;
  border: 1px solid #cbd5e1;
  background: white;
  outline: none;
}

.btn-schedule {
  background: white;
  border: 1px solid #cbd5e1;
  border-radius: 0.35rem;
  padding: 0.2rem 0.45rem;
  font-size: 0.8rem;
  transition: all 0.15s;
}
.btn-schedule:hover {
  background: #f8fafc;
  border-color: #94a3b8;
  transform: scale(1.05);
}

.btn-load {
  background: #eef2ff;
  color: #4f46e5;
  font-weight: 600;
  font-size: 0.8rem;
  padding: 0.3rem 0.85rem;
  border-radius: 0.375rem;
  border: 1px solid #c7d2fe;
  transition: all 0.2s;
}
.btn-load:hover { 
  background: #e0e7ff; 
  transform: translateY(-1px);
}

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

.btn-default {
  background: #fef3c7;
  color: #b45309;
  font-weight: 600;
  font-size: 0.75rem;
  padding: 0.3rem 0.65rem;
  border-radius: 0.375rem;
  border: 1px solid #fde68a;
  transition: background 0.15s;
}
.btn-default:hover { background: #fde68a; color: #92400e; }

.default-star {
  color: #eab308;
  font-size: 0.8rem;
  margin-right: 0.2rem;
}

/* Transitions */
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.4s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.list-leave-active {
  position: absolute;
  width: 100%;
}
</style>
