import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import './style.css'
import App from './App.vue'

// Importa las vistas
import Dashboard from './views/Dashboard.vue'
import Login from './views/Login.vue'
import ListaMiembros from './views/miembros/ListaMiembros.vue'
import ListaCampanias from './views/campanias/ListaCampanias.vue'
import ListaGrupos from './views/grupos/ListaGrupos.vue'

// Configuración de rutas
const routes = [
  { path: '/', component: Dashboard, name: 'Dashboard' },
  { path: '/login', component: Login, name: 'Login' },
  { path: '/miembros', component: ListaMiembros, name: 'Miembros' },
  { path: '/campanias', component: ListaCampanias, name: 'Campañas' },
  { path: '/grupos', component: ListaGrupos, name: 'Grupos' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')