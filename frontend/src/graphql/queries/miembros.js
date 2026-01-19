// Query para obtener miembros con filtros
export const GET_MIEMBROS = `
  query Miembros($filters: MiembroFilters) {
    miembros(filters: $filters) {
      id
      nombre
      apellido1
      apellido2
      email
      telefono
      tipo_persona
      tipo_membresia
      tipo_miembro {
        id
        nombre
        codigo
      }
      agrupacion {
        id
        codigo
        nombre
        es_nacional
      }
      fecha_alta
      fecha_baja
    }
  }
`

// Query para obtener un miembro por ID
export const GET_MIEMBRO_BY_ID = `
  query Miembro($id: UUID!) {
    miembro(id: $id) {
      id
      nombre
      apellido1
      apellido2
      email
      telefono
      tipo_persona
      tipo_membresia
      tipo_documento
      numero_documento
      tipo_miembro {
        id
        nombre
        codigo
      }
      agrupacion {
        id
        codigo
        nombre
        es_nacional
      }
      fecha_alta
      fecha_baja
      direccion
      codigo_postal
      localidad
      provincia {
        nombre
      }
      pais_domicilio {
        nombre
      }
      iban
      es_voluntario
      disponibilidad
    }
  }
`

// Query para estadísticas de miembros
export const GET_ESTADISTICAS_MIEMBROS = `
  query EstadisticasMiembros {
    estadisticasMiembros {
      total
      activos
      inactivos
      porTipo {
        tipo
        cantidad
      }
      porAgrupacion {
        agrupacion
        cantidad
      }
    }
  }
`

// Query para obtener tipos de miembro
export const GET_TIPOS_MIEMBRO = `
  query TiposMiembro {
    tiposMiembro {
      id
      nombre
      codigo
      requiere_cuota
      activo
    }
  }
`

// Query para obtener agrupaciones territoriales
export const GET_AGRUPACIONES = `
  query Agrupaciones {
    agrupaciones {
      id
      codigo
      nombre
      es_nacional
      telefono
      email
      activo
    }
  }
`