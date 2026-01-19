<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Recursos Humanos -->
      <div class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">Recursos Humanos</h3>
        </div>

        <div v-if="recursosHumanos.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
          <p class="text-gray-500">No hay recursos humanos asignados</p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="recurso in recursosHumanos"
            :key="recurso.id"
            class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
          >
            <div class="flex justify-between items-center">
              <div>
                <h5 class="font-medium text-gray-900">{{ recurso.rol }}</h5>
                <p class="text-sm text-gray-600">{{ recurso.personas }} personas</p>
                <p class="text-xs text-gray-500 mt-1">{{ recurso.descripcion }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recursos Materiales -->
      <div class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">Recursos Materiales</h3>
        </div>

        <div v-if="recursosMateriales.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
          <p class="text-gray-500">No hay recursos materiales requeridos</p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="recurso in recursosMateriales"
            :key="recurso.id"
            class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
          >
            <div class="flex justify-between items-center">
              <div class="flex-1">
                <h5 class="font-medium text-gray-900">{{ recurso.nombre }}</h5>
                <div class="flex items-center justify-between text-sm text-gray-600">
                  <span>Cantidad: {{ recurso.cantidad }} {{ recurso.unidad }}</span>
                  <span v-if="recurso.costo" class="text-green-600">
                    {{ formatearMoneda(recurso.costo) }}
                  </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ recurso.descripcion }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Presupuesto -->
    <div v-if="campania.presupuesto_total" class="pt-6 border-t border-gray-200">
      <h4 class="text-lg font-medium text-gray-900 mb-4">Presupuesto</h4>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
          <span class="text-sm text-gray-600">Total asignado</span>
          <div class="mt-1 font-medium text-gray-900">{{ formatearMoneda(campania.presupuesto_total) }}</div>
        </div>
        <div>
          <span class="text-sm text-gray-600">Ejecutado</span>
          <div class="mt-1 font-medium text-green-600">{{ formatearMoneda(campania.presupuesto_ejecutado || 0) }}</div>
        </div>
        <div>
          <span class="text-sm text-gray-600">Disponible</span>
          <div class="mt-1 font-medium text-blue-600">
            {{ formatearMoneda(campania.presupuesto_total - (campania.presupuesto_ejecutado || 0)) }}
          </div>
        </div>
      </div>

      <!-- Barra de progreso -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm font-medium text-gray-700">Ejecución del presupuesto</span>
          <span class="text-sm font-medium text-gray-900">
            {{ calcularPorcentajeEjecucion() }}%
          </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
          <div
            class="bg-purple-600 h-3 rounded-full transition-all duration-300"
            :style="{ width: calcularPorcentajeEjecucion() + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Metas financieras -->
    <div v-if="campania.meta_recaudacion" class="pt-6 border-t border-gray-200">
      <h4 class="text-lg font-medium text-gray-900 mb-4">Metas Financieras</h4>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
          <span class="text-sm text-gray-600">Meta de recaudación</span>
          <div class="mt-1 text-2xl font-bold text-green-700">{{ formatearMoneda(campania.meta_recaudacion) }}</div>
          <div v-if="campania.recaudacion_actual" class="mt-2">
            <div class="flex justify-between text-xs text-gray-600">
              <span>Recaudado: {{ formatearMoneda(campania.recaudacion_actual) }}</span>
              <span>{{ calcularPorcentajeRecaudacion() }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
              <div
                class="bg-green-600 h-2 rounded-full"
                :style="{ width: calcularPorcentajeRecaudacion() + '%' }"
              ></div>
            </div>
          </div>
        </div>
        
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
          <span class="text-sm text-gray-600">Financiación restante</span>
          <div class="mt-1 text-2xl font-bold text-blue-700">
            {{ formatearMoneda(campania.meta_recaudacion - (campania.recaudacion_actual || 0)) }}
          </div>
          <p class="text-xs text-gray-600 mt-2">Para alcanzar la meta de recaudación</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  campania: {
    type: Object,
    required: true
  },
  recursosHumanos: {
    type: Array,
    default: () => []
  },
  recursosMateriales: {
    type: Array,
    default: () => []
  }
})

const formatearMoneda = (cantidad) => {
  if (!cantidad) return '€0.00'
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR'
  }).format(cantidad)
}

const calcularPorcentajeEjecucion = () => {
  if (!props.campania.presupuesto_total) return 0
  const ejecutado = props.campania.presupuesto_ejecutado || 0
  return Math.round((ejecutado / props.campania.presupuesto_total) * 100)
}

const calcularPorcentajeRecaudacion = () => {
  if (!props.campania.meta_recaudacion) return 0
  const recaudado = props.campania.recaudacion_actual || 0
  return Math.round((recaudado / props.campania.meta_recaudacion) * 100)
}
</script>