"""add_orden_to_tipos_miembro

Revision ID: e4f5g6h7i8j9
Revises: d3e4f5g6h7i8
Create Date: 2026-01-20 16:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa


revision: str = 'e4f5g6h7i8j9'
down_revision: Union[str, None] = 'd3e4f5g6h7i8'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Añadir campo orden a tipos_miembro
    op.add_column('tipos_miembro', sa.Column('orden', sa.Integer, nullable=False, server_default='0'))


def downgrade() -> None:
    op.drop_column('tipos_miembro', 'orden')
