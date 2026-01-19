import strawberry
from strawberry.types import Info
from sqlalchemy import select, and_
from datetime import datetime, date, timedelta

from ..core.context import Context
from ..core.permissions import requiere_auth
from ..models.campania import (
    TipoCampania as TipoCampaniaModel,
    EstadoCampania as EstadoCampaniaModel,
    Campania as CampaniaModel,
    AccionCampania as AccionCampaniaModel,
    RolParticipante as RolParticipanteModel,
    ParticipanteCampania as ParticipanteCampaniaModel,
)
from ..schemas.campania import (
    TipoCampania,
    EstadoCampania,
    Campania,
    AccionCampania,
    RolParticipante,
    ParticipanteCampania,
    CampaniaInput,
    CampaniaUpdateInput,
    AccionCampaniaInput,
    AccionCampaniaUpdateInput,
    InscripcionCampaniaInput,
)
from ..schemas.tipos_base import AgrupacionTerritorial
from ..schemas.miembro import Miembro
from ..schemas.usuario import Usuario
from .mutations import model_to_miembro


# --- Conversores Model → Schema ---

def model_to_tipo_campania(m: TipoCampaniaModel) -> TipoCampania:
    return TipoCampania(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, activo=m.activo
    )


def model_to_estado_campania(m: EstadoCampaniaModel) -> EstadoCampania:
    return EstadoCampania(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        orden=m.orden, color=m.color, activo=m.activo
    )


def model_to_rol_participante(m: RolParticipanteModel) -> RolParticipante:
    return RolParticipante(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        es_voluntario=m.es_voluntario, es_coordinador=m.es_coordinador,
        es_donante=m.es_donante, activo=m.activo
    )


def model_to_accion_campania(m: AccionCampaniaModel) -> AccionCampania:
    return AccionCampania(
        id=m.id, nombre=m.nombre, descripcion=m.descripcion,
        fecha=m.fecha, hora_inicio=m.hora_inicio, hora_fin=m.hora_fin,
        lugar=m.lugar, direccion=m.direccion,
        voluntarios_necesarios=m.voluntarios_necesarios,
        voluntarios_confirmados=m.voluntarios_confirmados,
        materiales_necesarios=m.materiales_necesarios,
        observaciones=m.observaciones, completada=m.completada
    )


def model_to_participante_campania(m: ParticipanteCampaniaModel) -> ParticipanteCampania:
    miembro = model_to_miembro(m.miembro)
    rol = model_to_rol_participante(m.rol_participante)

    return ParticipanteCampania(
        miembro=miembro, rol_participante=rol,
        horas_aportadas=m.horas_aportadas, confirmado=m.confirmado,
        asistio=m.asistio, fecha_inscripcion=m.fecha_inscripcion,
        fecha_confirmacion=m.fecha_confirmacion, observaciones=m.observaciones
    )


def model_to_campania(m: CampaniaModel, include_relations: bool = True) -> Campania:
    tipo = model_to_tipo_campania(m.tipo_campania)
    estado = model_to_estado_campania(m.estado_campania)

    responsable = None
    if m.responsable:
        responsable = model_to_miembro(m.responsable)

    agrupacion = None
    if m.agrupacion:
        agrupacion = AgrupacionTerritorial(
            id=m.agrupacion.id, codigo=m.agrupacion.codigo, nombre=m.agrupacion.nombre,
            email_coordinador=m.agrupacion.email_coordinador,
            email_secretario=m.agrupacion.email_secretario,
            email_tesorero=m.agrupacion.email_tesorero,
            activo=m.agrupacion.activo
        )

    creador = Usuario(
        id=m.creador.id, email=m.creador.email, activo=m.creador.activo,
        created_at=m.creador.created_at, last_login=m.creador.last_login, roles=[]
    )

    acciones = []
    participantes = []
    if include_relations:
        acciones = [model_to_accion_campania(a) for a in m.acciones]
        participantes = [model_to_participante_campania(p) for p in m.participantes]

    return Campania(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion_corta=m.descripcion_corta, descripcion_larga=m.descripcion_larga,
        tipo_campania=tipo, estado_campania=estado,
        fecha_inicio_plan=m.fecha_inicio_plan, fecha_fin_plan=m.fecha_fin_plan,
        fecha_inicio_real=m.fecha_inicio_real, fecha_fin_real=m.fecha_fin_real,
        objetivo_principal=m.objetivo_principal,
        meta_recaudacion=m.meta_recaudacion, meta_participantes=m.meta_participantes,
        responsable=responsable, agrupacion=agrupacion,
        created_at=m.created_at, creador=creador, updated_at=m.updated_at,
        acciones=acciones, participantes=participantes
    )


