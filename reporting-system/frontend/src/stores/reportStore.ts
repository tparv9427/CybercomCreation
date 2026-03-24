import { defineStore } from 'pinia'
import axios from 'axios'
import { initEcho } from '../utils/echo'

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
  version: number
  parent_id?: number
  user_id?: number
  is_public: boolean
  user?: { name: string }
  schedule?: { frequency: string }
  tempFrequency?: string
  created_at: string
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
    columnWidths: (JSON.parse(localStorage.getItem('user') || '{}')?.column_config || {}) as Record<string, number>,
    docsA: [] as Record<string, any>[],
    docsB: [] as Record<string, any>[],
    currentViewId: null as number | null,
    adminStats: null as any,
  }),

  actions: {
    // ... existed actions ...

    async fetchAdminStats() {
      try {
        const { data } = await api.get('/admin/stats')
        this.adminStats = data
      } catch (e: any) {
        console.error('Failed to fetch admin stats:', e)
        this.error = 'Failed to load administrative analytics.'
      }
    },
    async login(email: string, password: string) {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.post('/login', { email, password })
        this.token = data.token
        this.user = data.user
        if (data.user.column_config) {
          this.columnWidths = data.user.column_config
        }
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
        if (data.user.default_view_id) {
          this.currentViewId = data.user.default_view_id
        }
        await this.fetchFields()
        await this.fetchData()
        this.setupEcho()
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
      this.error = null
      try {
        const { data } = await api.get('/views')
        this.savedViews = data
      } catch (e: any) {
        this.error = 'Failed to fetch saved views.'
      }
    },

    async saveView(name: string, parentId?: number, isPublic: boolean = false) {
      this.error = null
      try {
        const payload: any = {
          name,
          is_public: isPublic,
          config: {
            selectedColumns: this.selectedColumns,
            filters: this.filters,
            sort: this.sort,
          },
        }
        if (parentId) {
          payload.parent_id = parentId
        }
        const { data } = await api.post('/views', payload)
        await this.fetchSavedViews()
        this.currentViewId = data.id // Update active view to the new version
      } catch (e: any) {
        this.error = e.response?.data?.message || 'Failed to save view.'
      }
    },

    async setSchedule(viewId: number, frequency: string) {
      this.error = null
      try {
        await api.post(`/views/${viewId}/schedule`, { frequency })
        await this.fetchSavedViews()
      } catch (e: any) {
        this.error = 'Failed to update schedule.'
      }
    },

    async deleteView(id: number) {
      this.error = null
      try {
        await api.delete(`/views/${id}`)
        this.savedViews = this.savedViews.filter(v => v.id !== id)
        if (this.user?.default_view_id === id) {
          this.user.default_view_id = null
          localStorage.setItem('user', JSON.stringify(this.user))
        }
        if (this.currentViewId === id) {
          this.currentViewId = null
        }
      } catch (e: any) {
        this.error = 'Failed to delete view.'
      }
    },

    applyView(view: SavedView) {
      this.currentViewId = view.id
      const cfg = view.config
      this.selectedColumns = cfg.selectedColumns ?? this.selectedColumns
      this.filters = cfg.filters ?? { id: 'root', type: 'group', combinator: 'AND', rules: [] }
      this.sort = cfg.sort ?? 'id asc'
      this.cursor = '*'
      this.cursorHistory = []
      this.fetchData()
    },

    async setDefaultView(id: number | null) {
      try {
        const { data } = await api.patch('/user/config', { default_view_id: id })
        if (data && data.user) {
          this.user = data.user
          localStorage.setItem('user', JSON.stringify(data.user))
        }
      } catch (e) {
        console.error('Failed to set default view:', e)
      }
    },

    async applyDefaultView() {
      if (!this.user?.default_view_id) return
      if (this.savedViews.length === 0) {
        await this.fetchSavedViews()
      }
      const defaultView = this.savedViews.find(v => v.id === this.user.default_view_id)
      if (defaultView) {
        this.applyView(defaultView) // applyView fetches data itself
      } else {
        this.fetchData()
      }
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
      
      // Debounced save
      if ((window as any).saveConfigTimeout) {
        clearTimeout((window as any).saveConfigTimeout)
      }
      (window as any).saveConfigTimeout = setTimeout(() => {
        api.patch('/user/config', { column_config: this.columnWidths })
          .catch(err => console.error('Failed to save config:', err))
          
        // Update local storage user config to keep it in sync on reload
        const userStr = localStorage.getItem('user')
        if (userStr) {
          const u = JSON.parse(userStr)
          u.column_config = this.columnWidths
          localStorage.setItem('user', JSON.stringify(u))
        }
      }, 500)
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

    setupEcho() {
      if (!this.token || !this.user?.tenant_id) return
      
      const echo = initEcho(this.token)
      const channel = echo.private(`tenant.${this.user.tenant_id}`)

      channel.listen('SolrDataUpdated', (e: any) => {
          console.log('Real-time Solr update received:', e)
          this.fetchData()
          this.fetchAdminStats()
        })

      channel.listen('ModelUpdated', (e: any) => {
          console.log('Real-time MySQL update received:', e)
          if (e.model === 'SavedView' || e.model === 'ScheduledReport') {
            this.fetchSavedViews()
          }
        })
    },
  },
})
