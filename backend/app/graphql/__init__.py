"""GraphQL API con generación automática desde modelos SQLAlchemy."""

from strawchemy import Strawchemy, StrawchemyAsyncRepository, StrawchemyConfig

# Configuración de Strawchemy para PostgreSQL async
config = StrawchemyConfig(
    dialect="postgresql",
    repository_type=StrawchemyAsyncRepository,
)

# Inicializar Strawchemy para PostgreSQL (async con asyncpg)
strawchemy = Strawchemy(config)
