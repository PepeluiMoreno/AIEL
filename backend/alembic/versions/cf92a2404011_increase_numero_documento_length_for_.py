"""increase_numero_documento_length_for_encryption

Revision ID: cf92a2404011
Revises: 1706f7b803ae
Create Date: 2026-01-19 21:59:53.975634
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa


revision: str = 'cf92a2404011'
down_revision: Union[str, None] = '1706f7b803ae'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Aumentar tamaño de numero_documento para datos encriptados
    op.alter_column('miembros', 'numero_documento',
                    type_=sa.String(255),
                    existing_type=sa.String(50))


def downgrade() -> None:
    # Revertir tamaño de numero_documento
    op.alter_column('miembros', 'numero_documento',
                    type_=sa.String(50),
                    existing_type=sa.String(255))
