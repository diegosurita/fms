<script setup lang="ts">
import { onMounted, ref } from 'vue'
import DataTable from '@/components/common/DataTable.vue'
import PageHeader from '@/components/common/PageHeader.vue'
import { fmsApi } from '@/services/fmsApi'
import type { DuplicatedFund } from '@/types/fms'

const duplicatedFunds = ref<DuplicatedFund[]>([])
const loading = ref(false)
const error = ref('')

async function loadDuplicatedFunds(): Promise<void> {
  loading.value = true
  error.value = ''

  try {
    duplicatedFunds.value = await fmsApi.listDuplicatedFunds()
  } catch (requestError) {
    error.value =
      requestError instanceof Error ? requestError.message : 'Failed to load duplicated funds.'
  } finally {
    loading.value = false
  }
}

onMounted(loadDuplicatedFunds)
</script>

<template>
  <section>
    <PageHeader title="Duplicated Funds" description="Mapped to /funds/duplicated (read-only list).">
      <template #actions>
        <button
          type="button"
          class="rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200"
          @click="loadDuplicatedFunds"
        >
          Refresh
        </button>
      </template>
    </PageHeader>

    <p v-if="error" class="mb-4 rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
      {{ error }}
    </p>

    <DataTable
      :items="duplicatedFunds"
      :loading="loading"
      :show-actions="false"
      empty-message="No duplicated funds found."
    />
  </section>
</template>
