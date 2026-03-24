<template>
  <div class="filter-group-container" :class="{ 'is-nested': !isRoot }">
    <div class="group-header">
      <div class="combinator-select">
        <label>Match:</label>
        <select v-model="group.combinator">
          <option value="AND">ALL (AND)</option>
          <option value="OR">ANY (OR)</option>
        </select>
      </div>
      <div class="group-actions">
        <button class="btn-add" @click="store.addFilter(group)">+ Add Filter</button>
        <button class="btn-add secondary" @click="store.addGroup(group)">+ Add Group</button>
        <button v-if="!isRoot" class="btn-remove-group" @click="$emit('remove')">✕</button>
      </div>
    </div>

    <div class="rules-list">
      <transition-group name="fade">
        <div v-for="rule in group.rules" :key="rule.id" class="rule-item">
          <!-- Render Rule -->
          <div v-if="rule.type === 'rule'" class="rule-row">
            <select v-model="rule.field" class="rule-select">
              <option v-for="f in store.fields" :key="f.name" :value="f.name">{{ f.label }}</option>
            </select>

            <select v-model="rule.operator" class="rule-op">
              <option v-for="op in operatorsFor(rule.field)" :key="op.val" :value="op.val">{{ op.label }}</option>
            </select>

            <template v-if="fieldType(rule.field) !== 'boolean'">
              <!-- Range Filter (Between) -->
              <div v-if="rule.operator === 'between'" class="range-row">
                <input
                  :value="getRange(rule.value, 0)"
                  @input="e => setRange(rule, (e.target as any).value, 0)"
                  :type="inputType(rule.field)"
                  class="rule-input-sm"
                  placeholder="Min..."
                />
                <span class="range-sep">to</span>
                <input
                  :value="getRange(rule.value, 1)"
                  @input="e => setRange(rule, (e.target as any).value, 1)"
                  :type="inputType(rule.field)"
                  class="rule-input-sm"
                  placeholder="Max..."
                />
              </div>

              <!-- Single Select -->
              <select 
                v-else-if="fieldType(rule.field) === 'select'"
                v-model="rule.value" 
                class="rule-select rule-input"
              >
                <option value="">Select Option...</option>
                <option v-for="opt in fieldOptions(rule.field)" :key="opt" :value="opt">{{ opt }}</option>
              </select>

              <!-- Regular Text/Number/Date Input -->
              <div v-else class="autocomplete-wrapper">
                <input
                  v-model="rule.value"
                  :type="inputType(rule.field)"
                  class="rule-input"
                  placeholder="Value..."
                  @input="onInput(rule)"
                  @focus="onInput(rule)"
                  @blur="hideSuggestions(rule.id)"
                />
                <div v-if="suggestions[rule.id]?.length" class="suggestion-list">
                  <div 
                    v-for="s in suggestions[rule.id]" 
                    :key="s" 
                    class="suggestion-item"
                    @mousedown.prevent="selectSuggestion(rule, s)"
                  >
                    {{ s }}
                  </div>
                </div>
              </div>
            </template>

            <!-- Boolean select -->
            <div v-else class="rule-input-wrapper">
              <select v-model="rule.value" class="rule-select">
                <option value="true">True</option>
                <option value="false">False</option>
              </select>
            </div>

            <button class="btn-remove-rule" @click="remove(rule.id)">✕</button>
          </div>

          <!-- Recursively Render Group -->
          <FilterRow 
            v-else 
            :group="(rule as any)" 
            @remove="remove(rule.id)"
          />
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useReportStore, type FilterGroup } from '../stores/reportStore'

const props = defineProps<{
  group: FilterGroup
  isRoot?: boolean
}>()

const emit = defineEmits(['remove'])
const store = useReportStore()

function fieldType(fieldName: string) {
  return store.fields.find(f => f.name === fieldName)?.type ?? 'text'
}

function fieldOptions(fieldName: string) {
  return store.fields.find(f => f.name === fieldName)?.options ?? []
}

function inputType(fieldName: string) {
  const t = fieldType(fieldName)
  if (t === 'number') return 'number'
  if (t === 'date') return 'date'
  return 'text'
}

