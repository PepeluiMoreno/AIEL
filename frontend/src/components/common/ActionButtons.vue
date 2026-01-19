<template>
  <div class="flex items-center space-x-3">
    <!-- Botón Volver -->
    <router-link
      v-if="showBack"
      :to="backRoute"
      class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
    >
      ← {{ backText }}
    </router-link>
    
    <!-- Botón Editar -->
    <router-link
      v-if="editRoute"
      :to="editRoute"
      class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors flex items-center"
    >
      <span class="mr-1">✏️</span>
      {{ editText }}
    </router-link>
    
    <!-- Botón Eliminar -->
    <button
      v-if="showDelete"
      @click="handleDelete"
      class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors flex items-center"
    >
      <span class="mr-1">🗑️</span>
      {{ deleteText }}
    </button>
    
    <!-- Slot para botones adicionales -->
    <slot />
  </div>
</template>

<script setup>
import { defineEmits } from 'vue'

const props = defineProps({
  showBack: {
    type: Boolean,
    default: true
  },
  backRoute: {
    type: [String, Object],
    default: '/campanias'
  },
  backText: {
    type: String,
    default: 'Volver'
  },
  editRoute: {
    type: [String, Object],
    default: ''
  },
  editText: {
    type: String,
    default: 'Editar'
  },
  showDelete: {
    type: Boolean,
    default: false
  },
  deleteText: {
    type: String,
    default: 'Eliminar'
  }
})

const emit = defineEmits(['delete'])

const handleDelete = () => {
  if (confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
    emit('delete')
  }
}
</script>