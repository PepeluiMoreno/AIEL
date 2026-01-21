<template>
  <div class="bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">{{ nombreCompleto || 'Cargando...' }}</h2>
          <p v-if="miembro.tipoMiembro || miembro.estado" class="text-sm text-gray-600">
            {{ miembro.tipoMiembro?.nombre }}{{ miembro.tipoMiembro && miembro.estado ? ' - ' : '' }}{{ miembro.estado?.nombre }}
          </p>
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
            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700"
          >
            {{ editMode ? 'Cancelar' : 'Editar' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div v-if="loading" class="p-8 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
      <p class="mt-2 text-gray-600">Cargando...</p>
    </div>

    <div v-else-if="error" class="p-8 text-center">
      <p class="text-red-600">{{ error }}</p>
      <button @click="$router.push('/miembros')" class="mt-4 text-purple-600 hover:underline">
        Volver a la lista
      </button>
    </div>

    <div v-else class="p-6">
      <!-- Tabs -->
      <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id
                ? 'border-purple-500 text-purple-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <!-- Tab: Datos Personales -->
      <div v-show="activeTab === 'personal'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Identificacion</h3>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nombre</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.nombre || '-' }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Primer Apellido</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.apellido1 || '-' }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Segundo Apellido</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.apellido2 || '-' }}</div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Sexo</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ formatSexo(miembro.sexo) }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha Nacimiento</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ formatDate(miembro.fechaNacimiento) }}</div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Tipo Documento</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.tipoDocumento || '-' }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Numero Documento</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.numeroDocumento || '-' }}</div>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Contacto</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.email || '-' }}</div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Telefono</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.telefono || '-' }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Telefono 2</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.telefono2 || '-' }}</div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Direccion</label>
            <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.direccion || '-' }}</div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Codigo Postal</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.codigoPostal || '-' }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Localidad</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.localidad || '-' }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Provincia</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.provincia?.nombre || '-' }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab: Membresia -->
      <div v-show="activeTab === 'membresia'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Estado de Membresía</h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Tipo de Socio</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                  {{ miembro.tipoMiembro?.nombre || '-' }}
                </span>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Situación</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">
                <span
                  class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                  :style="{ backgroundColor: miembro.estado?.color + '20', color: miembro.estado?.color }"
                >
                  {{ miembro.estado?.nombre || '-' }}
                </span>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha Alta</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ formatDate(miembro.fechaAlta) }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha Baja</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ formatDate(miembro.fechaBaja) }}</div>
            </div>
          </div>

          <div v-if="miembro.fechaBaja">
            <label class="block text-sm font-medium text-gray-700">Motivo Baja</label>
            <div class="mt-1 p-2 bg-gray-50 rounded text-sm">
              {{ miembro.motivoBajaRel?.nombre || miembro.motivoBajaTexto || '-' }}
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Agrupacion Territorial</label>
            <div class="mt-1 p-2 bg-gray-50 rounded text-sm">
              {{ miembro.agrupacion?.nombre || '-' }}
              <span v-if="miembro.agrupacion?.tipo" class="text-gray-400 text-xs ml-1">({{ miembro.agrupacion.tipo }})</span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">IBAN</label>
            <div class="mt-1 p-2 bg-gray-50 rounded text-sm font-mono">{{ miembro.iban || '-' }}</div>
          </div>
        </div>

        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Flags</h3>

          <div class="space-y-2">
            <label class="flex items-center">
              <input type="checkbox" :checked="miembro.activo" disabled class="rounded border-gray-300 text-purple-600" />
              <span class="ml-2 text-sm text-gray-700">Activo</span>
            </label>
            <label class="flex items-center">
              <input type="checkbox" :checked="miembro.esVoluntario" disabled class="rounded border-gray-300 text-purple-600" />
              <span class="ml-2 text-sm text-gray-700">Es voluntario</span>
            </label>
            <label class="flex items-center">
              <input type="checkbox" :checked="miembro.solicitaSupresionDatos" disabled class="rounded border-gray-300 text-red-600" />
              <span class="ml-2 text-sm text-gray-700">Solicita supresion de datos (RGPD)</span>
            </label>
            <label class="flex items-center">
              <input type="checkbox" :checked="miembro.datosAnonimizados" disabled class="rounded border-gray-300 text-gray-600" />
              <span class="ml-2 text-sm text-gray-700">Datos anonimizados</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Tab: Voluntariado -->
      <div v-show="activeTab === 'voluntariado'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Disponibilidad</h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Disponibilidad</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.disponibilidad || '-' }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Horas/Semana</label>
              <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.horasDisponiblesSemana || '-' }}</div>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Perfil Profesional</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700">Profesion</label>
            <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.profesion || '-' }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Nivel de Estudios</label>
            <div class="mt-1 p-2 bg-gray-50 rounded text-sm">{{ miembro.nivelEstudios || '-' }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Intereses</label>
            <div class="mt-1 p-2 bg-gray-50 rounded text-sm min-h-[60px]">{{ miembro.intereses || '-' }}</div>
          </div>
        </div>
      </div>

      <!-- Tab: Observaciones -->
      <div v-show="activeTab === 'observaciones'">
        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-900">Observaciones</h3>
          <div class="p-4 bg-gray-50 rounded min-h-[200px] whitespace-pre-wrap text-sm">
            {{ miembro.observaciones || 'Sin observaciones' }}
          </div>
        </div>
      </div>

      <!-- Botones de accion -->
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
const editMode = ref(false)
const activeTab = ref('personal')

const tabs = [
  { id: 'personal', name: 'Datos Personales' },
  { id: 'membresia', name: 'Membresía' },
  { id: 'voluntariado', name: 'Voluntariado' },
  { id: 'observaciones', name: 'Observaciones' },
]

const { miembro, loading, error, nombreCompleto, fetchMiembro, saveMiembro } = useMiembro()

onMounted(() => {
  if (route.params.id) {
    fetchMiembro(route.params.id)
  }
})

const handleSave = async () => {
  await saveMiembro()
  editMode.value = false
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatSexo = (sexo) => {
  if (!sexo) return '-'
  return sexo === 'H' ? 'Hombre' : sexo === 'M' ? 'Mujer' : sexo
}
</script>
