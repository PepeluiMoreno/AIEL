"""Add observaciones field to miembros

Revision ID: 1706f7b803ae
Revises: c5c4f3434510
Create Date: 2026-01-19 21:45:29.820199
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '1706f7b803ae'
down_revision: Union[str, None] = 'c5c4f3434510'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Agregar campo observaciones a tabla miembros
    op.add_column('miembros', sa.Column('observaciones', sa.Text(), nullable=True))


def downgrade() -> None:
    # Eliminar campo observaciones de tabla miembros
    op.drop_column('miembros', 'observaciones')
