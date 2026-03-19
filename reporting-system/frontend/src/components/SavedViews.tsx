import { useState, useEffect } from 'react';
import { getSavedViews, saveView, deleteView } from '../api';
import type { SavedView } from '../types';
import { useReportStore } from '../store';

export default function SavedViews() {
  const [views, setViews] = useState<SavedView[]>([]);
  const [open, setOpen] = useState(false);
  const [newName, setNewName] = useState('');
  const [saving, setSaving] = useState(false);
  const [busyId, setBusyId] = useState<number | null>(null);

  const { filters, visibleColumns, sort, rows, setFilters, setVisibleColumns, setSort, setRows, setActiveView } = useReportStore();

  const load = async () => {
    try {
      const data = await getSavedViews();
      setViews(data);
    } catch (e) {
      console.error(e);
    }
  };

  useEffect(() => { load(); }, []);

  const handleSave = async () => {
    if (!newName.trim()) return;
    setSaving(true);
    try {
      await saveView(newName.trim(), { filters, visibleColumns, sort, rows });
      setNewName('');
      await load();
    } finally {
      setSaving(false);
    }
  };

  const handleLoad = (e: React.MouseEvent, view: SavedView) => {
    e.stopPropagation();
    setBusyId(view.id);

    // Give the browser a chance to render the "Loading..." button state
    setTimeout(() => {
      try {
        let config = view.config as any;
        if (typeof config === 'string') {
          config = JSON.parse(config);
        }

        if (config.filters) setFilters(config.filters);
        if (config.visibleColumns) setVisibleColumns(config.visibleColumns);
        if (config.sort) setSort(config.sort);
        if (config.rows) setRows(config.rows);
        setActiveView(view.name);
      } catch (err) {
        console.error('Failed to apply view config', err);
      } finally {
        setBusyId(null);
      }
    }, 20); // 20ms macrotask deferral
  };

  const handleDelete = async (e: React.MouseEvent, id: number) => {
    e.stopPropagation();

    // Optimistically remove from the UI immediately for instant feedback
    setViews((prev) => prev.filter(v => v.id !== id));

    try {
      await deleteView(id);
    } catch (err) {
      console.error('Delete failed, fetching fresh list', err);
      await load(); // Only reload if the optimistic deletion failed
    }
  };

  return (
    <div style={{ marginBottom: 16 }}>
      <div onClick={() => setOpen(!open)} style={{ cursor: 'pointer', fontWeight: 700, fontSize: 15, marginBottom: 8, userSelect: 'none', display: 'flex', alignItems: 'center', gap: 6 }}>
        <span>{open ? '▼' : '▶'}</span> Saved Views ({views.length})
      </div>
      {open && (
        <div style={{ border: '1px solid #e2e8f0', borderRadius: 8, padding: 16, background: '#fff' }}>
          <div style={{ display: 'flex', gap: 8, marginBottom: 16 }}>
            <input value={newName} onChange={(e) => setNewName(e.target.value)} placeholder="View name..." onKeyDown={(e) => e.key === 'Enter' && handleSave()} style={{ flex: 1, padding: '6px 10px', borderRadius: 6, border: '1px solid #ccc', fontSize: 13 }} />
            <button onClick={handleSave} disabled={saving || !newName.trim()} style={{ padding: '6px 16px', borderRadius: 6, border: 'none', background: '#3182ce', color: '#fff', cursor: saving || !newName.trim() ? 'not-allowed' : 'pointer', fontWeight: 600, fontSize: 13 }}>
              {saving ? 'Saving...' : 'Save Current'}
            </button>
          </div>
          {views.length === 0 ? (
            <div style={{ color: '#a0aec0', textAlign: 'center', padding: '12px 0', fontSize: 13 }}>No saved views yet</div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
              {views.map((v) => (
                <div key={v.id} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '8px 12px', borderRadius: 6, border: '1px solid #e2e8f0', background: '#f7fafc', opacity: busyId === v.id ? 0.6 : 1 }}>
                  <div>
                    <div style={{ fontWeight: 600, fontSize: 13 }}>{v.name}</div>
                    <div style={{ fontSize: 11, color: '#a0aec0' }}>{new Date(v.created_at).toLocaleDateString()}</div>
                  </div>
                  <div style={{ display: 'flex', gap: 6 }}>
                    <button disabled={busyId === v.id} onClick={(e) => handleLoad(e, v)} style={{ padding: '4px 12px', borderRadius: 4, border: '1px solid #3182ce', color: '#3182ce', background: '#fff', cursor: busyId === v.id ? 'not-allowed' : 'pointer', fontSize: 12 }}>
                      {busyId === v.id ? 'Loading...' : 'Load'}
                    </button>
                    <button disabled={busyId === v.id} onClick={(e) => handleDelete(e, v.id)} style={{ padding: '4px 12px', borderRadius: 4, border: '1px solid #e53e3e', color: '#e53e3e', background: '#fff', cursor: busyId === v.id ? 'not-allowed' : 'pointer', fontSize: 12 }}>
                      Delete
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  );
}