import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useGraphQL } from './useGraphQL'
import { GET_MIEMBRO_BY_ID } from '@/graphql/queries/miembros.js'

export function useMiembro() {
  const router = useRouter()
  const { query } = useGraphQL()
  const loading = ref(false)
  const error = ref(null)
  const miembro = ref({
    id: null,
    nombre: '',
    apellido1: '',
    apellido2: '',
    sexo: '',
    fechaNacimiento: null,
    email: '',
    telefono: '',
    telefono2: '',
    tipoDocumento: '',
    numeroDocumento: '',
    tipoMiembro: null,
    estado: null,
    motivoBajaRel: null,
    motivoBajaTexto: '',
    agrupacion: null,
    fechaAlta: null,
    fechaBaja: null,
    direccion: '',
    codigoPostal: '',
    localidad: '',
    provincia: null,
    paisDomicilio: null,
    iban: '',
    esVoluntario: false,
    disponibilidad: '',
    horasDisponiblesSemana: null,
    profesion: '',
    nivelEstudios: '',
    intereses: '',
    observaciones: '',
    solicitaSupresionDatos: false,
    datosAnonimizados: false,
    activo: true,
  })

  const fetchMiembro = async (id) => {
    loading.value = true
    error.value = null
    try {
      const data = await query(GET_MIEMBRO_BY_ID, { id })
      if (data?.miembros && data.miembros.length > 0) {
        miembro.value = data.miembros[0]
      } else {
        error.value = 'Miembro no encontrado'
      }
    } catch (err) {
      console.error('Error fetching miembro:', err)
      error.value = err.message || 'Error al cargar miembro'
    } finally {
      loading.value = false
    }
  }

  const saveMiembro = async () => {
    loading.value = true
    try {
      // TODO: Implementar mutation de guardado
      console.log('Saving miembro:', miembro.value)
      router.push('/miembros')
    } catch (err) {
      console.error('Error saving miembro:', err)
      error.value = err.message || 'Error al guardar miembro'
    } finally {
      loading.value = false
    }
  }

  const deleteMiembro = async (id) => {
    if (!confirm('¿Estás seguro de eliminar este miembro?')) return

    loading.value = true
    try {
      // TODO: Implementar mutation de eliminación
      console.log('Deleting miembro:', id)
      router.push('/miembros')
    } catch (err) {
      console.error('Error deleting miembro:', err)
      error.value = err.message || 'Error al eliminar miembro'
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
    error,
    nombreCompleto,
    fetchMiembro,
    saveMiembro,
    deleteMiembro
  }
}
