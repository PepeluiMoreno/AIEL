<template>
  <AppLayout :title="titulo" :subtitle="subtitulo">
    <!-- Estados de carga/error -->
    <div v-if="cargando">
      <div class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
        <p class="mt-2 text-gray-600">Cargando detalles de la campaña...</p>
      </div>
    </div>

    <div v-else-if="error">
      <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <span class="text-red-400">⚠️</span>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error al cargar la campaña</h3>
            <p class="text-sm text-red-700 mt-1">{{ error.message }}</p>
            <button @click="cargarCampania" class="mt-2 text-sm text-red-600 hover:text-red-500">
              Intentar de nuevo
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenido principal -->
    <div v-else-if="campania" class="bg-white rounded-lg shadow">
      <!-- Encabezado -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-start">
          <div>
            <div class="flex items-center space-x-3">
              <h2 class="text-2xl font-bold text-gray-900">{{ campania.nombre }}</h2>
              <span :class="claseEstado" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                {{ obtenerNombreEstado(campania.estado) }}
              </span>
            </div>
            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
              <span>Código: <strong>{{ campania.codigo }}</strong></span>
              <span>📅 {{ formatearFecha(campania.fecha_inicio_plan) }} - {{ formatearFecha(campania.fecha_fin_plan) }}</span>
            </div>
            <p v-if="campania.descripcion_corta" class="mt-2 text-gray-700">
              {{ campania.descripcion_corta }}
            </p>
          </div>
          
          <div class="flex space-x-3">
            <router-link
              to="/campanias"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Volver
            </router-link>
            <router-link
              :to="`/campanias/${campania.id}/editar`"
              class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700"
            >
              Editar
            </router-link>
          </div>
        </div>
      </div>

      <!-- Pestañas -->
      <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
          <button
            v-for="pestania in pestañas"
            :key="pestania.id"
            @click="pestañaActiva = pestania.id"
            :class="[
              pestañaActiva === pestania.id
                ? 'border-purple-500 text-purple-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center'
            ]"
          >
            <span class="mr-2">{{ pestania.icono }}</span>
            {{ pestania.nombre }}
            <span v-if="pestania.contador" class="ml-2 bg-gray-100 text-gray-900 text-xs font-medium px-2 py-0.5 rounded-full">
              {{ pestania.contador }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Contenido de pestañas -->
      <div class="p-6">
        <InformacionGeneralTab 
          v-if="pestañaActiva === 'informacion'" 
          :campania="campania" 
        />
        
        <ObjetivosTab 
          v-else-if="pestañaActiva === 'objetivos'" 
          :objetivos="objetivos" 
        />
        
        <ActividadesTab 
          v-else-if="pestañaActiva === 'actividades'" 
          :actividades="actividades" 
        />
        
        <RecursosTab 
          v-else-if="pestañaActiva === 'recursos'" 
          :campania="campania"
          :recursos-humanos="recursosHumanos"
          :recursos-materiales="recursosMateriales"
        />
        
        <ResultadosTab 
          v-else-if="pestañaActiva === 'resultados'" 
          :campania="campania" 
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'

// Importar componentes de pestañas
import InformacionGeneralTab from '@/components/campanias/tabs/InformacionGeneralTab.vue'
import ObjetivosTab from '@/components/campanias/tabs/ObjetivosTab.vue'
import ActividadesTab from '@/components/campanias/tabs/ActividadesTab.vue'
import RecursosTab from '@/components/campanias/tabs/RecursosTab.vue'
import ResultadosTab from '@/components/campanias/tabs/ResultadosTab.vue'

const route = useRoute()
const cargando = ref(true)
const error = ref(null)
const campania = ref(null)
const pestañaActiva = ref('informacion')

// Datos relacionados
const objetivos = ref([])
const actividades = ref([])
const recursosHumanos = ref([])
const recursosMateriales = ref([])

const titulo = computed(() => campania.value?.nombre || 'Detalle de Campaña')
const subtitulo = computed(() => campania.value?.codigo || '')

const pestañas = computed(() => [
  { 
    id: 'informacion', 
    nombre: 'Información', 
    icono: '📋' 
  },
  { 
    id: 'objetivos', 
    nombre: 'Objetivos', 
    icono: '🎯', 
    contador: objetivos.value.length 
  },
  { 
    id: 'actividades', 
    nombre: 'Actividades', 
    icono: '📅', 
    contador: actividades.value.length 
  },
  { 
    id: 'recursos', 
    nombre: 'Recursos', 
    icono: '💰' 
  },
  { 
    id: 'resultados', 
    nombre: 'Resultados', 
    icono: '📊' 
  }
])

onMounted(() => {
  cargarCampania()
})

const cargarCampania = async () => {
  cargando.value = true
  error.value = null
  
  try {
    // Simular carga de API
    await new Promise(resolve => setTimeout(resolve, 800))
    
    const campaniaId = route.params.id
    
    // Datos de ejemplo según el ID
    const campaniasEjemplo = {
      '1': {
        id: 1,
        codigo: 'CAMP-2025-001',
        nombre: 'Campaña Día del Laicismo 2025',
        descripcion_corta: 'Actividades y eventos para celebrar el Día Internacional del Laicismo',
        descripcion_larga: 'Esta es una campaña completa para celebrar el Día Internacional del Laicismo. Incluye charlas, conferencias, actos públicos y actividades de concienciación sobre los valores laicos en la sociedad contemporánea.\n\nObjetivos:\n- Promover la separación Iglesia-Estado\n- Educar sobre los principios laicos\n- Movilizar a la sociedad civil',
        estado: 'ACTIVA',
        tipo_campania_id: 1,
        fecha_inicio_plan: '2025-01-15',
        fecha_fin_plan: '2025-02-15',
        fecha_inicio_real: '2025-01-15',
        meta_recaudacion: 5000,
        recaudacion_actual: 2350,
        recaudacion_real: 2350,
        meta_participantes: 50,
        participantes_reales: 24,
        objetivo_principal: 'Promover los valores laicos y la separación Iglesia-Estado en la sociedad',
        responsable: 'María García López',
        agrupacion_nombre: 'Europa Laica Nacional',
        agrupacion_codigo: 'EL-NAC',
        presupuesto_total: 3000,
        presupuesto_ejecutado: 1500,
        alcance: 120,
        resultados_cualitativos: 'Excelente respuesta del público. Alta participación en las charlas y buena cobertura mediática.',
        lecciones_aprendidas: 'Necesitamos más voluntarios para tareas logísticas. La comunicación anticipada es clave.',
        nivel_exito: 8,
        recomendaciones: 'Planificar con más antelación y diversificar las actividades'
      },
      '2': {
        id: 2,
        codigo: 'CAMP-2025-002',
        nombre: 'Jornadas sobre Educación Laica',
        descripcion_corta: 'Ciclo de conferencias sobre la importancia de la educación laica',
        descripcion_larga: 'Ciclo completo de conferencias sobre educación laica con expertos nacionales e internacionales.',
        estado: 'PLANIFICADA',
        tipo_campania_id: 2,
        fecha_inicio_plan: '2025-03-01',
        fecha_fin_plan: '2025-03-30',
        meta_recaudacion: 3000,
        meta_participantes: 100,
        responsable: 'Juan Martínez',
        agrupacion_nombre: 'Madrid'
      }
    }
    
    campania.value = campaniasEjemplo[campaniaId] || null
    
    if (!campania.value) {
      throw new Error('Campaña no encontrada')
    }
    
    // Cargar objetivos de ejemplo
    objetivos.value = [
      {
        id: 1,
        titulo: 'Recaudar 5000€ para financiación',
        descripcion: 'Alcanzar la meta de recaudación para cubrir los gastos de las actividades',
        tipo: 'cuantitativo',
        prioridad: 'alta',
        meta: '5000€',
        progreso: 47,
        fecha_limite: '2025-02-15'
      },
      {
        id: 2,
        titulo: 'Alcanzar 50 participantes activos',
        descripcion: 'Involucrar al menos 50 personas en las actividades de la campaña',
        tipo: 'cuantitativo',
        prioridad: 'media',
        meta: '50 personas',
        progreso: 48,
        fecha_limite: '2025-02-15'
      },
      {
        id: 3,
        titulo: 'Generar impacto mediático',
        descripcion: 'Conseguir cobertura en al menos 3 medios de comunicación',
        tipo: 'cualitativo',
        prioridad: 'media',
        meta: '3 medios',
        progreso: 67,
        fecha_limite: '2025-02-10'
      }
    ]
    
    // Cargar actividades de ejemplo
    actividades.value = [
      {
        id: 1,
        nombre: 'Recogida de firmas en Plaza Mayor',
        fecha: '2025-01-20',
        hora_inicio: '10:00',
        hora_fin: '14:00',
        lugar: 'Plaza Mayor, Madrid',
        descripcion: 'Recogida de firmas para la iniciativa por una sociedad laica',
        voluntarios_necesarios: 5,
        voluntarios_confirmados: 3,
        completada: true,
        materiales_necesarios: 'Mesas, sillas, folletos, formularios'
      },
      {
        id: 2,
        nombre: 'Charla: "Laicismo en el siglo XXI"',
        fecha: '2025-01-25',
        hora_inicio: '18:00',
        hora_fin: '20:00',
        lugar: 'Centro Cultural Municipal',
        descripcion: 'Conferencia sobre los desafíos del laicismo en la sociedad actual',
        voluntarios_necesarios: 3,
        voluntarios_confirmados: 2,
        completada: false,
        materiales_necesarios: 'Proyector, micrófono, sillas'
      },
      {
        id: 3,
        nombre: 'Taller para jóvenes',
        fecha: '2025-02-05',
        hora_inicio: '16:00',
        hora_fin: '19:00',
        lugar: 'Casa de la Juventud',
        descripcion: 'Taller interactivo sobre valores laicos para jóvenes',
        voluntarios_necesarios: 4,
        voluntarios_confirmados: 4,
        completada: false
      }
    ]
    
    // Cargar recursos de ejemplo
    recursosHumanos.value = [
      {
        id: 1,
        rol: 'Coordinador general',
        personas: 1,
        descripcion: 'Responsable de la organización y supervisión'
      },
      {
        id: 2,
        rol: 'Voluntarios logística',
        personas: 5,
        descripcion: 'Montaje, atención al público, apoyo general'
      },
      {
        id: 3,
        rol: 'Comunicación',
        personas: 2,
        descripcion: 'Redes sociales, prensa, fotografía'
      }
    ]
    
    recursosMateriales.value = [
      {
        id: 1,
        nombre: 'Material impreso',
        cantidad: 500,
        unidad: 'unidades',
        costo: 250,
        descripcion: 'Folletos informativos y carteles'
      },
      {
        id: 2,
        nombre: 'Equipo de sonido',
        cantidad: 1,
        unidad: 'set',
        costo: 150,
        descripcion: 'Alquiler para las charlas'
      },
      {
        id: 3,
        nombre: 'Merchandising',
        cantidad: 100,
        unidad: 'unidades',
        costo: 300,
        descripcion: 'Camisetas y chapas promocionales'
      }
    ]
    
  } catch (err) {
    error.value = err
    console.error('Error cargando campaña:', err)
  } finally {
    cargando.value = false
  }
}

const claseEstado = computed(() => {
  const estado = campania.value?.estado
  if (estado === 'ACTIVA') return 'bg-green-100 text-green-800'
  if (estado === 'PLANIFICADA') return 'bg-blue-100 text-blue-800'
  if (estado === 'FINALIZADA') return 'bg-gray-100 text-gray-800'
  if (estado === 'CANCELADA') return 'bg-red-100 text-red-800'
  return 'bg-gray-100 text-gray-800'
})

const obtenerNombreEstado = (estado) => {
  const estados = {
    'ACTIVA': 'Activa',
    'PLANIFICADA': 'Planificada',
    'FINALIZADA': 'Finalizada',
    'CANCELADA': 'Cancelada',
    'SUSPENDIDA': 'Suspendida'
  }
  return estados[estado] || estado
}

const formatearFecha = (fecha) => {
  if (!fecha) return 'No especificada'
  try {
    return new Date(fecha).toLocaleDateString('es-ES', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch {
    return fecha
  }
}
</script>