// Query para obtener grupos de trabajo con filtros
export const GET_GRUPOS = `
  query GruposTrabajo($filters: GrupoTrabajoFilters) {
    gruposTrabajo(filters: $filters) {
      id
      nombre
      descripcion
      tipo
      activo
      fecha_creacion
      coordinador {
        id
        nombre
        apellido1
      }
      miembros {
        id
        nombre
        apellido1
      }
    }
  }
`

// Query para obtener un grupo por ID
export const GET_GRUPO_BY_ID = `
  query GrupoTrabajo($id: Int!) {
    grupoTrabajo(id: $id) {
      id
      nombre
      descripcion
      tipo
      activo
      fecha_creacion
      coordinador {
        id
        nombre
        apellido1
        email
      }
      miembros {
        id
        nombre
        apellido1
        email
        rol_en_grupo
      }
      reuniones {
        id
        fecha
        lugar
        acta
      }
    }
  }
`

// Query para estadísticas de grupos
export const GET_ESTADISTICAS_GRUPOS = `
  query EstadisticasGrupos {
    estadisticasGrupos {
      total
      activos
      permanentes
      temporales
      totalMiembros
    }
  }
`
