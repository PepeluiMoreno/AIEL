"""
Proceso nocturno de anonimización de datos personales (RGPD).

Este script debe ejecutarse periódicamente (idealmente cada noche) para:
1. Identificar miembros cuyo fecha_limite_retencion ha vencido
2. Anonimizar sus datos personales (nombre, email, teléfono, DNI, dirección)
3. Conservar datos contables (cuotas, donaciones) para cumplir PGC ESFL

IMPORTANTE: Solo anonimiza miembros que:
- Tienen fecha_limite_retencion < HOY (han pasado 6 años desde la baja)
- Tienen solicita_supresion_datos = true O fecha_limite_retencion vencida
- NO están ya anonimizados (datos_anonimizados = false)

Ejecución:
    python -m app.scripts.jobs.anonimizar_datos_rgpd [--dry-run] [--force-all]

Opciones:
    --dry-run    Simula la ejecución sin hacer cambios
    --force-all  Anonimiza todos los que cumplen fecha, aunque no hayan solicitado supresión
"""
import asyncio
import argparse
import uuid
from datetime import date, datetime
from typing import Optional
from sqlalchemy.ext.asyncio import AsyncSession, create_async_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy import text

from app.core.database import get_database_url


# Valor para campos anonimizados
NOMBRE_ANONIMO = "ANONIMIZADO"
APELLIDO_ANONIMO = "RGPD"


