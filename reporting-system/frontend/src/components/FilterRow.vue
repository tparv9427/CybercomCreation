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
      <div v-for="rule in group.rules" :key="rule.id" class="rule-item">
        <!-- Render Rule -->
        <div v-if="rule.type === 'rule'" class="rule-row">
          <select v-model="rule.field" class="rule-select">
            <option v-for="f in store.fields" :key="f.name" :value="f.name">{{ f.label }}</option>
          </select>

          <select v-model="rule.operator" class="rule-op">
            <option v-for="op in operatorsFor(rule.field)" :key="op.val" :value="op.val">{{ op.label }}</option>
          </select>

          <input
            v-if="fieldType(rule.field) !== 'boolean'"
            v-model="rule.value"
            :type="inputType(rule.field)"
            class="rule-input"
            placeholder="Value..."
          />
          <select v-else v-model="rule.value" class="rule-select">
            <option value="true">True</option>
            <option value="false">False</option>
          </select>

          <button class="btn-remove-rule" @click="remove(rule.id)">✕</button>
        </div>

        <!-- Recursively Render Group -->
        <FilterRow 
          v-else 
          :group="(rule as any)" 
          @remove="remove(rule.id)"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
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
</script>

<style scoped>
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
</style>
