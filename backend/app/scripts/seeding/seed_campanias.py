"""
Script de seeding para campañas desde CSV.

Lee el archivo data/campanias.csv y carga las campañas en PostgreSQL.
Usa SQL directo para mayor control y trazabilidad.
"""
import asyncio
import csv
import uuid
from pathlib import Path
from sqlalchemy.ext.asyncio import AsyncSession, create_async_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy import text

from app.core.database import get_database_url


# Mapeo de tipos de campaña (código CSV → código en BD)
TIPOS_CAMPANIA = {
    "EDUCACION": "EDUCACION",
    "FINANCIACION": "FINANCIACION",
    "INSTITUCIONAL": "INSTITUCIONAL",
    "PATRIMONIO": "PATRIMONIO",
    "JUDICIAL": "JUDICIAL",
    "EVENTO": "EVENTO",
}

# Mapeo de estados de campaña
ESTADOS_CAMPANIA = {
    "ACTIVA": "ACTIVA",
    "FINALIZADA": "FINALIZADA",
    "SUSPENDIDA": "SUSPENDIDA",
    "PLANIFICADA": "PLANIFICADA",
}


async def obtener_o_crear_tipo_campania(session: AsyncSession, codigo: str) -> uuid.UUID:
    """Obtiene o crea un tipo de campaña."""
    result = await session.execute(
        text("SELECT id FROM tipos_campania WHERE codigo = :codigo"),
        {"codigo": codigo}
    )
    row = result.fetchone()

    if row:
        return row[0]

    # Crear tipo
    nuevo_id = uuid.uuid4()
    nombres = {
        "EDUCACION": "Educación",
        "FINANCIACION": "Financiación",
        "INSTITUCIONAL": "Institucional",
        "PATRIMONIO": "Patrimonio",
        "JUDICIAL": "Judicial",
        "EVENTO": "Evento",
    }
    await session.execute(
        text("""
            INSERT INTO tipos_campania (id, codigo, nombre, activo)
            VALUES (:id, :codigo, :nombre, true)
        """),
        {"id": nuevo_id, "codigo": codigo, "nombre": nombres.get(codigo, codigo)}
    )
    print(f"  + Tipo campaña creado: {codigo}")
    return nuevo_id


async def obtener_o_crear_estado_campania(session: AsyncSession, codigo: str) -> uuid.UUID:
    """Obtiene o crea un estado de campaña."""
    result = await session.execute(
        text("SELECT id FROM estados_campania WHERE codigo = :codigo"),
        {"codigo": codigo}
    )
    row = result.fetchone()

    if row:
        return row[0]

    # Crear estado
    nuevo_id = uuid.uuid4()
    nombres = {
        "ACTIVA": "Activa",
        "FINALIZADA": "Finalizada",
        "SUSPENDIDA": "Suspendida",
        "PLANIFICADA": "Planificada",
    }
    await session.execute(
        text("""
            INSERT INTO estados_campania (id, codigo, nombre, descripcion, activo, es_estado_final, orden)
            VALUES (:id, :codigo, :nombre, :descripcion, true, :es_final, :orden)
        """),
        {
            "id": nuevo_id,
            "codigo": codigo,
            "nombre": nombres.get(codigo, codigo),
            "descripcion": f"Campaña en estado {nombres.get(codigo, codigo).lower()}",
            "es_final": codigo in ("FINALIZADA", "SUSPENDIDA"),
            "orden": {"PLANIFICADA": 1, "ACTIVA": 2, "FINALIZADA": 3, "SUSPENDIDA": 4}.get(codigo, 5)
        }
    )
    print(f"  + Estado campaña creado: {codigo}")
    return nuevo_id


async def cargar_campanias_desde_csv(session: AsyncSession, csv_path: Path) -> int:
    """Carga campañas desde archivo CSV."""
    print(f"\nLeyendo campañas desde {csv_path}...")

    campanias_creadas = 0
    campanias_existentes = 0

    with open(csv_path, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)

        for row in reader:
            codigo = row['codigo'].strip()
            nombre = row['nombre'].strip()
            lema = row.get('lema', '').strip() or None
            descripcion_corta = row.get('descripcion_corta', '').strip() or None
            url_externa = row.get('url_externa', '').strip() or None
            tipo_codigo = row.get('tipo', 'INSTITUCIONAL').strip()
            estado_codigo = row.get('estado', 'ACTIVA').strip()

            # Verificar si ya existe
            result = await session.execute(
                text("SELECT id FROM campanias WHERE codigo = :codigo"),
                {"codigo": codigo}
            )
            if result.fetchone():
                campanias_existentes += 1
                continue

            # Obtener o crear tipo y estado
            tipo_id = await obtener_o_crear_tipo_campania(session, tipo_codigo)
            estado_id = await obtener_o_crear_estado_campania(session, estado_codigo)

            # Insertar campaña
            campania_id = uuid.uuid4()
            await session.execute(
                text("""
                    INSERT INTO campanias (
                        id, codigo, nombre, lema, descripcion_corta, url_externa,
                        tipo_campania_id, estado_id
                    ) VALUES (
                        :id, :codigo, :nombre, :lema, :descripcion_corta, :url_externa,
                        :tipo_campania_id, :estado_id
                    )
                """),
                {
                    "id": campania_id,
                    "codigo": codigo,
                    "nombre": nombre,
                    "lema": lema,
                    "descripcion_corta": descripcion_corta,
                    "url_externa": url_externa,
                    "tipo_campania_id": tipo_id,
                    "estado_id": estado_id,
                }
            )
            campanias_creadas += 1
            print(f"  + {codigo}: {nombre}")

    return campanias_creadas, campanias_existentes


async def main():
    """Función principal."""
    print("\n" + "=" * 80)
    print("SEEDING DE CAMPAÑAS DESDE CSV")
    print("=" * 80)

    # Ruta al CSV (en la carpeta data del proyecto raíz SIGA)
    # __file__ está en backend/app/scripts/seeding/seed_campanias.py
    # Subimos 4 niveles para llegar a SIGA/
    proyecto_raiz = Path(__file__).resolve().parent.parent.parent.parent.parent
    csv_path = proyecto_raiz / "data" / "campanias.csv"

    if not csv_path.exists():
        print(f"\n[ERROR] No se encontró el archivo: {csv_path}")
        return

    # Conectar a PostgreSQL
    print("\nConectando a PostgreSQL...")
    database_url = get_database_url()
    engine = create_async_engine(
        database_url,
        echo=False,
        connect_args={"server_settings": {"jit": "off"}, "statement_cache_size": 0}
    )
    async_session = sessionmaker(engine, class_=AsyncSession, expire_on_commit=False)

    async with async_session() as session:
        try:
            creadas, existentes = await cargar_campanias_desde_csv(session, csv_path)

            await session.commit()

            print("\n" + "=" * 80)
            print("[OK] SEEDING COMPLETADO")
            print("=" * 80)
            print(f"\nCampañas creadas: {creadas}")
            print(f"Campañas existentes (omitidas): {existentes}")

        except Exception as e:
            await session.rollback()
            print(f"\n[ERROR]: {e}")
            import traceback
            traceback.print_exc()
            raise
        finally:
            await engine.dispose()


if __name__ == "__main__":
    asyncio.run(main())