function operatorsFor(fieldName: string) {
  const t = fieldType(fieldName)
  if (t === 'number') return [
    { val: '=', label: 'equals' }, { val: '>', label: 'greater than' }, { val: '<', label: 'less than' }, { val: 'between', label: 'between' },
  ]
  if (t === 'date') return [
    { val: '=', label: 'on' }, { val: '>', label: 'after' }, { val: '<', label: 'before' }, { val: 'between', label: 'between' },
  ]
  if (t === 'boolean') return [{ val: '=', label: 'is' }]
  return [{ val: '=', label: 'equals' }, { val: 'contains', label: 'contains' }, { val: 'starts_with', label: 'starts with' }]
}

function remove(id: string) {
  props.group.rules = props.group.rules.filter(r => r.id !== id)
}

function getRange(val: string, idx: number) {
  const p = (val || '').split(',')
  return p[idx] || ''
}

function setRange(rule: any, val: string, idx: number) {
  const p = (rule.value || '').split(',')
  while (p.length < 2) p.push('')
  p[idx] = val
  rule.value = p.join(',')
}

// ── Autocomplete Logic ───────────────────────────────────────────────────────
const suggestions = ref<Record<string, string[]>>({})

async function onInput(rule: any) {
  if (rule.operator !== '=') {
    suggestions.value[rule.id] = []
    return
  }
  const type = fieldType(rule.field)
  if (type !== 'text') {
    suggestions.value[rule.id] = []
    return
  }

  const res = await store.fetchSuggestions(rule.field, rule.value)
  suggestions.value[rule.id] = res
}

function selectSuggestion(rule: any, s: string) {
  rule.value = s
  suggestions.value[rule.id] = []
}

function hideSuggestions(id: string) {
  // Use timeout to allow click event on suggestion item to fire first
  setTimeout(() => {
    delete suggestions.value[id]
  }, 200)
}
</script>

<style scoped>
.autocomplete-wrapper {
  position: relative;
  flex: 1;
  min-width: 120px;
}

.suggestion-list {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  z-index: 100;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  margin-top: 4px;
  max-height: 200px;
  overflow-y: auto;
}

.suggestion-item {
  padding: 0.5rem 0.75rem;
  font-size: 0.8rem;
  cursor: pointer;
  color: #1e293b;
  transition: background 0.15s;
}

.suggestion-item:hover {
  background: #f8fafc;
  color: #4f46e5;
}
.range-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.range-sep {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #94a3b8;
}

.rule-input-sm {
  width: 100px;
  padding: 0.35rem 0.65rem;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
  font-size: 0.8rem;
  outline: none;
}
.filter-group-container {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.is-nested {
  border-left: 2px solid #e5e7eb;
  padding-left: 1rem;
  margin-left: 0.25rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  background: #fcfcfd;
  border-radius: 0 0.5rem 0.5rem 0;
}

.group-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.combinator-select {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  font-weight: 600;
  color: #6b7280;
}

.combinator-select select {
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  border: 1px solid #d1d5db;
  font-size: 0.75rem;
}

.group-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-add {
  background: #f5f3ff;
  color: #4f46e5;
  font-weight: 600;
  font-size: 0.75rem;
  padding: 0.3rem 0.75rem;
  border-radius: 0.375rem;
  border: 1px solid #ddd6fe;
}

.btn-add.secondary {
  background: #f0fdf4;
  color: #16a34a;
  border-color: #bbf7d0;
}

.btn-remove-group {
  color: #ef4444;
  padding: 0.25rem 0.5rem;
  font-size: 0.8rem;
}

.rules-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.rule-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.rule-select, .rule-op, .rule-input {
  padding: 0.35rem 0.65rem;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
  font-size: 0.8rem;
  outline: none;
}

.rule-select { min-width: 160px; }
.rule-op { min-width: 120px; }
.rule-input { flex: 1; min-width: 120px; }

.btn-remove-rule {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  color: #ef4444;
  background: #fee2e2;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
}

/* Transitions */
.fade-move,
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

.fade-leave-active {
  position: absolute; /* Ensures smooth movement of remaining items */
  width: 100%;
}
</style>
