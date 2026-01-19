"""Seeding de actividades, propuestas y KPIs usando mutations GraphQL."""

import asyncio

from .graphql_client import execute_mutation, execute_query


# Queries para obtener datos necesarios
GET_MIEMBROS = """
query {
    miembros(limite: 10) { id nombre apellido1 }
}
"""

GET_GRUPOS_TRABAJO = """
query {
    gruposTrabajo { id nombre codigo }
}
"""

GET_PLANIFICACION = """
query GetPlanificacion($ejercicio: Int!) {
    planificacionAnual(ejercicio: $ejercicio) { id ejercicio nombre }
}
"""

GET_PARTIDAS = """
query GetPartidas($ejercicio: Int!) {
    partidasPresupuestarias(ejercicio: $ejercicio) { id codigo nombre }
}
"""

GET_TIPOS_ACTIVIDAD = """
query { tiposActividad { id codigo nombre } }
"""

GET_ESTADOS_ACTIVIDAD = """
query { estadosActividad { id codigo nombre } }
"""

GET_ESTADOS_PROPUESTA = """
query { estadosPropuesta { id codigo nombre } }
"""

GET_TIPOS_RECURSO = """
query { tiposRecurso { id codigo nombre } }
"""

GET_KPIS = """
query { kpis { id codigo nombre } }
"""

GET_PROPUESTAS = """
query { propuestas { id codigo titulo } }
"""

GET_ACTIVIDADES = """
query { actividades { id codigo nombre } }
"""

# Mutations
CREAR_PROPUESTA = """
mutation CrearPropuesta($input: PropuestaActividadInput!) {
    crearPropuesta(input: $input) {
        id codigo titulo
    }
}
"""

PRESENTAR_PROPUESTA = """
mutation PresentarPropuesta($id: UUID!) {
    presentarPropuesta(id: $id) {
        id estadoId fechaPresentacion
    }
}
"""

APROBAR_PROPUESTA = """
mutation AprobarPropuesta($id: UUID!, $presupuestoAprobado: Decimal!) {
    aprobarPropuesta(id: $id, presupuestoAprobado: $presupuestoAprobado) {
        id estadoId presupuestoAprobado fechaResolucion
    }
}
"""

CREAR_ACTIVIDAD = """
mutation CrearActividad($input: ActividadInput!) {
    crearActividad(input: $input) {
        id codigo nombre
    }
}
"""

CREAR_TAREA_ACTIVIDAD = """
mutation CrearTareaActividad($input: TareaActividadInput!) {
    crearTareaActividad(input: $input) {
        id nombre orden
    }
}
"""

ASIGNAR_KPI = """
mutation AsignarKPI($input: KPIActividadInput!) {
    asignarKpiActividad(input: $input) {
        actividadId kpiId valorObjetivo
    }
}
"""


async def get_or_none(query: str, variables: dict | None = None) -> dict | None:
    """Ejecuta una query y retorna None si hay error."""
    try:
        return await execute_query(query, variables)
    except Exception:
        return None


