import asyncio
from app.scripts.importacion.mysql_helper import get_mysql_connection

async def check():
    async with get_mysql_connection() as mysql_conn:
        async with mysql_conn.cursor() as cursor:
            await cursor.execute("DESCRIBE agrupacionterritorial")
            columns = await cursor.fetchall()

            print('Columns in agrupacionterritorial:')
            for col in columns:
                print(f"  {col[0]} ({col[1]})")

asyncio.run(check())
