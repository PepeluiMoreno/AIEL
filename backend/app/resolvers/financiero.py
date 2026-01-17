import strawberry
from strawberry.types import Info
from sqlalchemy import select
from datetime import date
from decimal import Decimal

from ..core.context import Context
from ..models import (
    CuotaAnio as CuotaModel,
    ImporteCuotaAnio as ImporteModel,
    Donacion as DonacionModel,
    DonacionConcepto as ConceptoModel,
    Remesa as RemesaModel,
    OrdenCobro as OrdenModel,
    EstadoCuota as EstadoCuotaModel,
)
from ..schemas.financiero import (
    CuotaAnio, ImporteCuotaAnio, Donacion, DonacionConcepto,
    Remesa, OrdenCobro, EstadoCuota, ModoIngreso,
    CuotaAnioInput, PagoCuotaInput, DonacionInput, RemesaInput,
)
from ..schemas.tipos_base import AgrupacionTerritorial
from .mutations import model_to_miembro


def model_to_cuota(c: CuotaModel) -> CuotaAnio:
    return CuotaAnio(
        id=c.id, anio=c.anio, importe=c.importe,
        importe_pagado=c.importe_pagado,
        estado=EstadoCuota(c.estado.value),
        modo_ingreso=ModoIngreso(c.modo_ingreso.value) if c.modo_ingreso else None,
        fecha_pago=c.fecha_pago, observaciones=c.observaciones,
        miembro=model_to_miembro(c.miembro),
        agrupacion=AgrupacionTerritorial(
            id=c.agrupacion.id, codigo=c.agrupacion.codigo, nombre=c.agrupacion.nombre,
            email_coordinador=c.agrupacion.email_coordinador,
            email_secretario=c.agrupacion.email_secretario,
            email_tesorero=c.agrupacion.email_tesorero,
            activo=c.agrupacion.activo
        )
    )


def model_to_donacion(d: DonacionModel) -> Donacion:
    concepto = None
    if d.concepto:
        concepto = DonacionConcepto(
            id=d.concepto.id, codigo=d.concepto.codigo,
            nombre=d.concepto.nombre, activo=d.concepto.activo
        )
    return Donacion(
        id=d.id, importe=d.importe, gastos=d.gastos, fecha=d.fecha,
        modo_ingreso=ModoIngreso(d.modo_ingreso.value) if d.modo_ingreso else None,
        observaciones=d.observaciones,
        miembro=model_to_miembro(d.miembro),
        concepto=concepto
    )


