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
  start_year?: number
  manager_id?: number
  startYear?: number
  managerId?: number
}

export interface DuplicatedFundRecord extends EntityRecord {
  id: number
  name: string
  startYear: number
  managerId: number
  managerName: string | null
  aliases: string[]
  createdAt?: string | null
  updatedAt?: string | null
}

export interface DuplicatedFund extends EntityRecord {
  source: DuplicatedFundRecord
  duplicated: DuplicatedFundRecord
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
