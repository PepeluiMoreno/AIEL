"""Resolvers GraphQL para actividades, propuestas y KPIs."""

import strawberry
from strawberry.types import Info
from sqlalchemy import select, and_
from datetime import datetime, date
from uuid import UUID
from decimal import Decimal

from ..core.context import Context
from ..models.actividad import (
    TipoActividad as TipoActividadModel,
    EstadoActividad as EstadoActividadModel,
    EstadoPropuesta as EstadoPropuestaModel,
    TipoRecurso as TipoRecursoModel,
    TipoKPI as TipoKPIModel,
    PropuestaActividad as PropuestaActividadModel,
    TareaPropuesta as TareaPropuestaModel,
    RecursoPropuesta as RecursoPropuestaModel,
    GrupoPropuesta as GrupoPropuestaModel,
    Actividad as ActividadModel,
    TareaActividad as TareaActividadModel,
    RecursoActividad as RecursoActividadModel,
    ParticipanteActividad as ParticipanteActividadModel,
    GrupoActividad as GrupoActividadModel,
    KPI as KPIModel,
    KPIActividad as KPIActividadModel,
    MedicionKPI as MedicionKPIModel,
)
from ..models.grupo_trabajo import (
    GrupoTrabajo as GrupoTrabajoModel,
    EstadoTarea as EstadoTareaModel,
)
from ..schemas.actividad import (
    TipoActividad, EstadoActividad, EstadoPropuesta, TipoRecurso, TipoKPI, EstadoTarea,
    GrupoTrabajoRef, CampaniaRef,
    PropuestaActividad, TareaPropuesta, RecursoPropuesta, GrupoPropuesta,
    Actividad, TareaActividad, RecursoActividad, ParticipanteActividad, GrupoActividad,
    KPI, KPIActividad, MedicionKPI,
    PropuestaActividadInput, PropuestaActividadUpdateInput,
    TareaPropuestaInput, RecursoPropuestaInput, GrupoPropuestaInput,
    ActividadInput, ActividadUpdateInput,
    TareaActividadInput, RecursoActividadInput, ParticipanteActividadInput,
    KPIInput, KPIActividadInput, MedicionKPIInput,
)
from .mutations import model_to_miembro
from .presupuesto import model_to_partida_presupuestaria


# --- Conversores Model → Schema ---

def model_to_tipo_actividad(m: TipoActividadModel) -> TipoActividad:
    return TipoActividad(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, requiere_grupo=m.requiere_grupo,
        requiere_presupuesto=m.requiere_presupuesto, activo=m.activo
    )


def model_to_estado_actividad(m: EstadoActividadModel) -> EstadoActividad:
    return EstadoActividad(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        orden=m.orden, color=m.color, es_final=m.es_final, activo=m.activo
    )


def model_to_estado_propuesta(m: EstadoPropuestaModel) -> EstadoPropuesta:
    return EstadoPropuesta(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        orden=m.orden, color=m.color, es_final=m.es_final, activo=m.activo
    )


def model_to_tipo_recurso(m: TipoRecursoModel) -> TipoRecurso:
    return TipoRecurso(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, requiere_importe=m.requiere_importe, activo=m.activo
    )


def model_to_tipo_kpi(m: TipoKPIModel) -> TipoKPI:
    return TipoKPI(
        id=m.id, codigo=m.codigo, nombre=m.nombre, formato=m.formato, activo=m.activo
    )


def model_to_estado_tarea(m: EstadoTareaModel) -> EstadoTarea:
    return EstadoTarea(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        orden=m.orden, color=m.color, es_final=m.es_final, activo=m.activo
    )


def model_to_grupo_trabajo_ref(m: GrupoTrabajoModel) -> GrupoTrabajoRef:
    return GrupoTrabajoRef(id=m.id, codigo=m.codigo, nombre=m.nombre)


def model_to_campania_ref(m) -> CampaniaRef:
    return CampaniaRef(id=m.id, codigo=m.codigo, nombre=m.nombre)


def model_to_kpi(m: KPIModel) -> KPI:
    return KPI(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, tipo_kpi=model_to_tipo_kpi(m.tipo_kpi),
        unidad=m.unidad, valor_objetivo_defecto=m.valor_objetivo_defecto,
        valor_minimo=m.valor_minimo, formula=m.formula, activo=m.activo
    )


