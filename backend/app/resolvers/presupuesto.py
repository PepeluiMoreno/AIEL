"""Resolvers GraphQL para presupuesto y planificación."""

import strawberry
from strawberry.types import Info
from sqlalchemy import select
from datetime import datetime

from ..core.context import Context
from ..models.presupuesto import (
    EstadoPlanificacion as EstadoPlanificacionModel,
    CategoriaPartida as CategoriaPartidaModel,
    PartidaPresupuestaria as PartidaPresupuestariaModel,
    PlanificacionAnual as PlanificacionAnualModel,
)
from ..schemas.presupuesto import (
    EstadoPlanificacion,
    CategoriaPartida,
    PartidaPresupuestaria,
    PlanificacionAnual,
    PlanificacionAnualInput,
    PlanificacionAnualUpdateInput,
    PartidaPresupuestariaInput,
    PartidaPresupuestariaUpdateInput,
    CategoriaPartidaInput,
)


# --- Conversores Model → Schema ---

def model_to_estado_planificacion(m: EstadoPlanificacionModel) -> EstadoPlanificacion:
    return EstadoPlanificacion(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        orden=m.orden, color=m.color, es_final=m.es_final, activo=m.activo
    )


def model_to_categoria_partida(m: CategoriaPartidaModel) -> CategoriaPartida:
    return CategoriaPartida(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, activo=m.activo
    )


def model_to_partida_presupuestaria(m: PartidaPresupuestariaModel) -> PartidaPresupuestaria:
    categoria = None
    if m.categoria:
        categoria = model_to_categoria_partida(m.categoria)

    return PartidaPresupuestaria(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, ejercicio=m.ejercicio, tipo=m.tipo,
        categoria=categoria,
        importe_presupuestado=m.importe_presupuestado,
        importe_comprometido=m.importe_comprometido,
        importe_ejecutado=m.importe_ejecutado,
        activo=m.activo,
        created_at=m.created_at, updated_at=m.updated_at
    )


def model_to_planificacion_anual(m: PlanificacionAnualModel) -> PlanificacionAnual:
    estado = model_to_estado_planificacion(m.estado)
    partidas = [model_to_partida_presupuestaria(p) for p in m.partidas] if m.partidas else []

    return PlanificacionAnual(
        id=m.id, ejercicio=m.ejercicio, nombre=m.nombre,
        descripcion=m.descripcion, objetivos=m.objetivos,
        estado=estado, fecha_aprobacion=m.fecha_aprobacion,
        presupuesto_total=m.presupuesto_total,
        partidas=partidas,
        created_at=m.created_at, updated_at=m.updated_at
    )


# --- Queries ---

