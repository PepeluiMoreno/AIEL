<template>
  <AppLayout :title="isEdit ? 'Editar Campaña' : 'Nueva Campaña'" :subtitle="isEdit ? `Editando: ${campania.nombre}` : 'Crear nueva campaña'">
    <div class="max-w-4xl mx-auto">
      <!-- Breadcrumb -->
      <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <router-link to="/campanias" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-purple-600">
              <span>🚩</span>
              <span class="ml-2">Campañas</span>
            </router-link>
          </li>
          <li>
            <div class="flex items-center">
              <span class="text-gray-400 mx-2">›</span>
              <span class="text-sm font-medium text-gray-500">
                {{ isEdit ? 'Editar' : 'Nueva' }}
              </span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Formulario -->
      <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
          <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
            <p class="mt-2 text-gray-600">Cargando...</p>
          </div>

          <form v-else @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Información básica -->
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900">Información básica</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Código *
                  </label>
                  <input
                    v-model="campania.codigo"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="Ej: CAMP-2025-001"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre *
                  </label>
                  <input
                    v-model="campania.nombre"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="Nombre de la campaña"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Descripción corta
                </label>
                <input
                  v-model="campania.descripcion_corta"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  placeholder="Breve descripción"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Descripción larga
                </label>
                <textarea
                  v-model="campania.descripcion_larga"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  placeholder="Descripción detallada de la campaña..."
                />
              </div>
            </div>

            <!-- Clasificación -->
            <div class="space-y-4 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Clasificación</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de campaña *
                  </label>
                  <select
                    v-model="campania.tipo_campania_id"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar tipo</option>
                    <option v-for="tipo in tiposCampania" :key="tipo.id" :value="tipo.id">
                      {{ tipo.nombre }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Estado *
                  </label>
                  <select
                    v-model="campania.estado_campania_id"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar estado</option>
                    <option value="1">Planificada</option>
                    <option value="2">Activa</option>
                    <option value="3">Finalizada</option>
                    <option value="4">Cancelada</option>
                    <option value="5">Suspendida</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Fechas -->
            <div class="space-y-4 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Fechas</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha inicio planificada
                  </label>
                  <input
                    v-model="campania.fecha_inicio_plan"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha fin planificada
                  </label>
                  <input
                    v-model="campania.fecha_fin_plan"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  />
                </div>
              </div>
            </div>

            <!-- Objetivos -->
            <div class="space-y-4 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Objetivos</h3>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Objetivo principal
                </label>
                <textarea
                  v-model="campania.objetivo_principal"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  placeholder="Objetivo principal de la campaña..."
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Meta de recaudación (€)
                  </label>
                  <input
                    v-model="campania.meta_recaudacion"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="0.00"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Meta de participantes
                  </label>
                  <input
                    v-model="campania.meta_participantes"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="0"
                  />
                </div>
              </div>
            </div>

            <!-- Responsable -->
            <div class="space-y-4 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Responsable</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Responsable
                  </label>
                  <select
                    v-model="campania.responsable_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar responsable</option>
                    <option v-for="miembro in miembros" :key="miembro.id" :value="miembro.id">
                      {{ miembro.nombre }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Agrupación
                  </label>
                  <select
                    v-model="campania.agrupacion_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar agrupación</option>
                    <option v-for="agrupacion in agrupaciones" :key="agrupacion.id" :value="agrupacion.id">
                      {{ agrupacion.nombre }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Botones -->
            <div class="pt-6 border-t border-gray-200 flex justify-end space-x-3">
              <router-link
                to="/campanias"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
              >
                Cancelar
              </router-link>
              <button
                type="submit"
                :disabled="submitting"
                class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
              >
                <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                <span>{{ isEdit ? 'Actualizar' : 'Crear' }} campaña</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '@/components/common/AppLayout.vue'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => route.params.id && !route.path.includes('/nueva'))
const loading = ref(false)
const submitting = ref(false)

// Datos del formulario
const campania = ref({
  codigo: '',
  nombre: '',
  descripcion_corta: '',
  descripcion_larga: '',
  tipo_campania_id: '',
  estado_campania_id: '',
  fecha_inicio_plan: '',
  fecha_fin_plan: '',
  fecha_inicio_real: '',
  fecha_fin_real: '',
  objetivo_principal: '',
  meta_recaudacion: null,
  meta_participantes: null,
  responsable_id: null,
  agrupacion_id: null
})

// Datos de ejemplo para selects
const tiposCampania = ref([])
const miembros = ref([])
const agrupaciones = ref([])

onMounted(() => {
  if (isEdit.value) {
    loadCampania()
  }
  loadData()
})

const loadCampania = async () => {
  loading.value = true
  try {
    // Simular carga de API
    await new Promise(resolve => setTimeout(resolve, 500))
    
    // Datos de ejemplo - en producción esto vendría de una API
    if (route.params.id === '1') {
      campania.value = {
        codigo: 'CAMP-2025-001',
        nombre: 'Campaña Día del Laicismo 2025',
        descripcion_corta: 'Actividades para celebrar el Día Internacional del Laicismo',
        descripcion_larga: 'Esta es una campaña completa para celebrar el Día Internacional del Laicismo. Incluye charlas, conferencias, actos públicos y actividades de concienciación.',
        tipo_campania_id: 1,
        estado_campania_id: 2,
        fecha_inicio_plan: '2025-01-15',
        fecha_fin_plan: '2025-02-15',
        objetivo_principal: 'Promover los valores laicos en la sociedad',
        meta_recaudacion: 5000,
        meta_participantes: 50,
        responsable_id: 1,
        agrupacion_id: 1
      }
    }
  } catch (error) {
    console.error('Error cargando campaña:', error)
  } finally {
    loading.value = false
  }
}

const loadData = () => {
  // Datos de ejemplo para selects
  tiposCampania.value = [
    { id: 1, nombre: 'Eventos' },
    { id: 2, nombre: 'Formación' },
    { id: 3, nombre: 'Denuncia' },
    { id: 4, nombre: 'Recaudación' },
    { id: 5, nombre: 'Ayuda Directa' }
  ]

  miembros.value = [
    { id: 1, nombre: 'María García' },
    { id: 2, nombre: 'Juan Martínez' },
    { id: 3, nombre: 'Ana López' },
    { id: 4, nombre: 'Carlos Ruiz' }
  ]

  agrupaciones.value = [
    { id: 1, nombre: 'Europa Laica Nacional' },
    { id: 2, nombre: 'Madrid' },
    { id: 3, nombre: 'Barcelona' },
    { id: 4, nombre: 'Valencia' }
  ]
}

const handleSubmit = async () => {
  submitting.value = true
  try {
    // Simular envío a API
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    console.log('Campaña guardada:', campania.value)
    
    // Redirigir a la vista de detalles o lista
    if (isEdit.value) {
      router.push(`/campanias/${route.params.id}`)
    } else {
      // Supongamos que el ID generado es 5
      router.push('/campanias/5')
    }
  } catch (error) {
    console.error('Error guardando campaña:', error)
    alert('Error al guardar la campaña. Por favor, inténtalo de nuevo.')
  } finally {
    submitting.value = false
  }
}
</script>