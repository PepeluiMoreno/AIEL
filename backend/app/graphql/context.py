"""Contexto GraphQL con sesión de base de datos."""

from dataclasses import dataclass
from typing import AsyncGenerator

from sqlalchemy.ext.asyncio import AsyncSession
from strawberry.fastapi import BaseContext

from ..core.database import async_session


@dataclass
class Context(BaseContext):
    """Contexto GraphQL con sesión de base de datos."""
    session: AsyncSession


async def get_context() -> AsyncGenerator[Context, None]:
    """
    Obtiene el contexto GraphQL con una sesión de base de datos.

    Usa un generador asíncrono para mantener la sesión abierta
    durante toda la petición GraphQL. Strawberry FastAPI soporta
    generadores asíncronos como context_getter.

    IMPORTANTE: Hace commit automático al finalizar para persistir
    los cambios de las mutations de Strawchemy.
    """
    async with async_session() as session:
        try:
            yield Context(session=session)
            await session.commit()
        except Exception:
            await session.rollback()
            raise
