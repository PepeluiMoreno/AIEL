<template>
  <div class="space-y-8">
    <!-- Descripción -->
    <div>
      <h3 class="text-lg font-medium text-gray-900 mb-4">Descripción</h3>
      <div class="prose max-w-none">
        <p class="text-gray-700 whitespace-pre-line">{{ campania.descripcion_larga || 'No hay descripción disponible.' }}</p>
      </div>
    </div>

    <!-- Información clave -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Tipo y Responsable -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-900 mb-3">Información General</h4>
        <div class="space-y-3">
          <div>
            <span class="text-sm text-gray-600">Tipo de campaña</span>
            <div class="mt-1 font-medium">{{ obtenerNombreTipo(campania.tipo_campania_id) }}</div>
          </div>
          <div>
            <span class="text-sm text-gray-600">Estado</span>
            <div class="mt-1">
              <span :class="obtenerClaseEstado(campania.estado)" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ obtenerNombreEstado(campania.estado) }}
              </span>
            </div>
          </div>
          <div>
            <span class="text-sm text-gray-600">Fecha inicio</span>
            <div class="mt-1 font-medium">{{ formatearFecha(campania.fecha_inicio_plan) }}</div>
          </div>
          <div>
            <span class="text-sm text-gray-600">Fecha fin</span>
            <div class="mt-1 font-medium">{{ formatearFecha(campania.fecha_fin_plan) }}</div>
          </div>
        </div>
      </div>

      <!-- Objetivos -->
      <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-900 mb-3">Objetivos</h4>
        <div class="space-y-3">
          <div v-if="campania.meta_recaudacion">
            <span class="text-sm text-gray-600">Meta recaudación</span>
            <div class="mt-1 font-medium text-green-600">{{ formatearMoneda(campania.meta_recaudacion) }}</div>
          </div>
          <div v-if="campania.meta_participantes">
            <span class="text-sm text-gray-600">Participantes objetivo</span>
            <div class="mt-1 font-medium">{{ campania.meta_participantes }}</div>
          </div>
          <div v-if="campania.objetivo_principal">
            <span class="text-sm text-gray-600">Objetivo principal</span>
            <p class="mt-1 text-sm text-gray-700">{{ campania.objetivo_principal }}</p>
          </div>
        </div>
      </div>

      <!-- Responsable -->
      <div v-if="campania.responsable" class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-900 mb-3">Responsable</h4>
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium">
            {{ obtenerIniciales(campania.responsable) }}
          </div>
          <div class="ml-3">
            <p class="font-medium text-gray-900">{{ campania.responsable }}</p>
            <p v-if="campania.agrupacion_nombre" class="text-sm text-gray-600">{{ campania.agrupacion_nombre }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Progreso de recaudación -->
    <div v-if="campania.meta_recaudacion && campania.recaudacion_actual" class="bg-green-50 p-4 rounded-lg border border-green-200">
      <h4 class="font-medium text-gray-900 mb-3">Progreso de Recaudación</h4>
      <div class="space-y-2">
        <div class="flex justify-between text-sm text-gray-700">
          <span>Recaudado: {{ formatearMoneda(campania.recaudacion_actual) }}</span>
          <span>Meta: {{ formatearMoneda(campania.meta_recaudacion) }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
          <div
            class="bg-green-600 h-3 rounded-full transition-all duration-300"
            :style="{ width: Math.min((campania.recaudacion_actual / campania.meta_recaudacion) * 100, 100) + '%' }"
          ></div>
        </div>
        <div class="text-center text-sm font-medium text-green-700">
          {{ Math.round((campania.recaudacion_actual / campania.meta_recaudacion) * 100) }}% completado
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
  }
})

const obtenerNombreTipo = (tipoId) => {
  const tipos = {
    1: 'Eventos',
    2: 'Formación',
    3: 'Denuncia',
    4: 'Recaudación'
  }
  return tipos[tipoId] || 'Otro'
}

const obtenerClaseEstado = (estado) => {
  if (estado === 'ACTIVA') return 'bg-green-100 text-green-800'
  if (estado === 'PLANIFICADA') return 'bg-blue-100 text-blue-800'
  if (estado === 'FINALIZADA') return 'bg-gray-100 text-gray-800'
  if (estado === 'CANCELADA') return 'bg-red-100 text-red-800'
  return 'bg-gray-100 text-gray-800'
}

const obtenerNombreEstado = (estado) => {
  const estados = {
    'ACTIVA': 'Activa',
    'PLANIFICADA': 'Planificada',
    'FINALIZADA': 'Finalizada',
    'CANCELADA': 'Cancelada'
  }
  return estados[estado] || estado
}

const formatearFecha = (fecha) => {
  if (!fecha) return ''
  return new Date(fecha).toLocaleDateString('es-ES', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const formatearMoneda = (cantidad) => {
  if (!cantidad) return '€0.00'
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: 'EUR'
  }).format(cantidad)
}

const obtenerIniciales = (nombre) => {
  if (!nombre) return '??'
  return nombre.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
}
</script>