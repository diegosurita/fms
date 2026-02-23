<script setup lang="ts">
import { reactive, watch } from 'vue'
import type { FundManager, FundPayload } from '@/types/fms'

const props = defineProps<{
  modelValue: FundPayload
  editing: boolean
  fundManagers: FundManager[]
}>()

const emit = defineEmits<{
  submit: [payload: FundPayload]
  cancel: []
}>()

const form = reactive<FundPayload>({
  name: '',
  start_year: new Date().getFullYear(),
  manager_id: 1,
})

watch(
  () => props.modelValue,
  (value) => {
    form.name = value.name
    form.start_year = value.start_year
    form.manager_id = value.manager_id
  },
  { immediate: true, deep: true },
)

function onSubmit(): void {
  emit('submit', {
    name: form.name.trim(),
    start_year: Number(form.start_year),
    manager_id: Number(form.manager_id),
  })
}
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <h3 class="text-base font-semibold text-slate-900">
      {{ editing ? 'Edit Fund' : 'Create Fund' }}
    </h3>

    <form class="mt-4 space-y-4" @submit.prevent="onSubmit">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700" for="fund-name">Name</label>
        <input
          id="fund-name"
          v-model="form.name"
          type="text"
          required
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none ring-slate-300 focus:ring"
        />
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700" for="fund-start-year"
          >Start Year</label
        >
        <input
          id="fund-start-year"
          v-model.number="form.start_year"
          type="number"
          min="1900"
          required
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none ring-slate-300 focus:ring"
        />
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700" for="fund-manager-id"
          >Manager</label
        >
        <select
          id="fund-manager-id"
          v-model.number="form.manager_id"
          required
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none ring-slate-300 focus:ring"
        >
          <option v-for="manager in props.fundManagers" :key="manager.id" :value="manager.id">
            {{ manager.name }}
          </option>
        </select>
      </div>

      <div class="flex gap-2 pt-2">
        <button
          type="submit"
          class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800"
        >
          {{ editing ? 'Update' : 'Create' }}
        </button>
        <button
          v-if="editing"
          type="button"
          class="rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200"
          @click="emit('cancel')"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>
