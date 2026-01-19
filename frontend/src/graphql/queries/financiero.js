// Query para obtener transacciones con filtros
export const GET_TRANSACCIONES = `
  query Transacciones($filters: TransaccionFilters) {
    transacciones(filters: $filters) {
      id
      tipo
      concepto
      importe
      fecha
      estado
      miembro {
        id
        nombre
        apellido1
      }
      metodo_pago
      referencia
    }
  }
`

// Query para obtener una transacción por ID
export const GET_TRANSACCION_BY_ID = `
  query Transaccion($id: Int!) {
    transaccion(id: $id) {
      id
      tipo
      concepto
      importe
      fecha
      estado
      miembro {
        id
        nombre
        apellido1
        email
      }
      metodo_pago
      referencia
      notas
      created_at
      updated_at
    }
  }
`

// Query para obtener cuotas
export const GET_CUOTAS = `
  query Cuotas($filters: CuotaFilters) {
    cuotas(filters: $filters) {
      id
      miembro {
        id
        nombre
        apellido1
      }
      tipo_cuota
      importe
      periodicidad
      fecha_inicio
      fecha_fin
      estado
      ultimo_cobro
    }
  }
`

// Query para estadísticas financieras
export const GET_ESTADISTICAS_FINANCIERAS = `
  query EstadisticasFinancieras($year: Int) {
    estadisticasFinancieras(year: $year) {
      totalIngresos
      totalGastos
      saldo
      cuotasPendientes
      cuotasCobradas
      donaciones
      porMes {
        mes
        ingresos
        gastos
      }
    }
  }
`

// Query para obtener remesas SEPA
export const GET_REMESAS_SEPA = `
  query RemesasSEPA($filters: RemesaFilters) {
    remesasSEPA(filters: $filters) {
      id
      fecha_creacion
      fecha_cobro
      total_recibos
      importe_total
      estado
      fichero_xml
    }
  }
`
