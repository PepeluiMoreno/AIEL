<template>
  <AppLayout title="Campañas" subtitle="Gestión de campañas y actividades">
    <!-- Barra superior con búsqueda y botón nuevo -->
    <div class="mb-4 flex items-center justify-between gap-4">
      <div class="relative flex-1 max-w-md">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre o lema..."
          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          @keyup.enter="aplicarFiltros"
        />
        <span class="absolute left-3 top-2.5 text-gray-400">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </span>
      </div>
      <router-link
        to="/campanias/nueva"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva Campaña
      </router-link>
    </div>

    <!-- Panel de Filtros Compacto -->
    <div class="mb-4 bg-white border border-gray-200 rounded-lg p-3">
      <div class="flex flex-wrap items-start gap-4">

        <!-- Estado -->
        <div class="flex-shrink-0">
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-medium text-gray-700">Estado:</span>
            <label class="flex items-center gap-1 cursor-pointer text-xs text-purple-600 hover:text-purple-800">
              <input
                type="checkbox"
                :checked="todosEstadosSeleccionados"
                :indeterminate="algunEstadoSeleccionado && !todosEstadosSeleccionados"
                @change="toggleTodosEstados"
                class="w-3 h-3 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
              <span>Todos</span>
            </label>
          </div>
          <div class="flex flex-wrap gap-x-3 gap-y-0.5">
            <label
              v-for="estado in estadosCampaniaOrdenados"
              :key="estado.id"
              class="flex items-center gap-1 cursor-pointer hover:bg-gray-50 px-1 rounded"
            >
              <input
                type="checkbox"
                :value="estado.id"
                v-model="filters.estados"
                class="w-3 h-3 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
              <span class="text-xs text-gray-700">{{ estado.nombre }}</span>
            </label>
          </div>
        </div>

        <div class="w-px h-12 bg-gray-200 hidden md:block"></div>

        <!-- Tipo de Campaña (matriz 2 columnas) -->
        <div class="flex-shrink-0">
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-medium text-gray-700">Tipo:</span>
            <label class="flex items-center gap-1 cursor-pointer text-xs text-purple-600 hover:text-purple-800">
              <input
                type="checkbox"
                :checked="todosTiposSeleccionados"
                :indeterminate="algunTipoSeleccionado && !todosTiposSeleccionados"
                @change="toggleTodosTipos"
                class="w-3 h-3 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
              <span>Todos</span>
            </label>
          </div>
          <div class="grid grid-cols-2 gap-x-3 gap-y-0.5">
            <label
              v-for="tipo in tiposCampaniaOrdenados"
              :key="tipo.id"
              class="flex items-center gap-1 cursor-pointer hover:bg-gray-50 px-1 rounded"
            >
              <input
                type="checkbox"
                :value="tipo.id"
                v-model="filters.tipos"
                class="w-3 h-3 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
              <span class="text-xs text-gray-700">{{ tipo.nombre }}</span>
            </label>
          </div>
        </div>

        <div class="w-px h-12 bg-gray-200 hidden md:block"></div>

        <!-- Años (checkboxes en fila) -->
        <div class="flex-shrink-0">
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-medium text-gray-700">Año:</span>
            <label class="flex items-center gap-1 cursor-pointer text-xs text-purple-600 hover:text-purple-800">
              <input
                type="checkbox"
                :checked="todosAniosSeleccionados"
                :indeterminate="algunAnioSeleccionado && !todosAniosSeleccionados"
                @change="toggleTodosAnios"
                class="w-3 h-3 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
              <span>Todos</span>
            </label>
          </div>
          <div class="flex flex-wrap gap-x-2 gap-y-0.5">
            <label
              v-for="anio in aniosDisponibles"
              :key="anio"
              class="flex items-center gap-1 cursor-pointer hover:bg-gray-50 px-1 rounded"
            >
              <input
                type="checkbox"
                :value="anio"
                v-model="filters.anios"
                class="w-3 h-3 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
              <span class="text-xs text-gray-700">{{ anio }}</span>
            </label>
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center gap-2 ml-auto">
          <button
            @click="aplicarFiltros"
            :disabled="loading"
            class="px-4 py-1.5 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 disabled:opacity-50"
          >
            {{ loading ? 'Buscando...' : 'Buscar' }}
          </button>
          <button
            @click="limpiarFiltros"
            class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800"
          >
            Limpiar
          </button>
        </div>
      </div>
    </div>

    <!-- Estado de carga -->
    <div v-if="loading" class="bg-white border border-gray-200 rounded-lg p-12 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-purple-600 border-t-transparent mb-3"></div>
      <p class="text-gray-600">Cargando campañas...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6">
      <p class="text-red-700 font-medium">Error al cargar datos</p>
      <p class="text-red-600 text-sm mt-1">{{ error.message || error }}</p>
      <button @click="aplicarFiltros" class="mt-3 text-red-600 hover:text-red-800 text-sm font-medium">
        Reintentar
      </button>
    </div>

    <!-- Resultados -->
    <div v-else class="bg-white border border-gray-200 rounded-lg overflow-hidden">
      <!-- Mensaje inicial -->
      <div v-if="!filtersApplied" class="p-12 text-center text-gray-500">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <p class="text-lg">Configura los filtros y pulsa "Buscar"</p>
      </div>

      <!-- Sin resultados -->
      <div v-else-if="campaniasFiltradas.length === 0" class="p-12 text-center text-gray-500">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-lg">No se encontraron campañas</p>
        <p class="text-sm mt-1">Prueba con otros filtros</p>
      </div>

      <!-- Grid de campañas -->
      <template v-else>
        <!-- Barra de resultados -->
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
          <span class="text-sm text-gray-600">
            <strong>{{ campaniasFiltradas.length }}</strong> {{ tituloDescriptivo }}
          </span>
          <button @click="limpiarFiltros" class="text-sm text-purple-600 hover:text-purple-800">
            Limpiar filtros
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
          <div
            v-for="campania in campaniasFiltradas"
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
                {{ campania.nombre }}
              </h3>
              <p v-if="campania.lema" class="text-sm text-gray-500 mb-2">Lema: "{{ campania.lema }}"</p>
              <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ campania.descripcionCorta }}</p>

              <div class="space-y-2 text-sm text-gray-500">
                <div v-if="campania.responsable" class="flex items-center">
                  <span class="mr-2">👤</span>
                  <span>Coordinador: {{ campania.responsable.nombre }} {{ campania.responsable.apellido1 }}</span>
                </div>
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
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/components/common/AppLayout.vue'
import { executeQuery } from '@/graphql/client'
import { GET_CAMPANIAS, GET_TIPOS_CAMPANIA, GET_ESTADOS_CAMPANIA } from '@/graphql/queries/campanias'

