<script setup lang="ts">
import { onMounted, ref } from 'vue'
import DataTable from '@/components/common/DataTable.vue'
import PageHeader from '@/components/common/PageHeader.vue'
import FundManagerForm from '@/components/fundManagers/FundManagerForm.vue'
import { fmsApi } from '@/services/fmsApi'
import type { FundManager, FundManagerPayload } from '@/types/fms'

const fundManagers = ref<FundManager[]>([])
const loading = ref(false)
const error = ref('')
const success = ref('')

const editingFundManagerId = ref<number | null>(null)
const formModel = ref<FundManagerPayload>({ name: '' })

async function loadFundManagers(): Promise<void> {
  loading.value = true
  error.value = ''

  try {
    fundManagers.value = await fmsApi.listFundManagers()
  } catch (requestError) {
    error.value =
      requestError instanceof Error ? requestError.message : 'Failed to load fund managers.'
  } finally {
    loading.value = false
  }
}

function resetForm(): void {
  editingFundManagerId.value = null
  formModel.value = { name: '' }
}

function editFundManager(item: Record<string, unknown>): void {
  const id = Number(item.id)

  if (!Number.isFinite(id)) {
    return
  }

  editingFundManagerId.value = id
  formModel.value = { name: String(item.name ?? '') }
}

async function saveFundManager(payload: FundManagerPayload): Promise<void> {
  error.value = ''
  success.value = ''

  try {
    if (editingFundManagerId.value === null) {
      await fmsApi.createFundManager(payload)
      success.value = 'Fund manager created successfully.'
    } else {
      await fmsApi.updateFundManager(editingFundManagerId.value, payload)
      success.value = 'Fund manager updated successfully.'
    }

    resetForm()
    await loadFundManagers()
  } catch (requestError) {
    error.value = requestError instanceof Error ? requestError.message : 'Failed to save fund manager.'
  }
}

async function removeFundManager(item: Record<string, unknown>): Promise<void> {
  const id = Number(item.id)

  if (!Number.isFinite(id)) {
    return
  }

  error.value = ''
  success.value = ''

  try {
    await fmsApi.deleteFundManager(id)
    success.value = 'Fund manager deleted successfully.'

    if (editingFundManagerId.value === id) {
      resetForm()
    }

    await loadFundManagers()
  } catch (requestError) {
    error.value =
      requestError instanceof Error ? requestError.message : 'Failed to delete fund manager.'
  }
}

onMounted(loadFundManagers)
</script>

<template>
  <section>
    <PageHeader title="Fund Managers" description="Mapped to /fund-managers and full CRUD operations.">
      <template #actions>
        <button
          type="button"
          class="rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200"
          @click="loadFundManagers"
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
      <FundManagerForm
        :model-value="formModel"
        :editing="editingFundManagerId !== null"
        @submit="saveFundManager"
        @cancel="resetForm"
      />

      <DataTable
        :items="fundManagers"
        :loading="loading"
        empty-message="No fund managers found."
        @edit="editFundManager"
        @delete="removeFundManager"
      />
    </div>
  </section>
</template>