class AnonimizadorRGPD:
    """Proceso de anonimización de datos personales."""

    def __init__(self, dry_run: bool = False, force_all: bool = False):
        self.dry_run = dry_run
        self.force_all = force_all
        self.stats = {
            'candidatos': 0,
            'anonimizados': 0,
            'ya_anonimizados': 0,
            'errores': 0
        }
        self.log_entries = []

    def log(self, message: str):
        """Registra mensaje en log."""
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        entry = f"[{timestamp}] {message}"
        self.log_entries.append(entry)
        print(entry, flush=True)

    async def obtener_candidatos(self, session: AsyncSession) -> list:
        """Obtiene miembros candidatos a anonimizar."""
        self.log("Buscando miembros candidatos a anonimizar...")

        # Condiciones:
        # 1. fecha_limite_retencion < HOY (periodo legal cumplido)
        # 2. datos_anonimizados = false (no anonimizado aún)
        # 3. (solicita_supresion_datos = true) O (force_all)

        if self.force_all:
            # Anonimizar todos los que cumplen fecha
            query = text("""
                SELECT id, nombre, apellido1, email, numero_documento, fecha_baja, fecha_limite_retencion
                FROM miembros
                WHERE fecha_limite_retencion IS NOT NULL
                  AND fecha_limite_retencion < CURRENT_DATE
                  AND datos_anonimizados = false
                  AND eliminado = false
                ORDER BY fecha_limite_retencion ASC
            """)
        else:
            # Solo los que solicitaron supresión
            query = text("""
                SELECT id, nombre, apellido1, email, numero_documento, fecha_baja, fecha_limite_retencion
                FROM miembros
                WHERE fecha_limite_retencion IS NOT NULL
                  AND fecha_limite_retencion < CURRENT_DATE
                  AND datos_anonimizados = false
                  AND solicita_supresion_datos = true
                  AND eliminado = false
                ORDER BY fecha_limite_retencion ASC
            """)

        result = await session.execute(query)
        candidatos = result.fetchall()
        self.stats['candidatos'] = len(candidatos)

        self.log(f"  Encontrados {len(candidatos)} miembros candidatos")
        return candidatos

    async def anonimizar_miembro(self, session: AsyncSession, miembro_id: str) -> bool:
        """Anonimiza los datos personales de un miembro.

        Datos que se anonimizan:
        - nombre → ANONIMIZADO
        - apellido1 → RGPD
        - apellido2 → NULL
        - email → NULL
        - telefono, telefono2 → NULL
        - direccion, codigo_postal, localidad → NULL
        - numero_documento → NULL (el tipo se conserva para estadísticas)
        - iban → NULL
        - fecha_nacimiento → NULL (la edad ya no es relevante)

        Datos que se CONSERVAN (para PGC ESFL):
        - id (para referencias en cuotas/donaciones)
        - tipo_miembro_id, estado_id, motivo_baja_id
        - agrupacion_id (para estadísticas territoriales)
        - fecha_alta, fecha_baja
        - Todos los campos de auditoría (fecha_creacion, etc.)
        """
        if self.dry_run:
            return True

        try:
            await session.execute(
                text("""
                    UPDATE miembros
                    SET
                        -- Datos personales anonimizados
                        nombre = :nombre_anonimo,
                        apellido1 = :apellido_anonimo,
                        apellido2 = NULL,
                        sexo = NULL,
                        fecha_nacimiento = NULL,

                        -- Documento de identidad
                        numero_documento = NULL,
                        -- tipo_documento se conserva para estadísticas

                        -- Contacto
                        email = NULL,
                        telefono = NULL,
                        telefono2 = NULL,

                        -- Dirección
                        direccion = NULL,
                        codigo_postal = NULL,
                        localidad = NULL,
                        -- provincia_id se conserva para estadísticas territoriales

                        -- Datos bancarios
                        iban = NULL,

                        -- Voluntariado (datos personales)
                        profesion = NULL,
                        nivel_estudios = NULL,
                        experiencia_voluntariado = NULL,
                        intereses = NULL,
                        observaciones_voluntariado = NULL,

                        -- Observaciones generales (pueden contener datos personales)
                        observaciones = '[Datos anonimizados por RGPD]',
                        motivo_baja_texto = NULL,

                        -- Marcar como anonimizado
                        datos_anonimizados = true,
                        fecha_anonimizacion = CURRENT_DATE,

                        -- Auditoría
                        fecha_modificacion = NOW()
                    WHERE id = :miembro_id
                """),
                {
                    "miembro_id": miembro_id,
                    "nombre_anonimo": NOMBRE_ANONIMO,
                    "apellido_anonimo": APELLIDO_ANONIMO
                }
            )
            return True
        except Exception as e:
            self.log(f"  ERROR anonimizando {miembro_id}: {e}")
            return False

    async def ejecutar(self, session: AsyncSession):
        """Ejecuta el proceso de anonimización."""
        self.log("="*70)
        self.log("PROCESO DE ANONIMIZACIÓN RGPD")
        self.log("="*70)

        if self.dry_run:
            self.log("*** MODO SIMULACIÓN (dry-run) - No se harán cambios ***")
        if self.force_all:
            self.log("*** MODO FORCE-ALL - Anonimizando todos los vencidos ***")

        # Obtener candidatos
        candidatos = await self.obtener_candidatos(session)

        if not candidatos:
            self.log("No hay miembros pendientes de anonimizar.")
            return

        # Procesar cada candidato
        self.log(f"\nProcesando {len(candidatos)} miembros...")

        for miembro in candidatos:
            miembro_id = str(miembro[0])
            nombre = miembro[1]
            apellido = miembro[2]
            email = miembro[3]
            fecha_limite = miembro[6]

            # Verificar si ya está anonimizado (por nombre)
            if nombre == NOMBRE_ANONIMO:
                self.stats['ya_anonimizados'] += 1
                continue

            self.log(f"  Anonimizando: {nombre} {apellido} (límite: {fecha_limite})")

            if await self.anonimizar_miembro(session, miembro_id):
                self.stats['anonimizados'] += 1
            else:
                self.stats['errores'] += 1

        # Refrescar vista materializada
        if not self.dry_run and self.stats['anonimizados'] > 0:
            self.log("\nRefrescando vista materializada...")
            await session.execute(text("REFRESH MATERIALIZED VIEW vista_miembros_segmentacion"))

        # Resumen
        self.log("\n" + "="*70)
        self.log("RESUMEN")
        self.log("="*70)
        self.log(f"Candidatos encontrados: {self.stats['candidatos']}")
        self.log(f"Anonimizados: {self.stats['anonimizados']}")
        self.log(f"Ya anonimizados (omitidos): {self.stats['ya_anonimizados']}")
        self.log(f"Errores: {self.stats['errores']}")

    async def guardar_log(self, session: AsyncSession):
        """Guarda el log de ejecución en la base de datos."""
        if self.dry_run:
            return

        try:
            log_text = "\n".join(self.log_entries)
            await session.execute(
                text("""
                    INSERT INTO historial_seguridad (
                        id, tipo_evento, descripcion, datos_adicionales,
                        fecha_creacion, eliminado
                    ) VALUES (
                        :id, 'ANONIMIZACION_RGPD', :descripcion, :datos,
                        NOW(), false
                    )
                """),
                {
                    "id": str(uuid.uuid4()),
                    "descripcion": f"Proceso nocturno RGPD: {self.stats['anonimizados']} anonimizados",
                    "datos": log_text[:4000]  # Truncar si es muy largo
                }
            )
        except Exception as e:
            self.log(f"[WARN] No se pudo guardar log en BD: {e}")


async def main():
    """Función principal."""
    parser = argparse.ArgumentParser(
        description='Proceso nocturno de anonimización de datos RGPD'
    )
    parser.add_argument(
        '--dry-run',
        action='store_true',
        help='Simula la ejecución sin hacer cambios'
    )
    parser.add_argument(
        '--force-all',
        action='store_true',
        help='Anonimiza todos los vencidos, aunque no hayan solicitado supresión'
    )
    args = parser.parse_args()

    # Conectar a PostgreSQL
    database_url = get_database_url()
    engine = create_async_engine(
        database_url,
        echo=False,
        connect_args={"server_settings": {"jit": "off"}, "statement_cache_size": 0}
    )
    async_session = sessionmaker(engine, class_=AsyncSession, expire_on_commit=False)

    async with async_session() as session:
        try:
            anonimizador = AnonimizadorRGPD(
                dry_run=args.dry_run,
                force_all=args.force_all
            )

            await anonimizador.ejecutar(session)

            if not args.dry_run:
                await anonimizador.guardar_log(session)
                await session.commit()
                print("\n[OK] Proceso completado y cambios guardados.")
            else:
                print("\n[OK] Simulación completada (sin cambios).")

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
