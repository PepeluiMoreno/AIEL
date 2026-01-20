import asyncio
from sqlalchemy.ext.asyncio import create_async_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy.ext.asyncio import AsyncSession
from app.core.database import get_database_url

async def test():
    print("Iniciando test de conexión...")
    database_url = get_database_url()
    print(f"URL obtenida")

    engine = create_async_engine(database_url, echo=True)
    print("Engine creado")

    async_session = sessionmaker(engine, class_=AsyncSession, expire_on_commit=False)
    print("SessionMaker creado")

    async with async_session() as session:
        print("Sesión abierta")
        result = await session.execute("SELECT 1")
        print(f"Query ejecutado: {result.scalar()}")

    await engine.dispose()
    print("Engine dispuesto")

if __name__ == "__main__":
    asyncio.run(test())
