import asyncio
from app.scripts.importacion.mysql_helper import get_mysql_connection

async def check():
    async with get_mysql_connection() as mysql_conn:
        async with mysql_conn.cursor() as cursor:
            await cursor.execute("SHOW TABLES")
            tables = await cursor.fetchall()

            print('Tablas en MySQL:')
            for t in tables:
                table_name = t[0]
                await cursor.execute(f"SELECT COUNT(*) FROM `{table_name}`")
                count = (await cursor.fetchone())[0]
                print(f"  {table_name}: {count} registros")

asyncio.run(check())
