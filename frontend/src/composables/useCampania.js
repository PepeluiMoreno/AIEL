import { ref } from 'vue'
import { useRouter } from 'vue-router'

export function useCampania() {
  const router = useRouter()
  const loading = ref(false)
  const campania = ref({
    id: null,
    nombre: '',
    descripcion_corta: '',
    estado_campania_id: null,
    tipo_campania_id: null,
    // ... otros campos
  })

  const acciones = ref([])
  const participantes = ref([])

  const fetchCampania = async (id) => {
    loading.value = true
    try {
      console.log('Fetching campaña:', id)
      // Simulación
      campania.value = {
        id,
        nombre: 'Campaña de Verano',
        estado_campania_id: 2,
        // ... otros datos
      }
    } catch (error) {
      console.error('Error fetching campaña:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchAcciones = async (campaniaId) => {
    try {
      console.log('Fetching acciones for campaña:', campaniaId)
      acciones.value = [
        { id: 1, nombre: 'Recogida de firmas', fecha: '2024-06-01' },
        { id: 2, nombre: 'Charla informativa', fecha: '2024-06-15' }
      ]
    } catch (error) {
      console.error('Error fetching acciones:', error)
    }
  }

  return {
    campania,
    acciones,
    participantes,
    loading,
    fetchCampania,
    fetchAcciones
  }
}