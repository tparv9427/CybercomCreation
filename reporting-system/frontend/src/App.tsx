import { useEffect, useCallback } from 'react';
import { useReportStore } from './store';
import { getFields, getData, exportData } from './api';
import FilterBuilder from './components/FilterBuilder';
import ColumnSelector from './components/ColumnSelector';
import DataTable from './components/DataTable';
import ChartRenderer from './components/ChartRenderer';
import SavedViews from './components/SavedViews';
import ComparisonTool from './components/ComparisonTool';

export default function App() {
  const {
    fields, setFields,
    setData, setLoading,
    filters, sort, rows, start, setStart,
    dateFrom, dateTo, setDateFrom, setDateTo,
    activeView,
  } = useReportStore();

  const fetchFields = useCallback(async () => {
    try {
      const data = await getFields();
      setFields(data);
      useReportStore.getState().setVisibleColumns(data.map((f) => f.name));
    } catch (e) {
      console.error('Failed to load fields', e);
    }
  }, []);

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = {
        rows,
        start,
        sort,
      };

      if (filters.rules.length > 0) {
        params.filters = JSON.stringify(filters);
      }
      if (dateFrom) params.date_from = dateFrom;
      if (dateTo) params.date_to = dateTo;

      const result = await getData(params);
      setData(result.docs, result.total, result.start);
    } catch (e) {
      console.error('Failed to load data', e);
    } finally {
      setLoading(false);
    }
  }, [filters, sort, rows, start, dateFrom, dateTo]);

  useEffect(() => {
    fetchFields();
  }, [fetchFields]);

  useEffect(() => {
    if (fields.length > 0) fetchData();
  }, [fields, filters, sort, rows, start, dateFrom, dateTo, fetchData]);

  const handleExport = () => {
    exportData(filters.rules.length > 0 ? filters : undefined);
  };

  return (
    <div style={{ backgroundColor: '#f8fafc', minHeight: '100vh', padding: 'clamp(16px, 4vw, 40px)' }}>
      <div style={{ maxWidth: 1600, margin: '0 auto' }}>
        
        {/* Top Navbar */}
        <header style={{ 
          display: 'flex', 
          flexDirection: window.innerWidth < 768 ? 'column' : 'row',
          alignItems: window.innerWidth < 768 ? 'flex-start' : 'center', 
          justifyContent: 'space-between', 
          marginBottom: 40,
          background: 'rgba(255, 255, 255, 0.8)',
          backdropFilter: 'blur(10px)',
          padding: '20px 32px',
          borderRadius: 20,
          boxShadow: '0 4px 12px rgba(0,0,0,0.03)',
          gap: 16
        }}>
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
              <div style={{ width: 32, height: 32, background: 'linear-gradient(135deg, #6366f1 0%, #a855f7 100%)', borderRadius: 8 }}></div>
              <h1 style={{ margin: 0, fontSize: 'clamp(20px, 2.5vw, 26px)', fontWeight: 800, letterSpacing: '-0.025em', color: '#0f172a' }}>
                Cybercom <span style={{ color: '#6366f1' }}>Analytics</span>
              </h1>
            </div>
            {activeView && (
              <div style={{ fontSize: 13, color: '#64748b', marginTop: 4, fontWeight: 500 }}>
                Project Dashboard / <span style={{ color: '#6366f1' }}>{activeView}</span>
              </div>
            )}
          </div>

          <div style={{ display: 'flex', gap: 12, alignItems: 'center', flexWrap: 'wrap' }}>
            <div style={{ display: 'flex', background: '#f1f5f9', padding: 4, borderRadius: 10, gap: 4, flexWrap: 'wrap' }}>
              <input
                type="date"
                value={dateFrom}
                onChange={(e) => { setDateFrom(e.target.value); setStart(0); }}
                style={{ background: 'transparent', border: 'none', padding: '6px 1px', fontSize: 12, fontWeight: 500, outline: 'none' }}
              />
              <div style={{ display: 'flex', alignItems: 'center', padding: '0 2px', color: '#94a3b8' }}>→</div>
              <input
                type="date"
                value={dateTo}
                onChange={(e) => { setDateTo(e.target.value); setStart(0); }}
                style={{ background: 'transparent', border: 'none', padding: '6px 1px', fontSize: 12, fontWeight: 500, outline: 'none' }}
              />
            </div>
            
            <button
              onClick={handleExport}
              style={{
                padding: '10px 16px',
                borderRadius: 10,
                border: '1px solid #e2e8f0',
                background: '#fff',
                color: '#475569',
                cursor: 'pointer',
                fontWeight: 600,
                fontSize: 13
              }}
            >
              Export
            </button>
            <button
              onClick={() => { setStart(0); fetchData(); }}
              style={{
                padding: '10px 16px',
                borderRadius: 10,
                border: 'none',
                background: '#0f172a',
                color: '#fff',
                cursor: 'pointer',
                fontWeight: 600,
                fontSize: 13,
                boxShadow: '0 4px 12px rgba(0,0,0,0.1)'
              }}
            >
              Refresh Data
            </button>
          </div>
        </header>

        <div style={{ 
          display: 'grid', 
          gridTemplateColumns: 'repeat(auto-fit, minmax(min(100%, 450px), 1fr))', 
          gap: 32,
          alignItems: 'start', // Ensure children don't stretch to full row height
          marginBottom: 40
        }}>
          
          {/* Sidebar / Left Column */}
          <aside style={{ display: 'flex', flexDirection: 'column', gap: 24, alignSelf: 'start' }}>
            <div style={{ background: '#fff', padding: 24, borderRadius: 20, boxShadow: '0 1px 3px rgba(0,0,0,0.05)', alignSelf: 'start', width: '100%', boxSizing: 'border-box' }}>
              <h3 style={{ margin: '0 0 16px 0', fontSize: 16, fontWeight: 700 }}>Saved Perspectives</h3>
              <SavedViews />
            </div>
            
            <div style={{ background: '#fff', padding: 24, borderRadius: 20, boxShadow: '0 1px 3px rgba(0,0,0,0.05)', alignSelf: 'start', width: '100%', boxSizing: 'border-box' }}>
              <h3 style={{ margin: '0 0 16px 0', fontSize: 16, fontWeight: 700 }}>Dynamic Filters</h3>
              <FilterBuilder />
              <button
                onClick={() => { useReportStore.getState().setStart(0); fetchData(); }}
                style={{
                  width: '100%',
                  marginTop: 8, // Reduced from 20
                  padding: '12px',
                  borderRadius: 10,
                  background: '#6366f1',
                  color: '#fff',
                  fontWeight: 700,
                  border: 'none',
                  cursor: 'pointer',
                  boxShadow: '0 4px 12px rgba(99, 102, 241, 0.2)'
                }}
              >
                Apply Changes
              </button>
              {filters.rules.length > 0 && (
                <button
                  onClick={() => {
                    useReportStore.getState().setFilters({ combinator: 'AND', rules: [] });
                    setStart(0);
                  }}
                  style={{
                    width: '100%',
                    marginTop: 8,
                    background: 'transparent',
                    color: '#ef4444',
                    fontWeight: 600,
                    border: 'none',
                    cursor: 'pointer',
                    fontSize: 13
                  }}
                >
                  Clear All Filters
                </button>
              )}
            </div>
          </aside>

          {/* Main Content Area */}
          <main style={{ display: 'flex', flexDirection: 'column', gap: 32 }}>
            <ChartRenderer />
            <ComparisonTool />
          </main>
        </div>

        {/* Full Width Data Table Section at the bottom */}
        <div style={{ marginTop: 32, background: '#fff', borderRadius: 20, boxShadow: '0 1px 3px rgba(0,0,0,0.05)', overflow: 'hidden' }}>
          <div style={{ padding: '24px 32px', borderBottom: '1px solid #f1f5f9', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <h3 style={{ margin: 0, fontSize: 18, fontWeight: 700 }}>Detailed Dataset</h3>
            <ColumnSelector />
          </div>
          <div style={{ padding: 24 }}>
            <DataTable />
          </div>
        </div>

      </div>
    </div>
  );
}