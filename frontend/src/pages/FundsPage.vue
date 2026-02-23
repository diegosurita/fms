<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import PageHeader from '@/components/common/PageHeader.vue'
import DuplicatedFundDrawer from '@/components/funds/DuplicatedFundDrawer.vue'
import FundFormDrawer from '@/components/funds/FundFormDrawer.vue'
import { fmsApi } from '@/services/fmsApi'
import type { Company, DuplicatedFund, DuplicatedFundRecord, Fund, FundManager, FundPayload } from '@/types/fms'

const funds = ref<Fund[]>([])
const duplicatedFunds = ref<DuplicatedFund[]>([])
const fundManagers = ref<FundManager[]>([])
const companies = ref<Company[]>([])
const loading = ref(false)
const error = ref('')
const success = ref('')
const managersById = ref<Record<number, string>>({})

const editingFundId = ref<number | null>(null)
const isFormDrawerOpen = ref(false)
const isDuplicatedDrawerOpen = ref(false)
const drawerCurrentFund = ref<DuplicatedFundRecord | null>(null)
const drawerDuplicatedFund = ref<DuplicatedFundRecord | null>(null)
const formModel = ref<FundPayload>({
  name: '',
  start_year: new Date().getFullYear(),
  manager_id: 1,
  aliases: [],
  companies: [],
})

const duplicatedPairByFundId = computed<Map<number, DuplicatedFund>>(() => {
  const pairs = new Map<number, DuplicatedFund>()

  for (const pair of duplicatedFunds.value) {
    pairs.set(pair.source.id, pair)
    pairs.set(pair.duplicated.id, pair)
  }

  return pairs
})

function getFundId(fund: Fund): number {
  return Number(fund.id)
}

function getFundName(fund: Fund): string {
  return String(fund.name ?? '')
}

function getFundStartYear(fund: Fund): number {
  return Number(fund.start_year ?? fund.startYear ?? new Date().getFullYear())
}

function getFundManagerId(fund: Fund): number {
  return Number(fund.manager_id ?? fund.managerId ?? 0)
}

function getFundManagerName(fund: Fund): string {
  return managersById.value[getFundManagerId(fund)] ?? '-'
}

function getFundAliases(fund: Fund): string[] {
  return Array.isArray(fund.aliases) ? fund.aliases.map((alias) => String(alias).trim()).filter(Boolean) : []
}

function getFundAliasesPreview(fund: Fund): string {
  const aliases = getFundAliases(fund)

  if (aliases.length === 0) {
    return '-'
  }

  const preview = aliases.slice(0, 3).join(', ')
  return aliases.length > 3 ? `${preview}, ...` : preview
}

function getManagerNameById(managerId: number): string {
  return managersById.value[managerId] ?? '-'
}

function withManagerName(record: DuplicatedFundRecord): DuplicatedFundRecord {
  return {
    ...record,
    managerName: record.managerName ?? getManagerNameById(record.managerId),
  }
}

async function loadFunds(): Promise<void> {
  loading.value = true
  error.value = ''

  try {
    const [fundsResponse, duplicatedResponse, managersResponse, companiesResponse] = await Promise.all([
      fmsApi.listFunds(),
      fmsApi.listDuplicatedFunds(),
      fmsApi.listFundManagers(),
      fmsApi.listCompanies(),
    ])

    funds.value = fundsResponse
    duplicatedFunds.value = duplicatedResponse
    fundManagers.value = managersResponse
    companies.value = companiesResponse
    managersById.value = managersResponse.reduce<Record<number, string>>((result, manager) => {
      result[Number(manager.id)] = String(manager.name)
      return result
    }, {})
  } catch (requestError) {
    error.value = requestError instanceof Error ? requestError.message : 'Failed to load funds.'
  } finally {
    loading.value = false
  }
}

function resetForm(): void {
  editingFundId.value = null
  formModel.value = {
    name: '',
    start_year: new Date().getFullYear(),
    manager_id: 1,
    aliases: [],
    companies: [],
  }
}

function openCreateFundDrawer(): void {
  resetForm()
  isFormDrawerOpen.value = true
}

function closeFormDrawer(): void {
  isFormDrawerOpen.value = false
  resetForm()
}

function editFund(item: Record<string, unknown>): void {
  const id = Number(item.id)

  if (!Number.isFinite(id)) {
    return
  }

  editingFundId.value = id
  formModel.value = {
    name: String(item.name ?? ''),
    start_year: Number(item.start_year ?? item.startYear ?? new Date().getFullYear()),
    manager_id: Number(item.manager_id ?? item.managerId ?? 1),
    aliases: Array.isArray(item.aliases)
      ? item.aliases.map((alias) => String(alias).trim()).filter((alias) => alias.length > 0)
      : [],
    companies: Array.isArray(item.companies)
      ? item.companies.map((companyId) => Number(companyId)).filter((companyId) => Number.isFinite(companyId))
      : [],
  }
  isFormDrawerOpen.value = true
}