def model_to_medicion_kpi(m: MedicionKPIModel) -> MedicionKPI:
    medido_por = model_to_miembro(m.medido_por) if m.medido_por else None
    return MedicionKPI(
        id=m.id, valor_medido=m.valor_medido, fecha_medicion=m.fecha_medicion,
        medido_por=medido_por, observaciones=m.observaciones
    )


def model_to_kpi_actividad(m: KPIActividadModel) -> KPIActividad:
    mediciones = [model_to_medicion_kpi(med) for med in m.mediciones] if m.mediciones else []
    return KPIActividad(
        id=m.id, kpi=model_to_kpi(m.kpi),
        valor_objetivo=m.valor_objetivo, peso=m.peso,
        valor_actual=m.valor_actual, fecha_ultima_medicion=m.fecha_ultima_medicion,
        porcentaje_logro=m.porcentaje_logro, observaciones=m.observaciones,
        mediciones=mediciones
    )


def model_to_tarea_propuesta(m: TareaPropuestaModel) -> TareaPropuesta:
    grupo = model_to_grupo_trabajo_ref(m.grupo_trabajo) if m.grupo_trabajo else None
    responsable = model_to_miembro(m.responsable) if m.responsable else None
    return TareaPropuesta(
        id=m.id, nombre=m.nombre, descripcion=m.descripcion, orden=m.orden,
        grupo_trabajo=grupo, responsable=responsable,
        fecha_inicio_estimada=m.fecha_inicio_estimada,
        fecha_fin_estimada=m.fecha_fin_estimada,
        horas_estimadas=m.horas_estimadas
    )


def model_to_recurso_propuesta(m: RecursoPropuestaModel) -> RecursoPropuesta:
    return RecursoPropuesta(
        id=m.id, tipo_recurso=model_to_tipo_recurso(m.tipo_recurso),
        descripcion=m.descripcion, cantidad=m.cantidad,
        importe_unitario_estimado=m.importe_unitario_estimado,
        importe_total_estimado=m.importe_total_estimado,
        importe_aprobado=m.importe_aprobado,
        proveedor=m.proveedor, observaciones=m.observaciones
    )


def model_to_grupo_propuesta(m: GrupoPropuestaModel) -> GrupoPropuesta:
    return GrupoPropuesta(
        grupo_trabajo=model_to_grupo_trabajo_ref(m.grupo_trabajo),
        tareas_asignadas=m.tareas_asignadas, horas_estimadas=m.horas_estimadas
    )


def model_to_propuesta_actividad(m: PropuestaActividadModel) -> PropuestaActividad:
    proponente = model_to_miembro(m.proponente)
    estado = model_to_estado_propuesta(m.estado)
    campania = model_to_campania_ref(m.campania) if m.campania else None
    partida = model_to_partida_presupuestaria(m.partida) if m.partida else None
    tareas = [model_to_tarea_propuesta(t) for t in m.tareas] if m.tareas else []
    recursos = [model_to_recurso_propuesta(r) for r in m.recursos] if m.recursos else []
    grupos = [model_to_grupo_propuesta(g) for g in m.grupos_asignados] if m.grupos_asignados else []

    return PropuestaActividad(
        id=m.id, codigo=m.codigo, titulo=m.titulo,
        descripcion=m.descripcion, justificacion=m.justificacion,
        proponente=proponente, estado=estado,
        fecha_presentacion=m.fecha_presentacion, fecha_resolucion=m.fecha_resolucion,
        motivo_resolucion=m.motivo_resolucion, campania=campania,
        fecha_inicio_propuesta=m.fecha_inicio_propuesta,
        fecha_fin_propuesta=m.fecha_fin_propuesta,
        presupuesto_solicitado=m.presupuesto_solicitado,
        presupuesto_aprobado=m.presupuesto_aprobado,
        partida=partida, observaciones=m.observaciones,
        tareas=tareas, recursos=recursos, grupos_asignados=grupos,
        created_at=m.created_at, updated_at=m.updated_at
    )


def model_to_tarea_actividad(m: TareaActividadModel) -> TareaActividad:
    grupo = model_to_grupo_trabajo_ref(m.grupo_trabajo) if m.grupo_trabajo else None
    responsable = model_to_miembro(m.responsable) if m.responsable else None
    estado = model_to_estado_tarea(m.estado)
    return TareaActividad(
        id=m.id, nombre=m.nombre, descripcion=m.descripcion, orden=m.orden,
        grupo_trabajo=grupo, responsable=responsable, estado=estado,
        fecha_limite=m.fecha_limite, fecha_completada=m.fecha_completada,
        horas_estimadas=m.horas_estimadas, horas_reales=m.horas_reales
    )


