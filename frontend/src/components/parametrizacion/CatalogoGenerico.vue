<template>
  <AppLayout :title="titulo" :subtitle="subtitulo">
    <!-- Botón volver y acciones -->
    <div class="mb-6 flex justify-between items-center">
      <router-link to="/parametrizacion" class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Volver a Parametrización
      </router-link>
      <button
        @click="abrirModalCrear"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo {{ nombreSingular }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
      <p class="mt-2 text-gray-600">Cargando...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
      <div class="flex">
        <span class="text-red-400 mr-3">Error</span>
        <div>
          <h3 class="text-sm font-medium text-red-800">Error al cargar datos</h3>
          <p class="text-sm text-red-700 mt-1">{{ error.message }}</p>
        </div>
      </div>
    </div>

    <!-- Tabla -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
      <div v-if="items.length === 0" class="text-center py-12">
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4 text-4xl">{{ icono }}</div>
        <h3 class="text-sm font-medium text-gray-900">No hay registros</h3>
        <p class="text-sm text-gray-500 mt-1">Crea el primer {{ nombreSingular.toLowerCase() }}</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="col in columnas"
              :key="col.key"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              {{ col.label }}
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50">
            <td
              v-for="col in columnas"
              :key="col.key"
              class="px-6 py-4 whitespace-nowrap"
            >
              <!-- Tipo boolean -->
              <template v-if="col.type === 'boolean'">
                <span
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                    item[col.key] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                  ]"
                >
                  {{ item[col.key] ? 'Sí' : 'No' }}
                </span>
              </template>

              <!-- Tipo color -->
              <template v-else-if="col.type === 'color'">
                <div class="flex items-center gap-2">
                  <span
                    class="w-6 h-6 rounded-full border border-gray-300"
                    :style="{ backgroundColor: item[col.key] || '#6C757D' }"
                  ></span>
                  <span class="text-sm text-gray-500">{{ item[col.key] }}</span>
                </div>
              </template>

              <!-- Tipo activo (especial) -->
              <template v-else-if="col.key === 'activo'">
                <span
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                    item.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ item.activo ? 'Activo' : 'Inactivo' }}
                </span>
              </template>

              <!-- Tipo texto normal -->
              <template v-else>
                <span class="text-sm text-gray-900">{{ item[col.key] ?? '-' }}</span>
              </template>
            </td>

            <!-- Acciones -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="abrirModalEditar(item)"
                class="text-purple-600 hover:text-purple-900 mr-3"
              >
                Editar
              </button>
              <button
                @click="confirmarEliminar(item)"
                class="text-red-600 hover:text-red-900"
              >
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Crear/Editar -->
    <div
      v-if="modalAbierto"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          @click="cerrarModal"
        ></div>

        <!-- Modal -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="guardar">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg font-medium text-gray-900 mb-4">
                {{ itemEditando ? 'Editar' : 'Nuevo' }} {{ nombreSingular }}
              </h3>

              <div class="space-y-4">
                <div v-for="campo in campos" :key="campo.name">
                  <label :for="campo.name" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ campo.label }}
                    <span v-if="campo.required" class="text-red-500">*</span>
                  </label>

                  <!-- Input texto -->
                  <input
                    v-if="campo.type === 'text' || !campo.type"
                    :id="campo.name"
                    v-model="formulario[campo.name]"
                    type="text"
                    :required="campo.required"
                    :maxlength="campo.maxLength"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  />

                  <!-- Textarea -->
                  <textarea
                    v-else-if="campo.type === 'textarea'"
                    :id="campo.name"
                    v-model="formulario[campo.name]"
                    rows="3"
                    :required="campo.required"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  ></textarea>

                  <!-- Checkbox -->
                  <div v-else-if="campo.type === 'checkbox'" class="flex items-center">
                    <input
                      :id="campo.name"
                      v-model="formulario[campo.name]"
                      type="checkbox"
                      class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                    />
                    <label :for="campo.name" class="ml-2 text-sm text-gray-600">
                      {{ campo.description || campo.label }}
                    </label>
                  </div>

                  <!-- Color picker -->
                  <div v-else-if="campo.type === 'color'" class="flex items-center gap-3">
                    <input
                      :id="campo.name"
                      v-model="formulario[campo.name]"
                      type="color"
                      class="h-10 w-14 p-1 border border-gray-300 rounded cursor-pointer"
                    />
                    <input
                      v-model="formulario[campo.name]"
                      type="text"
                      pattern="^#[0-9A-Fa-f]{6}$"
                      placeholder="#RRGGBB"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    />
                  </div>

                  <!-- Number -->
                  <input
                    v-else-if="campo.type === 'number'"
                    :id="campo.name"
                    v-model.number="formulario[campo.name]"
                    type="number"
                    :min="campo.min"
                    :max="campo.max"
                    :required="campo.required"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  />

                  <!-- Select -->
                  <select
                    v-else-if="campo.type === 'select'"
                    :id="campo.name"
                    v-model="formulario[campo.name]"
                    :required="campo.required"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar...</option>
                    <option v-for="opt in campo.options" :key="opt.value" :value="opt.value">
                      {{ opt.label }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
              <button
                type="submit"
                :disabled="guardando"
                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                {{ guardando ? 'Guardando...' : 'Guardar' }}
              </button>
              <button
                type="button"
                @click="cerrarModal"
                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:w-auto sm:text-sm"
              >
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Confirmar Eliminar -->
    <div
      v-if="modalEliminar"
      class="fixed inset-0 z-50 overflow-y-auto"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          @click="modalEliminar = false"
        ></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Eliminar {{ nombreSingular }}
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    ¿Estás seguro de que deseas eliminar "{{ itemAEliminar?.nombre }}"?
                    Esta acción no se puede deshacer.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
            <button
              type="button"
              @click="eliminar"
              :disabled="eliminando"
              class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ eliminando ? 'Eliminando...' : 'Eliminar' }}
            </button>
            <button
              type="button"
              @click="modalEliminar = false"
              class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm"
            >
              Cancelar
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
import { useGraphQL } from '@/composables/useGraphQL.js'

