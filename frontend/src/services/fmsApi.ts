import type {
  Company,
  CompanyPayload,
  DuplicatedFund,
  DuplicatedFundRecord,
  Fund,
  FundManager,
  FundManagerPayload,
  FundPayload,
} from '@/types/fms'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8080/v1'

interface RequestOptions {
  method?: 'GET' | 'POST' | 'PUT' | 'DELETE'
  body?: unknown
}

async function request<T>(path: string, options: RequestOptions = {}): Promise<T> {
  const response = await fetch(`${API_BASE_URL}${path}`, {
    method: options.method ?? 'GET',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    body: options.body !== undefined ? JSON.stringify(options.body) : undefined,
  })

  if (response.status === 204) {
    return null as T
  }

  const rawBody = await response.text()
  const parsedBody = rawBody ? (JSON.parse(rawBody) as unknown) : null

  if (!response.ok) {
    const message =
      typeof parsedBody === 'object' && parsedBody !== null && 'message' in parsedBody
        ? String(parsedBody.message)
        : `Request failed with status ${response.status}`

    throw new Error(message)
  }

  return parsedBody as T
}

function toArray<T>(payload: unknown): T[] {
  if (Array.isArray(payload)) {
    return payload as T[]
  }

  if (payload && typeof payload === 'object') {
    const dataPayload = payload as Record<string, unknown>

    if (Array.isArray(dataPayload.data)) {
      return dataPayload.data as T[]
    }

    if (Array.isArray(dataPayload.items)) {
      return dataPayload.items as T[]
    }

    if (Array.isArray(dataPayload.result)) {
      return dataPayload.result as T[]
    }
  }

  return []
}

function normalizeDuplicatedFundRecord(payload: unknown): DuplicatedFundRecord | null {
  if (!payload || typeof payload !== 'object') {
    return null
  }

  const data = payload as Record<string, unknown>
  const id = Number(data.id)

  if (!Number.isFinite(id)) {
    return null
  }

  return {
    id,
    name: String(data.name ?? ''),
    startYear: Number(data.startYear ?? data.start_year ?? new Date().getFullYear()),
    managerId: Number(data.managerId ?? data.manager_id ?? 0),
    managerName: data.managerName === null || data.managerName === undefined ? null : String(data.managerName),
    aliases: Array.isArray(data.aliases) ? data.aliases.map((alias) => String(alias)) : [],
    createdAt: data.createdAt === null || data.createdAt === undefined ? null : String(data.createdAt),
    updatedAt: data.updatedAt === null || data.updatedAt === undefined ? null : String(data.updatedAt),
  }
}

function normalizeDuplicatedFund(payload: unknown): DuplicatedFund | null {
  if (!payload || typeof payload !== 'object') {
    return null
  }

  const data = payload as Record<string, unknown>
  const source = normalizeDuplicatedFundRecord(data.source)
  const duplicated = normalizeDuplicatedFundRecord(data.duplicated)

  if (!source || !duplicated) {
    return null
  }

  return {
    source,
    duplicated,
  }
}

export const fmsApi = {
  async listFunds(): Promise<Fund[]> {
    const response = await request<unknown>('/funds')
    return toArray<Fund>(response)
  },

  async listDuplicatedFunds(): Promise<DuplicatedFund[]> {
    const response = await request<unknown>('/funds/duplicated')
    return toArray<unknown>(response)
      .map((item) => normalizeDuplicatedFund(item))
      .filter((item): item is DuplicatedFund => item !== null)
  },

  async createFund(payload: FundPayload): Promise<void> {
    await request('/funds', { method: 'POST', body: payload })
  },

  async updateFund(id: number, payload: FundPayload): Promise<void> {
    await request(`/funds/${id}`, { method: 'PUT', body: payload })
  },

  async deleteFund(id: number): Promise<void> {
    await request(`/funds/${id}`, { method: 'DELETE' })
  },

  async listCompanies(): Promise<Company[]> {
    const response = await request<unknown>('/companies')
    return toArray<Company>(response)
  },

  async createCompany(payload: CompanyPayload): Promise<void> {
    await request('/companies', { method: 'POST', body: payload })
  },

  async updateCompany(id: number, payload: CompanyPayload): Promise<void> {
    await request(`/companies/${id}`, { method: 'PUT', body: payload })
  },

  async deleteCompany(id: number): Promise<void> {
    await request(`/companies/${id}`, { method: 'DELETE' })
  },

  async listFundManagers(): Promise<FundManager[]> {
    const response = await request<unknown>('/fund-managers')
    return toArray<FundManager>(response)
  },

  async createFundManager(payload: FundManagerPayload): Promise<void> {
    await request('/fund-managers', { method: 'POST', body: payload })
  },

  async updateFundManager(id: number, payload: FundManagerPayload): Promise<void> {
    await request(`/fund-managers/${id}`, { method: 'PUT', body: payload })
  },

  async deleteFundManager(id: number): Promise<void> {
    await request(`/fund-managers/${id}`, { method: 'DELETE' })
  },
}
