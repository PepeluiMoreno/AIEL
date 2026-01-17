from pydantic_settings import BaseSettings
from pydantic import computed_field
from functools import lru_cache


class Settings(BaseSettings):
    # Supabase - todas las variables vienen del .env
    db_host: str
    db_port: int
    db_name: str
    db_user: str
    db_password: str
    db_direct_port: int

    # JWT
    jwt_secret: str
    jwt_algorithm: str = "HS256"
    jwt_expire_minutes: int = 1440

    @computed_field
    @property
    def database_url(self) -> str:
        """URL con pooler (pgbouncer) para la app."""
        return f"postgresql+asyncpg://{self.db_user}:{self.db_password}@{self.db_host}:{self.db_port}/{self.db_name}"

    @computed_field
    @property
    def database_url_sync(self) -> str:
        """URL síncrona para Alembic."""
        return f"postgresql://{self.db_user}:{self.db_password}@{self.db_host}:{self.db_direct_port}/{self.db_name}"

    class Config:
        env_file = ".env"


@lru_cache
def get_settings() -> Settings:
    return Settings()
