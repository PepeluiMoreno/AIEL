// Query para obtener campañas
export const GET_CAMPANIAS = `
  query Campanias($filters: CampaniaFilters) {
    campanias(filters: $filters) {
      id
      codigo
      nombre
      descripcion_corta
      tipo_campania {
        id
        nombre
      }
      estado_campania {
        id
        nombre
        color
      }
      fecha_inicio_plan
      fecha_fin_plan
      meta_recaudacion
      responsable {
        id
        nombre
        apellido1
      }
      agrupacion {
        id
        nombre
      }
    }
  }
`

// Query para obtener tipos de campaña
export const GET_TIPOS_CAMPANIA = `
  query TiposCampania {
    tiposCampania {
      id
      nombre
      codigo
      activo
    }
  }
`

// Query para obtener estados de campaña
export const GET_ESTADOS_CAMPANIA = `
  query EstadosCampania {
    estadosCampania {
      id
      nombre
      codigo
      orden
      color
      activo
    }
  }
`