function openDuplicatedFundDrawer(fund: Fund): void {
  const fundId = getFundId(fund)

  if (!Number.isFinite(fundId)) {
    return
  }

  const pair = duplicatedPairByFundId.value.get(fundId)

  if (!pair) {
    return
  }

  const currentFundRecord = pair.source.id === fundId ? pair.source : pair.duplicated
  const duplicatedFundRecord = pair.source.id === fundId ? pair.duplicated : pair.source

  drawerCurrentFund.value = withManagerName(currentFundRecord)
  drawerDuplicatedFund.value = withManagerName(duplicatedFundRecord)
  isDuplicatedDrawerOpen.value = true
}

function closeDuplicatedFundDrawer(): void {
  isDuplicatedDrawerOpen.value = false
  drawerCurrentFund.value = null
  drawerDuplicatedFund.value = null
}

async function saveFund(payload: FundPayload): Promise<void> {
  error.value = ''
  success.value = ''

  try {
    if (editingFundId.value === null) {
      await fmsApi.createFund(payload)
      success.value = 'Fund created successfully.'
    } else {
      await fmsApi.updateFund(editingFundId.value, payload)
      success.value = 'Fund updated successfully.'
    }

    closeFormDrawer()
    await loadFunds()
  } catch (requestError) {
    error.value = requestError instanceof Error ? requestError.message : 'Failed to save fund.'
  }
}

async function removeFund(item: Record<string, unknown>): Promise<void> {
  const id = Number(item.id)

  if (!Number.isFinite(id)) {
    return
  }

  error.value = ''
  success.value = ''

  try {
    await fmsApi.deleteFund(id)
    success.value = 'Fund deleted successfully.'

    if (editingFundId.value === id) {
      closeFormDrawer()
    }

    await loadFunds()
  } catch (requestError) {
    error.value = requestError instanceof Error ? requestError.message : 'Failed to delete fund.'
  }
}

onMounted(loadFunds)
</script>

<template>
  <section>
    <PageHeader title="Funds" description="Mapped to /funds and full CRUD operations.">
      <template #actions>
        <button
          type="button"
          class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800"
          @click="openCreateFundDrawer"
        >
          New Fund
        </button>
        <button
          type="button"
          class="rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200"
          @click="loadFunds"
        >
          Refresh
        </button>
      </template>
    </PageHeader>

    <div class="mb-4 space-y-2">
      <p v-if="error" class="rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
        {{ error }}
      </p>
      <p
        v-if="success"
        class="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700"
      >
        {{ success }}
      </p>
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
      <div v-if="loading" class="p-4 text-sm text-slate-600">Loading...</div>

      <div v-else-if="!funds.length" class="p-4 text-sm text-slate-600">No funds found.</div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">ID</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Name</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                Start Year
              </th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Manager</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Aliases</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100 bg-white">
            <tr v-for="fund in funds" :key="getFundId(fund)">
              <td class="px-4 py-3 text-sm text-slate-700">{{ getFundId(fund) }}</td>
              <td class="px-4 py-3 text-sm text-slate-700">
                <div class="flex items-center gap-2">
                  <button
                    v-if="duplicatedPairByFundId.has(getFundId(fund))"
                    type="button"
                    class="cursor-pointer rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 hover:bg-amber-200"
                    @click="openDuplicatedFundDrawer(fund)"
                  >
                    Duplicated
                  </button>
                  <span>{{ getFundName(fund) }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-slate-700">{{ getFundStartYear(fund) }}</td>
              <td class="px-4 py-3 text-sm text-slate-700">{{ getFundManagerName(fund) }}</td>
              <td class="px-4 py-3 text-sm text-slate-700">{{ getFundAliasesPreview(fund) }}</td>
              <td class="px-4 py-3 text-sm text-slate-700">
                <div class="flex gap-2">
                  <button
                    type="button"
                    class="rounded-md bg-slate-100 px-3 py-1.5 font-medium text-slate-800 hover:bg-slate-200"
                    @click="editFund(fund)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="rounded-md bg-rose-100 px-3 py-1.5 font-medium text-rose-700 hover:bg-rose-200"
                    @click="removeFund(fund)"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <FundFormDrawer
      :open="isFormDrawerOpen"
      :model-value="formModel"
      :editing="editingFundId !== null"
      :fund-managers="fundManagers"
      :companies="companies"
      @submit="saveFund"
      @close="closeFormDrawer"
    />

    <DuplicatedFundDrawer
      :open="isDuplicatedDrawerOpen"
      :current-fund="drawerCurrentFund"
      :duplicated-fund="drawerDuplicatedFund"
      @close="closeDuplicatedFundDrawer"
    />
  </section>
</template>
