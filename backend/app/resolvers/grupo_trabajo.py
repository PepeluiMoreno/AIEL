import strawberry
from strawberry.types import Info
from sqlalchemy import select
from sqlalchemy.orm import selectinload
from datetime import date, datetime

from ..core.context import Context
from ..models.grupo_trabajo import (
    TipoGrupo as TipoGrupoModel,
    RolGrupo as RolGrupoModel,
    EstadoTarea as EstadoTareaModel,
    GrupoTrabajo as GrupoTrabajoModel,
    MiembroGrupo as MiembroGrupoModel,
    TareaGrupo as TareaGrupoModel,
    ReunionGrupo as ReunionGrupoModel,
    AsistenteReunion as AsistenteReunionModel,
)
from ..schemas.grupo_trabajo import (
    TipoGrupo, RolGrupo, EstadoTarea, GrupoTrabajo, MiembroGrupo,
    TareaGrupo, ReunionGrupo, AsistenteReunion,
    GrupoTrabajoInput, GrupoTrabajoUpdateInput,
    MiembroGrupoInput, MiembroGrupoUpdateInput,
    TareaGrupoInput, TareaGrupoUpdateInput,
    ReunionGrupoInput, ReunionGrupoUpdateInput,
)
from ..schemas.tipos_base import AgrupacionTerritorial
from ..schemas.miembro import Miembro
from ..schemas.usuario import Usuario
from ..schemas.campania import Campania, TipoCampania, EstadoCampania


# --- Funciones de conversión ---

def model_to_tipo_grupo(m: TipoGrupoModel) -> TipoGrupo:
    return TipoGrupo(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, es_permanente=m.es_permanente, activo=m.activo
    )


def model_to_rol_grupo(m: RolGrupoModel) -> RolGrupo:
    return RolGrupo(
        id=m.id, codigo=m.codigo, nombre=m.nombre, descripcion=m.descripcion,
        es_coordinador=m.es_coordinador, puede_editar=m.puede_editar,
        puede_aprobar_gastos=m.puede_aprobar_gastos, activo=m.activo
    )


def model_to_estado_tarea(m: EstadoTareaModel) -> EstadoTarea:
    return EstadoTarea(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        orden=m.orden, color=m.color, es_final=m.es_final, activo=m.activo
    )


def model_to_miembro_simple(m) -> Miembro:
    from ..schemas.tipos_base import TipoMiembro, Provincia, Pais
    tipo = TipoMiembro(
        id=m.tipo_miembro.id, codigo=m.tipo_miembro.codigo,
        nombre=m.tipo_miembro.nombre, requiere_cuota=m.tipo_miembro.requiere_cuota,
        activo=m.tipo_miembro.activo
    )
    agrup = None
    if m.agrupacion:
        agrup = AgrupacionTerritorial(
            id=m.agrupacion.id, codigo=m.agrupacion.codigo, nombre=m.agrupacion.nombre,
            email_coordinador=m.agrupacion.email_coordinador,
            email_secretario=m.agrupacion.email_secretario,
            email_tesorero=m.agrupacion.email_tesorero,
            activo=m.agrupacion.activo
        )
    prov = Provincia(codprov=m.provincia.codprov, nombre=m.provincia.nombre) if m.provincia else None
    pais = Pais(codpais=m.pais_domicilio.codpais, nombre=m.pais_domicilio.nombre) if m.pais_domicilio else None
    return Miembro(
        id=m.id, nombre=m.nombre, apellido1=m.apellido1, apellido2=m.apellido2,
        fecha_nacimiento=m.fecha_nacimiento, tipo_documento=m.tipo_documento,
        numero_documento=m.numero_documento, direccion=m.direccion,
        codigo_postal=m.codigo_postal, localidad=m.localidad,
        telefono=m.telefono, iban=m.iban, fecha_alta=m.fecha_alta,
        fecha_baja=m.fecha_baja, tipo_miembro=tipo, agrupacion=agrup,
        provincia=prov, pais_domicilio=pais
    )


def model_to_usuario_simple(u) -> Usuario:
    return Usuario(
        id=u.id, email=u.email, activo=u.activo,
        created_at=u.created_at, last_login=u.last_login, roles=[]
    )


def model_to_asistente_reunion(m: AsistenteReunionModel) -> AsistenteReunion:
    return AsistenteReunion(
        miembro=model_to_miembro_simple(m.miembro),
        confirmado=m.confirmado, asistio=m.asistio, observaciones=m.observaciones
    )


