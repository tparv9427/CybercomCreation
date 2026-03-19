import { create } from 'zustand';
import type { Field, FilterGroup } from './types';

export type { FilterGroup, FilterRule } from './types';

interface ReportStore {
  // Fields
  fields: Field[];
  setFields: (fields: Field[]) => void;

  // Data
  docs: Record<string, unknown>[];
  total: number;
  start: number;
  rows: number;
  setData: (docs: Record<string, unknown>[], total: number, start: number) => void;
  setStart: (start: number) => void;
  setRows: (rows: number) => void;

  // Filters
  filters: FilterGroup;
  setFilters: (filters: FilterGroup) => void;

  // Column visibility
  visibleColumns: string[];
  setVisibleColumns: (cols: string[]) => void;

  // Sort
  sort: string;
  setSort: (sort: string) => void;

  // Date range
  dateFrom: string;
  dateTo: string;
  setDateFrom: (d: string) => void;
  setDateTo: (d: string) => void;

  // Loading
  loading: boolean;
  setLoading: (b: boolean) => void;

  // Active saved view
  activeView: string | null;
  setActiveView: (name: string | null) => void;

  // Comparison
  compareRangeA: { from: string; to: string };
  compareRangeB: { from: string; to: string };
  setCompareRangeA: (range: { from: string; to: string }) => void;
  setCompareRangeB: (range: { from: string; to: string }) => void;
  compareResult: any[];
  setCompareResult: (res: any[]) => void;
  showComparison: boolean;
  setShowComparison: (b: boolean) => void;
}

export const useReportStore = create<ReportStore>((set) => ({
  fields: [],
  setFields: (fields) => set({ fields }),

  docs: [],
  total: 0,
  start: 0,
  rows: 50,
  setData: (docs, total, start) => set({ docs, total, start }),
  setStart: (start) => set({ start }),
  setRows: (rows) => set({ rows }),

  filters: { combinator: 'AND', rules: [] },
  setFilters: (filters) => set({ filters }),

  visibleColumns: [],
  setVisibleColumns: (visibleColumns) => set({ visibleColumns }),

  sort: 'id asc',
  setSort: (sort) => set({ sort }),

  dateFrom: '',
  dateTo: '',
  setDateFrom: (dateFrom) => set({ dateFrom }),
  setDateTo: (dateTo) => set({ dateTo }),

  loading: false,
  setLoading: (loading) => set({ loading }),

  activeView: null,
  setActiveView: (activeView) => set({ activeView }),

  compareRangeA: { from: '', to: '' },
  compareRangeB: { from: '', to: '' },
  setCompareRangeA: (compareRangeA) => set({ compareRangeA }),
  setCompareRangeB: (compareRangeB) => set({ compareRangeB }),
  compareResult: [],
  setCompareResult: (compareResult) => set({ compareResult }),
  showComparison: false,
  setShowComparison: (showComparison) => set({ showComparison }),
}));