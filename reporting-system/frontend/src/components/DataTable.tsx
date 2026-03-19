import { useMemo, useState } from 'react';
import { useReactTable, getCoreRowModel, flexRender } from '@tanstack/react-table';
import type { ColumnDef, SortingState } from '@tanstack/react-table';
import { useReportStore } from '../store';

type Doc = Record<string, unknown>;

export default function DataTable() {
  const { docs, total, start, rows, setStart, setRows, visibleColumns, fields, sort, setSort, loading } = useReportStore();
  const [sorting, setSorting] = useState<SortingState>([]);

  const columns = useMemo<ColumnDef<Doc>[]>(() => {
    const cols = visibleColumns.length > 0
      ? fields.filter((f) => visibleColumns.includes(f.name))
      : fields;

    return cols.map((f) => ({
      accessorKey: f.name,
      header: () => (
        <span
          style={{ cursor: 'pointer', userSelect: 'none' }}
          onClick={() => {
            const isAsc = sort === `${f.name} asc`;
            setSort(isAsc ? `${f.name} desc` : `${f.name} asc`);
          }}
        >
          {f.label}
          {sort.startsWith(f.name) ? (sort.endsWith('asc') ? ' ▲' : ' ▼') : ''}
        </span>
      ),
      cell: (info) => {
        const val = info.getValue();
        if (val === null || val === undefined) return <span style={{ color: '#a0aec0' }}>—</span>;
        return String(val);
      },
    }));
  }, [visibleColumns, fields, sort]);

  const table = useReactTable({
    data: docs,
    columns,
    getCoreRowModel: getCoreRowModel(),
    manualSorting: true,
    state: { sorting },
    onSortingChange: setSorting,
  });

  const totalPages = Math.ceil(total / rows);
  const currentPage = Math.floor(start / rows) + 1;

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 20 }}>
      {/* Pagination & Stats Row */}
      <div style={{ 
        display: 'flex', 
        alignItems: 'center', 
        justifyContent: 'space-between',
        flexWrap: 'wrap',
        gap: 16,
        padding: '12px 16px',
        background: '#f8fafc',
        borderRadius: 12,
        border: '1px solid #e2e8f0'
      }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
          <span style={{ fontSize: 13, fontWeight: 600, color: '#475569' }}>
            {total.toLocaleString()} <span style={{ fontWeight: 400, color: '#64748b' }}>total records</span>
          </span>
          <span style={{ fontSize: 13, fontWeight: 600, color: '#475569' }}>
            Page {currentPage} <span style={{ fontWeight: 400, color: '#64748b' }}>of {totalPages || 1}</span>
          </span>
          <button
            onClick={() => {
              const { filters } = useReportStore.getState();
              import('../api').then(({ exportData }) => {
                exportData(filters.rules.length > 0 ? filters : undefined);
              });
            }}
            style={{
              display: 'flex',
              alignItems: 'center',
              gap: 6,
              padding: '6px 12px',
              borderRadius: 8,
              border: '1px solid #e2e8f0',
              background: '#fff',
              color: '#475569',
              fontSize: 12,
              fontWeight: 600,
              cursor: 'pointer',
              marginLeft: 8,
              boxShadow: '0 1px 2px rgba(0,0,0,0.05)',
              transition: 'all 0.2s'
            }}
          >
            <span style={{ fontSize: 14 }}>📥</span>
            Export CSV
          </button>
        </div>

        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: 6, marginRight: 12 }}>
            <span style={{ fontSize: 13, color: '#64748b' }}>Show</span>
            <select 
              value={rows} 
              onChange={(e) => { setRows(Number(e.target.value)); setStart(0); }} 
              style={{ 
                padding: '4px 8px', 
                borderRadius: 8, 
                border: '1px solid #e2e8f0', 
                fontSize: 13,
                fontWeight: 600,
                outline: 'none',
                background: '#fff'
              }}
            >
              {[25, 50, 100, 200, 500].map((n) => (<option key={n} value={n}>{n} / page</option>))}
            </select>
          </div>

          <div style={{ display: 'flex', gap: 4 }}>
            {[
              { label: '«', action: () => setStart(0), disabled: start === 0 },
              { label: '‹ Prev', action: () => setStart(Math.max(0, start - rows)), disabled: start === 0 },
              { label: 'Next ›', action: () => setStart(start + rows), disabled: start + rows >= total },
              { label: '»', action: () => setStart((totalPages - 1) * rows), disabled: start + rows >= total },
            ].map((btn, idx) => (
              <button
                key={idx}
                disabled={btn.disabled}
                onClick={btn.action}
                style={{
                  padding: '6px 12px',
                  borderRadius: 8,
                  border: '1px solid #e2e8f0',
                  cursor: btn.disabled ? 'not-allowed' : 'pointer',
                  background: btn.disabled ? '#f1f5f9' : '#fff',
                  color: btn.disabled ? '#cbd5e1' : '#475569',
                  fontSize: 12,
                  fontWeight: 600,
                  transition: 'all 0.2s'
                }}
              >
                {btn.label}
              </button>
            ))}
          </div>
        </div>
      </div>

      {loading ? (
        <div style={{ textAlign: 'center', padding: 40, fontSize: 16, color: '#888' }}>Loading...</div>
      ) : (
        <div style={{ overflowX: 'auto', borderRadius: 8, border: '1px solid #e2e8f0' }}>
          <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
            <thead>
              {table.getHeaderGroups().map((hg) => (
                <tr key={hg.id} style={{ background: '#f7fafc' }}>
                  {hg.headers.map((header) => (
                    <th key={header.id} style={{ padding: '10px 12px', textAlign: 'left', borderBottom: '2px solid #e2e8f0', fontWeight: 700, whiteSpace: 'nowrap', color: '#2d3748' }}>
                      {flexRender(header.column.columnDef.header, header.getContext())}
                    </th>
                  ))}
                </tr>
              ))}
            </thead>
            <tbody>
              {table.getRowModel().rows.length === 0 ? (
                <tr><td colSpan={columns.length || 1} style={{ textAlign: 'center', padding: 32, color: '#a0aec0' }}>No data found</td></tr>
              ) : (
                table.getRowModel().rows.map((row, i) => (
                  <tr key={row.id} style={{ background: i % 2 === 0 ? '#fff' : '#f7fafc' }}>
                    {row.getVisibleCells().map((cell) => (
                      <td key={cell.id} style={{ padding: '8px 12px', borderBottom: '1px solid #e2e8f0', whiteSpace: 'nowrap', maxWidth: 200, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                        {flexRender(cell.column.columnDef.cell, cell.getContext())}
                      </td>
                    ))}
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}