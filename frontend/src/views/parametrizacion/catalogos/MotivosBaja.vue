<template>
  <CatalogoGenerico
    titulo="Motivos de Baja"
    subtitulo="Razones para dar de baja a un miembro"
    nombre-singular="Motivo de Baja"
    icono="📝"
    query-name="motivosBaja"
    :query-string="GET_MOTIVOS_BAJA"
    :mutation-create="CREATE_MOTIVO_BAJA"
    :columnas="columnas"
    :campos="campos"
  />
</template>

<script setup>
import CatalogoGenerico from '@/components/parametrizacion/CatalogoGenerico.vue'
import { GET_MOTIVOS_BAJA, CREATE_MOTIVO_BAJA } from '@/graphql/queries/catalogos.js'

const columnas = [
  { key: 'nombre', label: 'Nombre' },
  { key: 'descripcion', label: 'Descripción' },
  { key: 'requiereDocumentacion', label: 'Req. Documentación', type: 'boolean' },
  { key: 'activo', label: 'Estado' },
]

const campos = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true, maxLength: 100 },
  { name: 'descripcion', label: 'Descripción', type: 'textarea' },
  { name: 'requiereDocumentacion', label: 'Requiere Documentación', type: 'checkbox', default: false, description: 'Si el motivo requiere adjuntar documentación justificativa' },
  { name: 'activo', label: 'Activo', type: 'checkbox', default: true },
]
</script>
