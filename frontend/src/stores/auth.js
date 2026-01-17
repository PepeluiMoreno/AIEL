import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  
  const token = ref(localStorage.getItem('ong_token'))
  const user = ref(JSON.parse(localStorage.getItem('ong_user') || 'null'))
  
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
    localStorage.setItem('ong_token', authData.token)
    localStorage.setItem('ong_user', JSON.stringify(authData.user))
  }
  
  function clearAuth() {
    token.value = null
    user.value = null
    localStorage.removeItem('ong_token')
    localStorage.removeItem('ong_user')
  }
  
  async function login(email, password) {
    try {
      // En producción, aquí harías la llamada a tu API
      // Por ahora, simulamos un login exitoso
      const mockUser = {
        id: 1,
        nombre: 'Admin',
        apellido1: 'Usuario',
        email: email,
        roles: ['ADMIN']
      }
      
      const authData = {
        token: 'mock-jwt-token-' + Date.now(),
        user: mockUser
      }
      
      setAuth(authData)
      return mockUser
    } catch (error) {
      console.error('Login error:', error)
      throw error
    }
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