const props = defineProps({
  titulo: { type: String, required: true },
  subtitulo: { type: String, default: '' },
  nombreSingular: { type: String, required: true },
  icono: { type: String, default: '📋' },
  queryName: { type: String, required: true },
  queryString: { type: String, required: true },
  mutationCreate: { type: String, default: null },
  mutationUpdate: { type: String, default: null },
  mutationDelete: { type: String, default: null },
  columnas: { type: Array, required: true },
  campos: { type: Array, required: true },
})

const emit = defineEmits(['created', 'updated', 'deleted'])

const { loading, error, query, mutation } = useGraphQL()

const items = ref([])
const modalAbierto = ref(false)
const modalEliminar = ref(false)
const itemEditando = ref(null)
const itemAEliminar = ref(null)
const guardando = ref(false)
const eliminando = ref(false)
const formulario = ref({})

// Inicializar formulario con valores por defecto
const inicializarFormulario = () => {
  const form = {}
  props.campos.forEach(campo => {
    if (campo.type === 'checkbox') {
      form[campo.name] = campo.default ?? false
    } else if (campo.type === 'number') {
      form[campo.name] = campo.default ?? 0
    } else if (campo.type === 'color') {
      form[campo.name] = campo.default ?? '#6C757D'
    } else {
      form[campo.name] = campo.default ?? ''
    }
  })
  return form
}

const cargarDatos = async () => {
  try {
    const data = await query(props.queryString)
    if (data && data[props.queryName]) {
      items.value = data[props.queryName]
    }
  } catch (err) {
    console.error('Error cargando datos:', err)
  }
}

const abrirModalCrear = () => {
  itemEditando.value = null
  formulario.value = inicializarFormulario()
  modalAbierto.value = true
}

const abrirModalEditar = (item) => {
  itemEditando.value = item
  formulario.value = { ...item }
  modalAbierto.value = true
}

const cerrarModal = () => {
  modalAbierto.value = false
  itemEditando.value = null
  formulario.value = {}
}

const guardar = async () => {
  guardando.value = true
  try {
    // Preparar data (Strawchemy usa 'data' en vez de 'input')
    const data = {}
    props.campos.forEach(campo => {
      if (formulario.value[campo.name] !== undefined && formulario.value[campo.name] !== '') {
        data[campo.name] = formulario.value[campo.name]
      }
    })

    if (itemEditando.value && props.mutationUpdate) {
      // Actualizar - incluir el id en el data para update_by_pk
      data.id = itemEditando.value.id
      await mutation(props.mutationUpdate, { data })
      emit('updated', itemEditando.value)
    } else if (props.mutationCreate) {
      // Crear
      await mutation(props.mutationCreate, { data })
      emit('created')
    }

    cerrarModal()
    await cargarDatos()
  } catch (err) {
    console.error('Error guardando:', err)
    alert('Error al guardar: ' + err.message)
  } finally {
    guardando.value = false
  }
}

const confirmarEliminar = (item) => {
  itemAEliminar.value = item
  modalEliminar.value = true
}

const eliminar = async () => {
  if (!props.mutationDelete || !itemAEliminar.value) return

  eliminando.value = true
  try {
    // Strawchemy delete usa filter con id._eq
    await mutation(props.mutationDelete, {
      filter: { id: { _eq: itemAEliminar.value.id } }
    })
    emit('deleted', itemAEliminar.value)
    modalEliminar.value = false
    itemAEliminar.value = null
    await cargarDatos()
  } catch (err) {
    console.error('Error eliminando:', err)
    alert('Error al eliminar: ' + err.message)
  } finally {
    eliminando.value = false
  }
}

onMounted(() => {
  cargarDatos()
})
</script>
