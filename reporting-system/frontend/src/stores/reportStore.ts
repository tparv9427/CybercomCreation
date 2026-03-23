import { defineStore } from 'pinia'
import axios from 'axios'

const API = '/api'

// Central Axios setup with token interceptor
const api = axios.create({ baseURL: API })
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Auto-logout on 401
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.reload()
    }
    return Promise.reject(error)
  }
)

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
  type: 'text' | 'number' | 'date' | 'boolean' | 'select'
  options?: string[]
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
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token') || '',
    fields: [] as Field[],
    selectedColumns: [] as string[],
    docs: [] as Record<string, any>[],
    total: 0,
    cursor: '*', // Initial cursor for first page
    nextCursor: null as string | null,
    cursorHistory: [] as string[],
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
    docsA: [] as Record<string, any>[],
    docsB: [] as Record<string, any>[],
  }),

  actions: {
    async login(email: string, password: string) {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.post('/login', { email, password })
        this.token = data.token
        this.user = data.user
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
        await this.fetchFields()
        await this.fetchData()
        return true
      } catch (e: any) {
        this.error = e.response?.data?.message || e.message || 'Invalid credentials'
        return false
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await api.post('/logout')
      } finally {
        this.token = ''
        this.user = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        window.location.reload()
      }
    },

    async fetchFields() {
      const { data } = await api.get('/report/fields')
      if (!Array.isArray(data)) {
        console.error('Expected fields array but got:', data)
        this.fields = []
        return
      }
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
          cursor: this.cursor,
          sort: this.sort,
        }
        if (this.filters.rules.length) {
          params.filters = JSON.stringify(this.filters)
        }
        if (this.dateFrom) params.date_from = this.dateFrom
        if (this.dateTo) params.date_to = this.dateTo
        
        const { data } = await api.get('/report/data', { params })
        this.docs = data.docs
        this.total = data.total
        this.nextCursor = data.nextCursor
      } catch (e: any) {
        this.error = e.message
      } finally {
        this.loading = false
      }
    },

    async fetchSavedViews() {
      const { data } = await api.get('/views')
      this.savedViews = data
    },

    async saveView(name: string) {
      await api.post('/views', {
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
      await api.delete(`/views/${id}`)
      this.savedViews = this.savedViews.filter(v => v.id !== id)
    },

    applyView(view: SavedView) {
      const cfg = view.config
      this.selectedColumns = cfg.selectedColumns ?? this.selectedColumns
      this.filters = cfg.filters ?? { id: 'root', type: 'group', combinator: 'AND', rules: [] }
      this.sort = cfg.sort ?? 'id asc'
      this.cursor = '*'
      this.cursorHistory = []
      this.fetchData()
    },

    async compareRanges(payload: {
      dateField: string
      dateFromA: string
      dateToA: string
      dateFromB: string
      dateToB: string
    }) {
      this.comparing = true
      this.docsA = []
      this.docsB = []
      
      const baseParams: Record<string, any> = {
        rows: this.rows,
        cursor: '*',
        sort: this.sort,
      }
      this.cursorHistory = []
      if (this.filters.rules.length) {
        baseParams.filters = JSON.stringify(this.filters)
      }

      try {
        const [resA, resB] = await Promise.all([
          api.get('/report/data', {
            params: {
              ...baseParams,
              date_from: payload.dateFromA,
              date_to:   payload.dateToA,
              date_field: payload.dateField,
            }
          }),
          api.get('/report/data', {
            params: {
              ...baseParams,
              date_from: payload.dateFromB,
              date_to:   payload.dateToB,
              date_field: payload.dateField,
            }
          })
        ])
        
        this.docsA = resA.data.docs
        this.docsB = resB.data.docs
        this.comparisonData = [{ completed: true }] // trigger dependent UI
      } catch (e) {
        console.error('Comparison error', e)
        this.comparisonData = []
      } finally {
        this.comparing = false
      }
    },

    async exportCsv() {
      try {
        this.loading = true
        const params: Record<string, any> = {}
        if (this.filters.rules.length) {
          params.filters = JSON.stringify(this.filters)
        }
        const resp = await api.get('/report/export', {
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

    nextPage() {
      if (this.nextCursor && this.nextCursor !== this.cursor) {
        this.cursorHistory.push(this.cursor)
        this.cursor = this.nextCursor
        this.fetchData()
      }
    },

    prevPage() {
      if (this.cursorHistory.length > 0) {
        this.cursor = this.cursorHistory.pop()!
        this.fetchData()
      }
    },

    resetPaging() {
      this.cursor = '*'
      this.cursorHistory = []
      this.fetchData()
    },

    setColWidth(col: string, width: number) {
      this.columnWidths[col] = width
    },

    async fetchFacets(metric: string, groupBy: string | string[]) {
      if (!metric || !groupBy) return []
      
      const params: Record<string, any> = {
        metric,
        group_by: Array.isArray(groupBy) ? groupBy.join(',') : groupBy,
        limit: 15,
        date_from: this.dateFrom,
        date_to: this.dateTo,
      }
      
      if (this.filters.rules.length) {
        params.filters = JSON.stringify(this.filters)
      }

      try {
        const { data } = await api.get('/report/facets', { params })
        return data || []
      } catch (e) {
        console.error('Failed to fetch facets:', e)
        return []
      }
    },
    async fetchSuggestions(field: string, q: string) {
      if (!field || q.length < 1) return []
      try {
        const { data } = await api.get('/report/suggest', { params: { field, q } })
        return data || []
      } catch (e) {
        console.error('Suggest error:', e)
        return []
      }
    },
  },
})
