"""Script para actualizar el tipo_campania_id de las campañas existentes."""
import asyncio
from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession
from sqlalchemy.orm import sessionmaker
from sqlalchemy import text
from app.core.database import get_database_url


async def main():
    print("Conectando a PostgreSQL...")
    database_url = get_database_url()
    engine = create_async_engine(
        database_url,
        echo=False,
        connect_args={"server_settings": {"jit": "off"}, "statement_cache_size": 0}
    )
    async_session = sessionmaker(engine, class_=AsyncSession, expire_on_commit=False)

    async with async_session() as session:
        try:
            # Crear tipos faltantes
            print("\nCreando tipos de campaña faltantes...")

            for codigo, nombre in [("JUDICIAL", "Judicial"), ("EVENTO", "Evento")]:
                result = await session.execute(
                    text("SELECT id FROM tipos_campania WHERE codigo = :codigo"),
                    {"codigo": codigo}
                )
                if not result.fetchone():
                    await session.execute(
                        text("""
                            INSERT INTO tipos_campania (id, codigo, nombre, activo)
                            VALUES (gen_random_uuid(), :codigo, :nombre, true)
                        """),
                        {"codigo": codigo, "nombre": nombre}
                    )
                    print(f"  + Tipo creado: {codigo}")
                else:
                    print(f"  - Tipo ya existe: {codigo}")

            # Mapeo de códigos de campaña a tipos
            mapeo = {
                "EDUCACION": ["CAMP-MATRICULA", "CAMP-RELIGION-FUERA", "CAMP-UNIVERSIDAD", "CAMP-SIMBOLOS"],
                "INSTITUCIONAL": ["CAMP-SEPARACION", "CAMP-MUNICIPIOS", "CAMP-ACUERDOS"],
                "FINANCIACION": ["CAMP-IRPF", "CAMP-FINANCIACION"],
                "PATRIMONIO": ["CAMP-INMATRICULACIONES"],
                "JUDICIAL": ["CAMP-MEDALLA-POLICIAL", "CAMP-FONDO-JUDICIAL"],
                "EVENTO": ["CAMP-CONGRESO-AILP-2022"],
            }

            print("\nActualizando tipo_campania_id de campañas...")
            for tipo_codigo, campanias_codigos in mapeo.items():
                result = await session.execute(
                    text("""
                        UPDATE campanias c
                        SET tipo_campania_id = tc.id
                        FROM tipos_campania tc
                        WHERE tc.codigo = :tipo_codigo
                        AND c.codigo = ANY(:campanias_codigos)
                    """),
                    {"tipo_codigo": tipo_codigo, "campanias_codigos": campanias_codigos}
                )
                print(f"  {tipo_codigo}: {result.rowcount} campañas actualizadas")

            await session.commit()

            # Verificar resultados
            print("\nResultados:")
            result = await session.execute(
                text("""
                    SELECT c.codigo, c.nombre, tc.nombre as tipo
                    FROM campanias c
                    JOIN tipos_campania tc ON c.tipo_campania_id = tc.id
                    ORDER BY tc.nombre, c.nombre
                """)
            )
            for row in result.fetchall():
                print(f"  {row[0]}: {row[1]} -> {row[2]}")

            print("\n[OK] Actualización completada")

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
