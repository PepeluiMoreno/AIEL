"""GraphQL API con generación automática desde modelos SQLAlchemy."""

from typing import Any
from strawberry import Info
from strawchemy import Strawchemy, StrawchemyAsyncRepository, StrawchemyConfig


def get_session_from_context(info: Info[Any, Any]) -> Any:
    """Obtiene la sesión de base de datos del contexto GraphQL."""
    return info.context.session


# Configuración de Strawchemy para PostgreSQL async
config = StrawchemyConfig(
    dialect="postgresql",
    repository_type=StrawchemyAsyncRepository,
    session_getter=get_session_from_context,
)

# Inicializar Strawchemy para PostgreSQL (async con asyncpg)
strawchemy = Strawchemy(config)
