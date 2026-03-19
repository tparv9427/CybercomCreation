import { useState, useMemo, useEffect } from 'react';
import {
  BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, LabelList,
  PieChart, Pie, Cell, LineChart, Line, ResponsiveContainer,
} from 'recharts';
import { useReportStore } from '../store';
import { getFacets } from '../api';

// Keywords that indicate a field is NOT useful for charts
const JUNK_KEYWORDS = ['_id', '_sku', '_url', 'price', 'image', 'date', 'time', 'screenshot', 'extracted', 'parent', 'product_id', 'map_violation_s'];

function isChartableField(name: string): boolean {
  const lower = name.toLowerCase();
  // Allow these even if they contain price-like keywords
  if (lower === 'map_violation_s') return true;
  // Exclude number fields (prices, quantities) — they're high-cardinality
  if (name.endsWith('_f') || name.endsWith('_i') || name.endsWith('_l')) return false;
  // Exclude junk keyword fields
  return !JUNK_KEYWORDS.some(kw => lower.includes(kw));
}

const COLORS = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#ec4899', '#14b8a6', '#84cc16'];

// Abbreviated number formatter
const abbrev = (n: number): string => {
  if (n >= 1_000_000) return `${(n / 1_000_000).toFixed(1)}M`;
  if (n >= 1_000) return `${(n / 1_000).toFixed(1)}K`;
  return String(n);
};

// Custom rotated label that renders vertically inside each bar
const RotatedBarLabel = (props: any) => {
  const { x, y, width, height, value } = props;
  if (!value || height < 20) return null; // skip tiny bars
  return (
    <text
      x={x + width / 2}
      y={y + height / 2}
      fill="#fff"
      fontSize={11}
      fontWeight={700}
      textAnchor="middle"
      dominantBaseline="middle"
      transform={`rotate(-90, ${x + width / 2}, ${y + height / 2})`}
    >
      {abbrev(Number(value))}
    </text>
  );
};


type ChartItem = { name: string; count: number };

// Custom Tooltip for Bar and Line charts
const CustomTooltip = ({ active, payload, label }: any) => {
  if (active && payload && payload.length) {
    return (
      <div style={{
        background: 'rgba(15, 23, 42, 0.92)',
        border: '1px solid rgba(255,255,255,0.1)',
        borderRadius: 10,
        padding: '10px 16px',
        color: '#fff',
        fontSize: 13,
        boxShadow: '0 8px 24px rgba(0,0,0,0.2)'
      }}>
        <div style={{ fontWeight: 700, marginBottom: 4, color: '#94a3b8', fontSize: 11, textTransform: 'uppercase' }}>{label}</div>
        <div style={{ fontWeight: 800, fontSize: 18, color: COLORS[0] }}>{payload[0].value.toLocaleString()}</div>
        <div style={{ fontSize: 11, color: '#64748b', marginTop: 2 }}>records</div>
      </div>
    );
  }
  return null;
};

// Custom Pie label with percentage
const renderPieLabel = ({ cx, cy, midAngle, innerRadius, outerRadius, name, percent }: any) => {
  const RADIAN = Math.PI / 180;
  const radius = innerRadius + (outerRadius - innerRadius) * 1.3;
  const x = cx + radius * Math.cos(-midAngle * RADIAN);
  const y = cy + radius * Math.sin(-midAngle * RADIAN);
  if (percent < 0.03) return null; // skip tiny slices
  return (
    <text x={x} y={y} fill="#334155" textAnchor={x > cx ? 'start' : 'end'} dominantBaseline="central" style={{ fontSize: 11, fontWeight: 600 }}>
      {`${name} (${(percent * 100).toFixed(1)}%)`}
    </text>
  );
};

