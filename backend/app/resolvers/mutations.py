import strawberry
from strawberry.types import Info
from sqlalchemy import select
from datetime import datetime

from ..core.context import Context
from ..core.auth import hash_password, verify_password, create_token
from ..core.permissions import requiere_auth, requiere_rol
from ..models import Usuario as UsuarioModel, UsuarioRol, Rol as RolModel, Miembro as MiembroModel
from ..schemas.usuario import AuthPayload, LoginInput, Usuario, UsuarioRol as UsuarioRolSchema
from ..schemas.tipos_base import Rol, AgrupacionTerritorial, TipoMiembro, Pais, Provincia
from ..schemas.miembro import Miembro, MiembroInput, MiembroUpdateInput
from .financiero import FinancieroMutation
from .campania import CampaniaMutation
from .grupo_trabajo import GrupoTrabajoMutation
from .voluntariado import VoluntariadoMutation
from .presupuesto import PresupuestoMutation
from .actividad import ActividadMutation


def model_to_usuario(u: UsuarioModel) -> Usuario:
    roles = []
    for ur in u.roles:
        rol = Rol(
            id=ur.rol.id, codigo=ur.rol.codigo, nombre=ur.rol.nombre,
            descripcion=ur.rol.descripcion, activo=ur.rol.activo
        )
        agrup = None
        if ur.agrupacion:
            agrup = AgrupacionTerritorial(
                id=ur.agrupacion.id, codigo=ur.agrupacion.codigo,
                nombre=ur.agrupacion.nombre,
                email_coordinador=ur.agrupacion.email_coordinador,
                email_secretario=ur.agrupacion.email_secretario,
                email_tesorero=ur.agrupacion.email_tesorero,
                activo=ur.agrupacion.activo
            )
        roles.append(UsuarioRolSchema(rol=rol, agrupacion=agrup))

    return Usuario(
        id=u.id, email=u.email, activo=u.activo,
        created_at=u.created_at, last_login=u.last_login,
        roles=roles
    )


@strawberry.type
class Mutation(FinancieroMutation, CampaniaMutation, GrupoTrabajoMutation, VoluntariadoMutation, PresupuestoMutation, ActividadMutation):
    @strawberry.mutation
    async def login(self, info: Info[Context, None], input: LoginInput) -> AuthPayload:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(UsuarioModel).where(UsuarioModel.email == input.email)
            )
            user = result.scalar_one_or_none()

            if not user or not verify_password(input.password, user.password_hash):
                raise Exception("Credenciales inválidas")

            if not user.activo:
                raise Exception("Usuario inactivo")

            # Cargar roles
            await db.refresh(user, ["roles"])
            for ur in user.roles:
                await db.refresh(ur, ["rol", "agrupacion"])

            role_codes = [ur.rol.codigo for ur in user.roles]
            token = create_token(user.id, role_codes)

            user.last_login = datetime.utcnow()
            await db.commit()

            return AuthPayload(token=token, usuario=model_to_usuario(user))

    @strawberry.mutation
    async def me(self, info: Info[Context, None]) -> Usuario | None:
        """Obtiene el usuario actual autenticado."""
        user_id = info.context.user_id
        if not user_id:
            return None

        async with await info.context.get_db() as db:
            result = await db.execute(
                select(UsuarioModel).where(UsuarioModel.id == user_id)
            )
            user = result.scalar_one_or_none()
            if not user:
                return None

            await db.refresh(user, ["roles"])
            for ur in user.roles:
                await db.refresh(ur, ["rol", "agrupacion"])

            return model_to_usuario(user)

    @strawberry.mutation
    async def crear_miembro(self, info: Info[Context, None], input: MiembroInput) -> Miembro:
        async with await info.context.get_db() as db:
            miembro = MiembroModel(
                nombre=input.nombre,
                apellido1=input.apellido1,
                apellido2=input.apellido2,
                fecha_nacimiento=input.fecha_nacimiento,
                tipo_miembro_id=input.tipo_miembro_id,
                tipo_documento=input.tipo_documento,
                numero_documento=input.numero_documento,
                pais_documento_id=input.pais_documento_id,
                direccion=input.direccion,
                codigo_postal=input.codigo_postal,
                localidad=input.localidad,
                provincia_id=input.provincia_id,
                pais_domicilio_id=input.pais_domicilio_id,
                telefono=input.telefono,
                telefono2=input.telefono2,
                agrupacion_id=input.agrupacion_id,
                iban=input.iban,
            )
            db.add(miembro)
            await db.commit()
            await db.refresh(miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_miembro(miembro)

    @strawberry.mutation
    async def actualizar_miembro(self, info: Info[Context, None], id: int, input: MiembroUpdateInput) -> Miembro:
        async with await info.context.get_db() as db:
            result = await db.execute(select(MiembroModel).where(MiembroModel.id == id))
            miembro = result.scalar_one_or_none()
            if not miembro:
                raise Exception("Miembro no encontrado")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(miembro, field, value)

            await db.commit()
            await db.refresh(miembro, ["tipo_miembro", "agrupacion", "provincia", "pais_domicilio"])
            return model_to_miembro(miembro)

    @strawberry.mutation
    async def baja_miembro(self, info: Info[Context, None], id: int) -> bool:
        async with await info.context.get_db() as db:
            result = await db.execute(select(MiembroModel).where(MiembroModel.id == id))
            miembro = result.scalar_one_or_none()
            if not miembro:
                raise Exception("Miembro no encontrado")

            from datetime import date
            miembro.fecha_baja = date.today()
            miembro.deleted_at = datetime.utcnow()
            await db.commit()
            return True


def model_to_miembro(m: MiembroModel) -> Miembro:
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
