// Query para obtener voluntarios con filtros
export const GET_VOLUNTARIOS = `
  query Voluntarios($filters: VoluntarioFilters) {
    voluntarios(filters: $filters) {
      id
      miembro {
        id
        nombre
        apellido1
        email
        telefono
      }
      habilidades
      disponibilidad
      activo
      fecha_alta
      campanias_participadas {
        id
        nombre
      }
    }
  }
`

// Query para obtener un voluntario por ID
export const GET_VOLUNTARIO_BY_ID = `
  query Voluntario($id: Int!) {
    voluntario(id: $id) {
      id
      miembro {
        id
        nombre
        apellido1
        apellido2
        email
        telefono
      }
      habilidades
      disponibilidad
      notas
      activo
      fecha_alta
      campanias_participadas {
        id
        nombre
        fecha_inicio
        fecha_fin
      }
      historial_participacion {
        campania_id
        campania_nombre
        horas_dedicadas
        tareas_realizadas
        valoracion
      }
    }
  }
`

// Query para estadísticas de voluntariado
export const GET_ESTADISTICAS_VOLUNTARIADO = `
  query EstadisticasVoluntariado {
    estadisticasVoluntariado {
      totalVoluntarios
      activos
      inactivos
      horasTotales
      porHabilidad {
        habilidad
        cantidad
      }
      porDisponibilidad {
        disponibilidad
        cantidad
      }
    }
  }
`

// Query para obtener habilidades disponibles
export const GET_HABILIDADES = `
  query Habilidades {
    habilidades {
      id
      nombre
      descripcion
    }
  }
`
