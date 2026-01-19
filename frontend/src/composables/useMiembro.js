import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export function useMiembro() {
  const router = useRouter()
  const loading = ref(false)
  const miembro = ref({
    id: null,
    nombre: '',
    apellido1: '',
    apellido2: '',
    email: '',
    telefono: '',
    tipo_miembro_id: null,
    agrupacion_id: null,
    es_voluntario: false,
    // ... otros campos
  })

  const fetchMiembro = async (id) => {
    loading.value = true
    try {
      // Aquí iría la llamada a la API
      console.log('Fetching miembro:', id)
      // Simulación
      miembro.value = {
        id,
        nombre: 'Juan',
        apellido1: 'Pérez',
        email: 'juan@ejemplo.com',
        // ... otros datos
      }
    } catch (error) {
      console.error('Error fetching miembro:', error)
    } finally {
      loading.value = false
    }
  }

  const saveMiembro = async () => {
    loading.value = true
    try {
      // Lógica de guardado
      console.log('Saving miembro:', miembro.value)
      router.push('/miembros')
    } catch (error) {
      console.error('Error saving miembro:', error)
    } finally {
      loading.value = false
    }
  }

  const deleteMiembro = async (id) => {
    if (!confirm('¿Estás seguro de eliminar este miembro?')) return
    
    loading.value = true
    try {
      // Lógica de eliminación
      console.log('Deleting miembro:', id)
      router.push('/miembros')
    } catch (error) {
      console.error('Error deleting miembro:', error)
    } finally {
      loading.value = false
    }
  }

  const nombreCompleto = computed(() => {
    const { nombre, apellido1, apellido2 } = miembro.value
    return [nombre, apellido1, apellido2].filter(Boolean).join(' ')
  })

  return {
    miembro,
    loading,
    nombreCompleto,
    fetchMiembro,
    saveMiembro,
    deleteMiembro
  }
}