# --- Queries ---

@strawberry.type
class CampaniaQuery:
    @strawberry.field
    async def tipos_campania(self, info: Info[Context, None]) -> list[TipoCampania]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(TipoCampaniaModel).where(TipoCampaniaModel.activo == True)
            )
            return [model_to_tipo_campania(t) for t in result.scalars()]

    @strawberry.field
    async def estados_campania(self, info: Info[Context, None]) -> list[EstadoCampania]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(EstadoCampaniaModel)
                .where(EstadoCampaniaModel.activo == True)
                .order_by(EstadoCampaniaModel.orden)
            )
            return [model_to_estado_campania(e) for e in result.scalars()]

    @strawberry.field
    async def roles_participante(self, info: Info[Context, None]) -> list[RolParticipante]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(RolParticipanteModel).where(RolParticipanteModel.activo == True)
            )
            return [model_to_rol_participante(r) for r in result.scalars()]

    @strawberry.field
    async def campania(
        self,
        info: Info[Context, None],
        id: int | None = None,
        codigo: str | None = None
    ) -> Campania | None:
        if not id and not codigo:
            return None

        async with await info.context.get_db() as db:
            query = select(CampaniaModel)
            if id:
                query = query.where(CampaniaModel.id == id)
            elif codigo:
                query = query.where(CampaniaModel.codigo == codigo)

            result = await db.execute(query)
            m = result.scalar_one_or_none()
            if not m:
                return None

            # Cargar relaciones
            await db.refresh(m, [
                "tipo_campania", "estado_campania", "responsable",
                "agrupacion", "creador", "acciones", "participantes"
            ])

            # Cargar relaciones de responsable si existe
            if m.responsable:
                await db.refresh(m.responsable, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])

            # Cargar relaciones de participantes
            for p in m.participantes:
                await db.refresh(p, ["miembro", "rol_participante"])
                await db.refresh(p.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])

            return model_to_campania(m)

    @strawberry.field
    async def campanias(
        self,
        info: Info[Context, None],
        tipo_campania_id: int | None = None,
        estado_campania_id: int | None = None,
        agrupacion_id: int | None = None,
        solo_activas: bool = False,
        limite: int = 50,
        offset: int = 0,
    ) -> list[Campania]:
        async with await info.context.get_db() as db:
            query = select(CampaniaModel)

            if tipo_campania_id:
                query = query.where(CampaniaModel.tipo_campania_id == tipo_campania_id)
            if estado_campania_id:
                query = query.where(CampaniaModel.estado_campania_id == estado_campania_id)
            if agrupacion_id:
                query = query.where(CampaniaModel.agrupacion_id == agrupacion_id)
            if solo_activas:
                # Estados activos: PLANIFICADA (1) y ACTIVA (2) típicamente
                query = query.join(EstadoCampaniaModel).where(
                    EstadoCampaniaModel.codigo.in_(["PLANIFICADA", "ACTIVA"])
                )

            query = query.order_by(CampaniaModel.fecha_inicio_plan.desc())
            query = query.limit(limite).offset(offset)

            result = await db.execute(query)
            campanias = []

            for m in result.scalars():
                await db.refresh(m, [
                    "tipo_campania", "estado_campania", "responsable",
                    "agrupacion", "creador", "acciones", "participantes"
                ])
                if m.responsable:
                    await db.refresh(m.responsable, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                for p in m.participantes:
                    await db.refresh(p, ["miembro", "rol_participante"])
                    await db.refresh(p.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])

                campanias.append(model_to_campania(m))

            return campanias

    @strawberry.field
    async def acciones_proximas(
        self,
        info: Info[Context, None],
        dias: int = 30,
        limite: int = 20
    ) -> list[AccionCampania]:
        async with await info.context.get_db() as db:
            hoy = date.today()
            fecha_limite = hoy + timedelta(days=dias)

            query = select(AccionCampaniaModel).join(CampaniaModel).where(
                and_(
                    AccionCampaniaModel.fecha >= hoy,
                    AccionCampaniaModel.fecha <= fecha_limite,
                    AccionCampaniaModel.completada == False
                )
            ).order_by(AccionCampaniaModel.fecha.asc()).limit(limite)

            result = await db.execute(query)
            return [model_to_accion_campania(a) for a in result.scalars()]

    @strawberry.field
    async def campanias_por_miembro(
        self,
        info: Info[Context, None],
        miembro_id: int
    ) -> list[Campania]:
        async with await info.context.get_db() as db:
            query = select(CampaniaModel).join(
                ParticipanteCampaniaModel,
                CampaniaModel.id == ParticipanteCampaniaModel.campania_id
            ).where(
                ParticipanteCampaniaModel.miembro_id == miembro_id
            ).order_by(CampaniaModel.fecha_inicio_plan.desc())

            result = await db.execute(query)
            campanias = []

            for m in result.scalars():
                await db.refresh(m, [
                    "tipo_campania", "estado_campania", "responsable",
                    "agrupacion", "creador", "acciones", "participantes"
                ])
                if m.responsable:
                    await db.refresh(m.responsable, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                campanias.append(model_to_campania(m, include_relations=False))

            return campanias


# --- Mutations ---

@strawberry.type
class CampaniaMutation:
    @strawberry.mutation
    async def crear_campania(
        self,
        info: Info[Context, None],
        input: CampaniaInput
    ) -> Campania:
        user_id = info.context.user_id
        if not user_id:
            raise Exception("Usuario no autenticado")

        async with await info.context.get_db() as db:
            # Estado por defecto: PLANIFICADA (id=1)
            estado_id = input.estado_campania_id
            if not estado_id:
                result = await db.execute(
                    select(EstadoCampaniaModel).where(EstadoCampaniaModel.codigo == "PLANIFICADA")
                )
                estado = result.scalar_one_or_none()
                estado_id = estado.id if estado else 1

            campania = CampaniaModel(
                codigo=input.codigo,
                nombre=input.nombre,
                descripcion_corta=input.descripcion_corta,
                descripcion_larga=input.descripcion_larga,
                tipo_campania_id=input.tipo_campania_id,
                estado_campania_id=estado_id,
                fecha_inicio_plan=input.fecha_inicio_plan,
                fecha_fin_plan=input.fecha_fin_plan,
                objetivo_principal=input.objetivo_principal,
                meta_recaudacion=input.meta_recaudacion,
                meta_participantes=input.meta_participantes,
                responsable_id=input.responsable_id,
                agrupacion_id=input.agrupacion_id,
                created_by_id=user_id,
            )

            db.add(campania)
            await db.commit()
            await db.refresh(campania, [
                "tipo_campania", "estado_campania", "responsable",
                "agrupacion", "creador", "acciones", "participantes"
            ])

            if campania.responsable:
                await db.refresh(campania.responsable, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])

            return model_to_campania(campania)

    @strawberry.mutation
    async def actualizar_campania(
        self,
        info: Info[Context, None],
        id: int,
        input: CampaniaUpdateInput
    ) -> Campania:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(CampaniaModel).where(CampaniaModel.id == id)
            )
            campania = result.scalar_one_or_none()
            if not campania:
                raise Exception("Campaña no encontrada")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(campania, field, value)

            campania.updated_at = datetime.utcnow()
            await db.commit()
            await db.refresh(campania, [
                "tipo_campania", "estado_campania", "responsable",
                "agrupacion", "creador", "acciones", "participantes"
            ])

            if campania.responsable:
                await db.refresh(campania.responsable, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])

            return model_to_campania(campania)

    @strawberry.mutation
    async def cambiar_estado_campania(
        self,
        info: Info[Context, None],
        campania_id: int,
        estado_id: int
    ) -> Campania:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(CampaniaModel).where(CampaniaModel.id == campania_id)
            )
            campania = result.scalar_one_or_none()
            if not campania:
                raise Exception("Campaña no encontrada")

            campania.estado_campania_id = estado_id
            campania.updated_at = datetime.utcnow()

            # Si se activa, registrar fecha inicio real
            estado_result = await db.execute(
                select(EstadoCampaniaModel).where(EstadoCampaniaModel.id == estado_id)
            )
            estado = estado_result.scalar_one_or_none()
            if estado and estado.codigo == "ACTIVA" and not campania.fecha_inicio_real:
                campania.fecha_inicio_real = date.today()
            elif estado and estado.codigo == "FINALIZADA" and not campania.fecha_fin_real:
                campania.fecha_fin_real = date.today()

            await db.commit()
            await db.refresh(campania, [
                "tipo_campania", "estado_campania", "responsable",
                "agrupacion", "creador", "acciones", "participantes"
            ])

            return model_to_campania(campania)

    @strawberry.mutation
    async def crear_accion_campania(
        self,
        info: Info[Context, None],
        campania_id: int,
        input: AccionCampaniaInput
    ) -> AccionCampania:
        async with await info.context.get_db() as db:
            # Verificar que la campaña existe
            result = await db.execute(
                select(CampaniaModel).where(CampaniaModel.id == campania_id)
            )
            if not result.scalar_one_or_none():
                raise Exception("Campaña no encontrada")

            accion = AccionCampaniaModel(
                campania_id=campania_id,
                nombre=input.nombre,
                descripcion=input.descripcion,
                fecha=input.fecha,
                hora_inicio=input.hora_inicio,
                hora_fin=input.hora_fin,
                lugar=input.lugar,
                direccion=input.direccion,
                voluntarios_necesarios=input.voluntarios_necesarios,
                materiales_necesarios=input.materiales_necesarios,
                observaciones=input.observaciones,
            )

            db.add(accion)
            await db.commit()
            await db.refresh(accion)

            return model_to_accion_campania(accion)

    @strawberry.mutation
    async def actualizar_accion_campania(
        self,
        info: Info[Context, None],
        id: int,
        input: AccionCampaniaUpdateInput
    ) -> AccionCampania:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(AccionCampaniaModel).where(AccionCampaniaModel.id == id)
            )
            accion = result.scalar_one_or_none()
            if not accion:
                raise Exception("Acción no encontrada")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(accion, field, value)

            await db.commit()
            await db.refresh(accion)

            return model_to_accion_campania(accion)

    @strawberry.mutation
    async def inscribir_participante(
        self,
        info: Info[Context, None],
        input: InscripcionCampaniaInput
    ) -> ParticipanteCampania:
        async with await info.context.get_db() as db:
            # Verificar si ya está inscrito
            existing = await db.execute(
                select(ParticipanteCampaniaModel).where(
                    and_(
                        ParticipanteCampaniaModel.campania_id == input.campania_id,
                        ParticipanteCampaniaModel.miembro_id == input.miembro_id
                    )
                )
            )
            if existing.scalar_one_or_none():
                raise Exception("El miembro ya está inscrito en esta campaña")

            participante = ParticipanteCampaniaModel(
                campania_id=input.campania_id,
                miembro_id=input.miembro_id,
                rol_participante_id=input.rol_participante_id,
                observaciones=input.observaciones,
            )

            db.add(participante)
            await db.commit()
            await db.refresh(participante, ["miembro", "rol_participante"])
            await db.refresh(participante.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])

            return model_to_participante_campania(participante)

    @strawberry.mutation
    async def confirmar_participante(
        self,
        info: Info[Context, None],
        campania_id: int,
        miembro_id: int
    ) -> ParticipanteCampania:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(ParticipanteCampaniaModel).where(
                    and_(
                        ParticipanteCampaniaModel.campania_id == campania_id,
                        ParticipanteCampaniaModel.miembro_id == miembro_id
                    )
                )
            )
            participante = result.scalar_one_or_none()
            if not participante:
                raise Exception("Participante no encontrado")

            participante.confirmado = True
            participante.fecha_confirmacion = datetime.utcnow()

            await db.commit()
            await db.refresh(participante, ["miembro", "rol_participante"])
            await db.refresh(participante.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])

            return model_to_participante_campania(participante)

    @strawberry.mutation
    async def registrar_asistencia(
        self,
        info: Info[Context, None],
        campania_id: int,
        miembro_id: int,
        asistio: bool,
        horas: float | None = None
    ) -> bool:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(ParticipanteCampaniaModel).where(
                    and_(
                        ParticipanteCampaniaModel.campania_id == campania_id,
                        ParticipanteCampaniaModel.miembro_id == miembro_id
                    )
                )
            )
            participante = result.scalar_one_or_none()
            if not participante:
                raise Exception("Participante no encontrado")

            participante.asistio = asistio
            if horas is not None:
                from decimal import Decimal
                participante.horas_aportadas = Decimal(str(horas))

            await db.commit()
            return True

    @strawberry.mutation
    async def eliminar_participante(
        self,
        info: Info[Context, None],
        campania_id: int,
        miembro_id: int
    ) -> bool:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(ParticipanteCampaniaModel).where(
                    and_(
                        ParticipanteCampaniaModel.campania_id == campania_id,
                        ParticipanteCampaniaModel.miembro_id == miembro_id
                    )
                )
            )
            participante = result.scalar_one_or_none()
            if not participante:
                raise Exception("Participante no encontrado")

            await db.delete(participante)
            await db.commit()
            return True
