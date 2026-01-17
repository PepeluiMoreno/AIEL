from functools import wraps
from typing import Callable, Any

from strawberry.types import Info
from sqlalchemy import select

from .context import Context


class PermissionError(Exception):
    pass


def requiere_auth(func: Callable) -> Callable:
    """Decorator: requiere usuario autenticado."""
    @wraps(func)
    async def wrapper(*args, info: Info[Context, Any], **kwargs):
        if not info.context.user_id:
            raise PermissionError("No autenticado")
        return await func(*args, info=info, **kwargs)
    return wrapper


def requiere_rol(*roles_permitidos: str) -> Callable:
    """Decorator: requiere uno de los roles indicados."""
    def decorator(func: Callable) -> Callable:
        @wraps(func)
        async def wrapper(*args, info: Info[Context, Any], **kwargs):
            if not info.context.user_id:
                raise PermissionError("No autenticado")
            user_roles = set(info.context.user_roles)
            if not user_roles.intersection(roles_permitidos):
                raise PermissionError(f"Requiere rol: {', '.join(roles_permitidos)}")
            return await func(*args, info=info, **kwargs)
        return wrapper
    return decorator


def requiere_transaccion(codigo_transaccion: str) -> Callable:
    """Decorator: verifica permiso via tabla rol_transaccion."""
    def decorator(func: Callable) -> Callable:
        @wraps(func)
        async def wrapper(*args, info: Info[Context, Any], **kwargs):
            if not info.context.user_id:
                raise PermissionError("No autenticado")

            user_roles = info.context.user_roles
            if not user_roles:
                raise PermissionError("Sin roles asignados")

            from ..models import RolTransaccion, Transaccion, Rol

            async with await info.context.get_db() as db:
                # Buscar si alguno de los roles del usuario tiene permiso para esta transacción
                result = await db.execute(
                    select(RolTransaccion)
                    .join(Rol, RolTransaccion.rol_id == Rol.id)
                    .join(Transaccion, RolTransaccion.transaccion_id == Transaccion.id)
                    .where(
                        Rol.codigo.in_(user_roles),
                        Transaccion.codigo == codigo_transaccion,
                        Transaccion.activo == True
                    )
                )
                permiso = result.scalar_one_or_none()

                if not permiso:
                    raise PermissionError(f"Sin permiso para: {codigo_transaccion}")

            return await func(*args, info=info, **kwargs)
        return wrapper
    return decorator
