<script setup lang="ts">
import SideDrawer from '@/components/common/SideDrawer.vue'
import FundManagerForm from '@/components/fundManagers/FundManagerForm.vue'
import type { FundManagerPayload } from '@/types/fms'

const props = defineProps<{
  open: boolean
  modelValue: FundManagerPayload
  editing: boolean
}>()

const emit = defineEmits<{
  submit: [payload: FundManagerPayload]
  close: []
}>()
</script>

<template>
  <SideDrawer
    :open="props.open"
    :title="props.editing ? 'Edit Fund Manager' : 'Create Fund Manager'"
    @close="emit('close')"
  >
    <FundManagerForm
      :model-value="props.modelValue"
      :editing="props.editing"
      @submit="emit('submit', $event)"
      @cancel="emit('close')"
    />
  </SideDrawer>
</template>
