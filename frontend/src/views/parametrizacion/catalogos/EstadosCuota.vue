<template>
  <CatalogoGenerico
    titulo="Estados de Cuota"
    subtitulo="Estados del ciclo de cobro de cuotas"
    nombre-singular="Estado de Cuota"
    icono="💰"
    query-name="estadosCuota"
    :query-string="GET_ESTADOS_CUOTA"
    :mutation-create="CREATE_ESTADO_CUOTA"
    :columnas="columnas"
    :campos="campos"
  />
</template>

<script setup>
import CatalogoGenerico from '@/components/parametrizacion/CatalogoGenerico.vue'
import { GET_ESTADOS_CUOTA, CREATE_ESTADO_CUOTA } from '@/graphql/queries/catalogos.js'

const columnas = [
  { key: 'nombre', label: 'Nombre' },
  { key: 'color', label: 'Color', type: 'color' },
  { key: 'orden', label: 'Orden' },
  { key: 'esInicial', label: 'Inicial', type: 'boolean' },
  { key: 'esFinal', label: 'Final', type: 'boolean' },
  { key: 'activo', label: 'Estado' },
]

const campos = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true, maxLength: 100 },
  { name: 'descripcion', label: 'Descripción', type: 'textarea' },
  { name: 'color', label: 'Color', type: 'color', default: '#6C757D' },
  { name: 'orden', label: 'Orden', type: 'number', default: 0 },
  { name: 'esInicial', label: 'Es estado inicial', type: 'checkbox', default: false, description: 'Estado asignado automáticamente al crear una cuota' },
  { name: 'esFinal', label: 'Es estado final', type: 'checkbox', default: false, description: 'Estado que cierra el ciclo de la cuota' },
  { name: 'activo', label: 'Activo', type: 'checkbox', default: true },
]
</script>
