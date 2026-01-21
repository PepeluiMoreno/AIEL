"""
Script para cargar miembros desde miembros_import.csv a PostgreSQL.

Trunca la tabla miembros y carga los 2272 registros desde el CSV.
"""
import asyncio
import csv
from pathlib import Path
from datetime import datetime
from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession
from sqlalchemy.orm import sessionmaker
from sqlalchemy import text

DATABASE_URL = 'postgresql+asyncpg://siga:siga_dev_2024@localhost:5432/siga'


async def main():
    """Función principal."""
    data_dir = Path(__file__).parent / 'data'
    csv_path = data_dir / 'miembros_import.csv'

    print("\n" + "="*70)
    print("CARGA DE MIEMBROS DESDE CSV")
    print("="*70)

    if not csv_path.exists():
        print(f"[ERROR] No existe {csv_path}")
        return

    # Conectar
    print("\nConectando a PostgreSQL...")
    engine = create_async_engine(DATABASE_URL, echo=False)
    async_session = sessionmaker(engine, class_=AsyncSession, expire_on_commit=False)

    async with async_session() as session:
        try:
            # 1. Contar registros actuales
            result = await session.execute(text("SELECT COUNT(*) FROM miembros"))
            count_antes = result.scalar()
            print(f"  Miembros actuales: {count_antes}")

            # 2. Truncar tabla
            print("\nTruncando tabla miembros...")
            await session.execute(text("TRUNCATE TABLE miembros CASCADE"))
            print("  [OK] Tabla truncada")

            # 3. Cargar CSV
            print("\nCargando desde CSV...")

            with open(csv_path, 'r', encoding='utf-8') as f:
                reader = csv.DictReader(f)

                insertados = 0
                errores = 0

                for row in reader:
                    try:
                        # Convertir valores vacíos a None
                        def to_none(val):
                            if val == '' or val is None:
                                return None
                            return val

                        def to_bool(val):
                            return val.lower() == 'true' if val else False

                        def to_date(val):
                            if val == '' or val is None:
                                return None
                            try:
                                return datetime.strptime(val.split('T')[0], '%Y-%m-%d').date()
                            except:
                                return None

                        def to_datetime(val):
                            if val == '' or val is None:
                                return None
                            try:
                                if 'T' in val:
                                    return datetime.fromisoformat(val)
                                return datetime.strptime(val, '%Y-%m-%d')
                            except:
                                return None

                        await session.execute(
                            text("""
                                INSERT INTO miembros (
                                    id, nombre, apellido1, apellido2, sexo, fecha_nacimiento,
                                    tipo_miembro_id, estado_id, motivo_baja_id,
                                    tipo_documento, numero_documento, pais_documento_id,
                                    direccion, codigo_postal, localidad, provincia_id, pais_domicilio_id,
                                    telefono, telefono2, email, agrupacion_id, iban,
                                    fecha_alta, fecha_baja, observaciones, activo,
                                    es_voluntario, profesion, nivel_estudios, intereses,
                                    puede_conducir, vehiculo_propio, disponibilidad_viajar,
                                    datos_anonimizados, fecha_creacion, eliminado
                                ) VALUES (
                                    :id, :nombre, :apellido1, :apellido2, :sexo, :fecha_nacimiento,
                                    :tipo_miembro_id, :estado_id, :motivo_baja_id,
                                    :tipo_documento, :numero_documento, :pais_documento_id,
                                    :direccion, :codigo_postal, :localidad, :provincia_id, :pais_domicilio_id,
                                    :telefono, :telefono2, :email, :agrupacion_id, :iban,
                                    COALESCE(:fecha_alta, '1900-01-01'::date), :fecha_baja, :observaciones, :activo,
                                    :es_voluntario, :profesion, :nivel_estudios, :intereses,
                                    :puede_conducir, :vehiculo_propio, :disponibilidad_viajar,
                                    :datos_anonimizados, :fecha_creacion, :eliminado
                                )
                            """),
                            {
                                'id': row['id'],
                                'nombre': row['nombre'],
                                'apellido1': row['apellido1'],
                                'apellido2': to_none(row['apellido2']),
                                'sexo': to_none(row['sexo']),
                                'fecha_nacimiento': to_date(row['fecha_nacimiento']),
                                'tipo_miembro_id': row['tipo_miembro_id'],
                                'estado_id': row['estado_id'],
                                'motivo_baja_id': to_none(row['motivo_baja_id']),
                                'tipo_documento': to_none(row['tipo_documento']),
                                'numero_documento': to_none(row['numero_documento']),
                                'pais_documento_id': to_none(row['pais_documento_id']),
                                'direccion': to_none(row['direccion']),
                                'codigo_postal': to_none(row['codigo_postal']),
                                'localidad': to_none(row['localidad']),
                                'provincia_id': to_none(row['provincia_id']),
                                'pais_domicilio_id': to_none(row['pais_domicilio_id']),
                                'telefono': to_none(row['telefono']),
                                'telefono2': to_none(row['telefono2']),
                                'email': to_none(row['email']),
                                'agrupacion_id': to_none(row['agrupacion_id']),
                                'iban': to_none(row['iban']),
                                'fecha_alta': to_date(row['fecha_alta']),
                                'fecha_baja': to_date(row['fecha_baja']),
                                'observaciones': to_none(row['observaciones']),
                                'activo': to_bool(row['activo']),
                                'es_voluntario': to_bool(row['es_voluntario']),
                                'profesion': to_none(row['profesion']),
                                'nivel_estudios': to_none(row['nivel_estudios']),
                                'intereses': to_none(row['intereses']),
                                'puede_conducir': to_bool(row['puede_conducir']),
                                'vehiculo_propio': to_bool(row['vehiculo_propio']),
                                'disponibilidad_viajar': to_bool(row['disponibilidad_viajar']),
                                'datos_anonimizados': to_bool(row['datos_anonimizados']),
                                'fecha_creacion': to_datetime(row['fecha_creacion']),
                                'eliminado': to_bool(row['eliminado']),
                            }
                        )
                        insertados += 1

                        if insertados % 500 == 0:
                            await session.flush()
                            print(f"  Insertados {insertados}...")

                    except Exception as e:
                        errores += 1
                        if errores <= 3:
                            print(f"  [ERROR] {row['nombre']}: {e}")

            await session.commit()
            print(f"  [OK] {insertados} miembros insertados")
            if errores > 0:
                print(f"  [WARN] {errores} errores")

            # 4. Verificar
            print("\n" + "="*70)
            print("VERIFICACIÓN")
            print("="*70)

            result = await session.execute(text("""
                SELECT em.nombre, COUNT(*)
                FROM miembros m
                JOIN estados_miembro em ON m.estado_id = em.id
                GROUP BY em.nombre
                ORDER BY COUNT(*) DESC
            """))
            print("\nPor estado:")
            for row in result:
                print(f"  {row[0]:20} {row[1]:>5}")

            result = await session.execute(text("""
                SELECT
                    COALESCE(mb.nombre, '(activos)') as motivo,
                    COUNT(*)
                FROM miembros m
                LEFT JOIN motivos_baja mb ON m.motivo_baja_id = mb.id
                GROUP BY COALESCE(mb.nombre, '(activos)')
                ORDER BY COUNT(*) DESC
            """))
            print("\nPor motivo de baja:")
            for row in result:
                print(f"  {row[0]:20} {row[1]:>5}")

            result = await session.execute(text("SELECT COUNT(*) FROM miembros"))
            total = result.scalar()
            print(f"\nTOTAL: {total} miembros")

        except Exception as e:
            await session.rollback()
            print(f"\n[ERROR] {e}")
            import traceback
            traceback.print_exc()
            raise
        finally:
            await engine.dispose()


if __name__ == "__main__":
    asyncio.run(main())