@strawberry.type
class FinancieroQuery:
    @strawberry.field
    async def importes_cuota(self, info: Info[Context, None]) -> list[ImporteCuotaAnio]:
        async with await info.context.get_db() as db:
            result = await db.execute(select(ImporteModel).order_by(ImporteModel.anio.desc()))
            return [ImporteCuotaAnio(id=i.id, anio=i.anio, importe=i.importe) for i in result.scalars()]

    @strawberry.field
    async def cuotas(
        self,
        info: Info[Context, None],
        anio: int | None = None,
        miembro_id: int | None = None,
        agrupacion_id: int | None = None,
        estado: EstadoCuota | None = None,
        limite: int = 100,
        offset: int = 0,
    ) -> list[CuotaAnio]:
        async with await info.context.get_db() as db:
            query = select(CuotaModel)
            if anio:
                query = query.where(CuotaModel.anio == anio)
            if miembro_id:
                query = query.where(CuotaModel.miembro_id == miembro_id)
            if agrupacion_id:
                query = query.where(CuotaModel.agrupacion_id == agrupacion_id)
            if estado:
                query = query.where(CuotaModel.estado == EstadoCuotaModel(estado.value))

            query = query.limit(limite).offset(offset)
            result = await db.execute(query)
            cuotas = []
            for c in result.scalars():
                await db.refresh(c, ["miembro", "agrupacion"])
                await db.refresh(c.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                cuotas.append(model_to_cuota(c))
            return cuotas

    @strawberry.field
    async def conceptos_donacion(self, info: Info[Context, None]) -> list[DonacionConcepto]:
        async with await info.context.get_db() as db:
            result = await db.execute(select(ConceptoModel).where(ConceptoModel.activo == True))
            return [DonacionConcepto(id=c.id, codigo=c.codigo, nombre=c.nombre, activo=c.activo) for c in result.scalars()]

    @strawberry.field
    async def donaciones(
        self,
        info: Info[Context, None],
        miembro_id: int | None = None,
        concepto_id: int | None = None,
        limite: int = 100,
        offset: int = 0,
    ) -> list[Donacion]:
        async with await info.context.get_db() as db:
            query = select(DonacionModel).where(DonacionModel.deleted_at == None)
            if miembro_id:
                query = query.where(DonacionModel.miembro_id == miembro_id)
            if concepto_id:
                query = query.where(DonacionModel.concepto_id == concepto_id)

            query = query.limit(limite).offset(offset)
            result = await db.execute(query)
            donaciones = []
            for d in result.scalars():
                await db.refresh(d, ["miembro", "concepto"])
                await db.refresh(d.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                donaciones.append(model_to_donacion(d))
            return donaciones

    @strawberry.field
    async def remesa(self, info: Info[Context, None], id: int) -> Remesa | None:
        async with await info.context.get_db() as db:
            result = await db.execute(select(RemesaModel).where(RemesaModel.id == id))
            r = result.scalar_one_or_none()
            if not r:
                return None
            await db.refresh(r, ["ordenes"])
            return await _model_to_remesa(db, r)

    @strawberry.field
    async def remesas(
        self,
        info: Info[Context, None],
        fecha_desde: date | None = None,
        fecha_hasta: date | None = None,
        limite: int = 50,
        offset: int = 0,
    ) -> list[Remesa]:
        async with await info.context.get_db() as db:
            query = select(RemesaModel).order_by(RemesaModel.fecha.desc())
            if fecha_desde:
                query = query.where(RemesaModel.fecha >= fecha_desde)
            if fecha_hasta:
                query = query.where(RemesaModel.fecha <= fecha_hasta)

            query = query.limit(limite).offset(offset)
            result = await db.execute(query)
            remesas = []
            for r in result.scalars():
                await db.refresh(r, ["ordenes"])
                remesas.append(await _model_to_remesa(db, r))
            return remesas


@strawberry.type
class FinancieroMutation:
    @strawberry.mutation
    async def crear_cuota(self, info: Info[Context, None], input: CuotaAnioInput) -> CuotaAnio:
        async with await info.context.get_db() as db:
            cuota = CuotaModel(
                miembro_id=input.miembro_id,
                anio=input.anio,
                agrupacion_id=input.agrupacion_id,
                importe=input.importe,
            )
            db.add(cuota)
            await db.commit()
            await db.refresh(cuota, ["miembro", "agrupacion"])
            await db.refresh(cuota.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_cuota(cuota)

    @strawberry.mutation
    async def pagar_cuota(self, info: Info[Context, None], input: PagoCuotaInput) -> CuotaAnio:
        async with await info.context.get_db() as db:
            result = await db.execute(select(CuotaModel).where(CuotaModel.id == input.cuota_id))
            cuota = result.scalar_one_or_none()
            if not cuota:
                raise Exception("Cuota no encontrada")

            from ..models import ModoIngreso as ModoIngresoModel
            cuota.importe_pagado = input.importe_pagado
            cuota.modo_ingreso = ModoIngresoModel(input.modo_ingreso.value)
            cuota.fecha_pago = date.today()
            if input.observaciones:
                cuota.observaciones = input.observaciones

            if cuota.importe_pagado >= cuota.importe:
                cuota.estado = EstadoCuotaModel.COBRADA
            elif cuota.importe_pagado > 0:
                cuota.estado = EstadoCuotaModel.COBRADA_PARCIAL

            await db.commit()
            await db.refresh(cuota, ["miembro", "agrupacion"])
            await db.refresh(cuota.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_cuota(cuota)

    @strawberry.mutation
    async def crear_donacion(self, info: Info[Context, None], input: DonacionInput) -> Donacion:
        async with await info.context.get_db() as db:
            from ..models import ModoIngreso as ModoIngresoModel
            donacion = DonacionModel(
                miembro_id=input.miembro_id,
                importe=input.importe,
                concepto_id=input.concepto_id,
                gastos=input.gastos,
                modo_ingreso=ModoIngresoModel(input.modo_ingreso.value) if input.modo_ingreso else None,
                observaciones=input.observaciones,
            )
            db.add(donacion)
            await db.commit()
            await db.refresh(donacion, ["miembro", "concepto"])
            await db.refresh(donacion.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_donacion(donacion)

    @strawberry.mutation
    async def crear_remesa(self, info: Info[Context, None], input: RemesaInput) -> Remesa:
        """Crea una remesa SEPA con las cuotas indicadas."""
        async with await info.context.get_db() as db:
            # Obtener cuotas pendientes
            result = await db.execute(
                select(CuotaModel).where(
                    CuotaModel.id.in_(input.cuota_ids),
                    CuotaModel.estado == EstadoCuotaModel.PENDIENTE
                )
            )
            cuotas = list(result.scalars())
            if not cuotas:
                raise Exception("No hay cuotas pendientes para la remesa")

            importe_total = sum(c.importe - c.importe_pagado for c in cuotas)

            remesa = RemesaModel(
                importe_total=importe_total,
                observaciones=input.observaciones,
            )
            db.add(remesa)
            await db.flush()

            for cuota in cuotas:
                orden = OrdenModel(
                    remesa_id=remesa.id,
                    cuota_id=cuota.id,
                    importe=cuota.importe - cuota.importe_pagado,
                )
                db.add(orden)

            await db.commit()
            await db.refresh(remesa, ["ordenes"])
            return await _model_to_remesa(db, remesa)

    @strawberry.mutation
    async def generar_sepa_xml(
        self,
        info: Info[Context, None],
        remesa_id: int,
        acreedor_nombre: str,
        acreedor_iban: str,
        acreedor_bic: str,
        acreedor_id: str,
    ) -> str:
        """Genera fichero SEPA XML para una remesa."""
        from ..services.sepa import generar_sepa_xml

        async with await info.context.get_db() as db:
            result = await db.execute(select(RemesaModel).where(RemesaModel.id == remesa_id))
            remesa = result.scalar_one_or_none()
            if not remesa:
                raise Exception("Remesa no encontrada")

            await db.refresh(remesa, ["ordenes"])
            for orden in remesa.ordenes:
                await db.refresh(orden, ["cuota"])
                await db.refresh(orden.cuota, ["miembro"])

            xml = generar_sepa_xml(
                remesa=remesa,
                ordenes=remesa.ordenes,
                acreedor_nombre=acreedor_nombre,
                acreedor_iban=acreedor_iban,
                acreedor_bic=acreedor_bic,
                acreedor_id=acreedor_id,
            )

            # Guardar nombre archivo en remesa
            nombre_archivo = f"SEPA_{remesa.id}_{date.today().isoformat()}.xml"
            remesa.archivo_sepa = nombre_archivo
            await db.commit()

            return xml


async def _model_to_remesa(db, r: RemesaModel) -> Remesa:
    ordenes = []
    for o in r.ordenes:
        await db.refresh(o, ["cuota"])
        await db.refresh(o.cuota, ["miembro", "agrupacion"])
        await db.refresh(o.cuota.miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
        ordenes.append(OrdenCobro(
            id=o.id, importe=o.importe, estado=o.estado,
            cuota=model_to_cuota(o.cuota)
        ))
    return Remesa(
        id=r.id, fecha=r.fecha, importe_total=r.importe_total,
        gastos=r.gastos, archivo_sepa=r.archivo_sepa,
        observaciones=r.observaciones, ordenes=ordenes
    )