const router = useRouter()

// Datos
const allCampanias = ref([])
const tiposCampania = ref([])
const estadosCampania = ref([])
const searchQuery = ref('')
const loading = ref(false)
const error = ref(null)
const filtersApplied = ref(false)

// Filtros
const filters = ref({
  estados: [],
  tipos: [],
  anios: []
})

// Computed: años disponibles (año actual y 5 años hacia atrás)
const aniosDisponibles = computed(() => {
  const currentYear = new Date().getFullYear()
  const years = []
  for (let y = currentYear; y >= currentYear - 5; y--) {
    years.push(y)
  }
  return years
})

// Computed: ordenar estados por orden
const estadosCampaniaOrdenados = computed(() =>
  [...estadosCampania.value].sort((a, b) => (a.orden ?? 999) - (b.orden ?? 999))
)

// Computed: ordenar tipos alfabéticamente
const tiposCampaniaOrdenados = computed(() =>
  [...tiposCampania.value].sort((a, b) => a.nombre.localeCompare(b.nombre, 'es'))
)

// Computed: checkbox "Todos" para estados
const todosEstadosSeleccionados = computed(() =>
  estadosCampania.value.length > 0 && filters.value.estados.length === estadosCampania.value.length
)
const algunEstadoSeleccionado = computed(() => filters.value.estados.length > 0)

// Computed: checkbox "Todos" para tipos
const todosTiposSeleccionados = computed(() =>
  tiposCampania.value.length > 0 && filters.value.tipos.length === tiposCampania.value.length
)
const algunTipoSeleccionado = computed(() => filters.value.tipos.length > 0)

// Computed: checkbox "Todos" para años
const todosAniosSeleccionados = computed(() =>
  aniosDisponibles.value.length > 0 && filters.value.anios.length === aniosDisponibles.value.length
)
const algunAnioSeleccionado = computed(() => filters.value.anios.length > 0)

