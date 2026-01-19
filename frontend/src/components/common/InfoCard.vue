<template>
  <div :class="['rounded-lg border p-4', bgClass]">
    <div class="flex items-start justify-between">
      <div class="flex-1">
        <h3 v-if="title" class="text-sm font-medium text-gray-900 mb-2">{{ title }}</h3>
        <div v-if="$slots.default" class="space-y-2">
          <slot />
        </div>
        <div v-else class="space-y-2">
          <div v-for="(item, index) in items" :key="index" class="flex justify-between">
            <span class="text-sm text-gray-600">{{ item.label }}</span>
            <span class="text-sm font-medium text-gray-900">{{ item.value }}</span>
          </div>
        </div>
        <p v-if="description" class="text-xs text-gray-500 mt-3">{{ description }}</p>
      </div>
      <div v-if="icon" class="ml-4 text-2xl">
        {{ icon }}
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  bgClass: {
    type: String,
    default: 'bg-gray-50 border-gray-200'
  },
  items: {
    type: Array,
    default: () => []
  }
})
</script>