async def seed_propuestas():
    """Crear propuestas de actividad usando GraphQL."""

    # Verificar si ya existen propuestas
    propuestas = await get_or_none(GET_PROPUESTAS)
    if propuestas and propuestas.get("propuestas"):
        print("  = Propuestas ya existen")
        return

    # Obtener datos necesarios
    miembros_data = await execute_query(GET_MIEMBROS)
    miembros = miembros_data.get("miembros", [])
    if not miembros:
        print("  ! No hay miembros, ejecuta primero seed_miembros")
        return

    grupos_data = await execute_query(GET_GRUPOS_TRABAJO)
    grupos = grupos_data.get("gruposTrabajo", [])

    plan_data = await get_or_none(GET_PLANIFICACION, {"ejercicio": 2026})
    if not plan_data or not plan_data.get("planificacionAnual"):
        print("  ! Planificacion 2026 no encontrada, ejecuta primero seed_presupuesto")
        return
    plan = plan_data["planificacionAnual"]

    partidas_data = await execute_query(GET_PARTIDAS, {"ejercicio": 2026})
    partidas = {p["codigo"]: p for p in partidas_data.get("partidasPresupuestarias", [])}

    estados_data = await execute_query(GET_ESTADOS_PROPUESTA)
    estados = {e["codigo"]: e for e in estados_data.get("estadosPropuesta", [])}

    tipos_recurso_data = await execute_query(GET_TIPOS_RECURSO)
    tipos_recurso = {t["codigo"]: t for t in tipos_recurso_data.get("tiposRecurso", [])}

    # Propuesta 1: Jornadas de Laicismo
    propuesta1_input = {
        "codigo": "PRO-2026-001",
        "titulo": "Jornadas Anuales de Laicismo 2026",
        "descripcion": "Organizacion de las jornadas anuales de laicismo con ponentes nacionales e internacionales",
        "justificacion": "Las jornadas son el evento principal de la organizacion y permiten visibilizar nuestra labor",
        "proponenteId": miembros[0]["id"],
        "estadoId": estados.get("BORRADOR", {}).get("id"),
        "planificacionId": plan["id"],
        "fechaInicioPropuesta": "2026-03-15",
        "fechaFinPropuesta": "2026-03-17",
        "presupuestoSolicitado": "10000.00",
        "partidaId": partidas.get("2026-EVT-001", {}).get("id"),
        "tareas": [
            {"nombre": "Seleccion de ponentes", "orden": 1, "horasEstimadas": "20", "grupoTrabajoId": grupos[0]["id"] if grupos else None},
            {"nombre": "Reserva de local", "orden": 2, "horasEstimadas": "5", "grupoTrabajoId": grupos[0]["id"] if grupos else None},
            {"nombre": "Diseno de carteleria", "orden": 3, "horasEstimadas": "15", "grupoTrabajoId": grupos[0]["id"] if grupos else None},
            {"nombre": "Difusion en RRSS", "orden": 4, "horasEstimadas": "10", "grupoTrabajoId": grupos[0]["id"] if grupos else None},
            {"nombre": "Coordinacion dia del evento", "orden": 5, "horasEstimadas": "24", "grupoTrabajoId": grupos[0]["id"] if grupos else None},
        ],
        "recursos": [
            {
                "tipoRecursoId": tipos_recurso.get("LOCAL", {}).get("id"),
                "descripcion": "Alquiler salon de actos (3 dias)",
                "cantidad": 3,
                "importeUnitarioEstimado": "800.00",
                "importeTotalEstimado": "2400.00",
                "proveedor": "Centro Cultural Municipal"
            },
            {
                "tipoRecursoId": tipos_recurso.get("MATERIAL", {}).get("id"),
                "descripcion": "Material promocional (folletos, carteles)",
                "cantidad": 1,
                "importeUnitarioEstimado": "500.00",
                "importeTotalEstimado": "500.00"
            }
        ]
    }

    try:
        result = await execute_mutation(CREAR_PROPUESTA, {"input": propuesta1_input})
        propuesta1 = result.get("crearPropuesta")
        if propuesta1:
            print(f"  + Propuesta: {propuesta1['codigo']}")

            # Presentar la propuesta
            await execute_mutation(PRESENTAR_PROPUESTA, {"id": propuesta1["id"]})
            print(f"    - Propuesta presentada")

            # Aprobar la propuesta
            await execute_mutation(APROBAR_PROPUESTA, {
                "id": propuesta1["id"],
                "presupuestoAprobado": "9500.00"
            })
            print(f"    - Propuesta aprobada con 9500.00 EUR")
    except Exception as e:
        print(f"  ! Error al crear propuesta 1: {e}")

    # Propuesta 2: Campana escolar (pendiente de aprobacion)
    propuesta2_input = {
        "codigo": "PRO-2026-002",
        "titulo": "Campana Laicismo en la Escuela Publica",
        "descripcion": "Campana de sensibilizacion sobre la importancia del laicismo en la educacion publica",
        "justificacion": "Es necesario defender la escuela publica laica frente a los intentos de adoctrinamiento",
        "proponenteId": miembros[1]["id"] if len(miembros) > 1 else miembros[0]["id"],
        "estadoId": estados.get("BORRADOR", {}).get("id"),
        "planificacionId": plan["id"],
        "fechaInicioPropuesta": "2026-09-01",
        "fechaFinPropuesta": "2026-10-31",
        "presupuestoSolicitado": "8000.00",
        "partidaId": partidas.get("2026-CAMP-001", {}).get("id"),
        "tareas": [
            {"nombre": "Diseno de materiales", "orden": 1, "horasEstimadas": "20"},
            {"nombre": "Contacto con centros educativos", "orden": 2, "horasEstimadas": "30"},
            {"nombre": "Charlas informativas", "orden": 3, "horasEstimadas": "40"},
        ]
    }

    try:
        result = await execute_mutation(CREAR_PROPUESTA, {"input": propuesta2_input})
        propuesta2 = result.get("crearPropuesta")
        if propuesta2:
            print(f"  + Propuesta: {propuesta2['codigo']}")

            # Solo presentarla, no aprobarla
            await execute_mutation(PRESENTAR_PROPUESTA, {"id": propuesta2["id"]})
            print(f"    - Propuesta presentada (pendiente de aprobacion)")
    except Exception as e:
        print(f"  ! Error al crear propuesta 2: {e}")

    print("Propuestas completadas.")


