<script setup lang="ts">
import { onMounted, ref } from 'vue'
import CompanyForm from '@/components/companies/CompanyForm.vue'
import DataTable from '@/components/common/DataTable.vue'
import PageHeader from '@/components/common/PageHeader.vue'
import { fmsApi } from '@/services/fmsApi'
import type { Company, CompanyPayload } from '@/types/fms'

const companies = ref<Company[]>([])
const loading = ref(false)
const error = ref('')
const success = ref('')

const editingCompanyId = ref<number | null>(null)
const formModel = ref<CompanyPayload>({ name: '' })

async function loadCompanies(): Promise<void> {
  loading.value = true
  error.value = ''

  try {
    companies.value = await fmsApi.listCompanies()
  } catch (requestError) {
    error.value = requestError instanceof Error ? requestError.message : 'Failed to load companies.'
  } finally {
    loading.value = false
  }
}

function resetForm(): void {
  editingCompanyId.value = null
  formModel.value = { name: '' }
}

function editCompany(item: Record<string, unknown>): void {
  const id = Number(item.id)

  if (!Number.isFinite(id)) {
    return
  }

  editingCompanyId.value = id
  formModel.value = { name: String(item.name ?? '') }
}

async function saveCompany(payload: CompanyPayload): Promise<void> {
  error.value = ''
  success.value = ''

  try {
    if (editingCompanyId.value === null) {
      await fmsApi.createCompany(payload)
      success.value = 'Company created successfully.'
    } else {
      await fmsApi.updateCompany(editingCompanyId.value, payload)
      success.value = 'Company updated successfully.'
    }

    resetForm()
    await loadCompanies()
  } catch (requestError) {
    error.value = requestError instanceof Error ? requestError.message : 'Failed to save company.'
  }
}

async function removeCompany(item: Record<string, unknown>): Promise<void> {
  const id = Number(item.id)

  if (!Number.isFinite(id)) {
    return
  }

  error.value = ''
  success.value = ''

  try {
    await fmsApi.deleteCompany(id)
    success.value = 'Company deleted successfully.'

    if (editingCompanyId.value === id) {
      resetForm()
    }

    await loadCompanies()
  } catch (requestError) {
    error.value = requestError instanceof Error ? requestError.message : 'Failed to delete company.'
  }
}

onMounted(loadCompanies)
</script>

<template>
  <section>
    <PageHeader title="Companies" description="Mapped to /companies and full CRUD operations.">
      <template #actions>
        <button
          type="button"
          class="rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200"
          @click="loadCompanies"
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
      <CompanyForm
        :model-value="formModel"
        :editing="editingCompanyId !== null"
        @submit="saveCompany"
        @cancel="resetForm"
      />

      <DataTable
        :items="companies"
        :loading="loading"
        empty-message="No companies found."
        @edit="editCompany"
        @delete="removeCompany"
      />
    </div>
  </section>
</template>
