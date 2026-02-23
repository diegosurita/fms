<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    open: boolean
    title?: string
  }>(),
  {
    title: '',
  },
)

const emit = defineEmits<{
  close: []
}>()
</script>

<template>
  <div v-if="props.open" class="fixed inset-0 z-50">
    <button
      type="button"
      class="absolute inset-0 bg-slate-900/40"
      aria-label="Close drawer"
      @click="emit('close')"
    />

    <aside class="absolute right-0 top-0 h-full w-1/2 max-w-none bg-white shadow-xl">
      <header class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
        <h3 class="text-base font-semibold text-slate-900">{{ props.title }}</h3>
        <button
          type="button"
          class="rounded-md bg-slate-100 px-2 py-1 text-sm font-medium text-slate-700 hover:bg-slate-200"
          @click="emit('close')"
        >
          Close
        </button>
      </header>

      <div class="h-[calc(100%-57px)] overflow-y-auto p-4">
        <slot />
      </div>
    </aside>
  </div>
</template>
