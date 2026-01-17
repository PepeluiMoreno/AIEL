import strawberry
from strawberry.types import Info
from sqlalchemy import select

from ..core.context import Context
from ..core.permissions import requiere_auth
from ..models import (
    Pais as PaisModel,
    Provincia as ProvinciaModel,
    TipoMiembro as TipoMiembroModel,
    Rol as RolModel,
    AgrupacionTerritorial as AgrupacionModel,
    Miembro as MiembroModel,
    Usuario as UsuarioModel,
)
from ..schemas.tipos_base import Pais, Provincia, TipoMiembro, Rol, AgrupacionTerritorial
from ..schemas.miembro import Miembro
from ..schemas.usuario import Usuario
from .financiero import FinancieroQuery
from .campania import CampaniaQuery
from .grupo_trabajo import GrupoTrabajoQuery
from .voluntariado import VoluntariadoQuery


def model_to_pais(m: PaisModel) -> Pais:
    return Pais(codpais=m.codpais, nombre=m.nombre)


def model_to_provincia(m: ProvinciaModel) -> Provincia:
    return Provincia(codprov=m.codprov, nombre=m.nombre)


def model_to_tipo_miembro(m: TipoMiembroModel) -> TipoMiembro:
    return TipoMiembro(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        requiere_cuota=m.requiere_cuota, activo=m.activo
    )


def model_to_rol(m: RolModel) -> Rol:
    return Rol(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, activo=m.activo
    )


def model_to_agrupacion(m: AgrupacionModel) -> AgrupacionTerritorial:
    return AgrupacionTerritorial(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        email_coordinador=m.email_coordinador,
        email_secretario=m.email_secretario,
        email_tesorero=m.email_tesorero,
        activo=m.activo
    )


@strawberry.type
class Query(FinancieroQuery, CampaniaQuery, GrupoTrabajoQuery, VoluntariadoQuery):
    @strawberry.field
    def health(self) -> str:
        return "ok"

    @strawberry.field
    async def paises(self, info: Info[Context, None]) -> list[Pais]:
        async with await info.context.get_db() as db:
            result = await db.execute(select(PaisModel))
            return [model_to_pais(p) for p in result.scalars()]

    @strawberry.field
    async def provincias(self, info: Info[Context, None]) -> list[Provincia]:
        async with await info.context.get_db() as db:
            result = await db.execute(select(ProvinciaModel))
            return [model_to_provincia(p) for p in result.scalars()]

    @strawberry.field
    async def tipos_miembro(self, info: Info[Context, None]) -> list[TipoMiembro]:
        async with await info.context.get_db() as db:
            result = await db.execute(select(TipoMiembroModel).where(TipoMiembroModel.activo == True))
            return [model_to_tipo_miembro(t) for t in result.scalars()]

    @strawberry.field
    async def roles(self, info: Info[Context, None]) -> list[Rol]:
        async with await info.context.get_db() as db:
            result = await db.execute(select(RolModel).where(RolModel.activo == True))
            return [model_to_rol(r) for r in result.scalars()]

    @strawberry.field
    async def agrupaciones(self, info: Info[Context, None]) -> list[AgrupacionTerritorial]:
        async with await info.context.get_db() as db:
            result = await db.execute(select(AgrupacionModel).where(AgrupacionModel.activo == True))
            return [model_to_agrupacion(a) for a in result.scalars()]

    @strawberry.field
    async def miembro(self, info: Info[Context, None], id: int) -> Miembro | None:
        from .mutations import model_to_miembro
        async with await info.context.get_db() as db:
            result = await db.execute(select(MiembroModel).where(MiembroModel.id == id, MiembroModel.deleted_at == None))
            m = result.scalar_one_or_none()
            if not m:
                return None
            await db.refresh(m, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_miembro(m)

    @strawberry.field
    async def miembros(
        self,
        info: Info[Context, None],
        tipo_miembro_id: int | None = None,
        agrupacion_id: int | None = None,
        solo_activos: bool = True,
        limite: int = 100,
        offset: int = 0,
    ) -> list[Miembro]:
        from .mutations import model_to_miembro
        async with await info.context.get_db() as db:
            query = select(MiembroModel)

            if solo_activos:
                query = query.where(MiembroModel.deleted_at == None)
            if tipo_miembro_id:
                query = query.where(MiembroModel.tipo_miembro_id == tipo_miembro_id)
            if agrupacion_id:
                query = query.where(MiembroModel.agrupacion_id == agrupacion_id)

            query = query.limit(limite).offset(offset)
            result = await db.execute(query)
            miembros = []
            for m in result.scalars():
                await db.refresh(m, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
                miembros.append(model_to_miembro(m))
            return miembros
