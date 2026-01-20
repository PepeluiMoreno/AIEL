"""add estados_notificacion table and change notificacion.estado to FK

Revision ID: b2c3d4e5f6g7
Revises: a1b2c3d4e5f6
Create Date: 2026-01-20 18:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa
import uuid


revision: str = 'b2c3d4e5f6g7'
down_revision: Union[str, None] = 'a1b2c3d4e5f6'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


# Estados de notificación predefinidos
ESTADOS_NOTIFICACION = [
    {
        'id': str(uuid.uuid4()),
        'codigo': 'PENDIENTE',
        'nombre': 'Pendiente',
        'descripcion': 'Notificación creada pero no enviada',
        'color': '#FFC107',
        'orden': 1,
    },
    {
        'id': str(uuid.uuid4()),
        'codigo': 'ENVIADA',
        'nombre': 'Enviada',
        'descripcion': 'Notificación enviada al canal correspondiente',
        'color': '#17A2B8',
        'orden': 2,
    },
    {
        'id': str(uuid.uuid4()),
        'codigo': 'LEIDA',
        'nombre': 'Leída',
        'descripcion': 'Notificación leída por el usuario',
        'color': '#28A745',
        'orden': 3,
    },
    {
        'id': str(uuid.uuid4()),
        'codigo': 'ERROR',
        'nombre': 'Error',
        'descripcion': 'Error al enviar la notificación',
        'color': '#DC3545',
        'orden': 4,
    },
]


def upgrade() -> None:
    # 1. Crear tabla estados_notificacion
    op.create_table('estados_notificacion',
        sa.Column('id', sa.Uuid(), nullable=False),
        sa.Column('codigo', sa.String(length=50), nullable=False),
        sa.Column('nombre', sa.String(length=100), nullable=False),
        sa.Column('descripcion', sa.Text(), nullable=True),
        sa.Column('orden', sa.Integer(), nullable=False, server_default='0'),
        sa.Column('es_inicial', sa.Boolean(), nullable=False, server_default='false'),
        sa.Column('es_final', sa.Boolean(), nullable=False, server_default='false'),
        sa.Column('activo', sa.Boolean(), nullable=False, server_default='true'),
        sa.Column('color', sa.String(length=20), nullable=True),
        sa.Column('fecha_creacion', sa.DateTime(), server_default=sa.text('now()'), nullable=False),
        sa.Column('fecha_modificacion', sa.DateTime(), nullable=True),
        sa.Column('fecha_eliminacion', sa.DateTime(), nullable=True),
        sa.Column('eliminado', sa.Boolean(), server_default='false', nullable=False),
        sa.Column('creado_por_id', sa.Uuid(), nullable=True),
        sa.Column('modificado_por_id', sa.Uuid(), nullable=True),
        sa.ForeignKeyConstraint(['creado_por_id'], ['usuarios.id'], ),
        sa.ForeignKeyConstraint(['modificado_por_id'], ['usuarios.id'], ),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index('ix_estados_notificacion_codigo', 'estados_notificacion', ['codigo'], unique=True)
    op.create_index('ix_estados_notificacion_activo', 'estados_notificacion', ['activo'], unique=False)
    op.create_index('ix_estados_notificacion_eliminado', 'estados_notificacion', ['eliminado'], unique=False)

    # 2. Insertar estados predefinidos
    estados_table = sa.table('estados_notificacion',
        sa.column('id', sa.Uuid),
        sa.column('codigo', sa.String),
        sa.column('nombre', sa.String),
        sa.column('descripcion', sa.Text),
        sa.column('color', sa.String),
        sa.column('orden', sa.Integer),
        sa.column('activo', sa.Boolean),
        sa.column('eliminado', sa.Boolean),
    )
    op.bulk_insert(estados_table, ESTADOS_NOTIFICACION)

    # 3. Guardar mapeo de códigos a UUIDs para migrar datos
    # Construir mapeo dinámico
    mapeo_estados = {e['codigo']: e['id'] for e in ESTADOS_NOTIFICACION}

    # 4. Añadir columna estado_id a notificaciones
    op.add_column('notificaciones',
        sa.Column('estado_id', sa.Uuid(), nullable=True)
    )

    # 5. Migrar datos: convertir estado (string) a estado_id (UUID)
    for codigo, estado_id in mapeo_estados.items():
        op.execute(f"""
            UPDATE notificaciones
            SET estado_id = '{estado_id}'
            WHERE estado = '{codigo}'
        """)

    # 6. Establecer estado por defecto para cualquier valor no mapeado
    estado_pendiente_id = mapeo_estados['PENDIENTE']
    op.execute(f"""
        UPDATE notificaciones
        SET estado_id = '{estado_pendiente_id}'
        WHERE estado_id IS NULL
    """)

    # 7. Hacer estado_id NOT NULL
    op.alter_column('notificaciones', 'estado_id',
        existing_type=sa.Uuid(),
        nullable=False
    )

    # 8. Crear FK e índice
    op.create_foreign_key(
        'fk_notificaciones_estado',
        'notificaciones', 'estados_notificacion',
        ['estado_id'], ['id']
    )
    op.create_index('ix_notificaciones_estado_id', 'notificaciones', ['estado_id'], unique=False)

    # 9. Eliminar columna estado antigua
    op.drop_index('ix_notificaciones_estado', table_name='notificaciones')
    op.drop_column('notificaciones', 'estado')


def downgrade() -> None:
    # 1. Restaurar columna estado
    op.add_column('notificaciones',
        sa.Column('estado', sa.String(20), nullable=True, server_default='PENDIENTE')
    )
    op.create_index('ix_notificaciones_estado', 'notificaciones', ['estado'], unique=False)

    # 2. Migrar datos de vuelta
    op.execute("""
        UPDATE notificaciones n
        SET estado = (
            SELECT codigo FROM estados_notificacion WHERE id = n.estado_id
        )
    """)

    # 3. Hacer estado NOT NULL
    op.alter_column('notificaciones', 'estado',
        existing_type=sa.String(20),
        nullable=False
    )

    # 4. Eliminar FK y columna estado_id
    op.drop_index('ix_notificaciones_estado_id', table_name='notificaciones')
    op.drop_constraint('fk_notificaciones_estado', 'notificaciones', type_='foreignkey')
    op.drop_column('notificaciones', 'estado_id')

    # 5. Eliminar tabla estados_notificacion
    op.drop_index('ix_estados_notificacion_eliminado', table_name='estados_notificacion')
    op.drop_index('ix_estados_notificacion_activo', table_name='estados_notificacion')
    op.drop_index('ix_estados_notificacion_codigo', table_name='estados_notificacion')
    op.drop_table('estados_notificacion')
