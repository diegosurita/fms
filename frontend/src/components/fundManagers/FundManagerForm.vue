<script setup lang="ts">
import { reactive, watch } from 'vue'
import type { FundManagerPayload } from '@/types/fms'

const props = defineProps<{
  modelValue: FundManagerPayload
  editing: boolean
}>()

const emit = defineEmits<{
  submit: [payload: FundManagerPayload]
  cancel: []
}>()

const form = reactive<FundManagerPayload>({
  name: '',
})

watch(
  () => props.modelValue,
  (value) => {
    form.name = value.name
  },
  { immediate: true, deep: true },
)

function onSubmit(): void {
  emit('submit', { name: form.name.trim() })
}
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <h3 class="text-base font-semibold text-slate-900">
      {{ editing ? 'Edit Fund Manager' : 'Create Fund Manager' }}
    </h3>

    <form class="mt-4 space-y-4" @submit.prevent="onSubmit">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700" for="manager-name">Name</label>
        <input
          id="manager-name"
          v-model="form.name"
          type="text"
          required
          class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none ring-slate-300 focus:ring"
        />
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
