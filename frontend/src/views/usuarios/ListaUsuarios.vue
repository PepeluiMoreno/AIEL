<template>
  <AppLayout title="Usuarios" subtitle="Gestión de usuarios del sistema SIGA">
    <!-- Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-purple-50 rounded-lg shadow p-4 border border-purple-100">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
            <span class="text-lg">👥</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">Total usuarios</p>
            <p class="text-xl font-bold text-purple-600">{{ resumen.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-green-50 rounded-lg shadow p-4 border border-green-100">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
            <span class="text-lg">✅</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">Activos</p>
            <p class="text-xl font-bold text-green-600">{{ resumen.activos }}</p>
          </div>
        </div>
      </div>
      <div class="bg-blue-50 rounded-lg shadow p-4 border border-blue-100">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
            <span class="text-lg">🔐</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">Administradores</p>
            <p class="text-xl font-bold text-blue-600">{{ resumen.admins }}</p>
          </div>
        </div>
      </div>
      <div class="bg-yellow-50 rounded-lg shadow p-4 border border-yellow-100">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-lg bg-yellow-100 flex items-center justify-center mr-3">
            <span class="text-lg">🕒</span>
          </div>
          <div>
            <p class="text-sm text-gray-500">Último acceso hoy</p>
            <p class="text-xl font-bold text-yellow-600">{{ resumen.activosHoy }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="mb-6 bg-white rounded-lg shadow p-4 border border-gray-100">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar usuarios..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              @input="onSearch"
            />
            <div class="absolute left-3 top-2.5">
              <span>🔍</span>
            </div>
          </div>
        </div>
        <div class="flex gap-2">
          <select v-model="filters.rol" class="border border-gray-300 rounded-lg px-3 py-2">
            <option value="">Todos los roles</option>
            <option value="Admin">Administrador</option>
            <option value="Gestor">Gestor</option>
            <option value="Usuario">Usuario</option>
          </select>
          <select v-model="filters.activo" class="border border-gray-300 rounded-lg px-3 py-2">
            <option value="">Todos</option>
            <option value="true">Activos</option>
            <option value="false">Inactivos</option>
          </select>
          <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            + Nuevo Usuario
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
      <p class="mt-2 text-gray-600">Cargando usuarios...</p>
    </div>

    <!-- Tabla de usuarios -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden border border-gray-100">
      <div v-if="usuarios.length === 0" class="text-center py-12">
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
          <span class="text-4xl">👤</span>
        </div>
        <h3 class="text-sm font-medium text-gray-900">No hay usuarios</h3>
        <p class="text-sm text-gray-500 mt-1">No se encontraron usuarios con los filtros seleccionados.</p>
      </div>

      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último acceso</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="usuario in usuarios" :key="usuario.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                  <span class="text-sm font-medium text-purple-700">{{ usuario.iniciales }}</span>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ usuario.nombre }}</div>
                  <div class="text-xs text-gray-500">{{ usuario.cargo }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ usuario.email }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getRolClass(usuario.rol)">{{ usuario.rol }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="usuario.activo ? 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800' : 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800'">
                {{ usuario.activo ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ usuario.ultimoAcceso }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="editarUsuario(usuario)" class="text-purple-600 hover:text-purple-800 mr-3">Editar</button>
              <button @click="toggleActivo(usuario)" :class="usuario.activo ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800'">
                {{ usuario.activo ? 'Desactivar' : 'Activar' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import AppLayout from '@/components/common/AppLayout.vue'

const loading = ref(false)
const usuarios = ref([])
const searchQuery = ref('')

const resumen = ref({
  total: 1,
  activos: 1,
  admins: 1,
  activosHoy: 1
})

const filters = ref({
  rol: '',
  activo: ''
})

onMounted(() => {
  loadUsuarios()
})

const loadUsuarios = async () => {
  loading.value = true
  // TODO: Reemplazar por llamada a API GraphQL
  setTimeout(() => {
    usuarios.value = [
      {
        id: 1,
        nombre: 'Administrador',
        iniciales: 'AD',
        email: 'admin@europalaica.org',
        cargo: 'Administrador del Sistema',
        rol: 'Admin',
        activo: true,
        ultimoAcceso: 'Hoy'
      }
    ]
    loading.value = false
  }, 500)
}

const onSearch = () => {
  console.log('Buscando:', searchQuery.value)
}

const getRolClass = (rol) => {
  const classes = {
    'Admin': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800',
    'Gestor': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800',
    'Usuario': 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
  }
  return classes[rol] || 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
}

const editarUsuario = (usuario) => {
  console.log('Editar usuario:', usuario)
}

const toggleActivo = (usuario) => {
  console.log('Toggle activo:', usuario)
  usuario.activo = !usuario.activo
}

watch(filters, () => {
  loadUsuarios()
}, { deep: true })
</script>
