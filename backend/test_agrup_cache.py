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
        result = await conn.execute(text("SELECT codigo FROM agrupaciones_territoriales LIMIT 10"))
        rows = result.fetchall()
        print('Códigos in agrupaciones_territoriales:')
        for r in rows:
            print(f"  '{r[0]}'")
    await engine.dispose()

asyncio.run(check())
