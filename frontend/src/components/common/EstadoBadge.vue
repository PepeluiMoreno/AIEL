<template>
  <span :class="estadoClass" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
    {{ estadoTexto }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  estado: {
    type: [String, Number],
    default: ''
  },
  tipo: {
    type: String,
    default: 'campania' // 'campania', 'tarea', 'actividad'
  },
  textoPersonalizado: {
    type: String,
    default: ''
  }
})

const estadoTexto = computed(() => {
  if (props.textoPersonalizado) return props.textoPersonalizado
  
  const mapEstados = {
    campania: {
      '1': 'Planificada',
      '2': 'Activa', 
      '3': 'Finalizada',
      '4': 'Cancelada',
      '5': 'Suspendida',
      'PLANIFICADA': 'Planificada',
      'ACTIVA': 'Activa',
      'FINALIZADA': 'Finalizada',
      'CANCELADA': 'Cancelada',
      'SUSPENDIDA': 'Suspendida'
    },
    tarea: {
      '1': 'Pendiente',
      '2': 'En Progreso',
      '3': 'Completada',
      '4': 'Cancelada'
    },
    actividad: {
      'true': 'Completada',
      'false': 'Pendiente'
    }
  }
  
  const mapa = mapEstados[props.tipo] || mapEstados.campania
  return mapa[props.estado] || props.estado || 'Desconocido'
})

const estadoClass = computed(() => {
  const estado = props.estado.toString()
  const clases = {
    campania: {
      '2': 'bg-green-100 text-green-800',
      'ACTIVA': 'bg-green-100 text-green-800',
      '1': 'bg-blue-100 text-blue-800',
      'PLANIFICADA': 'bg-blue-100 text-blue-800',
      '3': 'bg-gray-100 text-gray-800',
      'FINALIZADA': 'bg-gray-100 text-gray-800',
      '4': 'bg-red-100 text-red-800',
      'CANCELADA': 'bg-red-100 text-red-800',
      '5': 'bg-yellow-100 text-yellow-800',
      'SUSPENDIDA': 'bg-yellow-100 text-yellow-800'
    },
    tarea: {
      '3': 'bg-green-100 text-green-800',
      '2': 'bg-blue-100 text-blue-800',
      '1': 'bg-yellow-100 text-yellow-800',
      '4': 'bg-red-100 text-red-800'
    },
    actividad: {
      'true': 'bg-green-100 text-green-800',
      'false': 'bg-yellow-100 text-yellow-800'
    }
  }
  
  const mapaClases = clases[props.tipo] || clases.campania
  return mapaClases[estado] || 'bg-gray-100 text-gray-800'
})
</script>