@strawberry.type
class PresupuestoQuery:
    @strawberry.field
    async def estados_planificacion(self, info: Info[Context, None]) -> list[EstadoPlanificacion]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(EstadoPlanificacionModel)
                .where(EstadoPlanificacionModel.activo == True)
                .order_by(EstadoPlanificacionModel.orden)
            )
            return [model_to_estado_planificacion(e) for e in result.scalars()]

    @strawberry.field
    async def categorias_partida(self, info: Info[Context, None]) -> list[CategoriaPartida]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(CategoriaPartidaModel).where(CategoriaPartidaModel.activo == True)
            )
            return [model_to_categoria_partida(c) for c in result.scalars()]

    @strawberry.field
    async def planificacion_anual(
        self,
        info: Info[Context, None],
        ejercicio: int | None = None,
        id: strawberry.ID | None = None
    ) -> PlanificacionAnual | None:
        async with await info.context.get_db() as db:
            query = select(PlanificacionAnualModel).where(
                PlanificacionAnualModel.deleted_at == None
            )

            if ejercicio:
                query = query.where(PlanificacionAnualModel.ejercicio == ejercicio)
            elif id:
                from uuid import UUID
                query = query.where(PlanificacionAnualModel.id == UUID(str(id)))
            else:
                return None

            result = await db.execute(query)
            m = result.scalar_one_or_none()
            if not m:
                return None

            await db.refresh(m, ["estado", "partidas"])
            for p in m.partidas:
                await db.refresh(p, ["categoria"])

            return model_to_planificacion_anual(m)

    @strawberry.field
    async def planificaciones_anuales(
        self,
        info: Info[Context, None],
        limite: int = 10
    ) -> list[PlanificacionAnual]:
        async with await info.context.get_db() as db:
            query = select(PlanificacionAnualModel).where(
                PlanificacionAnualModel.deleted_at == None
            ).order_by(PlanificacionAnualModel.ejercicio.desc()).limit(limite)

            result = await db.execute(query)
            planificaciones = []

            for m in result.scalars():
                await db.refresh(m, ["estado", "partidas"])
                for p in m.partidas:
                    await db.refresh(p, ["categoria"])
                planificaciones.append(model_to_planificacion_anual(m))

            return planificaciones

    @strawberry.field
    async def partida_presupuestaria(
        self,
        info: Info[Context, None],
        codigo: str | None = None,
        id: strawberry.ID | None = None
    ) -> PartidaPresupuestaria | None:
        async with await info.context.get_db() as db:
            query = select(PartidaPresupuestariaModel).where(
                PartidaPresupuestariaModel.deleted_at == None
            )

            if codigo:
                query = query.where(PartidaPresupuestariaModel.codigo == codigo)
            elif id:
                from uuid import UUID
                query = query.where(PartidaPresupuestariaModel.id == UUID(str(id)))
            else:
                return None

            result = await db.execute(query)
            m = result.scalar_one_or_none()
            if not m:
                return None

            await db.refresh(m, ["categoria"])
            return model_to_partida_presupuestaria(m)

    @strawberry.field
    async def partidas_presupuestarias(
        self,
        info: Info[Context, None],
        ejercicio: int | None = None,
        tipo: str | None = None,
        categoria_id: int | None = None,
        solo_activas: bool = True,
        limite: int = 50
    ) -> list[PartidaPresupuestaria]:
        async with await info.context.get_db() as db:
            query = select(PartidaPresupuestariaModel).where(
                PartidaPresupuestariaModel.deleted_at == None
            )

            if ejercicio:
                query = query.where(PartidaPresupuestariaModel.ejercicio == ejercicio)
            if tipo:
                query = query.where(PartidaPresupuestariaModel.tipo == tipo)
            if categoria_id:
                query = query.where(PartidaPresupuestariaModel.categoria_id == categoria_id)
            if solo_activas:
                query = query.where(PartidaPresupuestariaModel.activo == True)

            query = query.order_by(PartidaPresupuestariaModel.codigo).limit(limite)

            result = await db.execute(query)
            partidas = []

            for m in result.scalars():
                await db.refresh(m, ["categoria"])
                partidas.append(model_to_partida_presupuestaria(m))

            return partidas


# --- Mutations ---

