"""
Microservicio de Anonimización RGPD.

Servicio que se ejecuta periódicamente para anonimizar datos personales
de miembros cuyo periodo de retención legal ha vencido.

Cumplimiento normativo:
- RGPD Art. 17: Derecho de supresión
- PGC ESFL: Retención contable 6 años

Uso:
    python main.py                    # Ejecuta una vez y termina
    python main.py --schedule         # Ejecuta con scheduler interno
    python main.py --dry-run          # Simula sin cambios
    python main.py --force-all        # Procesa todos los vencidos
"""
import asyncio
import os
import sys
import logging
import json
import uuid
from datetime import date, datetime, timedelta
from typing import Optional
import argparse

from sqlalchemy.ext.asyncio import AsyncSession, create_async_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy import text

# Configuración de logging estructurado
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[logging.StreamHandler(sys.stdout)]
)
logger = logging.getLogger("rgpd-anonimizer")


class Config:
    """Configuración del servicio desde variables de entorno."""

    # Base de datos
    DB_HOST: str = os.getenv("DB_HOST", "localhost")
    DB_PORT: int = int(os.getenv("DB_PORT", "5432"))
    DB_NAME: str = os.getenv("DB_NAME", "postgres")
    DB_USER: str = os.getenv("DB_USER", "postgres")
    DB_PASSWORD: str = os.getenv("DB_PASSWORD", "")

    # Scheduler
    SCHEDULE_HOUR: int = int(os.getenv("SCHEDULE_HOUR", "3"))  # 3 AM por defecto
    SCHEDULE_MINUTE: int = int(os.getenv("SCHEDULE_MINUTE", "0"))

    # Opciones de procesamiento
    BATCH_SIZE: int = int(os.getenv("BATCH_SIZE", "100"))
    DRY_RUN: bool = os.getenv("DRY_RUN", "false").lower() == "true"
    FORCE_ALL: bool = os.getenv("FORCE_ALL", "false").lower() == "true"

    # Años de retención legal
    RETENTION_YEARS: int = int(os.getenv("RETENTION_YEARS", "6"))

    @classmethod
    def get_database_url(cls) -> str:
        """Construye la URL de conexión a la base de datos."""
        return f"postgresql+asyncpg://{cls.DB_USER}:{cls.DB_PASSWORD}@{cls.DB_HOST}:{cls.DB_PORT}/{cls.DB_NAME}"


# Valores para anonimización
NOMBRE_ANONIMO = "ANONIMIZADO"
APELLIDO_ANONIMO = "RGPD"


