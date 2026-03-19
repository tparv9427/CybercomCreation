import axios from 'axios';
import type { Field, DataResponse, FacetItem, SavedView } from './types';

const api = axios.create({
  baseURL: '/api',
});

export const getFields = () =>
  api.get<Field[]>('/report/fields').then(r => r.data);

export const getData = (params: object) =>
  api.get<DataResponse>('/report/data', { params }).then(r => r.data);

export const getFacets = (field: string, limit = 50, filters?: object) =>
  api.get<FacetItem[]>('/report/facets', {
    params: {
      field,
      limit,
      ...(filters ? { filters: JSON.stringify(filters) } : {}),
    }
  }).then(r => r.data);

export const exportData = (filters?: object) => {
  const params = filters ? { filters: JSON.stringify(filters) } : {};
  window.open(`/api/report/export?${new URLSearchParams(params as Record<string, string>)}`);
};

export const getSavedViews = () =>
  api.get<SavedView[]>('/views').then(r => r.data);

export const saveView = (name: string, config: object) =>
  api.post('/views', { name, config }).then(r => r.data);

export const deleteView = (id: number) =>
  api.delete(`/views/${id}`).then(r => r.data);

export const getComparison = (params: object) =>
  api.get('/report/compare', { params }).then(r => r.data);