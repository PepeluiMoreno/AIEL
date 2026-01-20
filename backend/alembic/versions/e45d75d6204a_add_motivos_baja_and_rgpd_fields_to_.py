"""add motivos_baja and RGPD fields to miembros

Revision ID: e45d75d6204a
Revises: 34e1d423d0c9
Create Date: 2026-01-20 10:27:21.153328
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa
import uuid


revision: str = 'e45d75d6204a'
down_revision: Union[str, None] = '34e1d423d0c9'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


# Motivos de baja estándar para seeding
MOTIVOS_BAJA = [
    {
        'id': str(uuid.uuid4()),
        'codigo': 'VOLUNTARIA',
        'nombre': 'Baja voluntaria',
        'descripcion': 'El miembro solicita la baja por voluntad propia',
        'requiere_documentacion': False,
    },
    {
        'id': str(uuid.uuid4()),
        'codigo': 'IMPAGO',
        'nombre': 'Baja por impago',
        'descripcion': 'Baja automática por cuotas impagadas durante varios ejercicios',
        'requiere_documentacion': False,
    },
    {
        'id': str(uuid.uuid4()),
        'codigo': 'FALLECIMIENTO',
        'nombre': 'Fallecimiento',
        'descripcion': 'Baja por defunción del miembro',
        'requiere_documentacion': True,
    },
    {
        'id': str(uuid.uuid4()),
        'codigo': 'EXPULSION',
        'nombre': 'Expulsión',
        'descripcion': 'Baja disciplinaria por incumplimiento grave de estatutos',
        'requiere_documentacion': True,
    },
]


def upgrade() -> None:
    # Crear tabla motivos_baja
    op.create_table('motivos_baja',
        sa.Column('id', sa.Uuid(), nullable=False),
        sa.Column('codigo', sa.String(length=50), nullable=False),
        sa.Column('nombre', sa.String(length=100), nullable=False),
        sa.Column('descripcion', sa.String(length=500), nullable=True),
        sa.Column('requiere_documentacion', sa.Boolean(), nullable=False),
        sa.Column('activo', sa.Boolean(), nullable=False, server_default='true'),
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
    op.create_index(op.f('ix_motivos_baja_activo'), 'motivos_baja', ['activo'], unique=False)
    op.create_index(op.f('ix_motivos_baja_codigo'), 'motivos_baja', ['codigo'], unique=True)
    op.create_index(op.f('ix_motivos_baja_eliminado'), 'motivos_baja', ['eliminado'], unique=False)

    # Seeding de motivos de baja
    motivos_table = sa.table('motivos_baja',
        sa.column('id', sa.Uuid),
        sa.column('codigo', sa.String),
        sa.column('nombre', sa.String),
        sa.column('descripcion', sa.String),
        sa.column('requiere_documentacion', sa.Boolean),
        sa.column('activo', sa.Boolean),
        sa.column('eliminado', sa.Boolean),
    )
    op.bulk_insert(motivos_table, MOTIVOS_BAJA)

    # Añadir columnas RGPD a miembros (con defaults para registros existentes)
    op.add_column('miembros', sa.Column('motivo_baja_id', sa.Uuid(), nullable=True))
    op.add_column('miembros', sa.Column('motivo_baja_texto', sa.String(length=500), nullable=True))
    op.add_column('miembros', sa.Column('solicita_supresion_datos', sa.Boolean(), server_default='false', nullable=False))
    op.add_column('miembros', sa.Column('fecha_solicitud_supresion', sa.Date(), nullable=True))
    op.add_column('miembros', sa.Column('fecha_limite_retencion', sa.Date(), nullable=True))
    op.add_column('miembros', sa.Column('datos_anonimizados', sa.Boolean(), server_default='false', nullable=False))
    op.add_column('miembros', sa.Column('fecha_anonimizacion', sa.Date(), nullable=True))

    # Crear índices
    op.create_index(op.f('ix_miembros_datos_anonimizados'), 'miembros', ['datos_anonimizados'], unique=False)
    op.create_index(op.f('ix_miembros_fecha_limite_retencion'), 'miembros', ['fecha_limite_retencion'], unique=False)
    op.create_index(op.f('ix_miembros_motivo_baja_id'), 'miembros', ['motivo_baja_id'], unique=False)

    # Crear FK
    op.create_foreign_key('fk_miembros_motivo_baja', 'miembros', 'motivos_baja', ['motivo_baja_id'], ['id'])

    # Migrar datos: mover motivo_baja (texto) a motivo_baja_texto
    op.execute("""
        UPDATE miembros
        SET motivo_baja_texto = motivo_baja
        WHERE motivo_baja IS NOT NULL AND motivo_baja != ''
    """)

    # Eliminar columna antigua
    op.drop_column('miembros', 'motivo_baja')

    # Calcular fecha_limite_retencion para miembros con fecha_baja
    op.execute("""
        UPDATE miembros
        SET fecha_limite_retencion = fecha_baja + INTERVAL '6 years'
        WHERE fecha_baja IS NOT NULL
    """)

    # Refrescar vista materializada para incluir nuevos datos
    op.execute("REFRESH MATERIALIZED VIEW vista_miembros_segmentacion")


def downgrade() -> None:
    # Restaurar columna motivo_baja
    op.add_column('miembros', sa.Column('motivo_baja', sa.VARCHAR(length=500), autoincrement=False, nullable=True))

    # Migrar datos de vuelta
    op.execute("""
        UPDATE miembros
        SET motivo_baja = motivo_baja_texto
        WHERE motivo_baja_texto IS NOT NULL
    """)

    # Eliminar FK y columnas nuevas
    op.drop_constraint('fk_miembros_motivo_baja', 'miembros', type_='foreignkey')
    op.drop_index(op.f('ix_miembros_motivo_baja_id'), table_name='miembros')
    op.drop_index(op.f('ix_miembros_fecha_limite_retencion'), table_name='miembros')
    op.drop_index(op.f('ix_miembros_datos_anonimizados'), table_name='miembros')
    op.drop_column('miembros', 'fecha_anonimizacion')
    op.drop_column('miembros', 'datos_anonimizados')
    op.drop_column('miembros', 'fecha_limite_retencion')
    op.drop_column('miembros', 'fecha_solicitud_supresion')
    op.drop_column('miembros', 'solicita_supresion_datos')
    op.drop_column('miembros', 'motivo_baja_texto')
    op.drop_column('miembros', 'motivo_baja_id')

    # Eliminar tabla motivos_baja
    op.drop_index(op.f('ix_motivos_baja_eliminado'), table_name='motivos_baja')
    op.drop_index(op.f('ix_motivos_baja_codigo'), table_name='motivos_baja')
    op.drop_index(op.f('ix_motivos_baja_activo'), table_name='motivos_baja')
    op.drop_table('motivos_baja')
