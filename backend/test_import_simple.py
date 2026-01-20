import asyncio
import sys
from app.scripts.importacion.sql_dump_parser import SQLDumpParser

DUMP_FILE_PATH = r"C:\Users\Jose\dev\SIGA\data\europalaica_com_2026_01_01 apertura de año.sql"

async def main():
    print("\n" + "="*80, flush=True)
    print("TEST IMPORTACION", flush=True)
    print("="*80 + "\n", flush=True)

    print("Cargando parser SQL...", flush=True)
    parser = SQLDumpParser(DUMP_FILE_PATH)
    print(f"  [OK] Parser cargado", flush=True)

    print("\nContando registros de MIEMBRO...", flush=True)
    count = 0
    for row in parser.extraer_inserts('MIEMBRO'):
        count += 1
        if count % 500 == 0:
            print(f"  Procesados {count}...", flush=True)

    print(f"  [OK] Total registros: {count}", flush=True)

if __name__ == "__main__":
    asyncio.run(main())
