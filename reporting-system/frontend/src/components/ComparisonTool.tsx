import React, { useState } from 'react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip as RechartsTooltip, Legend, ResponsiveContainer } from 'recharts';
import { useReportStore } from '../store';
import { getComparison } from '../api';

const ComparisonTool: React.FC = () => {
  const {
    fields,
    compareRangeA, setCompareRangeA,
    compareRangeB, setCompareRangeB,
    compareResult, setCompareResult,
    showComparison, setShowComparison,
    filters
  } = useReportStore();

  const numericFields = fields.filter(f => f.type === 'number');
  const categoricalFields = fields.filter(f => f.type === 'select' || f.type === 'text');
  const dateFields = fields.filter(f => f.type === 'date');

  const [field, setField] = useState(numericFields[0]?.name || 'Price_f');
  const [groupBy, setGroupBy] = useState(categoricalFields[0]?.name || 'Brand_Name_s');
  const [dateField, setDateField] = useState('Date_dt');
  const [loading, setLoading] = useState(false);
  const [hasRun, setHasRun] = useState(false);

  const handleCompare = async () => {
    if (!compareRangeA.from || !compareRangeB.from) {
      alert('You must select a Start Date for both Period A and Period B to perform a comparison.');
      return;
    }

    setLoading(true);
    setHasRun(true);
    try {
      const params: any = {
        field,
        group_by: groupBy,
        date_field: dateField,
        date_from_a: compareRangeA.from,
        date_to_a: compareRangeA.to || 'NOW',
        date_from_b: compareRangeB.from,
        date_to_b: compareRangeB.to || 'NOW',
      };

      if (filters.rules.length > 0) {
        params.filters = JSON.stringify(filters);
      }

      const res = await getComparison(params);
      setCompareResult(res);
      setShowComparison(true);
    } catch (e) {
      console.error('Comparison failed', e);
      alert('The comparison could not be completed. Please ensure you have data within the selected date ranges.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ background: '#fff', padding: 'clamp(12px, 2vw, 24px)', borderRadius: 20, boxShadow: '0 1px 3px rgba(0,0,0,0.05)', marginBottom: 24 }}>
      <h3 style={{ margin: '0 0 20px 0', fontSize: 18, fontWeight: 700 }}>Period Comparison</h3>
      
      <div style={{ 
        display: 'grid', 
        gridTemplateColumns: 'repeat(auto-fit, minmax(min(100%, 280px), 1fr))', 
        gap: 20, 
        marginBottom: 20 
      }}>
        <div>
          <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#64748b', marginBottom: 6 }}>Period A (Baseline)</label>
          <div style={{ display: 'flex', gap: 8 }}>
            <input 
              type="date" 
              value={compareRangeA.from}
              onChange={e => setCompareRangeA({ ...compareRangeA, from: e.target.value })}
              style={{ flex: 1, padding: '10px 12px', borderRadius: 10, border: '1px solid #e2e8f0', fontSize: 13 }}
            />
            <input 
              type="date" 
              value={compareRangeA.to}
              onChange={e => setCompareRangeA({ ...compareRangeA, to: e.target.value })}
              style={{ flex: 1, padding: '10px 12px', borderRadius: 10, border: '1px solid #e2e8f0', fontSize: 13 }}
            />
          </div>
        </div>
        <div>
          <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#64748b', marginBottom: 6 }}>Period B (Comparison)</label>
          <div style={{ display: 'flex', gap: 8 }}>
            <input 
              type="date" 
              value={compareRangeB.from}
              onChange={e => setCompareRangeB({ ...compareRangeB, from: e.target.value })}
              style={{ flex: 1, padding: '10px 12px', borderRadius: 10, border: '1px solid #e2e8f0', fontSize: 13 }}
            />
            <input 
              type="date" 
              value={compareRangeB.to}
              onChange={e => setCompareRangeB({ ...compareRangeB, to: e.target.value })}
              style={{ flex: 1, padding: '10px 12px', borderRadius: 10, border: '1px solid #e2e8f0', fontSize: 13 }}
            />
          </div>
        </div>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: 16, marginBottom: 20, alignItems: 'end' }}>
        <div>
          <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#64748b', marginBottom: 6 }}>Metric</label>
          <select 
            value={field} 
            onChange={e => setField(e.target.value)}
            style={{ width: '100%', padding: '10px 12px', borderRadius: 10, border: '1px solid #e2e8f0', fontSize: 14, background: '#f8fafc', outline: 'none' }}
          >
            {numericFields.map(f => <option key={f.name} value={f.name}>{f.label}</option>)}
          </select>
        </div>
        <div>
          <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#64748b', marginBottom: 6 }}>Group By</label>
          <select 
            value={groupBy} 
            onChange={e => setGroupBy(e.target.value)}
            style={{ width: '100%', padding: '10px 12px', borderRadius: 10, border: '1px solid #e2e8f0', fontSize: 14, background: '#f8fafc', outline: 'none' }}
          >
            {categoricalFields.map(f => <option key={f.name} value={f.name}>{f.label}</option>)}
          </select>
        </div>
        <div>
          <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#64748b', marginBottom: 6 }}>Date Column</label>
          <select 
            value={dateField} 
            onChange={e => setDateField(e.target.value)}
            style={{ width: '100%', padding: '10px 12px', borderRadius: 10, border: '1px solid #e2e8f0', fontSize: 14, background: '#f8fafc', outline: 'none' }}
          >
            {dateFields.map(f => <option key={f.name} value={f.name}>{f.label}</option>)}
            {dateFields.length === 0 && <option value="Date_dt">Date (Default)</option>}
          </select>
        </div>
        <button
          onClick={handleCompare}
          disabled={loading}
          style={{
            padding: '12px',
            borderRadius: 12,
            background: '#6366f1',
            color: '#fff',
            fontWeight: 700,
            border: 'none',
            cursor: 'pointer',
            opacity: loading ? 0.7 : 1,
            boxShadow: '0 4px 12px rgba(99, 102, 241, 0.2)',
            fontSize: 14,
            transition: 'all 0.2s'
          }}
        >
          {loading ? 'Comparing...' : 'Run Analysis'}
        </button>
      </div>

      {showComparison && hasRun && !loading && (
        compareResult.length === 0 ? (
          <div style={{ 
            padding: 40, 
            textAlign: 'center', 
            background: '#f8fafc', 
            borderRadius: 12, 
            border: '1px dashed #cbd5e1',
            color: '#64748b'
          }}>
            <div style={{ fontSize: 24, marginBottom: 8 }}>🔍</div>
            <div style={{ fontWeight: 600 }}>No comparison data found.</div>
            <div style={{ fontSize: 12, marginTop: 4 }}>Try selecting different date ranges or a different Date Column.</div>
          </div>
        ) : (
          <>
            <div style={{ marginTop: 24, height: 400, width: '100%' }}>
              <ResponsiveContainer width="100%" height="100%">
                <BarChart
                  data={compareResult}
                  margin={{ top: 20, right: 30, left: 20, bottom: 60 }}
                >
                  <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#e2e8f0" />
                  <XAxis 
                    dataKey="group" 
                    tick={{ fontSize: 11, fill: '#64748b' }}
                    angle={-35}
                    textAnchor="end"
                    interval={0}
                    height={80}
                  />
                  <YAxis tick={{ fontSize: 12, fill: '#94a3b8' }} axisLine={false} tickLine={false} />
                  <RechartsTooltip 
                    contentStyle={{ borderRadius: 8, border: 'none', boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)' }}
                    itemStyle={{ fontSize: 13, fontWeight: 600 }}
                    labelStyle={{ fontSize: 12, color: '#64748b', marginBottom: 4 }}
                  />
                  <Legend wrapperStyle={{ paddingTop: 20 }} />
                  <Bar dataKey="period_a" name="Baseline" fill="#94a3b8" radius={[4, 4, 0, 0]} />
                  <Bar dataKey="period_b" name="Comparison" fill="#6366f1" radius={[4, 4, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </div>

            <div style={{ marginTop: 24, overflowX: 'auto', borderRadius: 12, border: '1px solid #e2e8f0' }}>
              <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                <thead style={{ background: '#f8fafc' }}>
                  <tr style={{ borderBottom: '1px solid #e2e8f0' }}>
                    <th style={{ textAlign: 'left', padding: '16px', fontWeight: 700, color: '#475569' }}>Category</th>
                    <th style={{ textAlign: 'right', padding: '16px', fontWeight: 700, color: '#475569' }}>Period A Avg</th>
                    <th style={{ textAlign: 'right', padding: '16px', fontWeight: 700, color: '#475569' }}>Period B Avg</th>
                    <th style={{ textAlign: 'right', padding: '16px', fontWeight: 700, color: '#475569' }}>Difference</th>
                    <th style={{ textAlign: 'right', padding: '16px', fontWeight: 700, color: '#475569' }}>% Change</th>
                  </tr>
                </thead>
                <tbody>
                  {compareResult.map((row, idx) => (
                    <tr key={idx} style={{ borderBottom: '1px solid #f1f5f9', background: idx % 2 === 0 ? '#fff' : '#fcfdfe' }}>
                      <td style={{ padding: '16px', fontWeight: 600, color: '#1e293b' }}>{row.group}</td>
                      <td style={{ padding: '16px', textAlign: 'right', color: '#64748b' }}>{Number(row.period_a).toLocaleString()}</td>
                      <td style={{ padding: '16px', textAlign: 'right', color: '#1e293b', fontWeight: 600 }}>{Number(row.period_b).toLocaleString()}</td>
                      <td style={{ padding: '16px', textAlign: 'right', fontWeight: 600, color: row.diff > 0 ? '#10b981' : row.diff < 0 ? '#ef4444' : '#64748b' }}>
                        {row.diff > 0 ? '+' : ''}{row.diff.toLocaleString()}
                      </td>
                      <td style={{ padding: '16px', textAlign: 'right', fontWeight: 800, color: row.pct_change > 0 ? '#10b981' : row.pct_change < 0 ? '#ef4444' : '#64748b' }}>
                        <div style={{ display: 'inline-block', padding: '4px 8px', borderRadius: 6, background: row.pct_change > 0 ? '#f0fdf4' : row.pct_change < 0 ? '#fef2f2' : '#f8fafc' }}>
                          {row.pct_change > 0 ? '+' : ''}{row.pct_change}%
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        )
      )}
    </div>
  );
};

export default ComparisonTool;
