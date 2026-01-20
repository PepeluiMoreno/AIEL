"""add vista_miembros_segmentacion materialized view

Revision ID: 34e1d423d0c9
Revises: 0e023bd10912
Create Date: 2026-01-20 10:23:24.324753
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa


revision: str = '34e1d423d0c9'
down_revision: Union[str, None] = '0e023bd10912'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Crear vista materializada para segmentación de miembros
    op.execute("""
        CREATE MATERIALIZED VIEW IF NOT EXISTS vista_miembros_segmentacion AS
        SELECT
            m.id,
            m.nombre,
            m.apellido1,
            m.apellido2,
            m.email,
            m.fecha_nacimiento,
            -- Cálculo de edad
            CASE
                WHEN m.fecha_nacimiento IS NOT NULL THEN
                    EXTRACT(YEAR FROM age(CURRENT_DATE, m.fecha_nacimiento))::integer
                ELSE NULL
            END as edad,
            -- Tipo y estado
            tm.codigo as tipo_miembro_codigo,
            em.codigo as estado_codigo,
            -- Agrupación
            m.agrupacion_id,
            a.codigo as agrupacion_codigo,
            -- Segmentación: es_joven (menores de 30)
            CASE
                WHEN m.fecha_nacimiento IS NOT NULL
                     AND EXTRACT(YEAR FROM age(CURRENT_DATE, m.fecha_nacimiento)) < 30
                THEN true
                ELSE false
            END as es_joven,
            -- Segmentación: es_simpatizante
            CASE
                WHEN tm.codigo = 'SIMPATIZANTE' THEN true
                ELSE false
            END as es_simpatizante,
            -- Segmentación: es_voluntario_disponible
            CASE
                WHEN m.es_voluntario = true
                     AND m.fecha_baja IS NULL
                     AND (m.disponibilidad IS NOT NULL OR COALESCE(m.horas_disponibles_semana, 0) > 0)
                THEN true
                ELSE false
            END as es_voluntario_disponible,
            -- Datos voluntariado
            m.es_voluntario,
            m.disponibilidad,
            -- Fechas
            m.fecha_alta,
            m.fecha_baja
        FROM miembros m
        INNER JOIN tipos_miembro tm ON m.tipo_miembro_id = tm.id
        INNER JOIN estados_miembro em ON m.estado_id = em.id
        LEFT JOIN vista_agrupaciones_territoriales a ON m.agrupacion_id = a.id
        WHERE m.eliminado = FALSE
    """)

    # Crear índices en la vista materializada
    op.execute("""
        CREATE UNIQUE INDEX IF NOT EXISTS idx_vista_miembro_seg_id
            ON vista_miembros_segmentacion(id)
    """)
    op.execute("""
        CREATE INDEX IF NOT EXISTS idx_vista_miembro_seg_es_joven
            ON vista_miembros_segmentacion(es_joven) WHERE es_joven = true
    """)
    op.execute("""
        CREATE INDEX IF NOT EXISTS idx_vista_miembro_seg_es_simpatizante
            ON vista_miembros_segmentacion(es_simpatizante) WHERE es_simpatizante = true
    """)
    op.execute("""
        CREATE INDEX IF NOT EXISTS idx_vista_miembro_seg_es_voluntario
            ON vista_miembros_segmentacion(es_voluntario_disponible) WHERE es_voluntario_disponible = true
    """)
    op.execute("""
        CREATE INDEX IF NOT EXISTS idx_vista_miembro_seg_estado
            ON vista_miembros_segmentacion(estado_codigo)
    """)
    op.execute("""
        CREATE INDEX IF NOT EXISTS idx_vista_miembro_seg_agrupacion
            ON vista_miembros_segmentacion(agrupacion_id)
    """)
    op.execute("""
        CREATE INDEX IF NOT EXISTS idx_vista_miembro_seg_email
            ON vista_miembros_segmentacion(email) WHERE email IS NOT NULL
    """)


def downgrade() -> None:
    op.execute("DROP MATERIALIZED VIEW IF EXISTS vista_miembros_segmentacion CASCADE")