def model_to_recurso_actividad(m: RecursoActividadModel) -> RecursoActividad:
    return RecursoActividad(
        id=m.id, tipo_recurso=model_to_tipo_recurso(m.tipo_recurso),
        descripcion=m.descripcion, cantidad=m.cantidad,
        importe_presupuestado=m.importe_presupuestado, importe_real=m.importe_real,
        proveedor=m.proveedor, factura_referencia=m.factura_referencia,
        fecha_factura=m.fecha_factura, pagado=m.pagado, fecha_pago=m.fecha_pago,
        observaciones=m.observaciones
    )


def model_to_grupo_actividad(m: GrupoActividadModel) -> GrupoActividad:
    return GrupoActividad(
        grupo_trabajo=model_to_grupo_trabajo_ref(m.grupo_trabajo),
        tareas_asignadas=m.tareas_asignadas,
        horas_estimadas=m.horas_estimadas, horas_reales=m.horas_reales
    )


def model_to_participante_actividad(m: ParticipanteActividadModel) -> ParticipanteActividad:
    miembro = model_to_miembro(m.miembro)
    return ParticipanteActividad(
        miembro=miembro, rol=m.rol, confirmado=m.confirmado,
        asistio=m.asistio, horas_aportadas=m.horas_aportadas,
        observaciones=m.observaciones
    )


def model_to_actividad(m: ActividadModel) -> Actividad:
    propuesta = model_to_propuesta_actividad(m.propuesta) if m.propuesta else None
    tipo = model_to_tipo_actividad(m.tipo_actividad)
    estado = model_to_estado_actividad(m.estado)
    campania = model_to_campania_ref(m.campania) if m.campania else None
    coordinador = model_to_miembro(m.coordinador)
    partida = model_to_partida_presupuestaria(m.partida) if m.partida else None
    tareas = [model_to_tarea_actividad(t) for t in m.tareas] if m.tareas else []
    recursos = [model_to_recurso_actividad(r) for r in m.recursos] if m.recursos else []
    participantes = [model_to_participante_actividad(p) for p in m.participantes] if m.participantes else []
    grupos = [model_to_grupo_actividad(g) for g in m.grupos_trabajo] if m.grupos_trabajo else []
    kpis = [model_to_kpi_actividad(k) for k in m.kpis] if m.kpis else []

    return Actividad(
        id=m.id, codigo=m.codigo, nombre=m.nombre, descripcion=m.descripcion,
        propuesta=propuesta, tipo_actividad=tipo, estado=estado, prioridad=m.prioridad,
        fecha_inicio=m.fecha_inicio, fecha_fin=m.fecha_fin,
        hora_inicio=m.hora_inicio, hora_fin=m.hora_fin, es_todo_el_dia=m.es_todo_el_dia,
        lugar=m.lugar, direccion=m.direccion, es_online=m.es_online, url_online=m.url_online,
        campania=campania, coordinador=coordinador, es_colectiva=m.es_colectiva,
        partida=partida, dotacion_economica=m.dotacion_economica, gasto_real=m.gasto_real,
        voluntarios_necesarios=m.voluntarios_necesarios,
        voluntarios_confirmados=m.voluntarios_confirmados,
        completada=m.completada, fecha_completada=m.fecha_completada,
        resultados=m.resultados, observaciones=m.observaciones,
        tareas=tareas, recursos=recursos, participantes=participantes,
        grupos_trabajo=grupos, kpis=kpis,
        created_at=m.created_at, updated_at=m.updated_at
    )


# --- Queries ---

