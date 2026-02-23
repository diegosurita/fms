<script setup lang="ts">
import { computed, ref } from 'vue'
import TopMenu from '@/components/layout/TopMenu.vue'
import CompaniesPage from '@/pages/CompaniesPage.vue'
import DuplicatedFundsPage from '@/pages/DuplicatedFundsPage.vue'
import FundManagersPage from '@/pages/FundManagersPage.vue'
import FundsPage from '@/pages/FundsPage.vue'

type MenuPage = '/funds' | '/funds/duplicated' | '/companies' | '/fund-managers'

const currentPage = ref<MenuPage>('/funds')

const currentView = computed(() => {
  if (currentPage.value === '/companies') {
    return CompaniesPage
  }

  if (currentPage.value === '/fund-managers') {
    return FundManagersPage
  }

  if (currentPage.value === '/funds/duplicated') {
    return DuplicatedFundsPage
  }

  return FundsPage
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-900">
    <TopMenu :selected="currentPage" @select="currentPage = $event" />

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
      <component :is="currentView" />
    </main>
  </div>
</template>
