<script setup lang="ts">
defineProps<{
  title: string
  description: string
}>()
</script>

<template>
  <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-semibold tracking-tight text-slate-900">{{ title }}</h2>
      <p class="mt-1 text-sm text-slate-600">{{ description }}</p>
    </div>

    <div class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>
