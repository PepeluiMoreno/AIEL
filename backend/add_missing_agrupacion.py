"""
Script para agregar la agrupación estatal faltante (código '00000000' / '0').
"""
import asyncio
import uuid
from datetime import datetime
from sqlalchemy.ext.asyncio import create_async_engine, AsyncSession
from sqlalchemy.orm import sessionmaker
from sqlalchemy import select, text

from app.core.database import get_database_url
from app.domains.geografico.models.direccion import AgrupacionTerritorial, Pais
from app.scripts.importacion.mysql_helper import get_mysql_connection
from app.infrastructure.services.encriptacion_service import get_encriptacion_service

async def main():
    print("\n" + "="*80)
    print("AGREGAR AGRUPACION ESTATAL FALTANTE")
    print("="*80 + "\n")

    # Get data from MySQL
    print("Obteniendo datos de MySQL...")
    async with get_mysql_connection() as mysql_conn:
        async with mysql_conn.cursor() as cursor:
            await cursor.execute("""
                SELECT CODAGRUPACION, NOMAGRUPACION, CIF, GESTIONCUOTAS, TITULARCUENTASBANCOS,
                       CUENTAAGRUPIBAN1, NOMBREIBAN1, CUENTAAGRUPIBAN2, NOMBREIBAN2,
                       TELFIJOTRABAJO, TELMOV, WEB, EMAIL, EMAILCOORD, EMAILSECRETARIO,
                       EMAILTESORERO, AMBITO, ESTADO, CODPAISDOM, DIRECCION, CP,
                       LOCALIDAD, OBSERVACIONES
                FROM agrupacionterritorial
                WHERE CODAGRUPACION = '00000000'
            """)
            row = await cursor.fetchone()

            if not row:
                print("[ERROR] No se encontró agrupación con código '00000000' en MySQL")
                return

            mysql_data = {
                'codigo': str(row[0]).lstrip('0') if row[0] else '0',
                'nombre': row[1],
                'cif': row[2],
                'gestion_cuotas': row[3],
                'titular_cuenta': row[4],
                'iban1': row[5],
                'nombre_iban1': row[6],
                'iban2': row[7],
                'nombre_iban2': row[8],
                'tel_fijo': row[9],
                'tel_mov': row[10],
                'web': row[11],
                'email': row[12],
                'email_coord': row[13],
                'email_sec': row[14],
                'email_tes': row[15],
                'ambito': row[16],
                'estado': row[17],
                'direccion': row[19],
                'cp': row[20],
                'localidad': row[21],
                'observaciones': row[22]
            }

            # Normalize codigo
            if mysql_data['codigo'] == '':
                mysql_data['codigo'] = '0'

            print(f"  Código: {mysql_data['codigo']}")
            print(f"  Nombre: {mysql_data['nombre']}")

    # Connect to PostgreSQL
    database_url = get_database_url()
    engine = create_async_engine(
        database_url,
        echo=False,
        connect_args={"server_settings": {"jit": "off"}, "statement_cache_size": 0}
    )
    async_session = sessionmaker(engine, class_=AsyncSession, expire_on_commit=False)

    async with async_session() as session:
        try:
            # Check if already exists
            result = await session.execute(
                select(AgrupacionTerritorial).where(
                    AgrupacionTerritorial.codigo == mysql_data['codigo']
                )
            )
            existe = result.scalar_one_or_none()

            if existe:
                print(f"\n[INFO] Agrupación con código '{mysql_data['codigo']}' ya existe")
                return

            # Get España as default country
            result = await session.execute(
                select(Pais).where(Pais.codigo == 'ES')
            )
            pais_espana = result.scalar_one_or_none()
            if not pais_espana:
                print("[ERROR] No se encontró el país España en la base de datos")
                return

            # Encrypt IBANs
            servicio_encriptacion = get_encriptacion_service()
            iban1_encriptado = None
            iban2_encriptado = None

            if mysql_data['iban1']:
                iban1_limpio = str(mysql_data['iban1']).strip().replace(' ', '')
                if len(iban1_limpio) > 5:
                    try:
                        iban1_encriptado = servicio_encriptacion.encriptar_iban(iban1_limpio)
                    except Exception as e:
                        print(f"  [WARN] Error encriptando IBAN1: {e}")

            if mysql_data['iban2']:
                iban2_limpio = str(mysql_data['iban2']).strip().replace(' ', '')
                if len(iban2_limpio) > 5:
                    try:
                        iban2_encriptado = servicio_encriptacion.encriptar_iban(iban2_limpio)
                    except Exception as e:
                        print(f"  [WARN] Error encriptando IBAN2: {e}")

            # Create agrupacion
            agrupacion_uuid = uuid.uuid4()
            agrupacion = AgrupacionTerritorial(
                id=agrupacion_uuid,
                codigo=mysql_data['codigo'],
                nombre=mysql_data['nombre'] or 'Europa Laica Estatal e Internacional',
                tipo='ESTATAL',
                activo=True,
                pais_id=pais_espana.id,
                provincia_id=None,
                telefono=mysql_data['tel_fijo'] or mysql_data['tel_mov'],
                email=mysql_data['email'],
                web=mysql_data['web']
            )

            session.add(agrupacion)

            # Add to temp_id_mapping
            await session.execute(
                text("""
                INSERT INTO temp_id_mapping (tabla, old_id, new_uuid)
                VALUES ('AGRUPACION', :old_id, :new_uuid)
                ON CONFLICT (tabla, old_id) DO UPDATE SET new_uuid = EXCLUDED.new_uuid
                """),
                {"old_id": 0, "new_uuid": str(agrupacion_uuid)}
            )

            await session.commit()

            print(f"\n[OK] Agrupación '{mysql_data['nombre']}' agregada con código '{mysql_data['codigo']}'")
            print(f"UUID: {agrupacion_uuid}")

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
