<template>
  <div class="border-b border-gray-200">
    <nav class="flex space-x-8 px-6" aria-label="Tabs">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="$emit('tab-change', tab.id)"
        :class="[
          activeTab === tab.id
            ? 'border-purple-500 text-purple-600'
            : 'border-transparent text-gray-700 hover:text-gray-900 hover:border-gray-400',
          'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center'
        ]"
      >
        <span class="mr-2 text-lg">{{ tab.icon }}</span>
        {{ tab.name }}
        <span v-if="tab.count !== undefined && tab.count > 0" 
              class="ml-2 bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded-full">
          {{ tab.count }}
        </span>
      </button>
    </nav>
  </div>
</template>

<script setup>
defineProps({
  tabs: {
    type: Array,
    required: true
  },
  activeTab: {
    type: String,
    required: true
  }
})

defineEmits(['tab-change'])
</script>