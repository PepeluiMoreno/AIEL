"""Contexto GraphQL con sesión de base de datos."""

from dataclasses import dataclass

from sqlalchemy.ext.asyncio import AsyncSession
from strawberry.fastapi import BaseContext

from ..core.database import async_session


@dataclass
class Context(BaseContext):
    """Contexto GraphQL con sesión de base de datos."""
    session: AsyncSession


async def get_context() -> Context:
    """
    Obtiene el contexto GraphQL con una sesión de base de datos.

    Nota: La sesión se crea para cada request GraphQL.
    El contexto de Strawberry maneja el ciclo de vida.
    """
    # async_session() es un async_sessionmaker, llamarlo crea una sesión
    session = async_session()
    return Context(session=session)
