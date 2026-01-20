<template>
  <CatalogoGenerico
    titulo="Estados de Miembro"
    subtitulo="Estados del ciclo de vida de los miembros"
    nombre-singular="Estado de Miembro"
    icono="📊"
    query-name="estadosMiembro"
    :query-string="GET_ESTADOS_MIEMBRO"
    :mutation-create="CREATE_ESTADO_MIEMBRO"
    :columnas="columnas"
    :campos="campos"
  />
</template>

<script setup>
import CatalogoGenerico from '@/components/parametrizacion/CatalogoGenerico.vue'
import { GET_ESTADOS_MIEMBRO, CREATE_ESTADO_MIEMBRO } from '@/graphql/queries/catalogos.js'

const columnas = [
  { key: 'nombre', label: 'Nombre' },
  { key: 'color', label: 'Color', type: 'color' },
  { key: 'orden', label: 'Orden' },
  { key: 'activo', label: 'Estado' },
]

const campos = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true, maxLength: 100 },
  { name: 'descripcion', label: 'Descripción', type: 'textarea' },
  { name: 'color', label: 'Color', type: 'color', default: '#6C757D' },
  { name: 'orden', label: 'Orden', type: 'number', default: 0 },
  { name: 'activo', label: 'Activo', type: 'checkbox', default: true },
]
</script>
