<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h3 class="text-lg font-medium text-gray-900">Objetivos de la Campaña</h3>
    </div>

    <div v-if="objetivos.length === 0" class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
      <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
        <span class="text-2xl">🎯</span>
      </div>
      <h3 class="text-sm font-medium text-gray-900">No hay objetivos definidos</h3>
      <p class="text-sm text-gray-500 mt-1">Esta campaña no tiene objetivos específicos definidos.</p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="objetivo in objetivos"
        :key="objetivo.id"
        class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <div class="flex items-center mb-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-2" 
                    :class="obtenerClaseTipoObjetivo(objetivo.tipo)">
                {{ obtenerNombreTipoObjetivo(objetivo.tipo) }}
              </span>
              <span class="text-sm text-gray-500">Prioridad: {{ objetivo.prioridad }}</span>
            </div>
            <h4 class="font-medium text-gray-900">{{ objetivo.titulo }}</h4>
            <p class="text-sm text-gray-600 mt-1">{{ objetivo.descripcion }}</p>
            
            <div v-if="objetivo.meta" class="mt-3">
              <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Progreso</span>
                <span>{{ objetivo.progreso || 0 }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-green-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: (objetivo.progreso || 0) + '%' }"
                ></div>
              </div>
            </div>

            <div v-if="objetivo.fecha_limite" class="mt-2 text-xs text-gray-500">
              <span>📅 Límite: {{ formatearFecha(objetivo.fecha_limite) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  objetivos: {
    type: Array,
    required: true
  }
})

const obtenerClaseTipoObjetivo = (tipo) => {
  const clases = {
    'cuantitativo': 'bg-blue-100 text-blue-800',
    'cualitativo': 'bg-purple-100 text-purple-800',
    'estrategico': 'bg-green-100 text-green-800',
    'operativo': 'bg-yellow-100 text-yellow-800'
  }
  return clases[tipo] || 'bg-gray-100 text-gray-800'
}

const obtenerNombreTipoObjetivo = (tipo) => {
  const nombres = {
    'cuantitativo': 'Cuantitativo',
    'cualitativo': 'Cualitativo',
    'estrategico': 'Estratégico',
    'operativo': 'Operativo'
  }
  return nombres[tipo] || tipo
}

const formatearFecha = (fecha) => {
  if (!fecha) return ''
  return new Date(fecha).toLocaleDateString('es-ES', {
    day: 'numeric',
    month: 'short'
  })
}
</script>