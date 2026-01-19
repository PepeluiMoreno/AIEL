// Query para obtener datos del dashboard
export const GET_DASHBOARD_DATA = `
  query DashboardData {
    # Aquí debes poner las queries reales de tu backend
    # Estos son ejemplos que debes ajustar según tu schema GraphQL
    
    # Para estadísticas (si tienes esta query)
    estadisticas {
      totalMiembros
      miembrosActivos
      miembrosInactivos
      miembrosNuevosMes
      
      totalCampanias
      campaniasActivas
      campaniasPlanificadas
      campaniasFinalizadas
      
      totalGrupos
      gruposActivos
      gruposPermanentes
      gruposTemporales
      
      recaudacionMes
      recaudacionAnual
      donacionesMes
      cuotasPendientes
    }
    
    # Para últimas campañas (si tienes esta query)
    ultimasCampanias(limit: 5) {
      id
      nombre
      descripcion_corta
      estado_campania {
        nombre
        color
      }
      fecha_inicio_plan
    }
    
    # Para próximas actividades (si tienes esta query)
    proximasActividades(limit: 5) {
      id
      nombre
      fecha
      lugar
      campania {
        nombre
      }
    }
    
    # Para últimos miembros (si tienes esta query)
    ultimosMiembros(limit: 5) {
      id
      nombre
      apellido1
      email
      tipo_miembro {
        nombre
      }
      fecha_alta
    }
  }
`

// Query básica para probar la conexión
export const TEST_CONNECTION = `
  query TestConnection {
    # Esto depende de tu backend - puedes usar una query simple
    # Por ejemplo, si tienes una query para contar miembros:
    miembrosCount
    
    # O si tienes una query para obtener tipos de miembro:
    tiposMiembro {
      id
      nombre
    }
    
    # O cualquier otra query simple que exista en tu backend
    __schema {
      types {
        name
      }
    }
  }
`