<script setup lang="ts">
import { computed, ref } from 'vue'
import type { Company } from '@/types/fms'

const props = defineProps<{
  modelValue: number[]
  companies: Company[]
}>()

const emit = defineEmits<{
  'update:modelValue': [value: number[]]
}>()

const isOpen = ref(false)

const selectedCompaniesLabel = computed<string>(() => {
  if (props.modelValue.length === 0) {
    return 'Select companies'
  }

  if (props.modelValue.length === 1) {
    const company = props.companies.find((item) => item.id === props.modelValue[0])
    return company?.name ?? '1 company selected'
  }

  return `${props.modelValue.length} companies selected`
})

function toggleCompany(companyId: number): void {
  if (props.modelValue.includes(companyId)) {
    emit(
      'update:modelValue',
      props.modelValue.filter((id) => id !== companyId),
    )
    return
  }

  emit('update:modelValue', [...props.modelValue, companyId])
}

function isCompanySelected(companyId: number): boolean {
  return props.modelValue.includes(companyId)
}
</script>

<template>
  <div>
    <label class="mb-1 block text-sm font-medium text-slate-700" for="fund-companies">Companies</label>
    <div class="relative">
      <button
        id="fund-companies"
        type="button"
        class="flex w-full items-center justify-between rounded-md border border-slate-300 px-3 py-2 text-left text-sm text-slate-700 outline-none ring-slate-300 focus:ring"
        @click="isOpen = !isOpen"
      >
        <span class="truncate">{{ selectedCompaniesLabel }}</span>
        <span class="text-slate-500">▾</span>
      </button>

      <div
        v-if="isOpen"
        class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md border border-slate-200 bg-white p-2 shadow-sm"
      >
        <div v-if="props.companies.length === 0" class="px-2 py-1 text-sm text-slate-500">
          No companies available
        </div>

        <label
          v-for="company in props.companies"
          :key="company.id"
          class="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 text-sm text-slate-700 hover:bg-slate-50"
        >
          <input
            type="checkbox"
            :checked="isCompanySelected(company.id)"
            class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
            @change="toggleCompany(company.id)"
          />
          <span>{{ company.name }}</span>
        </label>
      </div>
    </div>
  </div>
</template>