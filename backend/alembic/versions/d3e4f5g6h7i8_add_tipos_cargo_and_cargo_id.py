"""add_tipos_cargo_and_cargo_id

Revision ID: d3e4f5g6h7i8
Revises: c50daa249237
Create Date: 2026-01-20 14:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects.postgresql import UUID


revision: str = 'd3e4f5g6h7i8'
down_revision: Union[str, None] = 'c50daa249237'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Crear tabla tipos_cargo
    op.create_table(
        'tipos_cargo',
        sa.Column('id', UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('codigo', sa.String(50), unique=True, nullable=False, index=True),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=True),
        sa.Column('orden', sa.Integer, nullable=False, default=0),
        sa.Column('activo', sa.Boolean, nullable=False, default=True, index=True),
        # Campos de auditoría de BaseModel
        sa.Column('fecha_creacion', sa.DateTime, server_default=sa.text('now()'), nullable=False),
        sa.Column('fecha_modificacion', sa.DateTime, nullable=True),
        sa.Column('fecha_eliminacion', sa.DateTime, nullable=True),
        sa.Column('eliminado', sa.Boolean, server_default=sa.text('false'), nullable=False),
        sa.Column('creado_por_id', UUID(as_uuid=True), nullable=True),
        sa.Column('modificado_por_id', UUID(as_uuid=True), nullable=True),
    )

    # Seed de tipos de cargo
    op.execute("""
        INSERT INTO tipos_cargo (id, codigo, nombre, descripcion, orden, activo) VALUES
        (gen_random_uuid(), 'PRESIDENTE', 'Presidente/a', 'Presidente de la organización', 1, true),
        (gen_random_uuid(), 'VICEPRESIDENTE', 'Vicepresidente/a', 'Vicepresidente de la organización', 2, true),
        (gen_random_uuid(), 'SECRETARIO', 'Secretario/a', 'Secretario/a de la organización', 3, true),
        (gen_random_uuid(), 'TESORERO', 'Tesorero/a', 'Tesorero/a de la organización', 4, true),
        (gen_random_uuid(), 'VOCAL', 'Vocal', 'Vocal de la junta directiva', 5, true)
    """)

    # Añadir campo cargo_id a miembros
    op.add_column('miembros', sa.Column('cargo_id', UUID(as_uuid=True), nullable=True))
    op.create_index('ix_miembros_cargo_id', 'miembros', ['cargo_id'])
    op.create_foreign_key(
        'fk_miembros_cargo_id_tipos_cargo',
        'miembros', 'tipos_cargo',
        ['cargo_id'], ['id']
    )


def downgrade() -> None:
    op.drop_constraint('fk_miembros_cargo_id_tipos_cargo', 'miembros', type_='foreignkey')
    op.drop_index('ix_miembros_cargo_id', table_name='miembros')
    op.drop_column('miembros', 'cargo_id')
    op.drop_table('tipos_cargo')