def model_to_reunion_grupo(m: ReunionGrupoModel) -> ReunionGrupo:
    return ReunionGrupo(
        id=m.id, titulo=m.titulo, descripcion=m.descripcion,
        fecha=m.fecha, hora_inicio=m.hora_inicio, hora_fin=m.hora_fin,
        lugar=m.lugar, url_online=m.url_online,
        orden_del_dia=m.orden_del_dia, acta=m.acta, realizada=m.realizada,
        asistentes=[model_to_asistente_reunion(a) for a in m.asistentes]
    )


def model_to_tarea_grupo(m: TareaGrupoModel) -> TareaGrupo:
    asignado = model_to_miembro_simple(m.asignado_a) if m.asignado_a else None
    return TareaGrupo(
        id=m.id, titulo=m.titulo, descripcion=m.descripcion,
        asignado_a=asignado, estado=model_to_estado_tarea(m.estado),
        prioridad=m.prioridad, fecha_creacion=m.fecha_creacion,
        fecha_limite=m.fecha_limite, fecha_completada=m.fecha_completada,
        horas_estimadas=m.horas_estimadas, horas_reales=m.horas_reales
    )


def model_to_miembro_grupo(m: MiembroGrupoModel) -> MiembroGrupo:
    return MiembroGrupo(
        miembro=model_to_miembro_simple(m.miembro),
        rol_grupo=model_to_rol_grupo(m.rol_grupo),
        fecha_incorporacion=m.fecha_incorporacion, fecha_baja=m.fecha_baja,
        activo=m.activo, responsabilidades=m.responsabilidades,
        observaciones=m.observaciones
    )


def model_to_campania_simple(c) -> Campania | None:
    if c is None:
        return None
    tipo = TipoCampania(
        id=c.tipo_campania.id, codigo=c.tipo_campania.codigo,
        nombre=c.tipo_campania.nombre, descripcion=c.tipo_campania.descripcion,
        activo=c.tipo_campania.activo
    )
    estado = EstadoCampania(
        id=c.estado_campania.id, codigo=c.estado_campania.codigo,
        nombre=c.estado_campania.nombre, orden=c.estado_campania.orden,
        color=c.estado_campania.color, activo=c.estado_campania.activo
    )
    return Campania(
        id=c.id, codigo=c.codigo, nombre=c.nombre,
        descripcion_corta=c.descripcion_corta, descripcion_larga=c.descripcion_larga,
        tipo_campania=tipo, estado_campania=estado,
        fecha_inicio_plan=c.fecha_inicio_plan, fecha_fin_plan=c.fecha_fin_plan,
        fecha_inicio_real=c.fecha_inicio_real, fecha_fin_real=c.fecha_fin_real,
        objetivo_principal=c.objetivo_principal, meta_recaudacion=c.meta_recaudacion,
        meta_participantes=c.meta_participantes, responsable=None, agrupacion=None,
        created_at=c.created_at, creador=model_to_usuario_simple(c.creador),
        updated_at=c.updated_at, acciones=[], participantes=[]
    )


def model_to_grupo_trabajo(m: GrupoTrabajoModel) -> GrupoTrabajo:
    agrup = None
    if m.agrupacion:
        agrup = AgrupacionTerritorial(
            id=m.agrupacion.id, codigo=m.agrupacion.codigo, nombre=m.agrupacion.nombre,
            email_coordinador=m.agrupacion.email_coordinador,
            email_secretario=m.agrupacion.email_secretario,
            email_tesorero=m.agrupacion.email_tesorero,
            activo=m.agrupacion.activo
        )

    return GrupoTrabajo(
        id=m.id, codigo=m.codigo, nombre=m.nombre, descripcion=m.descripcion,
        tipo_grupo=model_to_tipo_grupo(m.tipo_grupo),
        campania=model_to_campania_simple(m.campania),
        fecha_inicio=m.fecha_inicio, fecha_fin=m.fecha_fin, objetivo=m.objetivo,
        presupuesto_asignado=m.presupuesto_asignado,
        presupuesto_ejecutado=m.presupuesto_ejecutado,
        activo=m.activo, agrupacion=agrup,
        created_at=m.created_at, creador=model_to_usuario_simple(m.creador),
        updated_at=m.updated_at,
        miembros=[model_to_miembro_grupo(mg) for mg in m.miembros],
        tareas=[model_to_tarea_grupo(t) for t in m.tareas],
        reuniones=[model_to_reunion_grupo(r) for r in m.reuniones]
    )


