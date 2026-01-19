// Query para obtener usuarios con filtros
export const GET_USUARIOS = `
  query Usuarios($filters: UsuarioFilters) {
    usuarios(filters: $filters) {
      id
      nombre
      email
      roles
      activo
      ultimo_acceso
      created_at
    }
  }
`

// Query para obtener un usuario por ID
export const GET_USUARIO_BY_ID = `
  query Usuario($id: Int!) {
    usuario(id: $id) {
      id
      nombre
      email
      roles
      activo
      ultimo_acceso
      created_at
      updated_at
      miembro {
        id
        nombre
        apellido1
      }
    }
  }
`

// Query para estadísticas de usuarios
export const GET_ESTADISTICAS_USUARIOS = `
  query EstadisticasUsuarios {
    estadisticasUsuarios {
      total
      activos
      inactivos
      porRol {
        rol
        cantidad
      }
      activosHoy
    }
  }
`

// Query para obtener roles disponibles
export const GET_ROLES = `
  query Roles {
    roles {
      id
      nombre
      codigo
      descripcion
      permisos
    }
  }
`
