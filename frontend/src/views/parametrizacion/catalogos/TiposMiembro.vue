<template>
  <CatalogoGenerico
    titulo="Tipos de Miembro"
    subtitulo="Configuración de tipos de membresía"
    nombre-singular="Tipo de Miembro"
    icono="👤"
    query-name="tiposMiembro"
    :query-string="GET_TIPOS_MIEMBRO"
    :mutation-create="CREATE_TIPO_MIEMBRO"
    :columnas="columnas"
    :campos="campos"
  />
</template>

<script setup>
import CatalogoGenerico from '@/components/parametrizacion/CatalogoGenerico.vue'
import { GET_TIPOS_MIEMBRO, CREATE_TIPO_MIEMBRO } from '@/graphql/queries/catalogos.js'

const columnas = [
  { key: 'nombre', label: 'Nombre' },
  { key: 'descripcion', label: 'Descripción' },
  { key: 'requiereCuota', label: 'Requiere Cuota', type: 'boolean' },
  { key: 'puedeVotar', label: 'Puede Votar', type: 'boolean' },
  { key: 'activo', label: 'Estado' },
]

const campos = [
  { name: 'nombre', label: 'Nombre', type: 'text', required: true, maxLength: 100 },
  { name: 'descripcion', label: 'Descripción', type: 'textarea' },
  { name: 'requiereCuota', label: 'Requiere Cuota', type: 'checkbox', default: true },
  { name: 'puedeVotar', label: 'Puede Votar', type: 'checkbox', default: false },
  { name: 'activo', label: 'Activo', type: 'checkbox', default: true },
]
</script>
