import { useState } from 'react';
import { useReportStore } from '../store';
import type { FilterGroup, FilterRule } from '../types';

const OPERATORS = [
  { value: '=', label: 'Equals' },
  { value: '!=', label: 'Not Equals' },
  { value: 'contains', label: 'Contains' },
  { value: 'doesNotContain', label: 'Does Not Contain' },
  { value: 'beginsWith', label: 'Begins With' },
  { value: 'endsWith', label: 'Ends With' },
  { value: '>', label: 'Greater Than' },
  { value: '<', label: 'Less Than' },
  { value: '>=', label: 'Greater Than or Equal' },
  { value: '<=', label: 'Less Than or Equal' },
  { value: 'between', label: 'Between (val1,val2)' },
  { value: 'in', label: 'In (val1,val2,...)' },
  { value: 'notIn', label: 'Not In' },
  { value: 'isNull', label: 'Is Null' },
  { value: 'isNotNull', label: 'Is Not Null' },
];

function RuleRow({ rule, onChange, onRemove }: { rule: FilterRule; onChange: (r: FilterRule) => void; onRemove: () => void }) {
  const fields = useReportStore((s) => s.fields);
  return (
    <div style={{ display: 'flex', gap: 6, alignItems: 'center', marginBottom: 8, flexWrap: 'wrap', padding: '8px', background: 'rgba(255,255,255,0.5)', borderRadius: 8 }}>
      <select 
        value={rule.field} 
        onChange={(e) => onChange({ ...rule, field: e.target.value })} 
        style={{ flex: '1 1 120px', padding: '6px 8px', borderRadius: 6, border: '1px solid #e2e8f0', fontSize: 13 }}
      >
        <option value="">-- Field --</option>
        {fields.map((f) => (<option key={f.name} value={f.name}>{f.label}</option>))}
      </select>
      <select 
        value={rule.operator} 
        onChange={(e) => onChange({ ...rule, operator: e.target.value })} 
        style={{ flex: '0 1 100px', padding: '6px 8px', borderRadius: 6, border: '1px solid #e2e8f0', fontSize: 13 }}
      >
        {OPERATORS.map((op) => (<option key={op.value} value={op.value}>{op.label}</option>))}
      </select>
      {!['isNull', 'isNotNull'].includes(rule.operator) && (
        <input 
          value={rule.value} 
          onChange={(e) => onChange({ ...rule, value: e.target.value })} 
          placeholder="Value" 
          style={{ flex: '2 1 120px', padding: '6px 10px', borderRadius: 6, border: '1px solid #e2e8f0', fontSize: 13 }} 
        />
      )}
      <button 
        onClick={onRemove} 
        style={{ background: '#fee2e2', color: '#ef4444', border: 'none', borderRadius: 6, width: 32, height: 32, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', fontWeight: 700 }}
      >
        ✕
      </button>
    </div>
  );
}

function GroupBlock({ group, onChange, onRemove, depth = 0 }: { group: FilterGroup; onChange: (g: FilterGroup) => void; onRemove?: () => void; depth?: number }) {
  const addRule = () => {
    const fields = useReportStore.getState().fields;
    const newRule: FilterRule = { field: fields[0]?.name || '', operator: '=', value: '' };
    onChange({ ...group, rules: [...group.rules, newRule] });
  };

  const addGroup = () => {
    const newGroup: FilterGroup = { combinator: 'AND', rules: [] };
    onChange({ ...group, rules: [...group.rules, newGroup] });
  };

  const updateRule = (index: number, updated: FilterRule | FilterGroup) => {
    const rules = [...group.rules];
    rules[index] = updated;
    onChange({ ...group, rules });
  };

  const removeRule = (index: number) => {
    onChange({ ...group, rules: group.rules.filter((_, i) => i !== index) });
  };

  const baseColor = depth === 0 ? '#6366f1' : '#10b981';

  return (
    <div style={{ 
      border: `1px solid ${baseColor}44`, 
      borderRadius: 12, 
      padding: '12px 10px', 
      marginBottom: 12, 
      background: depth === 0 ? '#f8fbfc' : '#f0fdf4' ,
      boxShadow: 'inset 0 2px 4px rgba(0,0,0,0.01)'
    }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 12, flexWrap: 'wrap' }}>
        <span style={{ fontWeight: 600, fontSize: 12, color: '#64748b', textTransform: 'uppercase' }}>Match</span>
        <select 
          value={group.combinator} 
          onChange={(e) => onChange({ ...group, combinator: e.target.value as 'AND' | 'OR' })} 
          style={{ padding: '4px 10px', borderRadius: 6, border: `1px solid ${baseColor}88`, fontWeight: 700, fontSize: 12, color: baseColor }}
        >
          <option value="AND">AND</option>
          <option value="OR">OR</option>
        </select>
        <div style={{ marginLeft: 'auto', display: 'flex', gap: 6, flexWrap: 'wrap' }}>
          <button onClick={addRule} style={{ background: '#6366f1', color: '#fff', border: 'none', borderRadius: 6, padding: '4px 10px', cursor: 'pointer', fontSize: 11, fontWeight: 700 }}>+ Rule</button>
          {depth < 2 && <button onClick={addGroup} style={{ background: '#10b981', color: '#fff', border: 'none', borderRadius: 6, padding: '4px 10px', cursor: 'pointer', fontSize: 11, fontWeight: 700 }}>+ Group</button>}
          {onRemove && <button onClick={onRemove} style={{ background: 'transparent', color: '#ef4444', border: '1px solid #fee2e2', borderRadius: 6, padding: '4px 10px', cursor: 'pointer', fontSize: 11, fontWeight: 600 }}>Remove</button>}
        </div>
      </div>
      <div style={{ paddingLeft: depth === 0 ? 0 : 4 }}>
        {group.rules.map((rule, i) =>
          'combinator' in rule ? (
            <GroupBlock key={i} group={rule as FilterGroup} onChange={(g) => updateRule(i, g)} onRemove={() => removeRule(i)} depth={depth + 1} />
          ) : (
            <RuleRow key={i} rule={rule as FilterRule} onChange={(r) => updateRule(i, r)} onRemove={() => removeRule(i)} />
          )
        )}
      </div>
      {group.rules.length === 0 && (
        <div style={{ color: '#94a3b8', fontSize: 12, textAlign: 'center', padding: '16px 0', border: '1px dashed #e2e8f0', borderRadius: 8 }}>
          No parameters specified. Click "+ Rule" to start.
        </div>
      )}
    </div>
  );
}

export default function FilterBuilder() {
  const { filters, setFilters } = useReportStore();
  const [open, setOpen] = useState(true);
  return (
    <div style={{ marginBottom: 16 }}>
      <div onClick={() => setOpen(!open)} style={{ cursor: 'pointer', fontWeight: 800, fontSize: 14, marginBottom: 12, userSelect: 'none', display: 'flex', alignItems: 'center', gap: 8, color: '#334155' }}>
        <span style={{ transition: 'transform 0.2s', transform: open ? 'rotate(180deg)' : 'rotate(0deg)', display: 'inline-block' }}>▼</span> 
        FILTERS
      </div>
      {open && <GroupBlock group={filters} onChange={setFilters} depth={0} />}
    </div>
  );
}