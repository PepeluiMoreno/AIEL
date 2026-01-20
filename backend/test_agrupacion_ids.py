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
        result = await conn.execute(text("SELECT old_id FROM temp_id_mapping WHERE tabla = 'AGRUPACION' LIMIT 10"))
        rows = result.fetchall()
        print('Old IDs stored in temp_id_mapping:')
        for r in rows:
            print(f"  {r[0]} (type: {type(r[0])})")
    await engine.dispose()

asyncio.run(check())
