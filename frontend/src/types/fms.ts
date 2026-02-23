export type EntityRecord = Record<string, unknown>

export interface Company extends EntityRecord {
  id: number
  name: string
}

export interface FundManager extends EntityRecord {
  id: number
  name: string
}

export interface Fund extends EntityRecord {
  id: number
  name: string
  start_year: number
  manager_id: number
}

export interface DuplicatedFund extends EntityRecord {
  source_fund_id: number
  duplicated_fund_id: number
}

export interface CompanyPayload {
  name: string
}

export interface FundManagerPayload {
  name: string
}

export interface FundPayload {
  name: string
  start_year: number
  manager_id: number
}
