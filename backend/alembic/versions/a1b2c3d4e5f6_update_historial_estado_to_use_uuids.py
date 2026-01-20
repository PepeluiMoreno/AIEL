"""update historial_estado to use UUIDs instead of literal codes

Revision ID: a1b2c3d4e5f6
Revises: e45d75d6204a
Create Date: 2026-01-20 17:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa


revision: str = 'a1b2c3d4e5f6'
down_revision: Union[str, None] = 'e45d75d6204a'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Modificar tabla historial_estados para usar UUIDs en lugar de códigos literales

    # 1. Cambiar entidad_id de String a UUID
    op.alter_column('historial_estados', 'entidad_id',
        existing_type=sa.String(100),
        type_=sa.Uuid(),
        existing_nullable=False,
        postgresql_using='entidad_id::uuid'
    )

    # 2. Añadir columna estado_tabla para identificar la tabla de estados
    op.add_column('historial_estados',
        sa.Column('estado_tabla', sa.String(length=50), nullable=True)
    )

    # 3. Añadir columnas UUID para estados
    op.add_column('historial_estados',
        sa.Column('estado_anterior_id', sa.Uuid(), nullable=True)
    )
    op.add_column('historial_estados',
        sa.Column('estado_nuevo_id', sa.Uuid(), nullable=True)
    )

    # 4. Migrar datos existentes: derivar estado_tabla de entidad_tipo
    op.execute("""
        UPDATE historial_estados
        SET estado_tabla = CASE
            WHEN entidad_tipo = 'cuotaanual' THEN 'estados_cuota'
            WHEN entidad_tipo = 'campania' THEN 'estados_campania'
            WHEN entidad_tipo = 'tarea' THEN 'estados_tarea'
            WHEN entidad_tipo = 'actividad' THEN 'estados_actividad'
            WHEN entidad_tipo = 'participante' THEN 'estados_participante'
            WHEN entidad_tipo = 'ordencobro' THEN 'estados_orden_cobro'
            WHEN entidad_tipo = 'remesa' THEN 'estados_remesa'
            WHEN entidad_tipo = 'donacion' THEN 'estados_donacion'
            WHEN entidad_tipo = 'miembro' THEN 'estados_miembro'
            ELSE 'estados_cuota'
        END
        WHERE estado_tabla IS NULL
    """)

    # 5. Hacer estado_tabla NOT NULL después de migrar datos
    op.alter_column('historial_estados', 'estado_tabla',
        existing_type=sa.String(50),
        nullable=False
    )

    # 6. Migrar códigos a UUIDs buscando en las tablas de estados correspondientes
    # Nota: esto solo funciona si los códigos existen en las tablas de estados
    op.execute("""
        UPDATE historial_estados h
        SET estado_anterior_id = (
            SELECT id FROM estados_cuota WHERE codigo = h.estado_anterior_codigo
        )
        WHERE h.estado_tabla = 'estados_cuota'
        AND h.estado_anterior_codigo IS NOT NULL
        AND h.estado_anterior_id IS NULL
    """)

    op.execute("""
        UPDATE historial_estados h
        SET estado_nuevo_id = (
            SELECT id FROM estados_cuota WHERE codigo = h.estado_nuevo_codigo
        )
        WHERE h.estado_tabla = 'estados_cuota'
        AND h.estado_nuevo_codigo IS NOT NULL
        AND h.estado_nuevo_id IS NULL
    """)

    # Repetir para otras tablas de estados si hay datos
    for tabla in ['estados_campania', 'estados_tarea', 'estados_actividad',
                  'estados_participante', 'estados_orden_cobro', 'estados_remesa',
                  'estados_donacion', 'estados_miembro']:
        op.execute(f"""
            UPDATE historial_estados h
            SET estado_anterior_id = (
                SELECT id FROM {tabla} WHERE codigo = h.estado_anterior_codigo
            )
            WHERE h.estado_tabla = '{tabla}'
            AND h.estado_anterior_codigo IS NOT NULL
            AND h.estado_anterior_id IS NULL
        """)

        op.execute(f"""
            UPDATE historial_estados h
            SET estado_nuevo_id = (
                SELECT id FROM {tabla} WHERE codigo = h.estado_nuevo_codigo
            )
            WHERE h.estado_tabla = '{tabla}'
            AND h.estado_nuevo_codigo IS NOT NULL
            AND h.estado_nuevo_id IS NULL
        """)

    # 7. Hacer estado_nuevo_id NOT NULL (estado_anterior_id puede ser NULL para creación inicial)
    op.alter_column('historial_estados', 'estado_nuevo_id',
        existing_type=sa.Uuid(),
        nullable=False
    )

    # 8. Eliminar columnas antiguas con códigos literales
    op.drop_column('historial_estados', 'estado_anterior_codigo')
    op.drop_column('historial_estados', 'estado_nuevo_codigo')

    # 9. Crear índices para las nuevas columnas
    op.create_index('ix_historial_estados_estado_tabla', 'historial_estados', ['estado_tabla'])
    op.create_index('ix_historial_estados_estado_anterior_id', 'historial_estados', ['estado_anterior_id'])
    op.create_index('ix_historial_estados_estado_nuevo_id', 'historial_estados', ['estado_nuevo_id'])


def downgrade() -> None:
    # Eliminar índices nuevos
    op.drop_index('ix_historial_estados_estado_nuevo_id', table_name='historial_estados')
    op.drop_index('ix_historial_estados_estado_anterior_id', table_name='historial_estados')
    op.drop_index('ix_historial_estados_estado_tabla', table_name='historial_estados')

    # Restaurar columnas con códigos
    op.add_column('historial_estados',
        sa.Column('estado_anterior_codigo', sa.String(50), nullable=True)
    )
    op.add_column('historial_estados',
        sa.Column('estado_nuevo_codigo', sa.String(50), nullable=False)
    )

    # Migrar UUIDs de vuelta a códigos (solo para estados_cuota como ejemplo)
    op.execute("""
        UPDATE historial_estados h
        SET estado_anterior_codigo = (
            SELECT codigo FROM estados_cuota WHERE id = h.estado_anterior_id
        )
        WHERE h.estado_tabla = 'estados_cuota'
        AND h.estado_anterior_id IS NOT NULL
    """)

    op.execute("""
        UPDATE historial_estados h
        SET estado_nuevo_codigo = (
            SELECT codigo FROM estados_cuota WHERE id = h.estado_nuevo_id
        )
        WHERE h.estado_tabla = 'estados_cuota'
    """)

    # Eliminar columnas UUID
    op.drop_column('historial_estados', 'estado_nuevo_id')
    op.drop_column('historial_estados', 'estado_anterior_id')
    op.drop_column('historial_estados', 'estado_tabla')

    # Restaurar entidad_id a String
    op.alter_column('historial_estados', 'entidad_id',
        existing_type=sa.Uuid(),
        type_=sa.String(100),
        existing_nullable=False,
        postgresql_using='entidad_id::text'
    )