# --- Queries ---

@strawberry.type
class GrupoTrabajoQuery:
    @strawberry.field
    async def tipos_grupo(self, info: Info[Context, None]) -> list[TipoGrupo]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(TipoGrupoModel).where(TipoGrupoModel.activo == True)
            )
            return [model_to_tipo_grupo(t) for t in result.scalars()]

    @strawberry.field
    async def roles_grupo(self, info: Info[Context, None]) -> list[RolGrupo]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(RolGrupoModel).where(RolGrupoModel.activo == True)
            )
            return [model_to_rol_grupo(r) for r in result.scalars()]

    @strawberry.field
    async def estados_tarea(self, info: Info[Context, None]) -> list[EstadoTarea]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(EstadoTareaModel).where(EstadoTareaModel.activo == True).order_by(EstadoTareaModel.orden)
            )
            return [model_to_estado_tarea(e) for e in result.scalars()]

    @strawberry.field
    async def grupo_trabajo(
        self, info: Info[Context, None], id: int | None = None, codigo: str | None = None
    ) -> GrupoTrabajo | None:
        if not id and not codigo:
            raise Exception("Debe proporcionar id o codigo")

        async with await info.context.get_db() as db:
            query = select(GrupoTrabajoModel).options(
                selectinload(GrupoTrabajoModel.tipo_grupo),
                selectinload(GrupoTrabajoModel.campania),
                selectinload(GrupoTrabajoModel.agrupacion),
                selectinload(GrupoTrabajoModel.creador),
                selectinload(GrupoTrabajoModel.miembros).selectinload(MiembroGrupoModel.miembro),
                selectinload(GrupoTrabajoModel.miembros).selectinload(MiembroGrupoModel.rol_grupo),
                selectinload(GrupoTrabajoModel.tareas).selectinload(TareaGrupoModel.estado),
                selectinload(GrupoTrabajoModel.tareas).selectinload(TareaGrupoModel.asignado_a),
                selectinload(GrupoTrabajoModel.reuniones).selectinload(ReunionGrupoModel.asistentes),
            )

            if id:
                query = query.where(GrupoTrabajoModel.id == id)
            else:
                query = query.where(GrupoTrabajoModel.codigo == codigo)

            result = await db.execute(query)
            grupo = result.scalar_one_or_none()
            if not grupo:
                return None

            # Cargar relaciones anidadas
            for mg in grupo.miembros:
                await db.refresh(mg.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            for t in grupo.tareas:
                if t.asignado_a:
                    await db.refresh(t.asignado_a, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            for r in grupo.reuniones:
                for a in r.asistentes:
                    await db.refresh(a.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            if grupo.campania:
                await db.refresh(grupo.campania, ["tipo_campania", "estado_campania", "creador"])

            return model_to_grupo_trabajo(grupo)

    @strawberry.field
    async def grupos_trabajo(
        self, info: Info[Context, None],
        tipo_grupo_id: int | None = None,
        campania_id: int | None = None,
        solo_permanentes: bool | None = None,
        solo_activos: bool = True,
        agrupacion_id: int | None = None,
        limite: int = 100,
        offset: int = 0,
    ) -> list[GrupoTrabajo]:
        async with await info.context.get_db() as db:
            query = select(GrupoTrabajoModel).options(
                selectinload(GrupoTrabajoModel.tipo_grupo),
                selectinload(GrupoTrabajoModel.campania),
                selectinload(GrupoTrabajoModel.agrupacion),
                selectinload(GrupoTrabajoModel.creador),
                selectinload(GrupoTrabajoModel.miembros).selectinload(MiembroGrupoModel.miembro),
                selectinload(GrupoTrabajoModel.miembros).selectinload(MiembroGrupoModel.rol_grupo),
                selectinload(GrupoTrabajoModel.tareas).selectinload(TareaGrupoModel.estado),
                selectinload(GrupoTrabajoModel.reuniones),
            )

            if solo_activos:
                query = query.where(GrupoTrabajoModel.activo == True)
            if tipo_grupo_id:
                query = query.where(GrupoTrabajoModel.tipo_grupo_id == tipo_grupo_id)
            if campania_id:
                query = query.where(GrupoTrabajoModel.campania_id == campania_id)
            if agrupacion_id:
                query = query.where(GrupoTrabajoModel.agrupacion_id == agrupacion_id)
            if solo_permanentes is not None:
                query = query.join(TipoGrupoModel).where(TipoGrupoModel.es_permanente == solo_permanentes)

            query = query.limit(limite).offset(offset)
            result = await db.execute(query)

            grupos = []
            for g in result.scalars():
                for mg in g.miembros:
                    await db.refresh(mg.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                if g.campania:
                    await db.refresh(g.campania, ["tipo_campania", "estado_campania", "creador"])
                grupos.append(model_to_grupo_trabajo(g))

            return grupos

    @strawberry.field
    async def grupos_por_miembro(
        self, info: Info[Context, None], miembro_id: int, solo_activos: bool = True
    ) -> list[GrupoTrabajo]:
        async with await info.context.get_db() as db:
            query = select(GrupoTrabajoModel).join(MiembroGrupoModel).options(
                selectinload(GrupoTrabajoModel.tipo_grupo),
                selectinload(GrupoTrabajoModel.campania),
                selectinload(GrupoTrabajoModel.agrupacion),
                selectinload(GrupoTrabajoModel.creador),
                selectinload(GrupoTrabajoModel.miembros).selectinload(MiembroGrupoModel.miembro),
                selectinload(GrupoTrabajoModel.miembros).selectinload(MiembroGrupoModel.rol_grupo),
            ).where(MiembroGrupoModel.miembro_id == miembro_id)

            if solo_activos:
                query = query.where(MiembroGrupoModel.activo == True)

            result = await db.execute(query)

            grupos = []
            for g in result.scalars():
                for mg in g.miembros:
                    await db.refresh(mg.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                if g.campania:
                    await db.refresh(g.campania, ["tipo_campania", "estado_campania", "creador"])
                grupos.append(model_to_grupo_trabajo(g))

            return grupos

    @strawberry.field
    async def tareas_pendientes_miembro(
        self, info: Info[Context, None], miembro_id: int, limite: int = 50
    ) -> list[TareaGrupo]:
        async with await info.context.get_db() as db:
            query = select(TareaGrupoModel).join(EstadoTareaModel).options(
                selectinload(TareaGrupoModel.estado),
                selectinload(TareaGrupoModel.asignado_a),
            ).where(
                TareaGrupoModel.asignado_a_id == miembro_id,
                EstadoTareaModel.es_final == False
            ).order_by(TareaGrupoModel.prioridad, TareaGrupoModel.fecha_limite).limit(limite)

            result = await db.execute(query)
            tareas = []
            for t in result.scalars():
                if t.asignado_a:
                    await db.refresh(t.asignado_a, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                tareas.append(model_to_tarea_grupo(t))

            return tareas

    @strawberry.field
    async def proximas_reuniones(
        self, info: Info[Context, None], dias: int = 30, grupo_id: int | None = None
    ) -> list[ReunionGrupo]:
        from datetime import timedelta
        async with await info.context.get_db() as db:
            fecha_limite = date.today() + timedelta(days=dias)
            query = select(ReunionGrupoModel).options(
                selectinload(ReunionGrupoModel.asistentes).selectinload(AsistenteReunionModel.miembro),
            ).where(
                ReunionGrupoModel.fecha >= date.today(),
                ReunionGrupoModel.fecha <= fecha_limite,
                ReunionGrupoModel.realizada == False
            ).order_by(ReunionGrupoModel.fecha, ReunionGrupoModel.hora_inicio)

            if grupo_id:
                query = query.where(ReunionGrupoModel.grupo_id == grupo_id)

            result = await db.execute(query)
            reuniones = []
            for r in result.scalars():
                for a in r.asistentes:
                    await db.refresh(a.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                reuniones.append(model_to_reunion_grupo(r))

            return reuniones


# --- Mutations ---

@strawberry.type
class GrupoTrabajoMutation:
    @strawberry.mutation
    async def crear_grupo_trabajo(
        self, info: Info[Context, None], input: GrupoTrabajoInput
    ) -> GrupoTrabajo:
        user_id = info.context.user_id
        if not user_id:
            raise Exception("No autenticado")

        async with await info.context.get_db() as db:
            grupo = GrupoTrabajoModel(
                codigo=input.codigo,
                nombre=input.nombre,
                descripcion=input.descripcion,
                tipo_grupo_id=input.tipo_grupo_id,
                campania_id=input.campania_id,
                fecha_inicio=input.fecha_inicio,
                fecha_fin=input.fecha_fin,
                objetivo=input.objetivo,
                presupuesto_asignado=input.presupuesto_asignado,
                agrupacion_id=input.agrupacion_id,
                created_by_id=user_id,
            )
            db.add(grupo)
            await db.commit()
            await db.refresh(grupo, [
                "tipo_grupo", "campania", "agrupacion", "creador",
                "miembros", "tareas", "reuniones"
            ])
            if grupo.campania:
                await db.refresh(grupo.campania, ["tipo_campania", "estado_campania", "creador"])
            return model_to_grupo_trabajo(grupo)

    @strawberry.mutation
    async def actualizar_grupo_trabajo(
        self, info: Info[Context, None], id: int, input: GrupoTrabajoUpdateInput
    ) -> GrupoTrabajo:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(GrupoTrabajoModel).where(GrupoTrabajoModel.id == id)
            )
            grupo = result.scalar_one_or_none()
            if not grupo:
                raise Exception("Grupo no encontrado")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(grupo, field, value)

            await db.commit()
            await db.refresh(grupo, [
                "tipo_grupo", "campania", "agrupacion", "creador",
                "miembros", "tareas", "reuniones"
            ])
            for mg in grupo.miembros:
                await db.refresh(mg.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                await db.refresh(mg, ["rol_grupo"])
            if grupo.campania:
                await db.refresh(grupo.campania, ["tipo_campania", "estado_campania", "creador"])
            return model_to_grupo_trabajo(grupo)

    @strawberry.mutation
    async def agregar_miembro_grupo(
        self, info: Info[Context, None], input: MiembroGrupoInput
    ) -> MiembroGrupo:
        async with await info.context.get_db() as db:
            # Verificar si ya existe
            result = await db.execute(
                select(MiembroGrupoModel).where(
                    MiembroGrupoModel.grupo_id == input.grupo_id,
                    MiembroGrupoModel.miembro_id == input.miembro_id
                )
            )
            existing = result.scalar_one_or_none()
            if existing:
                raise Exception("El miembro ya pertenece a este grupo")

            mg = MiembroGrupoModel(
                grupo_id=input.grupo_id,
                miembro_id=input.miembro_id,
                rol_grupo_id=input.rol_grupo_id,
                responsabilidades=input.responsabilidades,
                observaciones=input.observaciones,
            )
            db.add(mg)
            await db.commit()
            await db.refresh(mg, ["miembro", "rol_grupo"])
            await db.refresh(mg.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_miembro_grupo(mg)

    @strawberry.mutation
    async def actualizar_miembro_grupo(
        self, info: Info[Context, None],
        grupo_id: int, miembro_id: int, input: MiembroGrupoUpdateInput
    ) -> MiembroGrupo:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(MiembroGrupoModel).where(
                    MiembroGrupoModel.grupo_id == grupo_id,
                    MiembroGrupoModel.miembro_id == miembro_id
                )
            )
            mg = result.scalar_one_or_none()
            if not mg:
                raise Exception("Miembro no encontrado en el grupo")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(mg, field, value)

            await db.commit()
            await db.refresh(mg, ["miembro", "rol_grupo"])
            await db.refresh(mg.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_miembro_grupo(mg)

    @strawberry.mutation
    async def dar_baja_miembro_grupo(
        self, info: Info[Context, None], grupo_id: int, miembro_id: int
    ) -> bool:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(MiembroGrupoModel).where(
                    MiembroGrupoModel.grupo_id == grupo_id,
                    MiembroGrupoModel.miembro_id == miembro_id
                )
            )
            mg = result.scalar_one_or_none()
            if not mg:
                raise Exception("Miembro no encontrado en el grupo")

            mg.activo = False
            mg.fecha_baja = date.today()
            await db.commit()
            return True

    @strawberry.mutation
    async def crear_tarea_grupo(
        self, info: Info[Context, None], input: TareaGrupoInput
    ) -> TareaGrupo:
        user_id = info.context.user_id
        if not user_id:
            raise Exception("No autenticado")

        async with await info.context.get_db() as db:
            # Obtener estado PENDIENTE por defecto
            result = await db.execute(
                select(EstadoTareaModel).where(EstadoTareaModel.codigo == "PENDIENTE")
            )
            estado_pendiente = result.scalar_one_or_none()
            if not estado_pendiente:
                raise Exception("Estado PENDIENTE no configurado")

            tarea = TareaGrupoModel(
                grupo_id=input.grupo_id,
                titulo=input.titulo,
                descripcion=input.descripcion,
                asignado_a_id=input.asignado_a_id,
                estado_id=estado_pendiente.id,
                prioridad=input.prioridad,
                fecha_limite=input.fecha_limite,
                horas_estimadas=input.horas_estimadas,
                creado_por_id=user_id,
            )
            db.add(tarea)
            await db.commit()
            await db.refresh(tarea, ["estado", "asignado_a"])
            if tarea.asignado_a:
                await db.refresh(tarea.asignado_a, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_tarea_grupo(tarea)

    @strawberry.mutation
    async def actualizar_tarea_grupo(
        self, info: Info[Context, None], id: int, input: TareaGrupoUpdateInput
    ) -> TareaGrupo:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(TareaGrupoModel).where(TareaGrupoModel.id == id)
            )
            tarea = result.scalar_one_or_none()
            if not tarea:
                raise Exception("Tarea no encontrada")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(tarea, field, value)

            # Si se cambia a estado final, registrar fecha completada
            if input.estado_id:
                result = await db.execute(
                    select(EstadoTareaModel).where(EstadoTareaModel.id == input.estado_id)
                )
                nuevo_estado = result.scalar_one_or_none()
                if nuevo_estado and nuevo_estado.es_final:
                    tarea.fecha_completada = datetime.utcnow()

            await db.commit()
            await db.refresh(tarea, ["estado", "asignado_a"])
            if tarea.asignado_a:
                await db.refresh(tarea.asignado_a, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_tarea_grupo(tarea)

    @strawberry.mutation
    async def crear_reunion_grupo(
        self, info: Info[Context, None], input: ReunionGrupoInput
    ) -> ReunionGrupo:
        user_id = info.context.user_id
        if not user_id:
            raise Exception("No autenticado")

        async with await info.context.get_db() as db:
            reunion = ReunionGrupoModel(
                grupo_id=input.grupo_id,
                titulo=input.titulo,
                descripcion=input.descripcion,
                fecha=input.fecha,
                hora_inicio=input.hora_inicio,
                hora_fin=input.hora_fin,
                lugar=input.lugar,
                url_online=input.url_online,
                orden_del_dia=input.orden_del_dia,
                created_by_id=user_id,
            )
            db.add(reunion)
            await db.commit()
            await db.refresh(reunion, ["asistentes"])
            return model_to_reunion_grupo(reunion)

    @strawberry.mutation
    async def actualizar_reunion_grupo(
        self, info: Info[Context, None], id: int, input: ReunionGrupoUpdateInput
    ) -> ReunionGrupo:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(ReunionGrupoModel).where(ReunionGrupoModel.id == id)
            )
            reunion = result.scalar_one_or_none()
            if not reunion:
                raise Exception("Reunión no encontrada")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(reunion, field, value)

            await db.commit()
            await db.refresh(reunion, ["asistentes"])
            for a in reunion.asistentes:
                await db.refresh(a.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_reunion_grupo(reunion)

    @strawberry.mutation
    async def confirmar_asistencia_reunion(
        self, info: Info[Context, None],
        reunion_id: int, miembro_id: int, confirmado: bool = True
    ) -> AsistenteReunion:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(AsistenteReunionModel).where(
                    AsistenteReunionModel.reunion_id == reunion_id,
                    AsistenteReunionModel.miembro_id == miembro_id
                )
            )
            asistente = result.scalar_one_or_none()

            if not asistente:
                # Crear nuevo registro de asistente
                asistente = AsistenteReunionModel(
                    reunion_id=reunion_id,
                    miembro_id=miembro_id,
                    confirmado=confirmado,
                )
                db.add(asistente)
            else:
                asistente.confirmado = confirmado

            await db.commit()
            await db.refresh(asistente, ["miembro"])
            await db.refresh(asistente.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_asistente_reunion(asistente)

    @strawberry.mutation
    async def registrar_asistencia_reunion(
        self, info: Info[Context, None],
        reunion_id: int, miembro_id: int, asistio: bool
    ) -> AsistenteReunion:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(AsistenteReunionModel).where(
                    AsistenteReunionModel.reunion_id == reunion_id,
                    AsistenteReunionModel.miembro_id == miembro_id
                )
            )
            asistente = result.scalar_one_or_none()

            if not asistente:
                asistente = AsistenteReunionModel(
                    reunion_id=reunion_id,
                    miembro_id=miembro_id,
                    asistio=asistio,
                )
                db.add(asistente)
            else:
                asistente.asistio = asistio

            await db.commit()
            await db.refresh(asistente, ["miembro"])
            await db.refresh(asistente.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_asistente_reunion(asistente)
