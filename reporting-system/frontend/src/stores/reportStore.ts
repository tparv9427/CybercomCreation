import { defineStore } from 'pinia'
import axios from 'axios'

const API = 'http://localhost:9006/api'

export interface FilterRule {
  id: string
  type: 'rule'
  field: string
  operator: string
  value: string
}

export interface FilterGroup {
  id: string
  type: 'group'
  combinator: 'AND' | 'OR'
  rules: (FilterRule | FilterGroup)[]
}

export interface Field {
  name: string
  label: string
  type: 'text' | 'number' | 'date' | 'boolean'
}

export interface SavedView {
  id: number
  name: string
  config: {
    selectedColumns: string[]
    filters: FilterGroup
    sort: string
  }
}

export const useReportStore = defineStore('report', {
  state: () => ({
    fields: [] as Field[],
    selectedColumns: [] as string[],
    docs: [] as Record<string, any>[],
    total: 0,
    start: 0,
    rows: 50,
    sort: 'id asc',
    filters: { id: 'root', type: 'group', combinator: 'AND', rules: [] } as FilterGroup,
    dateFrom: '',
    dateTo: '',
    savedViews: [] as SavedView[],
    loading: false,
    error: null as string | null,
    chartField: '',
    chartGroupBy: '',
    comparisonData: [] as any[],
    comparing: false,
    columnWidths: {} as Record<string, number>,
  }),

  actions: {
    async fetchFields() {
      const { data } = await axios.get(`${API}/report/fields`)
      this.fields = data
      if (this.selectedColumns.length === 0) {
        this.selectedColumns = data.slice(0, 8).map((f: Field) => f.name)
      }
      if (!this.chartField && data.length) {
        const numField = data.find((f: Field) => f.type === 'number')
        const catField = data.find((f: Field) => f.type === 'text')
        this.chartField = numField?.name ?? data[0].name
        this.chartGroupBy = catField?.name ?? data[0].name
      }
    },

    async fetchData() {
      this.loading = true
      this.error = null
      try {
        const params: Record<string, any> = {
          rows: this.rows,
          start: this.start,
          sort: this.sort,
        }
        if (this.filters.rules.length) {
          params.filters = JSON.stringify(this.filters)
        }
        if (this.dateFrom) params.date_from = this.dateFrom
        if (this.dateTo) params.date_to = this.dateTo
        
        const { data } = await axios.get(`${API}/report/data`, { params })
        this.docs = data.docs
        this.total = data.total
        this.start = data.start
      } catch (e: any) {
        this.error = e.message
      } finally {
        this.loading = false
      }
    },

    async fetchSavedViews() {
      const { data } = await axios.get(`${API}/views`)
      this.savedViews = data
    },

    async saveView(name: string) {
      await axios.post(`${API}/views`, {
        name,
        config: {
          selectedColumns: this.selectedColumns,
          filters: this.filters,
          sort: this.sort,
        },
      })
      await this.fetchSavedViews()
    },

    async deleteView(id: number) {
      await axios.delete(`${API}/views/${id}`)
      this.savedViews = this.savedViews.filter(v => v.id !== id)
    },

    applyView(view: SavedView) {
      const cfg = view.config
      this.selectedColumns = cfg.selectedColumns ?? this.selectedColumns
      this.filters = cfg.filters ?? { id: 'root', type: 'group', combinator: 'AND', rules: [] }
      this.sort = cfg.sort ?? 'id asc'
      this.start = 0
      this.fetchData()
    },

    async compareRanges(payload: {
      field: string
      groupBy: string
      dateField: string
      dateFromA: string
      dateToA: string
      dateFromB: string
      dateToB: string
    }) {
      this.comparing = true
      const { data } = await axios.get(`${API}/report/compare`, {
        params: {
          field: payload.field,
          group_by: payload.groupBy,
          date_field: payload.dateField,
          date_from_a: payload.dateFromA,
          date_to_a: payload.dateToA,
          date_from_b: payload.dateFromB,
          date_to_b: payload.dateToB,
        },
      })
      this.comparisonData = data
      this.comparing = false
    },

    async exportCsv() {
      try {
        this.loading = true
        const params: Record<string, any> = {}
        if (this.filters.rules.length) {
          params.filters = JSON.stringify(this.filters)
        }
        const resp = await axios.get(`${API}/report/export`, {
          params,
          responseType: 'blob',
        })
        
        const blob = new Blob([resp.data], { type: 'text/csv' })
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `report_export_${Date.now()}.csv`)
        document.body.appendChild(link)
        link.click()
        
        // Cleanup
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url)
      } catch (e: any) {
        console.error('Export failed:', e)
        this.error = 'Failed to export CSV. The file might be too large or the server timed out.'
      } finally {
        this.loading = false
      }
    },

    addFilter(parent?: FilterGroup) {
      const p = parent || this.filters
      p.rules.push({
        id: Math.random().toString(36).slice(2, 9),
        type: 'rule',
        field: this.fields[0]?.name ?? '',
        operator: '=',
        value: '',
      })
    },

    addGroup(parent?: FilterGroup) {
      const p = parent || this.filters
      p.rules.push({
        id: Math.random().toString(36).slice(2, 9),
        type: 'group',
        combinator: 'AND',
        rules: [],
      })
    },

    removeFilter(id: string, parent?: FilterGroup) {
      const p = parent || this.filters
      p.rules = p.rules.filter(r => r.id !== id)
    },

    setPage(newStart: number) {
      this.start = newStart
      this.fetchData()
    },

    setColWidth(col: string, width: number) {
      this.columnWidths[col] = width
    },
  },
})
