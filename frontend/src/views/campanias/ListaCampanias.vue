<template>
  <AppLayout title="Campañas" subtitle="Gestión de campañas y actividades">
    <!-- Filtros y búsqueda -->
    <div class="mb-6 bg-gray-50 p-4 rounded-lg shadow border border-gray-200">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar campañas..."
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
          <router-link 
            to="/campanias/nueva"
            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2"
          >
            <span>+</span>
            <span>Nueva Campaña</span>
          </router-link>
        </div>
      </div>

      <!-- Filtros avanzados -->
      <div v-if="showFilters" class="mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select v-model="filters.estado" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todos</option>
              <option value="PLANIFICADA">Planificada</option>
              <option value="ACTIVA">Activa</option>
              <option value="FINALIZADA">Finalizada</option>
              <option value="CANCELADA">Cancelada</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select v-model="filters.tipo" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todos</option>
              <option v-for="tipo in tiposCampania" :key="tipo.id" :value="tipo.id">
                {{ tipo.nombre }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
            <select v-model="filters.anio" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todos</option>
              <option value="2025">2025</option>
              <option value="2024">2024</option>
              <option value="2023">2023</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
      <p class="mt-2 text-gray-600">Cargando campañas...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <span class="text-red-400">⚠️</span>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Error al cargar campañas</h3>
          <p class="text-sm text-red-700 mt-1">{{ error.message }}</p>
        </div>
      </div>
    </div>

    <!-- Grid de campañas -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-if="campanias.length === 0" class="col-span-full text-center py-12 bg-gray-50 rounded-lg shadow border border-gray-200">
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
          <span class="text-4xl">🚩</span>
        </div>
        <h3 class="text-sm font-medium text-gray-900">No hay campañas</h3>
        <p class="text-sm text-gray-500 mt-1">Aún no hay campañas registradas en el sistema.</p>
        <div class="mt-4">
          <router-link 
            to="/campanias/nueva"
            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700"
          >
            <span>+</span>
            <span class="ml-2">Crear primera campaña</span>
          </router-link>
        </div>
      </div>

      <div
        v-for="campania in campanias"
        :key="campania.id"
        class="bg-purple-50 rounded-lg shadow hover:shadow-md transition-shadow border border-purple-100 hover:border-purple-200 group cursor-pointer"
        @click="verDetalles(campania)"
      >
        <div class="p-6">
          <div class="flex justify-between items-start mb-3">
            <span :class="getEstadoClass(campania.estado?.nombre)">
              {{ campania.estado?.nombre || 'Sin estado' }}
            </span>
            <span v-if="campania.tipoCampania" class="text-xs text-purple-600 bg-purple-100 px-2 py-0.5 rounded">
              {{ campania.tipoCampania.nombre }}
            </span>
          </div>

          <h3 class="text-lg font-semibold text-gray-900 mb-1 group-hover:text-purple-700 transition-colors">
            {{ campania.lema || campania.nombre }}
          </h3>
          <p v-if="campania.lema" class="text-sm text-gray-500 mb-2">{{ campania.nombre }}</p>
          <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ campania.descripcionCorta }}</p>

          <div class="space-y-2 text-sm text-gray-500">
            <div v-if="campania.fechaInicioPlan || campania.fechaFinPlan" class="flex items-center">
              <span class="mr-2">📅</span>
              <span>{{ formatDate(campania.fechaInicioPlan) }} - {{ formatDate(campania.fechaFinPlan) }}</span>
            </div>
            <div v-if="campania.metaRecaudacion" class="flex items-center">
              <span class="mr-2">🎯</span>
              <span>Meta: {{ formatCurrency(campania.metaRecaudacion) }}</span>
            </div>
            <div v-if="campania.metaFirmas" class="flex items-center">
              <span class="mr-2">✍️</span>
              <span>Objetivo: {{ campania.metaFirmas.toLocaleString() }} firmas</span>
            </div>
          </div>

          <!-- URL externa -->
          <div v-if="campania.urlExterna" class="mt-3">
            <a
              :href="campania.urlExterna"
              target="_blank"
              rel="noopener noreferrer"
              class="text-xs text-purple-600 hover:text-purple-800 flex items-center gap-1"
              @click.stop
            >
              <span>🔗</span>
              <span>Ver en laicismo.org</span>
            </a>
          </div>
        </div>

        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
          <div class="text-xs text-gray-500 flex items-center gap-1">
            <span v-if="campania.metaParticipantes">
              🎯 {{ campania.metaParticipantes }} participantes objetivo
            </span>
          </div>
          <div class="flex space-x-2">
            <router-link
              :to="`/campanias/${campania.id}`"
              class="text-purple-600 hover:text-purple-800 text-sm font-medium px-3 py-1 rounded hover:bg-purple-50 transition-colors"
              @click.stop
            >
              Ver detalles
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/components/common/AppLayout.vue'
import { executeQuery } from '@/graphql/client'
import { GET_CAMPANIAS, GET_TIPOS_CAMPANIA } from '@/graphql/queries/campanias'

const router = useRouter()

const campanias = ref([])
const tiposCampania = ref([])
const searchQuery = ref('')
const showFilters = ref(false)
const loading = ref(false)
const error = ref(null)

const filters = ref({
  estado: '',
  tipo: '',
  anio: ''
})

onMounted(() => {
  loadCampanias()
  loadTiposCampania()
})

const loadCampanias = async () => {
  loading.value = true
  error.value = null

  try {
    const data = await executeQuery(GET_CAMPANIAS)
    campanias.value = data.campanias || []
  } catch (err) {
    error.value = err
    console.error('Error cargando campañas:', err)
  } finally {
    loading.value = false
  }
}

const loadTiposCampania = async () => {
  try {
    const data = await executeQuery(GET_TIPOS_CAMPANIA)
    tiposCampania.value = data.tipos_campania || []
  } catch (err) {
    console.error('Error cargando tipos de campaña:', err)
  }
}

const onSearch = () => {
  // Implementar búsqueda
  console.log('Buscando:', searchQuery.value)
}

const getEstadoClass = (estadoNombre) => {
  // Mapeo por nombre de estado para estilos
  const classes = {
    'Activa': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800',
    'Planificada': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800',
    'Finalizada': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800',
    'Cancelada': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800',
    'Suspendida': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800'
  }
  return classes[estadoNombre] || 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })
}

const formatCurrency = (amount) => {
  if (!amount) return '€0.00'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount)
}

const verDetalles = (campania) => {
  router.push(`/campanias/${campania.id}`)
}

watch(filters, () => {
  loadCampanias()
}, { deep: true })
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.group {
  transition: all 0.2s ease;
}

.group:hover {
  transform: translateY(-2px);
}
</style>
