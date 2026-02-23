<script setup lang="ts">
import SideDrawer from '@/components/common/SideDrawer.vue'
import type { DuplicatedFundRecord } from '@/types/fms'

const props = defineProps<{
  open: boolean
  currentFund: DuplicatedFundRecord | null
  duplicatedFund: DuplicatedFundRecord | null
}>()

const emit = defineEmits<{
  close: []
}>()

function aliasesText(aliases: string[]): string {
  return aliases.length ? aliases.join(', ') : '-'
}
</script>

<template>
  <SideDrawer :open="props.open" title="Duplicated Fund Details" @close="emit('close')">
    <div class="space-y-4">
      <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-700">Current fund</h4>

        <div v-if="props.currentFund" class="space-y-2 text-sm text-slate-800">
          <p><span class="font-medium">ID:</span> {{ props.currentFund.id }}</p>
          <p><span class="font-medium">Name:</span> {{ props.currentFund.name }}</p>
          <p><span class="font-medium">Start year:</span> {{ props.currentFund.startYear }}</p>
          <p><span class="font-medium">Manager:</span> {{ props.currentFund.managerName ?? '-' }}</p>
          <p><span class="font-medium">Aliases:</span> {{ aliasesText(props.currentFund.aliases) }}</p>
        </div>
        <p v-else class="text-sm text-slate-600">No current fund selected.</p>
      </div>

      <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-700">Duplicated fund</h4>

        <div v-if="props.duplicatedFund" class="space-y-2 text-sm text-slate-800">
          <p><span class="font-medium">ID:</span> {{ props.duplicatedFund.id }}</p>
          <p><span class="font-medium">Name:</span> {{ props.duplicatedFund.name }}</p>
          <p><span class="font-medium">Start year:</span> {{ props.duplicatedFund.startYear }}</p>
          <p><span class="font-medium">Manager:</span> {{ props.duplicatedFund.managerName ?? '-' }}</p>
          <p><span class="font-medium">Aliases:</span> {{ aliasesText(props.duplicatedFund.aliases) }}</p>
        </div>
        <p v-else class="text-sm text-slate-600">No duplicated fund found.</p>
      </div>
    </div>
  </SideDrawer>
</template>
