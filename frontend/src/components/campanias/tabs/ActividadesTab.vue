<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h3 class="text-lg font-medium text-gray-900">Actividades Programadas</h3>
    </div>

    <div v-if="actividades.length === 0" class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
      <p class="text-gray-500">No hay actividades programadas para esta campaña.</p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="actividad in actividades"
        :key="actividad.id"
        class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
              <h4 class="font-medium text-gray-900">{{ actividad.nombre }}</h4>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="actividad.completada ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                {{ actividad.completada ? 'Completada' : 'Pendiente' }}
              </span>
            </div>
            
            <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
              <span>📅 {{ formatearFecha(actividad.fecha) }}</span>
              <span v-if="actividad.hora_inicio">
                🕒 {{ actividad.hora_inicio }}{{ actividad.hora_fin ? ` - ${actividad.hora_fin}` : '' }}
              </span>
              <span v-if="actividad.lugar">📍 {{ actividad.lugar }}</span>
            </div>
            
            <p class="text-sm text-gray-700">{{ actividad.descripcion }}</p>
            
            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
              <span>👥 {{ actividad.voluntarios_confirmados || 0 }}/{{ actividad.voluntarios_necesarios || 0 }} voluntarios</span>
              <span v-if="actividad.materiales_necesarios">📦 {{ actividad.materiales_necesarios }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  actividades: {
    type: Array,
    required: true
  }
})

const formatearFecha = (fecha) => {
  if (!fecha) return ''
  return new Date(fecha).toLocaleDateString('es-ES', {
    day: 'numeric',
    month: 'short'
  })
}
</script>