<script setup lang="ts">
import { onMounted, ref } from 'vue'
import DataTable from '@/components/common/DataTable.vue'
import PageHeader from '@/components/common/PageHeader.vue'
import FundForm from '@/components/funds/FundForm.vue'
import { fmsApi } from '@/services/fmsApi'
import type { Fund, FundPayload } from '@/types/fms'

const funds = ref<Fund[]>([])
const loading = ref(false)
const error = ref('')
const success = ref('')

const editingFundId = ref<number | null>(null)
const formModel = ref<FundPayload>({
  name: '',
  start_year: new Date().getFullYear(),
  manager_id: 1,
})

async function loadFunds(): Promise<void> {
  loading.value = true
  error.value = ''

  try {
    funds.value = await fmsApi.listFunds()
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
  }
}

function editFund(item: Record<string, unknown>): void {
  const id = Number(item.id)

  if (!Number.isFinite(id)) {
    return
  }

  editingFundId.value = id
  formModel.value = {
    name: String(item.name ?? ''),
    start_year: Number(item.start_year ?? new Date().getFullYear()),
    manager_id: Number(item.manager_id ?? 1),
  }
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

    resetForm()
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
      resetForm()
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

    <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
      <FundForm :model-value="formModel" :editing="editingFundId !== null" @submit="saveFund" @cancel="resetForm" />

      <DataTable
        :items="funds"
        :loading="loading"
        empty-message="No funds found."
        @edit="editFund"
        @delete="removeFund"
      />
    </div>
  </section>
</template>
