import { useState } from 'react';
import { useReportStore } from '../store';

export default function ColumnSelector() {
  const { fields, visibleColumns, setVisibleColumns } = useReportStore();
  const [open, setOpen] = useState(false);

  const toggle = (name: string) => {
    if (visibleColumns.includes(name)) {
      setVisibleColumns(visibleColumns.filter((c) => c !== name));
    } else {
      setVisibleColumns([...visibleColumns, name]);
    }
  };

  const selectAll = () => setVisibleColumns(fields.map((f) => f.name));
  const clearAll = () => setVisibleColumns([]);

  return (
    <div style={{ position: 'relative', display: 'inline-block', marginBottom: 12 }}>
      <button
        onClick={() => setOpen(!open)}
        style={{ padding: '6px 14px', borderRadius: 6, border: '1px solid #ccc', background: '#fff', cursor: 'pointer', fontWeight: 600, fontSize: 13 }}
      >
        ⚙ Columns ({visibleColumns.length}/{fields.length})
      </button>

      {open && (
        <div style={{ position: 'absolute', top: '110%', left: 0, background: '#fff', border: '1px solid #ccc', borderRadius: 8, padding: 12, zIndex: 100, minWidth: 220, boxShadow: '0 4px 12px rgba(0,0,0,0.15)' }}>
          <div style={{ display: 'flex', gap: 8, marginBottom: 10 }}>
            <button onClick={selectAll} style={{ flex: 1, padding: '4px 0', fontSize: 12, borderRadius: 4, border: '1px solid #ccc', cursor: 'pointer', background: '#edf2f7' }}>All</button>
            <button onClick={clearAll} style={{ flex: 1, padding: '4px 0', fontSize: 12, borderRadius: 4, border: '1px solid #ccc', cursor: 'pointer', background: '#edf2f7' }}>None</button>
          </div>
          <div style={{ maxHeight: 260, overflowY: 'auto' }}>
            {fields.map((f) => (
              <label key={f.name} style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '4px 0', cursor: 'pointer', fontSize: 13 }}>
                <input type="checkbox" checked={visibleColumns.includes(f.name)} onChange={() => toggle(f.name)} />
                {f.label}
              </label>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}