@strawberry.type
class ActividadQuery:
    @strawberry.field
    async def tipos_actividad(self, info: Info[Context, None]) -> list[TipoActividad]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(TipoActividadModel).where(TipoActividadModel.activo == True)
            )
            return [model_to_tipo_actividad(t) for t in result.scalars()]

    @strawberry.field
    async def estados_actividad(self, info: Info[Context, None]) -> list[EstadoActividad]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(EstadoActividadModel)
                .where(EstadoActividadModel.activo == True)
                .order_by(EstadoActividadModel.orden)
            )
            return [model_to_estado_actividad(e) for e in result.scalars()]

    @strawberry.field
    async def estados_propuesta(self, info: Info[Context, None]) -> list[EstadoPropuesta]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(EstadoPropuestaModel)
                .where(EstadoPropuestaModel.activo == True)
                .order_by(EstadoPropuestaModel.orden)
            )
            return [model_to_estado_propuesta(e) for e in result.scalars()]

    @strawberry.field
    async def tipos_recurso(self, info: Info[Context, None]) -> list[TipoRecurso]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(TipoRecursoModel).where(TipoRecursoModel.activo == True)
            )
            return [model_to_tipo_recurso(t) for t in result.scalars()]

    @strawberry.field
    async def tipos_kpi(self, info: Info[Context, None]) -> list[TipoKPI]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(TipoKPIModel).where(TipoKPIModel.activo == True)
            )
            return [model_to_tipo_kpi(t) for t in result.scalars()]

    @strawberry.field
    async def kpis(self, info: Info[Context, None], solo_activos: bool = True) -> list[KPI]:
        async with await info.context.get_db() as db:
            query = select(KPIModel).where(KPIModel.deleted_at == None)
            if solo_activos:
                query = query.where(KPIModel.activo == True)

            result = await db.execute(query)
            kpis = []
            for m in result.scalars():
                await db.refresh(m, ["tipo_kpi"])
                kpis.append(model_to_kpi(m))
            return kpis

    @strawberry.field
    async def propuesta(
        self,
        info: Info[Context, None],
        codigo: str | None = None,
        id: strawberry.ID | None = None
    ) -> PropuestaActividad | None:
        async with await info.context.get_db() as db:
            query = select(PropuestaActividadModel).where(
                PropuestaActividadModel.deleted_at == None
            )

            if codigo:
                query = query.where(PropuestaActividadModel.codigo == codigo)
            elif id:
                query = query.where(PropuestaActividadModel.id == UUID(str(id)))
            else:
                return None

            result = await db.execute(query)
            m = result.scalar_one_or_none()
            if not m:
                return None

            await _refresh_propuesta(db, m)
            return model_to_propuesta_actividad(m)

    @strawberry.field
    async def propuestas(
        self,
        info: Info[Context, None],
        estado_id: int | None = None,
        proponente_id: strawberry.ID | None = None,
        campania_id: int | None = None,
        limite: int = 50
    ) -> list[PropuestaActividad]:
        async with await info.context.get_db() as db:
            query = select(PropuestaActividadModel).where(
                PropuestaActividadModel.deleted_at == None
            )

            if estado_id:
                query = query.where(PropuestaActividadModel.estado_id == estado_id)
            if proponente_id:
                query = query.where(PropuestaActividadModel.proponente_id == UUID(str(proponente_id)))
            if campania_id:
                query = query.where(PropuestaActividadModel.campania_id == campania_id)

            query = query.order_by(PropuestaActividadModel.created_at.desc()).limit(limite)

            result = await db.execute(query)
            propuestas = []
            for m in result.scalars():
                await _refresh_propuesta(db, m)
                propuestas.append(model_to_propuesta_actividad(m))
            return propuestas

    @strawberry.field
    async def actividad(
        self,
        info: Info[Context, None],
        codigo: str | None = None,
        id: strawberry.ID | None = None
    ) -> Actividad | None:
        async with await info.context.get_db() as db:
            query = select(ActividadModel).where(ActividadModel.deleted_at == None)

            if codigo:
                query = query.where(ActividadModel.codigo == codigo)
            elif id:
                query = query.where(ActividadModel.id == UUID(str(id)))
            else:
                return None

            result = await db.execute(query)
            m = result.scalar_one_or_none()
            if not m:
                return None

            await _refresh_actividad(db, m)
            return model_to_actividad(m)

    @strawberry.field
    async def actividades(
        self,
        info: Info[Context, None],
        tipo_id: int | None = None,
        estado_id: int | None = None,
        campania_id: int | None = None,
        coordinador_id: strawberry.ID | None = None,
        fecha_desde: date | None = None,
        fecha_hasta: date | None = None,
        solo_pendientes: bool = False,
        limite: int = 50
    ) -> list[Actividad]:
        async with await info.context.get_db() as db:
            query = select(ActividadModel).where(ActividadModel.deleted_at == None)

            if tipo_id:
                query = query.where(ActividadModel.tipo_actividad_id == tipo_id)
            if estado_id:
                query = query.where(ActividadModel.estado_id == estado_id)
            if campania_id:
                query = query.where(ActividadModel.campania_id == campania_id)
            if coordinador_id:
                query = query.where(ActividadModel.coordinador_id == UUID(str(coordinador_id)))
            if fecha_desde:
                query = query.where(ActividadModel.fecha_inicio >= fecha_desde)
            if fecha_hasta:
                query = query.where(ActividadModel.fecha_fin <= fecha_hasta)
            if solo_pendientes:
                query = query.where(ActividadModel.completada == False)

            query = query.order_by(ActividadModel.fecha_inicio.asc()).limit(limite)

            result = await db.execute(query)
            actividades = []
            for m in result.scalars():
                await _refresh_actividad(db, m)
                actividades.append(model_to_actividad(m))
            return actividades

    @strawberry.field
    async def calendario_actividades(
        self,
        info: Info[Context, None],
        mes: int,
        anio: int
    ) -> list[Actividad]:
        """Obtiene actividades para un mes específico (vista calendario)."""
        from calendar import monthrange
        inicio_mes = date(anio, mes, 1)
        _, ultimo_dia = monthrange(anio, mes)
        fin_mes = date(anio, mes, ultimo_dia)

        async with await info.context.get_db() as db:
            query = select(ActividadModel).where(
                and_(
                    ActividadModel.deleted_at == None,
                    ActividadModel.fecha_inicio <= fin_mes,
                    ActividadModel.fecha_fin >= inicio_mes
                )
            ).order_by(ActividadModel.fecha_inicio)

            result = await db.execute(query)
            actividades = []
            for m in result.scalars():
                await _refresh_actividad(db, m)
                actividades.append(model_to_actividad(m))
            return actividades


