<script setup lang="ts">
type MenuPage = '/funds' | '/funds/duplicated' | '/companies' | '/fund-managers'

defineProps<{
  selected: MenuPage
}>()

const emit = defineEmits<{
  select: [page: MenuPage]
}>()

const menuItems: Array<{ label: string; page: MenuPage }> = [
  { label: 'Funds', page: '/funds' },
  { label: 'Duplicated Funds', page: '/funds/duplicated' },
  { label: 'Companies', page: '/companies' },
  { label: 'Fund Managers', page: '/fund-managers' },
]
</script>

<template>
  <header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
      <div>
        <h1 class="text-xl font-semibold tracking-tight text-slate-900">FMS Frontend</h1>
        <p class="text-sm text-slate-600">Admin pages mapped to backend API v1 routes</p>
      </div>

      <nav class="flex flex-wrap items-center gap-2">
        <button
          v-for="item in menuItems"
          :key="item.page"
          type="button"
          class="rounded-md px-3 py-2 text-sm font-medium transition"
          :class="
            selected === item.page
              ? 'bg-slate-900 text-white'
              : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900'
          "
          @click="emit('select', item.page)"
        >
          {{ item.label }}
        </button>
      </nav>
    </div>
  </header>
</template>