// Computed: campañas filtradas
const campaniasFiltradas = computed(() => {
  if (!filtersApplied.value) return []

  let filtered = [...allCampanias.value]

  // Búsqueda por texto
  if (searchQuery.value.trim()) {
    const search = searchQuery.value.toLowerCase().trim()
    filtered = filtered.filter(c =>
      c.nombre?.toLowerCase().includes(search) ||
      c.lema?.toLowerCase().includes(search) ||
      c.descripcionCorta?.toLowerCase().includes(search)
    )
  }

  // Filtro por estados (OR)
  if (filters.value.estados.length > 0) {
    filtered = filtered.filter(c =>
      c.estado && filters.value.estados.includes(c.estado.id)
    )
  }

  // Filtro por tipos (OR)
  if (filters.value.tipos.length > 0) {
    filtered = filtered.filter(c =>
      c.tipoCampania && filters.value.tipos.includes(c.tipoCampania.id)
    )
  }

  // Filtro por años (OR entre años seleccionados)
  if (filters.value.anios.length > 0) {
    filtered = filtered.filter(c => {
      const fechaInicio = c.fechaInicioPlan ? new Date(c.fechaInicioPlan).getFullYear() : null
      const fechaFin = c.fechaFinPlan ? new Date(c.fechaFinPlan).getFullYear() : null
      // La campaña coincide si su rango de fechas incluye alguno de los años seleccionados
      return filters.value.anios.some(anio => {
        if (fechaInicio && fechaFin) {
          return fechaInicio <= anio && fechaFin >= anio
        }
        if (fechaInicio) return fechaInicio === anio
        if (fechaFin) return fechaFin === anio
        return false
      })
    })
  }

  return filtered
})

// Computed: título descriptivo
const tituloDescriptivo = computed(() => {
  const total = campaniasFiltradas.value.length
  const base = total === 1 ? 'campaña' : 'campañas'

  // Tipos
  let tiposStr = ''
  if (filters.value.tipos.length > 0 && filters.value.tipos.length < tiposCampania.value.length) {
    const nombres = filters.value.tipos.map(id => {
      const tipo = tiposCampania.value.find(t => t.id === id)
      return tipo ? tipo.nombre.toLowerCase() : ''
    }).filter(Boolean)
    if (nombres.length > 0) {
      tiposStr = ` de ${nombres.join(', ')}`
    }
  }

  // Estados
  let estadosStr = ''
  if (filters.value.estados.length > 0 && filters.value.estados.length < estadosCampania.value.length) {
    const nombres = filters.value.estados.map(id => {
      const estado = estadosCampania.value.find(e => e.id === id)
      return estado ? estado.nombre.toLowerCase() : ''
    }).filter(Boolean)
    if (nombres.length > 0) {
      estadosStr = ` ${nombres.join(', ')}`
    }
  }

  // Años
  let anioStr = ''
  if (filters.value.anios.length > 0 && filters.value.anios.length < aniosDisponibles.value.length) {
    if (filters.value.anios.length <= 2) {
      anioStr = ` en ${filters.value.anios.join(', ')}`
    } else {
      anioStr = ` (${filters.value.anios.length} años)`
    }
  }

  return `${base}${tiposStr}${estadosStr}${anioStr}`
})

// Toggle todos estados
const toggleTodosEstados = () => {
  if (todosEstadosSeleccionados.value) {
    filters.value.estados = []
  } else {
    filters.value.estados = estadosCampania.value.map(e => e.id)
  }
}

// Toggle todos tipos
const toggleTodosTipos = () => {
  if (todosTiposSeleccionados.value) {
    filters.value.tipos = []
  } else {
    filters.value.tipos = tiposCampania.value.map(t => t.id)
  }
}

// Toggle todos años
const toggleTodosAnios = () => {
  if (todosAniosSeleccionados.value) {
    filters.value.anios = []
  } else {
    filters.value.anios = [...aniosDisponibles.value]
  }
}

// Cargar catálogos al montar
const loadCatalogos = async () => {
  try {
    console.log('Cargando tipos de campaña...')
    const tiposData = await executeQuery(GET_TIPOS_CAMPANIA)
    console.log('Tipos recibidos:', tiposData)
    tiposCampania.value = tiposData?.tiposCampania || []

    console.log('Cargando estados de campaña...')
    const estadosData = await executeQuery(GET_ESTADOS_CAMPANIA)
    console.log('Estados recibidos:', estadosData)
    estadosCampania.value = estadosData?.estadosCampania || []
  } catch (err) {
    console.error('Error al cargar catálogos:', err)
  }
}

// Aplicar filtros y buscar
const aplicarFiltros = async () => {
  filtersApplied.value = true
  loading.value = true
  error.value = null

  try {
    const data = await executeQuery(GET_CAMPANIAS)
    allCampanias.value = data.campanias || []
  } catch (err) {
    error.value = err
    console.error('Error cargando campañas:', err)
  } finally {
    loading.value = false
  }
}

// Limpiar filtros
const limpiarFiltros = () => {
  filters.value = {
    estados: [],
    tipos: [],
    anios: []
  }
  searchQuery.value = ''
  filtersApplied.value = false
  allCampanias.value = []
}

const getEstadoClass = (estadoNombre) => {
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
  if (!amount) return '0,00 €'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount)
}

const verDetalles = (campania) => {
  router.push(`/campanias/${campania.id}`)
}

// Montar
onMounted(() => loadCatalogos())
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
