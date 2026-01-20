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
        # Check for '00000000'
        result = await conn.execute(text("SELECT id, codigo, nombre FROM agrupaciones_territoriales WHERE codigo = '00000000'"))
        row = result.fetchone()
        if row:
            print(f"Found with codigo '00000000': {row[2]}")
        else:
            print("Not found with codigo '00000000'")

        # Check for '0'
        result = await conn.execute(text("SELECT id, codigo, nombre FROM agrupaciones_territoriales WHERE codigo = '0'"))
        row = result.fetchone()
        if row:
            print(f"Found with codigo '0': {row[2]}")
        else:
            print("Not found with codigo '0'")

        # Show codes that start with '0' or '00'
        result = await conn.execute(text("SELECT codigo, nombre FROM agrupaciones_territoriales WHERE codigo LIKE '0%' ORDER BY codigo"))
        rows = result.fetchall()
        print(f"\nAgrupaciones with codigo starting with '0': {len(rows)}")
        for r in rows[:10]:
            print(f"  '{r[0]}' -> {r[1]}")

    await engine.dispose()

asyncio.run(check())