@strawberry.type
class PresupuestoMutation:
    @strawberry.mutation
    async def crear_planificacion_anual(
        self,
        info: Info[Context, None],
        input: PlanificacionAnualInput
    ) -> PlanificacionAnual:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            # Estado por defecto: BORRADOR
            estado_id = input.estado_id
            if not estado_id:
                result = await db.execute(
                    select(EstadoPlanificacionModel).where(EstadoPlanificacionModel.codigo == "BORRADOR")
                )
                estado = result.scalar_one_or_none()
                estado_id = estado.id if estado else 1

            planificacion = PlanificacionAnualModel(
                ejercicio=input.ejercicio,
                nombre=input.nombre,
                descripcion=input.descripcion,
                objetivos=input.objetivos,
                estado_id=estado_id,
                presupuesto_total=input.presupuesto_total,
                created_at=datetime.utcnow(),
                creado_por_id=user_id if user_id else None,
            )

            db.add(planificacion)
            await db.commit()
            await db.refresh(planificacion, ["estado", "partidas"])

            return model_to_planificacion_anual(planificacion)

    @strawberry.mutation
    async def actualizar_planificacion_anual(
        self,
        info: Info[Context, None],
        id: strawberry.ID,
        input: PlanificacionAnualUpdateInput
    ) -> PlanificacionAnual:
        from uuid import UUID
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            result = await db.execute(
                select(PlanificacionAnualModel).where(PlanificacionAnualModel.id == UUID(str(id)))
            )
            planificacion = result.scalar_one_or_none()
            if not planificacion:
                raise Exception("Planificación no encontrada")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(planificacion, field, value)

            planificacion.updated_at = datetime.utcnow()
            planificacion.modificado_por_id = user_id if user_id else None

            await db.commit()
            await db.refresh(planificacion, ["estado", "partidas"])

            return model_to_planificacion_anual(planificacion)

    @strawberry.mutation
    async def aprobar_planificacion(
        self,
        info: Info[Context, None],
        id: strawberry.ID
    ) -> PlanificacionAnual:
        from uuid import UUID
        from datetime import date
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            result = await db.execute(
                select(PlanificacionAnualModel).where(PlanificacionAnualModel.id == UUID(str(id)))
            )
            planificacion = result.scalar_one_or_none()
            if not planificacion:
                raise Exception("Planificación no encontrada")

            # Obtener estado APROBADO
            estado_result = await db.execute(
                select(EstadoPlanificacionModel).where(EstadoPlanificacionModel.codigo == "APROBADO")
            )
            estado = estado_result.scalar_one_or_none()
            if estado:
                planificacion.estado_id = estado.id
                planificacion.fecha_aprobacion = date.today()
                planificacion.updated_at = datetime.utcnow()
                planificacion.modificado_por_id = user_id if user_id else None

            await db.commit()
            await db.refresh(planificacion, ["estado", "partidas"])

            return model_to_planificacion_anual(planificacion)

    @strawberry.mutation
    async def crear_partida_presupuestaria(
        self,
        info: Info[Context, None],
        input: PartidaPresupuestariaInput
    ) -> PartidaPresupuestaria:
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            partida = PartidaPresupuestariaModel(
                codigo=input.codigo,
                nombre=input.nombre,
                descripcion=input.descripcion,
                ejercicio=input.ejercicio,
                tipo=input.tipo,
                categoria_id=input.categoria_id,
                importe_presupuestado=input.importe_presupuestado,
                planificacion_id=input.planificacion_id,
                created_at=datetime.utcnow(),
                creado_por_id=user_id if user_id else None,
            )

            db.add(partida)
            await db.commit()
            await db.refresh(partida, ["categoria"])

            return model_to_partida_presupuestaria(partida)

    @strawberry.mutation
    async def actualizar_partida_presupuestaria(
        self,
        info: Info[Context, None],
        id: strawberry.ID,
        input: PartidaPresupuestariaUpdateInput
    ) -> PartidaPresupuestaria:
        from uuid import UUID
        user_id = info.context.user_id

        async with await info.context.get_db() as db:
            result = await db.execute(
                select(PartidaPresupuestariaModel).where(PartidaPresupuestariaModel.id == UUID(str(id)))
            )
            partida = result.scalar_one_or_none()
            if not partida:
                raise Exception("Partida no encontrada")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(partida, field, value)

            partida.updated_at = datetime.utcnow()
            partida.modificado_por_id = user_id if user_id else None

            await db.commit()
            await db.refresh(partida, ["categoria"])

            return model_to_partida_presupuestaria(partida)

    @strawberry.mutation
    async def crear_categoria_partida(
        self,
        info: Info[Context, None],
        input: CategoriaPartidaInput
    ) -> CategoriaPartida:
        async with await info.context.get_db() as db:
            categoria = CategoriaPartidaModel(
                codigo=input.codigo,
                nombre=input.nombre,
                descripcion=input.descripcion,
            )

            db.add(categoria)
            await db.commit()
            await db.refresh(categoria)

            return model_to_categoria_partida(categoria)
