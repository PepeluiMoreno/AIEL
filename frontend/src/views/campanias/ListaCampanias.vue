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
            <span :class="getEstadoClass(campania.estado)">
              {{ campania.estado }}
            </span>
            <span class="text-xs text-gray-500">{{ campania.codigo }}</span>
          </div>

          <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-purple-700 transition-colors">
            {{ campania.nombre }}
          </h3>
          <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ campania.descripcion }}</p>

          <div class="space-y-2 text-sm text-gray-500">
            <div class="flex items-center">
              <span class="mr-2">📅</span>
              <span>{{ formatDate(campania.fechaInicio) }} - {{ formatDate(campania.fechaFin) }}</span>
            </div>
            <div v-if="campania.metaRecaudacion" class="flex items-center">
              <span class="mr-2">🎯</span>
              <span>Meta: {{ formatCurrency(campania.metaRecaudacion) }}</span>
            </div>
            <div v-if="campania.responsable" class="flex items-center">
              <span class="mr-2">👤</span>
              <span>{{ campania.responsable }}</span>
            </div>
          </div>

          <!-- Barra de progreso si hay meta -->
          <div v-if="campania.metaRecaudacion && campania.recaudado" class="mt-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
              <span>Recaudado</span>
              <span>{{ Math.round((campania.recaudado / campania.metaRecaudacion) * 100) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-purple-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: Math.min((campania.recaudado / campania.metaRecaudacion) * 100, 100) + '%' }"
              ></div>
            </div>
          </div>
        </div>

        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
          <div class="text-xs text-gray-500 flex items-center gap-1">
            <span>👥</span>
            <span>{{ campania.participantes || 0 }} participantes</span>
          </div>
          <div class="flex space-x-2">
            <router-link 
              :to="`/campanias/${campania.id}`"
              class="text-purple-600 hover:text-purple-800 text-sm font-medium px-3 py-1 rounded hover:bg-purple-50 transition-colors"
              @click.stop
            >
              Ver detalles
            </router-link>
            <button 
              @click.stop="editarCampania(campania)"
              class="text-gray-600 hover:text-gray-800 text-sm font-medium px-3 py-1 rounded hover:bg-gray-100 transition-colors"
            >
              Editar
            </button>
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

// Datos de ejemplo - reemplazar con datos reales de GraphQL
onMounted(() => {
  loadCampanias()
  loadTiposCampania()
})

const loadCampanias = async () => {
  loading.value = true
  error.value = null
  
  try {
    // Simulación de carga de datos
    await new Promise(resolve => setTimeout(resolve, 500))
    
    campanias.value = [
      {
        id: 1,
        codigo: 'CAMP-2025-001',
        nombre: 'Campaña Día del Laicismo 2025',
        descripcion: 'Actividades y eventos para celebrar el Día Internacional del Laicismo con charlas, conferencias y actos públicos.',
        estado: 'ACTIVA',
        fechaInicio: '2025-01-15',
        fechaFin: '2025-02-15',
        metaRecaudacion: 5000,
        recaudado: 2350,
        responsable: 'María García',
        participantes: 24,
        tipo_campania_id: 1,
        estado_campania_id: 2,
        descripcion_larga: 'Esta es una campaña completa para celebrar el Día Internacional del Laicismo. Incluye charlas, conferencias, actos públicos y actividades de concienciación.',
        fecha_inicio_plan: '2025-01-15',
        fecha_fin_plan: '2025-02-15',
        fecha_inicio_real: '2025-01-15',
        fecha_fin_real: null,
        objetivo_principal: 'Promover los valores laicos en la sociedad',
        meta_participantes: 50,
        responsable_id: 1,
        agrupacion_id: 1
      },
      {
        id: 2,
        codigo: 'CAMP-2025-002',
        nombre: 'Jornadas sobre Educación Laica',
        descripcion: 'Ciclo de conferencias sobre la importancia de la educación laica en el sistema educativo.',
        estado: 'PLANIFICADA',
        fechaInicio: '2025-03-01',
        fechaFin: '2025-03-30',
        metaRecaudacion: 3000,
        recaudado: 0,
        responsable: 'Juan Martínez',
        participantes: 8,
        tipo_campania_id: 2,
        estado_campania_id: 1,
        descripcion_larga: 'Ciclo completo de conferencias sobre educación laica con expertos nacionales e internacionales.',
        fecha_inicio_plan: '2025-03-01',
        fecha_fin_plan: '2025-03-30',
        fecha_inicio_real: null,
        fecha_fin_real: null,
        objetivo_principal: 'Concienciar sobre la importancia de la educación laica',
        meta_participantes: 100,
        responsable_id: 2,
        agrupacion_id: 1
      },
      {
        id: 3,
        codigo: 'CAMP-2024-015',
        nombre: 'Campaña Navidad Laica 2024',
        descripcion: 'Campaña de concienciación sobre alternativas laicas en las festividades.',
        estado: 'FINALIZADA',
        fechaInicio: '2024-12-01',
        fechaFin: '2024-12-31',
        metaRecaudacion: 2000,
        recaudado: 2150,
        responsable: 'Ana López',
        participantes: 45,
        tipo_campania_id: 3,
        estado_campania_id: 3,
        descripcion_larga: 'Campaña exitosa de concienciación sobre alternativas laicas durante las festividades navideñas.',
        fecha_inicio_plan: '2024-12-01',
        fecha_fin_plan: '2024-12-31',
        fecha_inicio_real: '2024-12-01',
        fecha_fin_real: '2024-12-31',
        objetivo_principal: 'Ofrecer alternativas laicas a las celebraciones navideñas',
        meta_participantes: 30,
        responsable_id: 3,
        agrupacion_id: 2
      },
      {
        id: 4,
        codigo: 'CAMP-2025-003',
        nombre: 'Defensa de la Laicidad Institucional',
        descripcion: 'Campaña de denuncia de vulneraciones del principio de laicidad en instituciones públicas.',
        estado: 'ACTIVA',
        fechaInicio: '2025-01-01',
        fechaFin: '2025-12-31',
        responsable: 'Carlos Ruiz',
        participantes: 32,
        tipo_campania_id: 4,
        estado_campania_id: 2,
        descripcion_larga: 'Campaña continua de monitoreo y denuncia de vulneraciones de la laicidad en instituciones públicas.',
        fecha_inicio_plan: '2025-01-01',
        fecha_fin_plan: '2025-12-31',
        fecha_inicio_real: '2025-01-01',
        fecha_fin_real: null,
        objetivo_principal: 'Garantizar el respeto a la laicidad en instituciones públicas',
        meta_participantes: 200,
        responsable_id: 4,
        agrupacion_id: 3
      }
    ]
  } catch (err) {
    error.value = err
    console.error('Error cargando campañas:', err)
  } finally {
    loading.value = false
  }
}

const loadTiposCampania = () => {
  tiposCampania.value = [
    { id: 1, nombre: 'Eventos', codigo: 'EVENTOS' },
    { id: 2, nombre: 'Formación', codigo: 'FORMACION' },
    { id: 3, nombre: 'Denuncia', codigo: 'DENUNCIA' },
    { id: 4, nombre: 'Recaudación', codigo: 'RECAUDACION' }
  ]
}

const onSearch = () => {
  // Implementar búsqueda
  console.log('Buscando:', searchQuery.value)
}

const getEstadoClass = (estado) => {
  const classes = {
    'ACTIVA': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800',
    'PLANIFICADA': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800',
    'FINALIZADA': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800',
    'CANCELADA': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800',
    'SUSPENDIDA': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800'
  }
  return classes[estado] || 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
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

const editarCampania = (campania) => {
  router.push(`/campanias/${campania.id}/editar`)
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
