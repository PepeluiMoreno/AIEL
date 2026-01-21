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


async def obtener_o_crear_tipo_campania(session: AsyncSession, tipo_csv: str) -> uuid.UUID:
    """Obtiene o crea un tipo de campaña por nombre."""
    # Mapeo de código CSV a nombre legible
    nombres = {
        "EDUCACION": "Educación",
        "FINANCIACION": "Financiación",
        "INSTITUCIONAL": "Institucional",
        "PATRIMONIO": "Patrimonio",
        "JUDICIAL": "Judicial",
        "EVENTO": "Evento",
    }
    nombre = nombres.get(tipo_csv, tipo_csv)

    result = await session.execute(
        text("SELECT id FROM tipos_campania WHERE nombre = :nombre"),
        {"nombre": nombre}
    )
    row = result.fetchone()

    if row:
        return row[0]

    # Crear tipo
    nuevo_id = uuid.uuid4()
    await session.execute(
        text("""
            INSERT INTO tipos_campania (id, nombre, activo)
            VALUES (:id, :nombre, true)
        """),
        {"id": nuevo_id, "nombre": nombre}
    )
    print(f"  + Tipo campaña creado: {nombre}")
    return nuevo_id


async def obtener_o_crear_estado_campania(session: AsyncSession, estado_csv: str) -> uuid.UUID:
    """Obtiene o crea un estado de campaña por nombre."""
    # Mapeo de código CSV a nombre legible
    nombres = {
        "ACTIVA": "Activa",
        "FINALIZADA": "Finalizada",
        "SUSPENDIDA": "Suspendida",
        "PLANIFICADA": "Planificada",
    }
    nombre = nombres.get(estado_csv, estado_csv)

    result = await session.execute(
        text("SELECT id FROM estados_campania WHERE nombre = :nombre"),
        {"nombre": nombre}
    )
    row = result.fetchone()

    if row:
        return row[0]

    # Crear estado
    nuevo_id = uuid.uuid4()
    await session.execute(
        text("""
            INSERT INTO estados_campania (id, nombre, descripcion, activo, orden)
            VALUES (:id, :nombre, :descripcion, true, :orden)
        """),
        {
            "id": nuevo_id,
            "nombre": nombre,
            "descripcion": f"Campaña en estado {nombre.lower()}",
            "orden": {"Planificada": 1, "Activa": 2, "Finalizada": 3, "Suspendida": 4}.get(nombre, 5)
        }
    )
    print(f"  + Estado campaña creado: {nombre}")
    return nuevo_id


# UUIDs conocidos
JOSE_ANTONIO_NAZ_ID = uuid.UUID('d5041b2a-7689-4634-a96a-321f67d94f9c')
CARGO_PRESIDENTE_ID = uuid.UUID('01c1a556-9a90-4cdf-a2f2-f6c82d797062')


async def obtener_responsable_default(session: AsyncSession) -> uuid.UUID:
    """Obtiene el ID del presidente (José Antonio Naz Álvarez) y le asigna el cargo si no lo tiene."""

    # Asignar cargo de presidente a José Antonio Naz si no lo tiene
    await session.execute(
        text("""
            UPDATE miembros
            SET cargo_id = :cargo_id
            WHERE id = :miembro_id AND (cargo_id IS NULL OR cargo_id != :cargo_id)
        """),
        {"cargo_id": CARGO_PRESIDENTE_ID, "miembro_id": JOSE_ANTONIO_NAZ_ID}
    )

    # Verificar que existe
    result = await session.execute(
        text("SELECT nombre, apellido1 FROM miembros WHERE id = :id"),
        {"id": JOSE_ANTONIO_NAZ_ID}
    )
    row = result.fetchone()
    if row:
        print(f"  Responsable (Presidente): {row[0]} {row[1]}")
    else:
        print(f"  [WARN] No se encontró el miembro con ID {JOSE_ANTONIO_NAZ_ID}")

    return JOSE_ANTONIO_NAZ_ID


async def cargar_campanias_desde_csv(session: AsyncSession, csv_path: Path) -> int:
    """Carga campañas desde archivo CSV."""
    print(f"\nLeyendo campañas desde {csv_path}...")

    # Obtener responsable por defecto (José Antonio Naz Álvarez)
    responsable_id = await obtener_responsable_default(session)

    campanias_creadas = 0
    campanias_existentes = 0

    with open(csv_path, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)

        for row in reader:
            nombre = row['nombre'].strip()
            lema = row.get('lema', '').strip() or None
            descripcion_corta = row.get('descripcion_corta', '').strip() or None
            url_externa = row.get('url_externa', '').strip() or None
            tipo_codigo = row.get('tipo', 'INSTITUCIONAL').strip()
            estado_codigo = row.get('estado', 'ACTIVA').strip()

            # Verificar si ya existe por nombre
            result = await session.execute(
                text("SELECT id FROM campanias WHERE nombre = :nombre"),
                {"nombre": nombre}
            )
            if result.fetchone():
                campanias_existentes += 1
                continue

            # Obtener o crear tipo y estado
            tipo_id = await obtener_o_crear_tipo_campania(session, tipo_codigo)
            estado_id = await obtener_o_crear_estado_campania(session, estado_codigo)

            # Insertar campaña con responsable
            campania_id = uuid.uuid4()
            await session.execute(
                text("""
                    INSERT INTO campanias (
                        id, nombre, lema, descripcion_corta, url_externa,
                        tipo_campania_id, estado_id, responsable_id
                    ) VALUES (
                        :id, :nombre, :lema, :descripcion_corta, :url_externa,
                        :tipo_campania_id, :estado_id, :responsable_id
                    )
                """),
                {
                    "id": campania_id,
                    "nombre": nombre,
                    "lema": lema,
                    "descripcion_corta": descripcion_corta,
                    "url_externa": url_externa,
                    "tipo_campania_id": tipo_id,
                    "estado_id": estado_id,
                    "responsable_id": responsable_id,
                }
            )
            campanias_creadas += 1
            print(f"  + {nombre}")

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
