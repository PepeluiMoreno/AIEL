import asyncio
from sqlalchemy.ext.asyncio import create_async_engine
from sqlalchemy import text
from app.core.database import get_database_url

async def check():
    engine = create_async_engine(
        get_database_url(),
        connect_args={"server_settings": {"jit": "off"}, "statement_cache_size": 0}
    )
    async with engine.connect() as conn:
        result = await conn.execute(text("""
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            ORDER BY table_name
        """))
        tables = result.fetchall()
        print(f'Tablas en PostgreSQL ({len(tables)}):')
        for t in tables:
            print(f"  {t[0]}")

    await engine.dispose()

asyncio.run(check())
