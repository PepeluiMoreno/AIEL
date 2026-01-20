import asyncio
from app.scripts.importacion.mysql_helper import get_mysql_connection

async def check():
    # Missing codes from comparison
    missing_codes = [
        '0', '125000', '500000', '800000', '900000', '1000000',
        '1505000', '1511000', '1533000', '1600000', '1805000', '1814000',
        '1820000', '2005000', '2010000', '2012000', '2100000', '2200000',
        '2300000', '2400000', '2505000', '2515000', '2526000', '2539000',
        '2600000', '2700000', '2800000', '2900000', '3000000', '3100000',
        '3200000', '3300000', '3400000'
    ]

    print(f'Checking {len(missing_codes)} missing agrupacion codes in MySQL...\n')

    async with get_mysql_connection() as mysql_conn:
        async with mysql_conn.cursor() as cursor:
            # Get all agrupaciones from MySQL
            await cursor.execute("SELECT CODAGRUPACION, NOMAGRUPACION FROM agrupacionterritorial ORDER BY CODAGRUPACION")
            all_agrupaciones = await cursor.fetchall()

            print(f'Total agrupaciones in MySQL: {len(all_agrupaciones)}\n')

            # Check each missing code
            found = []
            not_found = []

            for code in missing_codes:
                # Pad with leading zeros to match MySQL format (8 digits)
                mysql_code = code.zfill(8)

                # Find in MySQL data
                match = None
                for agrup in all_agrupaciones:
                    mysql_agrup_code = str(agrup[0]).lstrip('0')
                    if mysql_agrup_code == '':
                        mysql_agrup_code = '0'
                    if mysql_agrup_code == code:
                        match = agrup
                        break

                if match:
                    found.append((code, match[1]))
                else:
                    not_found.append(code)

            print(f'Found in MySQL agrupacionterritorial: {len(found)}')
            for code, nombre in found[:10]:  # Show first 10
                print(f"  '{code}' -> {nombre}")
            if len(found) > 10:
                print(f"  ... and {len(found) - 10} more")

            print(f'\nNOT found in MySQL agrupacionterritorial: {len(not_found)}')
            for code in not_found:
                print(f"  '{code}'")

asyncio.run(check())
