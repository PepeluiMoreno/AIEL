import { createRouter, createWebHistory } from 'vue-router'

// Importa las vistas
import Dashboard from '@/views/Dashboard.vue'
import Login from '@/views/Login.vue'
import ListaMiembros from '@/views/miembros/ListaMiembros.vue'
import ListaCampanias from '@/views/campanias/ListaCampanias.vue'
import ListaGrupos from '@/views/grupos/ListaGrupos.vue'
import ListaFinanciero from '@/views/financiero/ListaFinanciero.vue'
import ListaVoluntarios from '@/views/voluntariado/ListaVoluntarios.vue'
import ListaUsuarios from '@/views/usuarios/ListaUsuarios.vue'

// Configuración de rutas
const routes = [
  {
    path: '/',
    component: Dashboard,
    name: 'Dashboard',
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    component: Login,
    name: 'Login',
    meta: { guest: true }
  },
  {
    path: '/miembros',
    component: ListaMiembros,
    name: 'Miembros',
    meta: { requiresAuth: true }
  },
  {
    path: '/campanias',
    component: ListaCampanias,
    name: 'Campañas',
    meta: { requiresAuth: true }
  },
  {
    path: '/grupos',
    component: ListaGrupos,
    name: 'Grupos',
    meta: { requiresAuth: true }
  },
  {
    path: '/financiero',
    component: ListaFinanciero,
    name: 'Financiero',
    meta: { requiresAuth: true }
  },
  {
    path: '/voluntarios',
    component: ListaVoluntarios,
    name: 'Voluntarios',
    meta: { requiresAuth: true }
  },
  {
    path: '/usuarios',
    component: ListaUsuarios,
    name: 'Usuarios',
    meta: { requiresAuth: true }
  },
  {
    path: '/miembros/:id',
    component: () => import('@/views/miembros/DetalleMiembro.vue'),
    name: 'DetalleMiembro',
    meta: { requiresAuth: true }
  },
  {
    path: '/campanias/:id',
    component: () => import('@/views/campanias/DetalleCampania.vue'),
    name: 'DetalleCampania',
    meta: { requiresAuth: true }
  },
  {
    path: '/grupos/:id',
    component: () => import('@/views/grupos/DetalleGrupo.vue'),
    name: 'DetalleGrupo',
    meta: { requiresAuth: true }
  },
    {
    path: '/campanias/nueva',
    component: () => import('@/views/campanias/CampaniaForm.vue'),
    name: 'NuevaCampania',
    meta: { requiresAuth: true }
  },
  {
    path: '/campanias/:id',
    component: () => import('@/views/campanias/DetalleCampania.vue'),
    name: 'DetalleCampania',
    meta: { requiresAuth: true },
    props: true
  },
  {
    path: '/campanias/:id/editar',
    component: () => import('@/views/campanias/CampaniaForm.vue'),
    name: 'EditarCampania',
    meta: { requiresAuth: true },
    props: true
  }

]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guard de navegación para autenticación
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('aiel_token')

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
