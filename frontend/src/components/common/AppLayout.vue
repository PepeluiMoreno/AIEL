<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center">
                <span class="text-white font-bold">ONG</span>
              </div>
            </div>
            <div class="ml-4">
              <h1 class="text-lg font-semibold text-gray-900">Gestión ONG</h1>
            </div>
          </div>
          
          <!-- User menu -->
          <div class="flex items-center space-x-4">
            <div class="relative">
              <button class="flex items-center space-x-2">
                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                  <span>AU</span>
                </div>
                <span class="text-sm font-medium text-gray-700">
                  Admin User
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="flex">
      <!-- Sidebar -->
      <aside class="w-64 bg-white border-r border-gray-200 min-h-[calc(100vh-64px)]">
        <nav class="p-4">
          <div class="mb-8">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
              Navegación Principal
            </h2>
            <ul class="space-y-1">
              <li>
                <router-link to="/" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-50" :class="{'bg-blue-50 text-blue-700': $route.path === '/'}">
                  <span>🏠 Dashboard</span>
                </router-link>
              </li>
              <li>
                <router-link to="/miembros" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-50" :class="{'bg-blue-50 text-blue-700': $route.path === '/miembros'}">
                  <span>👥 Miembros</span>
                </router-link>
              </li>
              <li>
                <router-link to="/campanias" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-50" :class="{'bg-blue-50 text-blue-700': $route.path === '/campanias'}">
                  <span>🚩 Campañas</span>
                </router-link>
              </li>
              <li>
                <router-link to="/grupos" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-50" :class="{'bg-blue-50 text-blue-700': $route.path === '/grupos'}">
                  <span>👥 Grupos de Trabajo</span>
                </router-link>
              </li>
              <li>
                <router-link to="/financiero" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-50">
                  <span>💰 Financiero</span>
                </router-link>
              </li>
              <li>
                <router-link to="/voluntarios" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-50">
                  <span>❤️ Voluntarios</span>
                </router-link>
              </li>
            </ul>
          </div>
        </nav>
      </aside>

      <!-- Main content -->
      <main class="flex-1 p-6">
        <div class="max-w-7xl mx-auto">
          <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
            <p v-if="subtitle" class="text-gray-600 mt-1">{{ subtitle }}</p>
          </div>
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  }
})
</script>