<template>
  <AppLayout title="Miembros" subtitle="Gestión de socios y voluntarios">
    <!-- Filtros y búsqueda -->
    <div class="mb-6 bg-gray-50 p-4 rounded-lg shadow border border-gray-200">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar miembros..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              @input="onSearch"
            />
            <div class="absolute left-3 top-2.5">
              <span>🔍</span>
            </div>
          </div>
        </div>
        <div class="flex gap-2">
          <button @click="showFilters = !showFilters" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Filtros
          </button>
          <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            + Nuevo Miembro
          </button>
        </div>
      </div>
      
      <!-- Filtros avanzados -->
      <div v-if="showFilters" class="mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select v-model="filters.activo" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todos</option>
              <option value="true">Activos</option>
              <option value="false">Inactivos</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Miembro</label>
            <select v-model="filters.tipoMiembro" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todos</option>
              <option value="1">Socios</option>
              <option value="2">Simpatizantes</option>
              <option value="3">Voluntarios</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Persona</label>
            <select v-model="filters.tipoPersona" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todas</option>
              <option value="FISICA">Persona Física</option>
              <option value="JURIDICA">Persona Jurídica (Asociación)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Membresía</label>
            <select v-model="filters.tipoMembresia" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todas</option>
              <option value="DIRECTA">Directa (Nacional)</option>
              <option value="INDIRECTA">Indirecta (Territorial)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Agrupación Territorial</label>
            <select v-model="filters.agrupacion" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todas</option>
              <option v-for="agrup in agrupaciones" :key="agrup.id" :value="agrup.id">
                {{ agrup.nombre }}{{ agrup.tipo === 'NACIONAL' ? ' (Nacional)' : '' }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600">Cargando miembros...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <span class="text-red-400">⚠️</span>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Error al cargar miembros</h3>
          <p class="text-sm text-red-700 mt-1">{{ error.message }}</p>
        </div>
      </div>
    </div>

    <!-- Tabla de miembros -->
    <div v-else class="bg-gray-50 rounded-lg shadow overflow-hidden border border-gray-200">
      <div v-if="miembros.length === 0" class="text-center py-12">
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
          <span class="text-2xl">👤</span>
        </div>
        <h3 class="text-sm font-medium text-gray-900">No hay miembros</h3>
        <p class="text-sm text-gray-500 mt-1">Aún no hay miembros registrados en el sistema.</p>
      </div>

      <div v-else>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nombre
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Contacto
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tipo
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Agrupación
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha Alta
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
                  <div class="h-8 w-8 rounded-full flex items-center justify-center mr-3"
                       :class="miembro.tipo_persona === 'JURIDICA' ? 'bg-purple-100' : 'bg-gray-200'">
                    <span v-if="miembro.tipo_persona === 'JURIDICA'" class="text-sm">🏛️</span>
                    <span v-else class="text-sm font-medium text-gray-700">
                      {{ getInitials(miembro.nombre, miembro.apellido1) }}
                    </span>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      <span v-if="miembro.tipo_persona === 'JURIDICA'">{{ miembro.nombre }}</span>
                      <span v-else>{{ miembro.nombre }} {{ miembro.apellido1 || '' }} {{ miembro.apellido2 || '' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span v-if="miembro.tipo_persona === 'JURIDICA'" class="text-xs text-purple-600">
                        Persona Jurídica
                      </span>
                      <span v-if="miembro.tipo_membresia === 'DIRECTA'" class="text-xs text-blue-600">
                        Membresía Directa
                      </span>
                      <span v-if="!miembro.activo" class="text-xs text-red-600">
                        Inactivo
                      </span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900">{{ miembro.email }}</div>
                <div v-if="miembro.telefono" class="text-sm text-gray-500">{{ miembro.telefono }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span v-if="miembro.tipo_miembro" 
                      :class="getTipoMiembroClass(miembro.tipo_miembro.nombre)">
                  {{ miembro.tipo_miembro.nombre }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ miembro.agrupacion?.nombre || 'Sin agrupación' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(miembro.fecha_alta) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="editarMiembro(miembro)" class="text-purple-600 hover:text-purple-900 mr-3">
                  Editar
                </button>
                <button @click="eliminarMiembro(miembro)" class="text-red-600 hover:text-red-900">
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- Paginación -->
        <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Mostrando {{ from }} a {{ to }} de {{ total }} miembros
            </div>
            <div class="flex space-x-2">
              <button
                @click="previousPage"
                :disabled="currentPage === 1"
                class="px-3 py-1 rounded-md border border-gray-300 text-sm disabled:opacity-50"
              >
                Anterior
              </button>
              <span class="px-3 py-1 text-sm">Página {{ currentPage }}</span>
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="px-3 py-1 rounded-md border border-gray-300 text-sm disabled:opacity-50"
              >
                Siguiente
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import AppLayout from '@/components/common/AppLayout.vue'
import { useGraphQL } from '@/composables/useGraphQL.js'
import { GET_MIEMBROS, GET_AGRUPACIONES } from '@/graphql/queries/miembros.js'
const { loading, error, query } = useGraphQL()

const miembros = ref([])
const agrupaciones = ref([])
const searchQuery = ref('')
const showFilters = ref(false)
const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)

const filters = ref({
  activo: '',
  tipoMiembro: '',
  tipoPersona: '',
  tipoMembresia: '',
  agrupacion: ''
})

// Computed properties para paginación
const totalPages = computed(() => Math.ceil(total.value / pageSize.value))
const from = computed(() => (currentPage.value - 1) * pageSize.value + 1)
const to = computed(() => Math.min(currentPage.value * pageSize.value, total.value))

const loadMiembros = async () => {
  try {
    const variables = {
      filters: {
        search: searchQuery.value || null,
        activo: filters.value.activo ? filters.value.activo === 'true' : null,
        tipo_miembro_id: filters.value.tipoMiembro || null,
        agrupacion_codigo: filters.value.agrupacion || null,
        tipo_persona: filters.value.tipoPersona || null,
        tipo_membresia: filters.value.tipoMembresia || null,
        page: currentPage.value,
        pageSize: pageSize.value
      }
    }

    const data = await query(GET_MIEMBROS, variables)

    if (data?.miembros) {
      miembros.value = data.miembros
      total.value = data.miembros.length
    }
  } catch (err) {
    console.error('Error al cargar miembros:', err)
  }
}

const loadAgrupaciones = async () => {
  try {
    const data = await query(GET_AGRUPACIONES)
    if (data?.agrupaciones) {
      agrupaciones.value = data.agrupaciones
    }
  } catch (err) {
    console.error('Error al cargar agrupaciones:', err)
  }
}

const onSearch = () => {
  currentPage.value = 1
  loadMiembros()
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    loadMiembros()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    loadMiembros()
  }
}

const editarMiembro = (miembro) => {
  console.log('Editar miembro:', miembro)
  // Navegar a edición
}

const eliminarMiembro = (miembro) => {
  if (confirm(`¿Estás seguro de eliminar a ${miembro.nombre} ${miembro.apellido1}?`)) {
    console.log('Eliminar miembro:', miembro)
    // Ejecutar mutation
  }
}

// Funciones de utilidad
const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-ES')
}

const getInitials = (nombre, apellido1) => {
  const first = nombre?.[0] || ''
  const second = apellido1?.[0] || ''
  return `${first}${second}`.toUpperCase()
}

const getTipoMiembroClass = (tipo) => {
  const classes = {
    'SOCIO': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800',
    'SIMPATIZANTE': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800',
    'VOLUNTARIO': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800'
  }
  return classes[tipo] || 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
}

// Watch para cambios en filtros
watch(filters, () => {
  currentPage.value = 1
  loadMiembros()
}, { deep: true })

onMounted(() => {
  loadMiembros()
  loadAgrupaciones()
})
</script>