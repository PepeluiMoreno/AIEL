"""
Script para truncar la tabla cuotas_anuales y permitir re-importación.
"""
import asyncio
from sqlalchemy.ext.asyncio import create_async_engine
from sqlalchemy import text
from app.core.database import get_database_url

async def main():
    print("\nTruncando tabla cuotas_anuales...")

    database_url = get_database_url()
    engine = create_async_engine(
        database_url,
        echo=False,
        connect_args={"server_settings": {"jit": "off"}, "statement_cache_size": 0}
    )

    async with engine.connect() as conn:
        await conn.execute(text("TRUNCATE TABLE cuotas_anuales CASCADE"))
        await conn.commit()
        print("[OK] Tabla cuotas_anuales truncada (CASCADE)")

    await engine.dispose()

if __name__ == "__main__":
    asyncio.run(main())
