"""add_sexo_field_to_miembros

Revision ID: 70f5a36b7a69
Revises: cf92a2404011
Create Date: 2026-01-20 08:46:48.917319
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '70f5a36b7a69'
down_revision: Union[str, None] = 'cf92a2404011'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.add_column('miembros', sa.Column('sexo', sa.String(length=1), nullable=True))


def downgrade() -> None:
    op.drop_column('miembros', 'sexo')