# Helpers para refresh de relaciones
async def _refresh_propuesta(db, m):
    await db.refresh(m, ["proponente", "estado", "campania", "partida", "tareas", "recursos", "grupos_asignados"])
    await db.refresh(m.proponente, ["tipo_miembro", "agrupacion"])
    if m.partida:
        await db.refresh(m.partida, ["categoria"])
    for t in m.tareas:
        await db.refresh(t, ["grupo_trabajo", "responsable"])
        if t.responsable:
            await db.refresh(t.responsable, ["tipo_miembro", "agrupacion"])
    for r in m.recursos:
        await db.refresh(r, ["tipo_recurso"])
    for g in m.grupos_asignados:
        await db.refresh(g, ["grupo_trabajo"])


async def _refresh_actividad(db, m):
    await db.refresh(m, [
        "propuesta", "tipo_actividad", "estado", "campania", "coordinador",
        "partida", "tareas", "recursos", "participantes", "grupos_trabajo", "kpis"
    ])
    await db.refresh(m.coordinador, ["tipo_miembro", "agrupacion"])
    if m.propuesta:
        await _refresh_propuesta(db, m.propuesta)
    if m.partida:
        await db.refresh(m.partida, ["categoria"])
    for t in m.tareas:
        await db.refresh(t, ["grupo_trabajo", "responsable", "estado"])
        if t.responsable:
            await db.refresh(t.responsable, ["tipo_miembro", "agrupacion"])
    for r in m.recursos:
        await db.refresh(r, ["tipo_recurso"])
    for p in m.participantes:
        await db.refresh(p, ["miembro"])
        await db.refresh(p.miembro, ["tipo_miembro", "agrupacion"])
    for g in m.grupos_trabajo:
        await db.refresh(g, ["grupo_trabajo"])
    for k in m.kpis:
        await db.refresh(k, ["kpi", "mediciones"])
        await db.refresh(k.kpi, ["tipo_kpi"])


# --- Mutations ---

