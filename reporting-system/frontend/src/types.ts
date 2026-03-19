export interface Field {
  name: string;
  label: string;
  type: string;
}

export interface Doc {
  [key: string]: string | number | boolean;
}

export interface DataResponse {
  total: number;
  docs: Doc[];
  start: number;
}

export interface FacetItem {
  value: string;
  count: number;
}

export interface SavedView {
  id: number;
  name: string;
  config: object;
  created_at: string;
}

export interface FilterGroup {
  combinator: 'AND' | 'OR';
  rules: (FilterRule | FilterGroup)[];
}

export interface FilterRule {
  field: string;
  operator: string;
  value: string;
}