export default function ChartRenderer() {
  const { fields, filters } = useReportStore();
  const activeFilters = filters.rules.length > 0 ? filters : undefined;
  const [chartType, setChartType] = useState<'bar' | 'pie' | 'line'>('bar');
  const [selectedField, setSelectedField] = useState('');
  const [chartData, setChartData] = useState<ChartItem[]>([]);
  const [drillField, setDrillField] = useState<string | null>(null);
  const [drillData, setDrillData] = useState<ChartItem[]>([]);
  const [loading, setLoading] = useState(false);
  const [expandedTable, setExpandedTable] = useState(false);
  const [expandedChart, setExpandedChart] = useState(false);

  const selectFields = useMemo(
    () => fields.filter((f) => isChartableField(f.name)),
    [fields]
  );

  const loadChart = async (field: string) => {
    if (!field) return;
    setLoading(true);
    setDrillField(null);
    setDrillData([]);
    try {
      const data = await getFacets(field, 20, activeFilters);
      setChartData(data.map((d) => ({ name: d.value, count: Number(d.count) })));
      setSelectedField(field);
    } finally {
      setLoading(false);
    }
  };

  const handleBarClick = async (payload: ChartItem) => {
    const otherFields = selectFields.filter((f) => f.name !== selectedField);
    if (otherFields.length === 0) return;
    const nextField = otherFields[0].name;
    setLoading(true);
    try {
      const facets = await getFacets(nextField, 20, activeFilters);
      setDrillField(`${payload.name} → ${otherFields[0].label}`);
      setDrillData(facets.map((d) => ({ name: d.value, count: Number(d.count) })));
    } finally {
      setLoading(false);
    }
  };

  const clearDrill = () => {
    setDrillField(null);
    setDrillData([]);
  };

  const activeData = drillField ? drillData : chartData;

  const renderChart = () => (
    <ResponsiveContainer width="100%" height="100%">
      {chartType === 'bar' ? (
        <BarChart
          data={activeData}
          margin={{ top: 30, right: 60, left: 20, bottom: 100 }}
          onClick={(e: any) => {
            if (e?.activePayload?.[0]?.payload) {
              handleBarClick(e.activePayload[0].payload as ChartItem);
            }
          }}
        >
          <CartesianGrid strokeDasharray="3 3" stroke="#f1f5f9" vertical={false} />
          <XAxis
            dataKey="name"
            tick={{ fontSize: 11, fill: '#64748b', fontWeight: 500 }}
            angle={-35}
            textAnchor="end"
            interval={0}
            height={80}
            label={{ value: fields.find(f => f.name === selectedField)?.label ?? selectedField, position: 'insideBottom', offset: -10, style: { fontSize: 12, fill: '#475569', fontWeight: 700 } }}
          />
          <YAxis
            tick={{ fontSize: 12, fill: '#94a3b8' }}
            axisLine={false}
            tickLine={false}
            width={80}
            label={{ value: 'Number of Records', angle: -90, position: 'center', dx: -30, style: { fontSize: 12, fill: '#475569', fontWeight: 700, textAnchor: 'middle' } }}
          />
          <Tooltip content={<CustomTooltip />} cursor={{ fill: 'rgba(99,102,241,0.05)' }} />
          <Legend verticalAlign="top" wrapperStyle={{ fontSize: 13, paddingBottom: 12 }} />
          <Bar dataKey="count" name="Records" fill="#6366f1" radius={[6, 6, 0, 0]} cursor="pointer" maxBarSize={60}>
            <LabelList dataKey="count" content={<RotatedBarLabel />} />
            {activeData.map((_, i) => (
              <Cell key={i} fill={COLORS[i % COLORS.length]} />
            ))}
          </Bar>
        </BarChart>
      ) : chartType === 'pie' ? (
        <PieChart>
          <Pie
            data={activeData}
            dataKey="count"
            nameKey="name"
            cx="50%"
            cy="50%"
            outerRadius={130}
            label={renderPieLabel}
            labelLine={{ stroke: '#cbd5e1', strokeWidth: 1 }}
            onClick={(d: any) => handleBarClick(d as ChartItem)}
            cursor="pointer"
          >
            {activeData.map((_, i) => (
              <Cell key={i} fill={COLORS[i % COLORS.length]} stroke="#fff" strokeWidth={2} />
            ))}
          </Pie>
          <Tooltip content={<CustomTooltip />} />
          <Legend
            formatter={(value) => <span style={{ fontSize: 12, color: '#475569', fontWeight: 600 }}>{value}</span>}
          />
        </PieChart>
      ) : (
        <LineChart data={activeData} margin={{ top: 30, right: 60, left: 20, bottom: 100 }}>
          <CartesianGrid strokeDasharray="3 3" stroke="#f1f5f9" vertical={false} />
          <XAxis
            dataKey="name"
            tick={{ fontSize: 11, fill: '#64748b' }}
            angle={-35}
            textAnchor="end"
            interval={0}
            height={80}
            label={{ value: fields.find(f => f.name === selectedField)?.label ?? selectedField, position: 'insideBottom', offset: -10, style: { fontSize: 12, fill: '#475569', fontWeight: 700 } }}
          />
          <YAxis
            tick={{ fontSize: 12, fill: '#94a3b8' }}
            axisLine={false}
            tickLine={false}
            width={80}
            label={{ value: 'Number of Records', angle: -90, position: 'center', dx: -30, style: { fontSize: 12, fill: '#475569', fontWeight: 700, textAnchor: 'middle' } }}
          />
          <Tooltip content={<CustomTooltip />} />
          <Legend verticalAlign="top" wrapperStyle={{ fontSize: 13, paddingBottom: 12 }} />
          <Line
            type="monotone"
            dataKey="count"
            name="Records"
            stroke="#6366f1"
            strokeWidth={2.5}
            dot={{ r: 5, fill: '#6366f1', strokeWidth: 2, stroke: '#fff' }}
            activeDot={{ r: 7, fill: '#6366f1' }}
          >
            <LabelList dataKey="count" position="top" style={{ fontSize: 11, fill: '#475569', fontWeight: 700 }} formatter={(v: any) => Number(v).toLocaleString()} />
          </Line>
        </LineChart>
      )}
    </ResponsiveContainer>
  );

  // Auto-refresh chart when filters change
  useEffect(() => {
    if (selectedField) {
      loadChart(selectedField);
    }
  }, [filters]);

  const renderTable = (isExpanded: boolean) => (
    <div style={{ display: 'flex', flexDirection: 'column', height: isExpanded ? 'calc(100vh - 120px)' : '100%', background: '#fff', border: isExpanded ? 'none' : '1px solid #e2e8f0', borderRadius: 12, overflow: 'hidden' }}>
      <div style={{ padding: '16px 20px', borderBottom: '1px solid #e2e8f0', display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: '#f8fafc' }}>
        <h4 style={{ margin: 0, fontSize: 14, fontWeight: 700, color: '#334155' }}>
          {drillField ? 'Drilldown Data' : 'Current Chart Data'}
        </h4>
        <button
          onClick={() => setExpandedTable(!isExpanded)}
          style={{ padding: '6px 10px', borderRadius: 6, border: '1px solid #cbd5e1', background: '#fff', cursor: 'pointer', fontSize: 12, fontWeight: 600, color: '#475569', display: 'flex', alignItems: 'center', gap: 6 }}
        >
          {isExpanded ? '✕ Close' : '⤢ Expand View'}
        </button>
      </div>
      <div style={{ flex: 1, overflowY: 'auto' }}>
        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
          <thead style={{ position: 'sticky', top: 0, background: '#fff', zIndex: 1, boxShadow: '0 1px 2px rgba(0,0,0,0.05)' }}>
            <tr>
              <th style={{ textAlign: 'left', padding: '12px 20px', color: '#64748b', fontWeight: 600, borderBottom: '2px solid #e2e8f0' }}>Category</th>
              <th style={{ textAlign: 'right', padding: '12px 20px', color: '#64748b', fontWeight: 600, borderBottom: '2px solid #e2e8f0' }}>Records</th>
            </tr>
          </thead>
          <tbody>
            {activeData.map((d, i) => (
              <tr key={i} style={{ borderBottom: '1px solid #f1f5f9', background: i % 2 === 0 ? '#fff' : '#f8fafc' }}>
                <td style={{ padding: '12px 20px', fontWeight: 500, color: '#1e293b' }}>{d.name}</td>
                <td style={{ padding: '12px 20px', textAlign: 'right', fontWeight: 700, color: '#6366f1' }}>{d.count.toLocaleString()}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );

  return (
    <div style={{ marginBottom: 16 }}>

        <div style={{ border: '1px solid #e2e8f0', borderRadius: 12, padding: 24, background: '#fff' }}>
          {/* Controls */}
          <div style={{ display: 'flex', gap: 10, marginBottom: 20, flexWrap: 'wrap', alignItems: 'center' }}>
            <select
              value={selectedField}
              onChange={(e) => loadChart(e.target.value)}
              style={{ padding: '8px 12px', borderRadius: 8, border: '1px solid #e2e8f0', fontSize: 13, fontWeight: 600, outline: 'none', minWidth: 180 }}
            >
              <option value="">— Select Field to Chart —</option>
              {selectFields.map((f) => (
                <option key={f.name} value={f.name}>{f.label}</option>
              ))}
            </select>

            <div style={{ display: 'flex', gap: 4, background: '#f1f5f9', padding: 4, borderRadius: 8 }}>
              {(['bar', 'pie', 'line'] as const).map((t) => (
                <button
                  key={t}
                  onClick={() => setChartType(t)}
                  style={{
                    padding: '6px 16px',
                    borderRadius: 6,
                    border: 'none',
                    cursor: 'pointer',
                    fontWeight: chartType === t ? 700 : 500,
                    background: chartType === t ? '#6366f1' : 'transparent',
                    color: chartType === t ? '#fff' : '#64748b',
                    fontSize: 13,
                    transition: 'all 0.2s'
                  }}
                >
                  {t === 'bar' ? '📊 Bar' : t === 'pie' ? '🍕 Pie' : '📈 Line'}
                </button>
              ))}
              
              {!loading && activeData.length > 0 && (
                <button
                  onClick={() => setExpandedChart(true)}
                  title="Expand to Full View"
                  style={{
                    padding: '6px 14px',
                    borderRadius: 6,
                    border: 'none',
                    cursor: 'pointer',
                    fontWeight: 700,
                    background: '#fff',
                    color: '#475569',
                    fontSize: 14,
                    boxShadow: '0 1px 2px rgba(0,0,0,0.05)',
                    transition: 'all 0.2s',
                    marginLeft: 4
                  }}
                >
                  ⤢
                </button>
              )}
            </div>

            {/* Filter status badge */}
            {activeFilters ? (
              <div style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#eef2ff', border: '1px solid #c7d2fe', borderRadius: 8, padding: '5px 12px' }}>
                <div style={{ width: 8, height: 8, borderRadius: '50%', background: '#6366f1', animation: 'pulse 1.5s infinite' }} />
                <span style={{ fontSize: 12, fontWeight: 600, color: '#6366f1' }}>Filtered Data</span>
              </div>
                        ) : (
              <div style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#f8fafc', border: '1px solid #e2e8f0', borderRadius: 8, padding: '5px 12px' }}>
                <span style={{ fontSize: 12, color: '#94a3b8' }}>All Records (No Filter)</span>
              </div>
            )}

            {drillField && (
              <button
                onClick={clearDrill}
                style={{ padding: '6px 14px', borderRadius: 8, border: '1px solid #fca5a5', color: '#ef4444', cursor: 'pointer', background: '#fff5f5', fontSize: 13, fontWeight: 600 }}
              >
                ✕ Clear Drilldown
              </button>
            )}
          </div>

          {/* Drilldown label */}
          {drillField && (
            <div style={{ marginBottom: 12, fontSize: 13, color: '#6366f1', fontWeight: 600, background: '#eef2ff', padding: '6px 12px', borderRadius: 8, display: 'inline-block' }}>
              🔍 Drilldown: {drillField}
            </div>
          )}

          {/* Empty states */}
          {!loading && activeData.length === 0 && selectedField && (
            <div style={{ textAlign: 'center', padding: 40, color: '#94a3b8', fontSize: 14 }}>No data to display for this field</div>
          )}
          {!loading && !selectedField && (
            <div style={{ textAlign: 'center', padding: 40, color: '#94a3b8', fontSize: 14 }}>
              👆 Select a field above to render a chart
            </div>
          )}

          {/* Default Chart View (Compact) */}
          {!loading && activeData.length > 0 && (
            <div style={{ height: 400, marginTop: 12 }}>
              {renderChart()}
            </div>
          )}
        </div>

      {/* Expanded Chart Modal (Split View) */}
      {expandedChart && (
        <div style={{
          position: 'fixed', top: 0, left: 0, width: '100vw', height: '100vh',
          background: 'rgba(15, 23, 42, 0.6)',
          backdropFilter: 'blur(8px)',
          zIndex: 9990,
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          padding: 'clamp(16px, 3vw, 32px)', boxSizing: 'border-box'
        }}>
          <div style={{
            background: '#fff', width: '100%', maxWidth: 1400, height: '100%',
            borderRadius: 20, overflow: 'hidden', display: 'flex', flexDirection: 'column',
            boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.25)'
          }}>
            <div style={{ padding: '20px 24px', borderBottom: '1px solid #e2e8f0', display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: '#f8fafc' }}>
              <h3 style={{ margin: 0, fontSize: 18, fontWeight: 700, color: '#0f172a' }}>
                Expanded Analysis: {fields.find(f => f.name === selectedField)?.label ?? selectedField}
              </h3>
              <button
                onClick={() => setExpandedChart(false)}
                style={{ padding: '8px 16px', borderRadius: 8, border: 'none', background: '#e2e8f0', cursor: 'pointer', fontSize: 14, fontWeight: 700, color: '#475569' }}
              >
                ✕ Close
              </button>
            </div>
            
            <div style={{ display: 'flex', gap: 24, flexDirection: window.innerWidth < 1024 ? 'column' : 'row', flex: 1, padding: 24, minHeight: 0 }}>
              {/* Left side Chart */}
              <div style={{ flex: '1 1 65%', minWidth: 0, height: '100%' }}>
                {renderChart()}
              </div>

              {/* Right side Table */}
              <div style={{ flex: '1 1 35%', minWidth: 0, height: '100%' }}>
                {renderTable(false)}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Expanded Table Modal */}
      {expandedTable && (
        <div style={{
          position: 'fixed', top: 0, left: 0, width: '100vw', height: '100vh',
          background: 'rgba(15, 23, 42, 0.4)',
          backdropFilter: 'blur(4px)',
          zIndex: 9999,
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          padding: 'clamp(20px, 4vw, 40px)', boxSizing: 'border-box'
        }}>
          <div style={{
            background: '#fff', width: '100%', maxWidth: 1200, height: '100%',
            borderRadius: 16, overflow: 'hidden',
            boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)'
          }}>
            {renderTable(true)}
          </div>
        </div>
      )}
    </div>
  );
}