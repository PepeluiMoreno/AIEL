<template>
  <div class="bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Detalle del Miembro</h2>
          <p class="text-sm text-gray-600">ID: {{ miembro.id || 'Nuevo' }}</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="$router.push('/miembros')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Volver
          </button>
          <button
            v-if="miembro.id"
            @click="editMode = !editMode"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
          >
            {{ editMode ? 'Cancelar' : 'Editar' }}
          </button>
          <button
            v-if="miembro.id && !editMode"
            @click="handleDelete"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
          >
            Eliminar
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div v-if="loading" class="p-8 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Cargando...</p>
    </div>

    <div v-else class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información Personal -->
        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Información Personal</h3>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
            <div v-if="!editMode" class="mt-1 p-2 bg-gray-50 rounded">{{ nombreCompleto }}</div>
            <input
              v-else
              v-model="miembro.nombre"
              type="text"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <div v-if="!editMode" class="mt-1 p-2 bg-gray-50 rounded">{{ miembro.email }}</div>
            <input
              v-else
              v-model="miembro.email"
              type="email"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
            <div v-if="!editMode" class="mt-1 p-2 bg-gray-50 rounded">{{ miembro.telefono }}</div>
            <input
              v-else
              v-model="miembro.telefono"
              type="tel"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>

        <!-- Información de Membresía -->
        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Membresía</h3>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de Miembro</label>
            <div v-if="!editMode" class="mt-1 p-2 bg-gray-50 rounded">{{ miembro.tipo_miembro_nombre }}</div>
            <select
              v-else
              v-model="miembro.tipo_miembro_id"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Seleccionar tipo</option>
              <option value="1">Socio</option>
              <option value="2">Simpatizante</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Agrupación</label>
            <div v-if="!editMode" class="mt-1 p-2 bg-gray-50 rounded">{{ miembro.agrupacion_nombre }}</div>
            <select
              v-else
              v-model="miembro.agrupacion_id"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Seleccionar agrupación</option>
              <option value="1">Europa Laica Nacional</option>
              <option value="2">Madrid</option>
              <option value="3">Barcelona</option>
            </select>
          </div>

          <div>
            <label class="flex items-center">
              <input
                v-model="miembro.es_voluntario"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                :disabled="!editMode"
              />
              <span class="ml-2 text-sm text-gray-700">Es voluntario</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Botones de acción -->
      <div v-if="editMode" class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
        <button
          @click="editMode = false"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        >
          Cancelar
        </button>
        <button
          @click="handleSave"
          class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
        >
          Guardar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useMiembro } from '@/composables/useMiembro'

const route = useRoute()
const editMode = ref(!route.params.id) // Modo edición si es nuevo
const { miembro, loading, nombreCompleto, fetchMiembro, saveMiembro, deleteMiembro } = useMiembro()

onMounted(() => {
  if (route.params.id) {
    fetchMiembro(route.params.id)
  }
})

const handleSave = async () => {
  await saveMiembro()
  if (route.params.id) {
    editMode.value = false
  }
}

const handleDelete = async () => {
  await deleteMiembro(miembro.id)
}
</script>