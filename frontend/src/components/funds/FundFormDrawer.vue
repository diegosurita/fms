<script setup lang="ts">
import SideDrawer from '@/components/common/SideDrawer.vue'
import FundForm from '@/components/funds/FundForm.vue'
import type { FundManager, FundPayload } from '@/types/fms'

const props = defineProps<{
  open: boolean
  modelValue: FundPayload
  editing: boolean
  fundManagers: FundManager[]
}>()

const emit = defineEmits<{
  submit: [payload: FundPayload]
  close: []
}>()
</script>

<template>
  <SideDrawer :open="props.open" :title="props.editing ? 'Edit Fund' : 'Create Fund'" @close="emit('close')">
    <FundForm
      :model-value="props.modelValue"
      :editing="props.editing"
      :fund-managers="props.fundManagers"
      @submit="emit('submit', $event)"
      @cancel="emit('close')"
    />
  </SideDrawer>
</template>
