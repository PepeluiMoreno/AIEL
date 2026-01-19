import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  
  const token = ref(localStorage.getItem('aiel_token'))
  const user = ref(JSON.parse(localStorage.getItem('aiel_user') || 'null'))
  
  const isAuthenticated = computed(() => !!token.value)
  const userName = computed(() => user.value?.nombre || 'Usuario')
  const userInitials = computed(() => {
    const name = user.value?.nombre || 'U'
    const lastName = user.value?.apellido1 || 'S'
    return `${name.charAt(0)}${lastName.charAt(0)}`.toUpperCase()
  })
  
  function setAuth(authData) {
    token.value = authData.token
    user.value = authData.user
    localStorage.setItem('aiel_token', authData.token)
    localStorage.setItem('aiel_user', JSON.stringify(authData.user))
  }
  
  function clearAuth() {
    token.value = null
    user.value = null
    localStorage.removeItem('aiel_token')
    localStorage.removeItem('aiel_user')
  }
  
  async function login(email, password) {
    // En desarrollo: aceptar cualquier email/password
    const mockUser = {
      id: 1,
      nombre: 'Admin',
      apellido1: 'Europa Laica',
      apellido2: '',
      email: email,
      cargo: 'Administrador',
      roles: ['Admin']
    }

    const authData = {
      token: 'aiel-jwt-token-' + Date.now(),
      user: mockUser
    }

    setAuth(authData)
    return mockUser
  }
  
  async function logout() {
    clearAuth()
    router.push('/login')
  }
  
  return {
    token,
    user,
    isAuthenticated,
    userName,
    userInitials,
    login,
    logout,
    setAuth,
    clearAuth
  }
})
