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
        result = await conn.execute(text("SELECT codigo FROM agrupaciones_territoriales ORDER BY codigo"))
        rows = result.fetchall()
        print(f'Total agrupaciones in PostgreSQL: {len(rows)}')
        print('Códigos:')
        codes = []
        for r in rows:
            code = str(r[0])
            codes.append(code)
            print(f"  '{code}'")

        # MySQL codes (stripped of leading zeros)
        mysql_codes = [
            '0', '104000', '111000', '114000', '125000', '200000', '300000',
            '400000', '500000', '535000', '538000', '600000', '705000', '709000',
            '724000', '734000', '737000', '800000', '900000', '1000000', '1100000',
            '1300000', '1400000', '1505000', '1511000', '1533000', '1600000',
            '1700000', '1805000', '1814000', '1820000', '1900000', '2005000',
            '2010000', '2012000', '2100000', '2200000', '2300000', '2400000',
            '2505000', '2515000', '2526000', '2539000', '2600000', '2700000',
            '2800000', '2900000', '3000000', '3100000', '3200000', '3300000',
            '3400000'
        ]

        print(f'\nMySQL unique codes: {len(mysql_codes)}')

        # Find missing
        missing = [c for c in mysql_codes if c not in codes]
        print(f'\nMissing agrupaciones (in MySQL but not PostgreSQL): {len(missing)}')
        for m in missing:
            print(f"  '{m}'")

    await engine.dispose()

asyncio.run(check())
