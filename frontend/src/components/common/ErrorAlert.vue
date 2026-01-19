<template>
  <div class="bg-red-50 border border-red-200 rounded-lg p-4">
    <div class="flex">
      <div class="flex-shrink-0">
        <span class="text-red-400 text-xl">⚠️</span>
      </div>
      <div class="ml-3">
        <h3 class="text-sm font-medium text-red-800">{{ title }}</h3>
        <div class="text-sm text-red-700 mt-1">
          <slot>
            {{ message }}
          </slot>
        </div>
        <div v-if="retryAction" class="mt-3">
          <button
            @click="$emit('retry')"
            class="text-sm font-medium text-red-600 hover:text-red-500"
          >
            Reintentar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Error'
  },
  message: {
    type: String,
    default: 'Ha ocurrido un error.'
  },
  retryAction: {
    type: Boolean,
    default: false
  }
})

defineEmits(['retry'])
</script>