@strawberry.type
class ActividadMutation:
    # ===== PROPUESTAS =====

    @strawberry.mutation
    async def crear_propuesta(
        self,
        info: Info[Context, None],
        input: PropuestaActividadInput
    ) -> PropuestaActividad:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            # Estado por defecto: BORRADOR
            result = await db.execute(
                select(EstadoPropuestaModel).where(EstadoPropuestaModel.codigo == "BORRADOR")
            )
            estado = result.scalar_one_or_none()
            estado_id = estado.id if estado else 1

            propuesta = PropuestaActividadModel(
                codigo=input.codigo,
                titulo=input.titulo,
                descripcion=input.descripcion,
                justificacion=input.justificacion,
                proponente_id=input.proponente_id,
                estado_id=estado_id,
                campania_id=input.campania_id,
                planificacion_id=input.planificacion_id,
                fecha_inicio_propuesta=input.fecha_inicio_propuesta,
                fecha_fin_propuesta=input.fecha_fin_propuesta,
                presupuesto_solicitado=input.presupuesto_solicitado,
                partida_id=input.partida_id,
                observaciones=input.observaciones,
                created_at=datetime.utcnow(),
                creado_por_id=user_id if user_id else None,
            )

            db.add(propuesta)
            await db.commit()
            await _refresh_propuesta(db, propuesta)

            return model_to_propuesta_actividad(propuesta)

    @strawberry.mutation
    async def presentar_propuesta(
        self,
        info: Info[Context, None],
        id: strawberry.ID
    ) -> PropuestaActividad:
        """Presenta una propuesta para revisión (cambia de BORRADOR a PENDIENTE)."""
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            result = await db.execute(
                select(PropuestaActividadModel).where(PropuestaActividadModel.id == UUID(str(id)))
            )
            propuesta = result.scalar_one_or_none()
            if not propuesta:
                raise Exception("Propuesta no encontrada")

            # Obtener estado PENDIENTE
            estado_result = await db.execute(
                select(EstadoPropuestaModel).where(EstadoPropuestaModel.codigo == "PENDIENTE")
            )
            estado = estado_result.scalar_one_or_none()
            if estado:
                propuesta.estado_id = estado.id
                propuesta.fecha_presentacion = date.today()
                propuesta.updated_at = datetime.utcnow()
                propuesta.modificado_por_id = user_id if user_id else None

            await db.commit()
            await _refresh_propuesta(db, propuesta)

            return model_to_propuesta_actividad(propuesta)

    @strawberry.mutation
    async def aprobar_propuesta(
        self,
        info: Info[Context, None],
        id: strawberry.ID,
        presupuesto_aprobado: Decimal,
        motivo: str | None = None
    ) -> PropuestaActividad:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            result = await db.execute(
                select(PropuestaActividadModel).where(PropuestaActividadModel.id == UUID(str(id)))
            )
            propuesta = result.scalar_one_or_none()
            if not propuesta:
                raise Exception("Propuesta no encontrada")

            estado_result = await db.execute(
                select(EstadoPropuestaModel).where(EstadoPropuestaModel.codigo == "APROBADA")
            )
            estado = estado_result.scalar_one_or_none()
            if estado:
                propuesta.estado_id = estado.id
                propuesta.fecha_resolucion = date.today()
                propuesta.presupuesto_aprobado = presupuesto_aprobado
                propuesta.motivo_resolucion = motivo
                propuesta.updated_at = datetime.utcnow()
                propuesta.modificado_por_id = user_id if user_id else None

            await db.commit()
            await _refresh_propuesta(db, propuesta)

            return model_to_propuesta_actividad(propuesta)

    @strawberry.mutation
    async def crear_tarea_propuesta(
        self,
        info: Info[Context, None],
        input: TareaPropuestaInput
    ) -> TareaPropuesta:
        async with await info.context.get_db() as db:
            tarea = TareaPropuestaModel(
                propuesta_id=input.propuesta_id,
                nombre=input.nombre,
                descripcion=input.descripcion,
                orden=input.orden,
                grupo_trabajo_id=input.grupo_trabajo_id,
                responsable_id=input.responsable_id,
                fecha_inicio_estimada=input.fecha_inicio_estimada,
                fecha_fin_estimada=input.fecha_fin_estimada,
                horas_estimadas=input.horas_estimadas,
            )

            db.add(tarea)
            await db.commit()
            await db.refresh(tarea, ["grupo_trabajo", "responsable"])
            if tarea.responsable:
                await db.refresh(tarea.responsable, ["tipo_miembro", "agrupacion"])

            return model_to_tarea_propuesta(tarea)

    @strawberry.mutation
    async def crear_recurso_propuesta(
        self,
        info: Info[Context, None],
        input: RecursoPropuestaInput
    ) -> RecursoPropuesta:
        async with await info.context.get_db() as db:
            importe_total = input.importe_unitario_estimado * input.cantidad

            recurso = RecursoPropuestaModel(
                propuesta_id=input.propuesta_id,
                tipo_recurso_id=input.tipo_recurso_id,
                descripcion=input.descripcion,
                cantidad=input.cantidad,
                importe_unitario_estimado=input.importe_unitario_estimado,
                importe_total_estimado=importe_total,
                proveedor=input.proveedor,
                observaciones=input.observaciones,
            )

            db.add(recurso)
            await db.commit()
            await db.refresh(recurso, ["tipo_recurso"])

            return model_to_recurso_propuesta(recurso)

    # ===== ACTIVIDADES =====

    @strawberry.mutation
    async def crear_actividad(
        self,
        info: Info[Context, None],
        input: ActividadInput
    ) -> Actividad:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            # Estado por defecto: PLANIFICADA
            result = await db.execute(
                select(EstadoActividadModel).where(EstadoActividadModel.codigo == "PLANIFICADA")
            )
            estado = result.scalar_one_or_none()
            estado_id = estado.id if estado else 1

            actividad = ActividadModel(
                codigo=input.codigo,
                nombre=input.nombre,
                descripcion=input.descripcion,
                propuesta_id=input.propuesta_id,
                tipo_actividad_id=input.tipo_actividad_id,
                estado_id=estado_id,
                prioridad=input.prioridad,
                fecha_inicio=input.fecha_inicio,
                fecha_fin=input.fecha_fin,
                hora_inicio=input.hora_inicio,
                hora_fin=input.hora_fin,
                es_todo_el_dia=input.es_todo_el_dia,
                lugar=input.lugar,
                direccion=input.direccion,
                es_online=input.es_online,
                url_online=input.url_online,
                campania_id=input.campania_id,
                planificacion_id=input.planificacion_id,
                coordinador_id=input.coordinador_id,
                es_colectiva=input.es_colectiva,
                partida_id=input.partida_id,
                dotacion_economica=input.dotacion_economica,
                voluntarios_necesarios=input.voluntarios_necesarios,
                observaciones=input.observaciones,
                created_at=datetime.utcnow(),
                creado_por_id=user_id if user_id else None,
            )

            db.add(actividad)
            await db.commit()
            await _refresh_actividad(db, actividad)

            return model_to_actividad(actividad)

    @strawberry.mutation
    async def actualizar_actividad(
        self,
        info: Info[Context, None],
        id: strawberry.ID,
        input: ActividadUpdateInput
    ) -> Actividad:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            result = await db.execute(
                select(ActividadModel).where(ActividadModel.id == UUID(str(id)))
            )
            actividad = result.scalar_one_or_none()
            if not actividad:
                raise Exception("Actividad no encontrada")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(actividad, field, value)

            # Si se marca como completada, registrar fecha
            if input.completada and not actividad.fecha_completada:
                actividad.fecha_completada = datetime.utcnow()

            actividad.updated_at = datetime.utcnow()
            actividad.modificado_por_id = user_id if user_id else None

            await db.commit()
            await _refresh_actividad(db, actividad)

            return model_to_actividad(actividad)

    @strawberry.mutation
    async def crear_tarea_actividad(
        self,
        info: Info[Context, None],
        input: TareaActividadInput
    ) -> TareaActividad:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            # Estado por defecto: PENDIENTE
            result = await db.execute(
                select(EstadoTareaModel).where(EstadoTareaModel.codigo == "PENDIENTE")
            )
            estado = result.scalar_one_or_none()
            estado_id = estado.id if estado else 1

            tarea = TareaActividadModel(
                actividad_id=input.actividad_id,
                nombre=input.nombre,
                descripcion=input.descripcion,
                orden=input.orden,
                grupo_trabajo_id=input.grupo_trabajo_id,
                responsable_id=input.responsable_id,
                estado_id=estado_id,
                fecha_limite=input.fecha_limite,
                horas_estimadas=input.horas_estimadas,
                created_at=datetime.utcnow(),
                creado_por_id=user_id if user_id else None,
            )

            db.add(tarea)
            await db.commit()
            await db.refresh(tarea, ["grupo_trabajo", "responsable", "estado"])
            if tarea.responsable:
                await db.refresh(tarea.responsable, ["tipo_miembro", "agrupacion"])

            return model_to_tarea_actividad(tarea)

    @strawberry.mutation
    async def inscribir_participante_actividad(
        self,
        info: Info[Context, None],
        input: ParticipanteActividadInput
    ) -> ParticipanteActividad:
        async with await info.context.get_db() as db:
            participante = ParticipanteActividadModel(
                actividad_id=input.actividad_id,
                miembro_id=input.miembro_id,
                rol=input.rol,
                observaciones=input.observaciones,
            )

            db.add(participante)
            await db.commit()
            await db.refresh(participante, ["miembro"])
            await db.refresh(participante.miembro, ["tipo_miembro", "agrupacion"])

            return model_to_participante_actividad(participante)

    @strawberry.mutation
    async def confirmar_participante_actividad(
        self,
        info: Info[Context, None],
        actividad_id: strawberry.ID,
        miembro_id: strawberry.ID
    ) -> ParticipanteActividad:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(ParticipanteActividadModel).where(
                    and_(
                        ParticipanteActividadModel.actividad_id == UUID(str(actividad_id)),
                        ParticipanteActividadModel.miembro_id == UUID(str(miembro_id))
                    )
                )
            )
            participante = result.scalar_one_or_none()
            if not participante:
                raise Exception("Participante no encontrado")

            participante.confirmado = True
            await db.commit()
            await db.refresh(participante, ["miembro"])
            await db.refresh(participante.miembro, ["tipo_miembro", "agrupacion"])

            # Actualizar contador de voluntarios confirmados en la actividad
            act_result = await db.execute(
                select(ActividadModel).where(ActividadModel.id == UUID(str(actividad_id)))
            )
            actividad = act_result.scalar_one_or_none()
            if actividad:
                actividad.voluntarios_confirmados = (actividad.voluntarios_confirmados or 0) + 1
                await db.commit()

            return model_to_participante_actividad(participante)

    # ===== KPIs =====

    @strawberry.mutation
    async def crear_kpi(
        self,
        info: Info[Context, None],
        input: KPIInput
    ) -> KPI:
        async with await info.context.get_db() as db:
            kpi = KPIModel(
                codigo=input.codigo,
                nombre=input.nombre,
                descripcion=input.descripcion,
                tipo_kpi_id=input.tipo_kpi_id,
                unidad=input.unidad,
                valor_objetivo_defecto=input.valor_objetivo_defecto,
                valor_minimo=input.valor_minimo,
                formula=input.formula,
            )

            db.add(kpi)
            await db.commit()
            await db.refresh(kpi, ["tipo_kpi"])

            return model_to_kpi(kpi)

    @strawberry.mutation
    async def asignar_kpi_actividad(
        self,
        info: Info[Context, None],
        input: KPIActividadInput
    ) -> KPIActividad:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            kpi_act = KPIActividadModel(
                actividad_id=input.actividad_id,
                kpi_id=input.kpi_id,
                valor_objetivo=input.valor_objetivo,
                peso=input.peso,
                observaciones=input.observaciones,
                created_at=datetime.utcnow(),
                creado_por_id=user_id if user_id else None,
            )

            db.add(kpi_act)
            await db.commit()
            await db.refresh(kpi_act, ["kpi", "mediciones"])
            await db.refresh(kpi_act.kpi, ["tipo_kpi"])

            return model_to_kpi_actividad(kpi_act)

    @strawberry.mutation
    async def registrar_medicion_kpi(
        self,
        info: Info[Context, None],
        input: MedicionKPIInput
    ) -> MedicionKPI:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            medicion = MedicionKPIModel(
                kpi_actividad_id=input.kpi_actividad_id,
                valor_medido=input.valor_medido,
                fecha_medicion=datetime.utcnow(),
                medido_por_id=input.medido_por_id,
                observaciones=input.observaciones,
                created_at=datetime.utcnow(),
                creado_por_id=user_id if user_id else None,
            )

            db.add(medicion)
            await db.flush()

            # Actualizar valor_actual y porcentaje_logro en kpi_actividad
            kpi_act_result = await db.execute(
                select(KPIActividadModel).where(KPIActividadModel.id == input.kpi_actividad_id)
            )
            kpi_act = kpi_act_result.scalar_one_or_none()
            if kpi_act:
                kpi_act.valor_actual = input.valor_medido
                kpi_act.fecha_ultima_medicion = medicion.fecha_medicion
                if kpi_act.valor_objetivo and kpi_act.valor_objetivo > 0:
                    kpi_act.porcentaje_logro = (input.valor_medido / kpi_act.valor_objetivo) * 100
                kpi_act.updated_at = datetime.utcnow()

            await db.commit()
            await db.refresh(medicion, ["medido_por"])
            if medicion.medido_por:
                await db.refresh(medicion.medido_por, ["tipo_miembro", "agrupacion"])

            return model_to_medicion_kpi(medicion)