async def seed_actividades():
    """Crear actividades a partir de propuestas aprobadas usando GraphQL."""

    # Verificar si ya existen actividades
    actividades = await get_or_none(GET_ACTIVIDADES)
    if actividades and actividades.get("actividades"):
        print("  = Actividades ya existen")
        return

    # Obtener propuestas
    propuestas_data = await execute_query(GET_PROPUESTAS)
    propuestas = {p["codigo"]: p for p in propuestas_data.get("propuestas", [])}

    propuesta_aprobada = propuestas.get("PRO-2026-001")
    if not propuesta_aprobada:
        print("  ! Propuesta PRO-2026-001 no encontrada")
        return

    # Obtener catalogos
    tipos_data = await execute_query(GET_TIPOS_ACTIVIDAD)
    tipos = {t["codigo"]: t for t in tipos_data.get("tiposActividad", [])}

    estados_data = await execute_query(GET_ESTADOS_ACTIVIDAD)
    estados = {e["codigo"]: e for e in estados_data.get("estadosActividad", [])}

    miembros_data = await execute_query(GET_MIEMBROS)
    miembros = miembros_data.get("miembros", [])

    kpis_data = await execute_query(GET_KPIS)
    kpis = {k["codigo"]: k for k in kpis_data.get("kpis", [])}

    plan_data = await get_or_none(GET_PLANIFICACION, {"ejercicio": 2026})
    plan = plan_data.get("planificacionAnual") if plan_data else None

    partidas_data = await execute_query(GET_PARTIDAS, {"ejercicio": 2026})
    partidas = {p["codigo"]: p for p in partidas_data.get("partidasPresupuestarias", [])}

    # Actividad 1: Jornadas de Laicismo (desde propuesta)
    actividad1_input = {
        "codigo": "ACT-2026-001",
        "nombre": "Jornadas Anuales de Laicismo 2026",
        "descripcion": "Jornadas anuales de laicismo en Madrid con ponentes nacionales e internacionales",
        "propuestaId": propuesta_aprobada["id"],
        "tipoActividadId": tipos.get("EVENTO", {}).get("id"),
        "estadoId": estados.get("PLANIFICADA", {}).get("id"),
        "prioridad": 1,
        "fechaInicio": "2026-03-15",
        "fechaFin": "2026-03-17",
        "horaInicio": "09:00",
        "horaFin": "20:00",
        "esTodoElDia": False,
        "lugar": "Centro Cultural Conde Duque",
        "direccion": "Calle Conde Duque, 11, 28015 Madrid",
        "esOnline": False,
        "planificacionId": plan["id"] if plan else None,
        "coordinadorId": miembros[0]["id"],
        "esColectiva": True,
        "partidaId": partidas.get("2026-EVT-001", {}).get("id"),
        "dotacionEconomica": "9500.00",
        "voluntariosNecesarios": 15
    }

    try:
        result = await execute_mutation(CREAR_ACTIVIDAD, {"input": actividad1_input})
        actividad1 = result.get("crearActividad")
        if actividad1:
            print(f"  + Actividad: {actividad1['codigo']}")

            # Crear tareas
            tareas = [
                {"nombre": "Confirmacion ponentes", "orden": 1, "horasEstimadas": "10"},
                {"nombre": "Diseno programa", "orden": 2, "horasEstimadas": "8"},
                {"nombre": "Gestion inscripciones", "orden": 3, "horasEstimadas": "15"},
                {"nombre": "Montaje y desmontaje", "orden": 4, "horasEstimadas": "12"},
                {"nombre": "Atencion participantes", "orden": 5, "horasEstimadas": "24"},
            ]

            for t in tareas:
                tarea_input = {
                    "actividadId": actividad1["id"],
                    "nombre": t["nombre"],
                    "orden": t["orden"],
                    "horasEstimadas": t["horasEstimadas"],
                    "fechaLimite": "2026-03-10"
                }
                await execute_mutation(CREAR_TAREA_ACTIVIDAD, {"input": tarea_input})
                print(f"    - Tarea: {t['nombre']}")

            # Asignar KPIs
            kpis_asignar = [
                {"codigo": "KPI-PARTICIPANTES", "objetivo": "150"},
                {"codigo": "KPI-ASISTENCIA", "objetivo": "85"},
                {"codigo": "KPI-SATISFACCION", "objetivo": "8"},
            ]

            for kpi_data in kpis_asignar:
                kpi = kpis.get(kpi_data["codigo"])
                if kpi:
                    kpi_input = {
                        "actividadId": actividad1["id"],
                        "kpiId": kpi["id"],
                        "valorObjetivo": kpi_data["objetivo"],
                        "peso": "1"
                    }
                    await execute_mutation(ASIGNAR_KPI, {"input": kpi_input})
                    print(f"    - KPI asignado: {kpi_data['codigo']}")

    except Exception as e:
        print(f"  ! Error al crear actividad 1: {e}")

    # Actividad 2: Reunion de coordinacion
    actividad2_input = {
        "codigo": "ACT-2026-002",
        "nombre": "Reunion de Coordinacion Campanas 2026",
        "descripcion": "Reunion de coordinacion para planificar las campanas del primer semestre",
        "tipoActividadId": tipos.get("REUNION", {}).get("id"),
        "estadoId": estados.get("PLANIFICADA", {}).get("id"),
        "prioridad": 2,
        "fechaInicio": "2026-01-20",
        "fechaFin": "2026-01-20",
        "horaInicio": "18:00",
        "horaFin": "20:00",
        "esTodoElDia": False,
        "lugar": "Sede Europa Laica",
        "esOnline": True,
        "urlOnline": "https://meet.europalaica.org/coordinacion",
        "coordinadorId": miembros[0]["id"],
        "esColectiva": False,
        "voluntariosNecesarios": 0
    }

    try:
        result = await execute_mutation(CREAR_ACTIVIDAD, {"input": actividad2_input})
        actividad2 = result.get("crearActividad")
        if actividad2:
            print(f"  + Actividad: {actividad2['codigo']}")
    except Exception as e:
        print(f"  ! Error al crear actividad 2: {e}")

    print("Actividades completadas.")


async def seed_all_actividades():
    """Ejecuta todos los seeders de actividades en orden."""
    print("\n[A] Propuestas de actividad...")
    await seed_propuestas()

    print("\n[B] Actividades...")
    await seed_actividades()


if __name__ == "__main__":
    asyncio.run(seed_all_actividades())
