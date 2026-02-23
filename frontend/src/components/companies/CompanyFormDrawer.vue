<script setup lang="ts">
import SideDrawer from '@/components/common/SideDrawer.vue'
import CompanyForm from '@/components/companies/CompanyForm.vue'
import type { CompanyPayload } from '@/types/fms'

const props = defineProps<{
  open: boolean
  modelValue: CompanyPayload
  editing: boolean
}>()

const emit = defineEmits<{
  submit: [payload: CompanyPayload]
  close: []
}>()
</script>

<template>
  <SideDrawer :open="props.open" :title="props.editing ? 'Edit Company' : 'Create Company'" @close="emit('close')">
    <CompanyForm
      :model-value="props.modelValue"
      :editing="props.editing"
      @submit="emit('submit', $event)"
      @cancel="emit('close')"
    />
  </SideDrawer>
</template>