class AnonimizadorRGPD:
    """Motor de anonimización de datos personales."""

    def __init__(self, dry_run: bool = False, force_all: bool = False):
        self.dry_run = dry_run
        self.force_all = force_all
        self.stats = {
            'candidatos': 0,
            'anonimizados': 0,
            'ya_anonimizados': 0,
            'errores': 0,
            'inicio': None,
            'fin': None
        }

    async def obtener_candidatos(self, session: AsyncSession) -> list:
        """Obtiene miembros candidatos a anonimizar."""
        logger.info("Buscando miembros candidatos a anonimizar...")

        if self.force_all:
            query = text("""
                SELECT id, nombre, apellido1, email, fecha_baja, fecha_limite_retencion
                FROM miembros
                WHERE fecha_limite_retencion IS NOT NULL
                  AND fecha_limite_retencion < CURRENT_DATE
                  AND datos_anonimizados = false
                  AND eliminado = false
                ORDER BY fecha_limite_retencion ASC
                LIMIT :batch_size
            """)
        else:
            query = text("""
                SELECT id, nombre, apellido1, email, fecha_baja, fecha_limite_retencion
                FROM miembros
                WHERE fecha_limite_retencion IS NOT NULL
                  AND fecha_limite_retencion < CURRENT_DATE
                  AND datos_anonimizados = false
                  AND solicita_supresion_datos = true
                  AND eliminado = false
                ORDER BY fecha_limite_retencion ASC
                LIMIT :batch_size
            """)

        result = await session.execute(query, {"batch_size": Config.BATCH_SIZE})
        candidatos = result.fetchall()
        self.stats['candidatos'] = len(candidatos)

        logger.info(f"Encontrados {len(candidatos)} miembros candidatos")
        return candidatos

    async def anonimizar_miembro(self, session: AsyncSession, miembro_id: str) -> bool:
        """Anonimiza los datos personales de un miembro.

        Conserva:
        - ID y referencias (para cuotas/donaciones)
        - Tipo, estado, motivo de baja
        - Agrupación territorial (estadísticas)
        - Fechas de alta/baja
        - Campos de auditoría

        Anonimiza:
        - Nombre, apellidos
        - DNI, email, teléfonos
        - Dirección completa
        - IBAN
        - Datos de voluntariado
        """
        if self.dry_run:
            return True

        try:
            await session.execute(
                text("""
                    UPDATE miembros
                    SET
                        nombre = :nombre_anonimo,
                        apellido1 = :apellido_anonimo,
                        apellido2 = NULL,
                        sexo = NULL,
                        fecha_nacimiento = NULL,
                        numero_documento = NULL,
                        email = NULL,
                        telefono = NULL,
                        telefono2 = NULL,
                        direccion = NULL,
                        codigo_postal = NULL,
                        localidad = NULL,
                        iban = NULL,
                        profesion = NULL,
                        nivel_estudios = NULL,
                        experiencia_voluntariado = NULL,
                        intereses = NULL,
                        observaciones_voluntariado = NULL,
                        observaciones = '[Datos anonimizados por RGPD]',
                        motivo_baja_texto = NULL,
                        datos_anonimizados = true,
                        fecha_anonimizacion = CURRENT_DATE,
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
            logger.error(f"Error anonimizando {miembro_id}: {e}")
            return False

    async def ejecutar(self, session: AsyncSession) -> dict:
        """Ejecuta el proceso de anonimización."""
        self.stats['inicio'] = datetime.now().isoformat()

        logger.info("=" * 60)
        logger.info("PROCESO DE ANONIMIZACIÓN RGPD")
        logger.info("=" * 60)

        if self.dry_run:
            logger.info("*** MODO SIMULACIÓN (dry-run) ***")
        if self.force_all:
            logger.info("*** MODO FORCE-ALL ***")

        # Obtener candidatos
        candidatos = await self.obtener_candidatos(session)

        if not candidatos:
            logger.info("No hay miembros pendientes de anonimizar")
            self.stats['fin'] = datetime.now().isoformat()
            return self.stats

        # Procesar
        for miembro in candidatos:
            miembro_id = str(miembro[0])
            nombre = miembro[1]
            apellido = miembro[2]

            if nombre == NOMBRE_ANONIMO:
                self.stats['ya_anonimizados'] += 1
                continue

            logger.info(f"Anonimizando: {nombre} {apellido}")

            if await self.anonimizar_miembro(session, miembro_id):
                self.stats['anonimizados'] += 1
            else:
                self.stats['errores'] += 1

        # Refrescar vista materializada
        if not self.dry_run and self.stats['anonimizados'] > 0:
            logger.info("Refrescando vista materializada...")
            try:
                await session.execute(text(
                    "REFRESH MATERIALIZED VIEW vista_miembros_segmentacion"
                ))
            except Exception as e:
                logger.warning(f"No se pudo refrescar vista: {e}")

        self.stats['fin'] = datetime.now().isoformat()

        # Log resumen
        logger.info("=" * 60)
        logger.info("RESUMEN")
        logger.info("=" * 60)
        logger.info(f"Candidatos: {self.stats['candidatos']}")
        logger.info(f"Anonimizados: {self.stats['anonimizados']}")
        logger.info(f"Ya anonimizados: {self.stats['ya_anonimizados']}")
        logger.info(f"Errores: {self.stats['errores']}")

        return self.stats

    async def registrar_ejecucion(self, session: AsyncSession):
        """Registra la ejecución en historial de seguridad."""
        if self.dry_run:
            return

        try:
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
                    "descripcion": f"Anonimización RGPD: {self.stats['anonimizados']} procesados",
                    "datos": json.dumps(self.stats)
                }
            )
        except Exception as e:
            logger.warning(f"No se pudo registrar en historial: {e}")


class HealthCheck:
    """Verificaciones de salud del servicio."""

    @staticmethod
    async def check_database(session: AsyncSession) -> bool:
        """Verifica conexión a base de datos."""
        try:
            await session.execute(text("SELECT 1"))
            return True
        except Exception:
            return False

    @staticmethod
    async def check_tables(session: AsyncSession) -> bool:
        """Verifica que existan las tablas necesarias."""
        try:
            await session.execute(text("SELECT COUNT(*) FROM miembros LIMIT 1"))
            await session.execute(text("SELECT COUNT(*) FROM motivos_baja LIMIT 1"))
            return True
        except Exception:
            return False


async def run_once(dry_run: bool = False, force_all: bool = False):
    """Ejecuta el proceso una vez."""
    logger.info("Iniciando servicio de anonimización RGPD...")

    engine = create_async_engine(
        Config.get_database_url(),
        echo=False,
        connect_args={"server_settings": {"jit": "off"}, "statement_cache_size": 0}
    )
    async_session = sessionmaker(engine, class_=AsyncSession, expire_on_commit=False)

    async with async_session() as session:
        try:
            # Health check
            if not await HealthCheck.check_database(session):
                logger.error("No se puede conectar a la base de datos")
                return False

            if not await HealthCheck.check_tables(session):
                logger.error("Tablas necesarias no encontradas")
                return False

            # Ejecutar anonimización
            anonimizador = AnonimizadorRGPD(dry_run=dry_run, force_all=force_all)
            await anonimizador.ejecutar(session)
            await anonimizador.registrar_ejecucion(session)

            if not dry_run:
                await session.commit()

            logger.info("Proceso completado exitosamente")
            return True

        except Exception as e:
            await session.rollback()
            logger.error(f"Error en proceso: {e}")
            return False
        finally:
            await engine.dispose()


async def run_scheduled():
    """Ejecuta el proceso con scheduler interno."""
    logger.info(f"Scheduler iniciado. Ejecución programada: {Config.SCHEDULE_HOUR:02d}:{Config.SCHEDULE_MINUTE:02d}")

    while True:
        now = datetime.now()
        target = now.replace(
            hour=Config.SCHEDULE_HOUR,
            minute=Config.SCHEDULE_MINUTE,
            second=0,
            microsecond=0
        )

        # Si ya pasó la hora objetivo hoy, programar para mañana
        if now >= target:
            target += timedelta(days=1)

        wait_seconds = (target - now).total_seconds()
        logger.info(f"Próxima ejecución: {target.isoformat()} (en {wait_seconds/3600:.1f} horas)")

        await asyncio.sleep(wait_seconds)

        logger.info("Ejecutando tarea programada...")
        await run_once(
            dry_run=Config.DRY_RUN,
            force_all=Config.FORCE_ALL
        )


def main():
    """Punto de entrada del servicio."""
    parser = argparse.ArgumentParser(description='Servicio de Anonimización RGPD')
    parser.add_argument('--schedule', action='store_true', help='Ejecutar con scheduler')
    parser.add_argument('--dry-run', action='store_true', help='Simular sin cambios')
    parser.add_argument('--force-all', action='store_true', help='Procesar todos los vencidos')
    args = parser.parse_args()

    # Override desde args si se especifican
    dry_run = args.dry_run or Config.DRY_RUN
    force_all = args.force_all or Config.FORCE_ALL

    if args.schedule:
        asyncio.run(run_scheduled())
    else:
        success = asyncio.run(run_once(dry_run=dry_run, force_all=force_all))
        sys.exit(0 if success else 1)


if __name__ == "__main__":
    main()
