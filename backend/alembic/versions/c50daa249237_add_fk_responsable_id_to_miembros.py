"""add_fk_responsable_id_to_miembros

Revision ID: c50daa249237
Revises: b2c3d4e5f6g7
Create Date: 2026-01-20 13:57:11.085278
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa


revision: str = 'c50daa249237'
down_revision: Union[str, None] = 'b2c3d4e5f6g7'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Agregar FK de responsable_id a miembros
    op.create_foreign_key(
        'fk_campanias_responsable_id_miembros',
        'campanias', 'miembros',
        ['responsable_id'], ['id']
    )


def downgrade() -> None:
    op.drop_constraint('fk_campanias_responsable_id_miembros', 'campanias', type_='foreignkey')
