<template>
  <AppLayout title="Voluntarios" subtitle="Gestión del voluntariado de Europa Laica">
    <!-- Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
            <span class="text-lg">❤️</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">Total voluntarios</p>
            <p class="text-xl font-bold text-purple-600">{{ resumen.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
            <span class="text-lg">✅</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">Disponibles ahora</p>
            <p class="text-xl font-bold text-green-600">{{ resumen.disponibles }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
            <span class="text-lg">🚩</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">En campaña activa</p>
            <p class="text-xl font-bold text-blue-600">{{ resumen.enCampania }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-lg bg-yellow-100 flex items-center justify-center mr-3">
            <span class="text-lg">⏰</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">Horas este mes</p>
            <p class="text-xl font-bold text-yellow-600">{{ resumen.horasMes }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar voluntarios..."
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
            + Registrar Voluntario
          </button>
        </div>
      </div>

      <!-- Filtros avanzados -->
      <div v-if="showFilters" class="mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Disponibilidad</label>
            <select v-model="filters.disponibilidad" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todas</option>
              <option value="DISPONIBLE">Disponible</option>
              <option value="OCUPADO">Ocupado en campaña</option>
              <option value="INACTIVO">Inactivo</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Asociación adscrita</label>
            <select v-model="filters.asociacion" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todas las asociaciones</option>
              <option value="EUROPA_LAICA">Europa Laica (Nacional)</option>
              <option value="ANDALUCIA_LAICA">Andalucía Laica</option>
              <option value="ARAGON_LAICO">Aragón Laico</option>
              <option value="ASTURIAS_LAICA">Asturias Laica</option>
              <option value="CANARIAS_LAICA">Canarias Laica</option>
              <option value="CATALUNYA_LAICA">Catalunya Laica</option>
              <option value="EUSKADI_LAICA">Euskadi Laica</option>
              <option value="EXTREMADURA_LAICA">Extremadura Laica</option>
              <option value="GALICIA_LAICA">Galicia Laica</option>
              <option value="MADRID_LAICA">Madrid Laica</option>
              <option value="MURCIA_LAICA">Murcia Laica</option>
              <option value="VALENCIA_LAICA">País Valenciano Laico</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Provincia</label>
            <select v-model="filters.provincia" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todas las provincias</option>
              <option value="MADRID">Madrid</option>
              <option value="BARCELONA">Barcelona</option>
              <option value="VALENCIA">Valencia</option>
              <option value="SEVILLA">Sevilla</option>
              <option value="ZARAGOZA">Zaragoza</option>
              <option value="BILBAO">Vizcaya</option>
              <option value="MALAGA">Málaga</option>
              <option value="ALICANTE">Alicante</option>
              <option value="CORUNA">A Coruña</option>
              <option value="LAS_PALMAS">Las Palmas</option>
              <option value="GRANADA">Granada</option>
              <option value="MURCIA">Murcia</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Campaña activa</label>
            <select v-model="filters.campania" class="w-full border border-gray-300 rounded-lg px-3 py-2">
              <option value="">Todas</option>
              <option value="1">Día del Laicismo 2025</option>
              <option value="2">Jornadas Educación Laica</option>
              <option value="3">Religión fuera de la Escuela</option>
              <option value="4">Apostasía Colectiva</option>
            </select>
          </div>
        </div>

        <!-- Filtros de habilidades -->
        <div class="border-t border-gray-200 pt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Habilidades y competencias</label>
          <div class="flex flex-wrap gap-2">
            <label
              v-for="habilidad in habilidadesDisponibles"
              :key="habilidad.id"
              class="inline-flex items-center px-3 py-1.5 rounded-full border cursor-pointer transition-colors"
              :class="filters.habilidades.includes(habilidad.id) ? 'bg-purple-100 border-purple-300 text-purple-800' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'"
            >
              <input
                type="checkbox"
                :value="habilidad.id"
                v-model="filters.habilidades"
                class="sr-only"
              />
              <span class="mr-1.5">{{ habilidad.icono }}</span>
              <span class="text-sm">{{ habilidad.nombre }}</span>
            </label>
          </div>
        </div>

        <!-- Botones de filtro -->
        <div class="flex justify-end mt-4 pt-4 border-t border-gray-200">
          <button @click="limpiarFiltros" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 mr-2">
            Limpiar filtros
          </button>
          <button @click="aplicarFiltros" class="px-4 py-2 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            Aplicar filtros
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
      <p class="mt-2 text-gray-600">Cargando voluntarios...</p>
    </div>

    <!-- Lista de voluntarios -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-if="voluntarios.length === 0" class="col-span-full text-center py-12 bg-white rounded-lg shadow">
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
          <span class="text-4xl">❤️</span>
        </div>
        <h3 class="text-sm font-medium text-gray-900">No hay voluntarios</h3>
        <p class="text-sm text-gray-500 mt-1">No se encontraron voluntarios con los filtros seleccionados.</p>
      </div>

      <div
        v-for="voluntario in voluntarios"
        :key="voluntario.id"
        class="bg-white rounded-lg shadow hover:shadow-md transition-shadow"
      >
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center">
              <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                <span class="text-lg font-medium text-purple-700">{{ voluntario.iniciales }}</span>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ voluntario.nombre }}</h3>
                <p class="text-sm text-gray-500">{{ voluntario.agrupacion }}</p>
              </div>
            </div>
            <span :class="getDisponibilidadClass(voluntario.disponibilidad)">
              {{ voluntario.disponibilidad }}
            </span>
          </div>

          <div class="space-y-2 text-sm text-gray-600 mb-4">
            <div class="flex items-center">
              <span class="mr-2">📧</span>
              <span>{{ voluntario.email }}</span>
            </div>
            <div v-if="voluntario.telefono" class="flex items-center">
              <span class="mr-2">📱</span>
              <span>{{ voluntario.telefono }}</span>
            </div>
            <div class="flex items-center">
              <span class="mr-2">⏰</span>
              <span>{{ voluntario.horasAcumuladas }} horas acumuladas</span>
            </div>
          </div>

          <!-- Áreas de interés -->
          <div class="mb-4">
            <p class="text-xs text-gray-500 mb-2">Áreas de interés:</p>
            <div class="flex flex-wrap gap-1">
              <span
                v-for="area in voluntario.areas"
                :key="area"
                class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-purple-50 text-purple-700"
              >
                {{ area }}
              </span>
            </div>
          </div>

          <!-- Campaña actual -->
          <div v-if="voluntario.campaniaActual" class="p-3 bg-blue-50 rounded-lg mb-4">
            <p class="text-xs text-blue-600 font-medium">En campaña:</p>
            <p class="text-sm text-blue-800">{{ voluntario.campaniaActual }}</p>
          </div>

          <div class="flex justify-end space-x-2 pt-3 border-t border-gray-100">
            <button @click="verHistorial(voluntario)" class="text-sm text-gray-600 hover:text-gray-800">
              Historial
            </button>
            <button @click="asignarCampania(voluntario)" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
              Asignar
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import AppLayout from '@/components/common/AppLayout.vue'

const loading = ref(false)
const voluntarios = ref([])
const searchQuery = ref('')
const showFilters = ref(false)

const resumen = ref({
  total: 45,
  disponibles: 28,
  enCampania: 17,
  horasMes: 234
})

const filters = ref({
  disponibilidad: '',
  asociacion: '',
  provincia: '',
  campania: '',
  habilidades: []
})

const habilidadesDisponibles = ref([
  { id: 'COMUNICACION', nombre: 'Comunicación', icono: '📢' },
  { id: 'REDES_SOCIALES', nombre: 'Redes Sociales', icono: '📱' },
  { id: 'DISENO', nombre: 'Diseño Gráfico', icono: '🎨' },
  { id: 'REDACCION', nombre: 'Redacción', icono: '✍️' },
  { id: 'FOTOGRAFIA', nombre: 'Fotografía/Video', icono: '📷' },
  { id: 'JURIDICO', nombre: 'Asesoría Jurídica', icono: '⚖️' },
  { id: 'EDUCACION', nombre: 'Educación', icono: '📚' },
  { id: 'EVENTOS', nombre: 'Organización Eventos', icono: '🎪' },
  { id: 'INFORMATICA', nombre: 'Informática', icono: '💻' },
  { id: 'IDIOMAS', nombre: 'Idiomas', icono: '🌍' },
  { id: 'ADMINISTRATIVO', nombre: 'Administrativo', icono: '📋' },
  { id: 'ATENCION_PUBLICO', nombre: 'Atención al público', icono: '🤝' }
])

onMounted(() => {
  loadVoluntarios()
})

const loadVoluntarios = async () => {
  loading.value = true
  // Datos mock relativos a Europa Laica
  setTimeout(() => {
    voluntarios.value = [
      {
        id: 1,
        nombre: 'María García López',
        iniciales: 'MG',
        email: 'maria.garcia@email.com',
        telefono: '612 345 678',
        agrupacion: 'Madrid Laica',
        provincia: 'Madrid',
        disponibilidad: 'DISPONIBLE',
        horasAcumuladas: 120,
        areas: ['Educación', 'Comunicación'],
        habilidades: ['EDUCACION', 'COMUNICACION', 'REDACCION'],
        campaniaActual: null
      },
      {
        id: 2,
        nombre: 'Juan Martínez Ruiz',
        iniciales: 'JM',
        email: 'juan.martinez@email.com',
        telefono: '623 456 789',
        agrupacion: 'Madrid Laica',
        provincia: 'Madrid',
        disponibilidad: 'OCUPADO',
        horasAcumuladas: 85,
        areas: ['Eventos', 'Administrativo'],
        habilidades: ['EVENTOS', 'ADMINISTRATIVO', 'ATENCION_PUBLICO'],
        campaniaActual: 'Día del Laicismo 2025'
      },
      {
        id: 3,
        nombre: 'Ana López Fernández',
        iniciales: 'AL',
        email: 'ana.lopez@email.com',
        telefono: null,
        agrupacion: 'Catalunya Laica',
        provincia: 'Barcelona',
        disponibilidad: 'DISPONIBLE',
        horasAcumuladas: 45,
        areas: ['Jurídico'],
        habilidades: ['JURIDICO', 'REDACCION'],
        campaniaActual: null
      },
      {
        id: 4,
        nombre: 'Carlos Sánchez Vega',
        iniciales: 'CS',
        email: 'carlos.sanchez@email.com',
        telefono: '634 567 890',
        agrupacion: 'País Valenciano Laico',
        provincia: 'Valencia',
        disponibilidad: 'OCUPADO',
        horasAcumuladas: 200,
        areas: ['Comunicación', 'Eventos'],
        habilidades: ['COMUNICACION', 'REDES_SOCIALES', 'FOTOGRAFIA', 'EVENTOS'],
        campaniaActual: 'Jornadas Educación Laica'
      },
      {
        id: 5,
        nombre: 'Laura Díaz Moreno',
        iniciales: 'LD',
        email: 'laura.diaz@email.com',
        telefono: '645 678 901',
        agrupacion: 'Andalucía Laica',
        provincia: 'Sevilla',
        disponibilidad: 'INACTIVO',
        horasAcumuladas: 30,
        areas: ['Educación'],
        habilidades: ['EDUCACION'],
        campaniaActual: null
      },
      {
        id: 6,
        nombre: 'Pedro Hernández Gil',
        iniciales: 'PH',
        email: 'pedro.hernandez@email.com',
        telefono: '656 789 012',
        agrupacion: 'Europa Laica',
        provincia: 'Madrid',
        disponibilidad: 'DISPONIBLE',
        horasAcumuladas: 150,
        areas: ['Comunicación', 'Jurídico'],
        habilidades: ['COMUNICACION', 'JURIDICO', 'INFORMATICA'],
        campaniaActual: null
      },
      {
        id: 7,
        nombre: 'Elena Torres Blanco',
        iniciales: 'ET',
        email: 'elena.torres@email.com',
        telefono: '667 890 123',
        agrupacion: 'Galicia Laica',
        provincia: 'A Coruña',
        disponibilidad: 'DISPONIBLE',
        horasAcumuladas: 75,
        areas: ['Diseño', 'Redes Sociales'],
        habilidades: ['DISENO', 'REDES_SOCIALES', 'FOTOGRAFIA'],
        campaniaActual: null
      },
      {
        id: 8,
        nombre: 'Roberto Díaz Campos',
        iniciales: 'RD',
        email: 'roberto.diaz@email.com',
        telefono: '678 901 234',
        agrupacion: 'Aragón Laico',
        provincia: 'Zaragoza',
        disponibilidad: 'OCUPADO',
        horasAcumuladas: 95,
        areas: ['Informática', 'Administrativo'],
        habilidades: ['INFORMATICA', 'ADMINISTRATIVO'],
        campaniaActual: 'Religión fuera de la Escuela'
      }
    ]
    loading.value = false
  }, 500)
}

const onSearch = () => {
  console.log('Buscando:', searchQuery.value)
}

const getDisponibilidadClass = (disponibilidad) => {
  const classes = {
    'DISPONIBLE': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800',
    'OCUPADO': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800',
    'INACTIVO': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
  }
  return classes[disponibilidad] || 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
}

const verHistorial = (voluntario) => {
  console.log('Ver historial:', voluntario)
}

const asignarCampania = (voluntario) => {
  console.log('Asignar a campaña:', voluntario)
}

const limpiarFiltros = () => {
  filters.value = {
    disponibilidad: '',
    asociacion: '',
    provincia: '',
    campania: '',
    habilidades: []
  }
}

const aplicarFiltros = () => {
  console.log('Aplicando filtros:', filters.value)
  loadVoluntarios()
}

watch(filters, () => {
  // Solo recargar automáticamente para selects, no para checkboxes de habilidades
}, { deep: true })
</script>
