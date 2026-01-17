<template>
  <AppLayout title="Miembros" subtitle="Gestión de socios y voluntarios">
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              type="text"
              placeholder="Buscar miembros..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <div class="absolute left-3 top-2.5">
              <span>🔍</span>
            </div>
          </div>
        </div>
        <div class="flex gap-2">
          <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Filtros
          </button>
          <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Nuevo Miembro
          </button>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nombre
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tipo
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Agrupación
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="miembro in miembros" :key="miembro.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                  <span class="text-sm font-medium text-gray-700">{{ miembro.iniciales }}</span>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ miembro.nombre }}</div>
                  <div class="text-sm text-gray-500">{{ miembro.telefono }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ miembro.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="miembro.tipoClass">{{ miembro.tipo }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ miembro.agrupacion }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button class="text-blue-600 hover:text-blue-900 mr-3">Editar</button>
              <button class="text-red-600 hover:text-red-900">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '/src/components/common/AppLayout.vue'

const miembros = ref([
  {
    id: 1,
    nombre: 'Juan Pérez Gómez',
    iniciales: 'JP',
    email: 'juan.perez@email.com',
    telefono: '612 345 678',
    tipo: 'SOCIO',
    tipoClass: 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800',
    agrupacion: 'Madrid'
  },
  {
    id: 2,
    nombre: 'María López Sánchez',
    iniciales: 'ML',
    email: 'maria.lopez@email.com',
    telefono: '623 456 789',
    tipo: 'VOLUNTARIO',
    tipoClass: 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800',
    agrupacion: 'Barcelona'
  },
  {
    id: 3,
    nombre: 'Carlos Ruiz Martínez',
    iniciales: 'CR',
    email: 'carlos.ruiz@email.com',
    telefono: '634 567 890',
    tipo: 'SIMPATIZANTE',
    tipoClass: 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800',
    agrupacion: 'Valencia'
  }
])
</script>