export interface Company {
  id: number
  name: string
}

export interface FundManager {
  id: number
  name: string
}

export interface Fund {
  id: number
  name: string
  start_year?: number
  manager_id?: number
  startYear?: number
  managerId?: number
  aliases?: string[]
  companies?: number[]
}

export interface DuplicatedFundRecord {
  id: number
  name: string
  startYear: number
  managerId: number
  managerName: string | null
  aliases: string[]
  createdAt?: string | null
  updatedAt?: string | null
}

export interface DuplicatedFund {
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
  aliases: string[]
  companies: number[]
}
