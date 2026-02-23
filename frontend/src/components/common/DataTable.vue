<script setup lang="ts">
import { computed } from 'vue'

type TableRow = Record<string, unknown>

const props = withDefaults(
  defineProps<{
    items: TableRow[]
    loading?: boolean
    emptyMessage?: string
    showActions?: boolean
  }>(),
  {
    loading: false,
    emptyMessage: 'No records found.',
    showActions: true,
  },
)

const emit = defineEmits<{
  edit: [item: TableRow]
  delete: [item: TableRow]
}>()

const columns = computed<string[]>(() => {
  const keys = new Set<string>()

  for (const item of props.items) {
    Object.keys(item).forEach((key) => keys.add(key))
  }

  return Array.from(keys)
})

const DATE_COLUMN_PATTERN = /(?:^|_|-|\b)(date|time|timestamp)(?:$|_|-|\b)|(?:_at$|At$)/i

function padDatePart(value: number): string {
  return String(value).padStart(2, '0')
}

function formatDateTime(value: Date): string {
  const month = padDatePart(value.getMonth() + 1)
  const day = padDatePart(value.getDate())
  const year = value.getFullYear()
  const hours = padDatePart(value.getHours())
  const minutes = padDatePart(value.getMinutes())
  const seconds = padDatePart(value.getSeconds())

  return `${month}/${day}/${year} ${hours}:${minutes}:${seconds}`
}

function isDateColumn(column: string): boolean {
  return DATE_COLUMN_PATTERN.test(column)
}

function isParsableDateString(value: string): boolean {
  return /^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}:\d{2}(?:\.\d+)?)?(?:Z|[+-]\d{2}:?\d{2})?$/.test(value)
}

function formatColumnTitle(column: string): string {
  const normalized = column
    .replace(/([a-z\d])([A-Z])/g, '$1 $2')
    .replace(/[_-]+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()

  return normalized.replace(/\b\w/g, (char) => char.toUpperCase())
}

function formatCellValue(value: unknown, column: string): string {
  if (value === null || value === undefined) {
    return '-'
  }

  if (value instanceof Date && !Number.isNaN(value.getTime())) {
    return formatDateTime(value)
  }

  if (typeof value === 'string') {
    const trimmedValue = value.trim()

    if ((isDateColumn(column) || isParsableDateString(trimmedValue)) && trimmedValue) {
      const parsedDate = new Date(trimmedValue)

      if (!Number.isNaN(parsedDate.getTime())) {
        return formatDateTime(parsedDate)
      }
    }
  }

  if (typeof value === 'object') {
    return JSON.stringify(value)
  }

  return String(value)
}
</script>

<template>
  <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div v-if="loading" class="p-4 text-sm text-slate-600">Loading...</div>

    <div v-else-if="!items.length" class="p-4 text-sm text-slate-600">
      {{ emptyMessage }}
    </div>

    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column"
              class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"
            >
              {{ formatColumnTitle(column) }}
            </th>
            <th
              v-if="showActions"
              class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <tr v-for="(item, index) in items" :key="String(item.id ?? index)">
            <td
              v-for="column in columns"
              :key="column"
              class="max-w-xs truncate px-4 py-3 text-sm text-slate-700"
              :title="formatCellValue(item[column], column)"
            >
              {{ formatCellValue(item[column], column) }}
            </td>
            <td v-if="showActions" class="px-4 py-3 text-sm text-slate-700">
              <div class="flex gap-2">
                <button
                  type="button"
                  class="rounded-md bg-slate-100 px-3 py-1.5 font-medium text-slate-800 hover:bg-slate-200"
                  @click="emit('edit', item)"
                >
                  Edit
                </button>
                <button
                  type="button"
                  class="rounded-md bg-rose-100 px-3 py-1.5 font-medium text-rose-700 hover:bg-rose-200"
                  @click="emit('delete', item)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
