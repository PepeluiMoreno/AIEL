"""Crear tablas de campañas

Revision ID: 001_campania
Revises:
Create Date: 2026-01-17
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '001_campania'
down_revision: Union[str, None] = None
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Tabla tipo_campania
    op.create_table(
        'tipo_campania',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=True),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla estado_campania
    op.create_table(
        'estado_campania',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('orden', sa.Integer(), default=0, nullable=False),
        sa.Column('color', sa.String(7), nullable=True),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla rol_participante
    op.create_table(
        'rol_participante',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('es_voluntario', sa.Boolean(), default=False, nullable=False),
        sa.Column('es_coordinador', sa.Boolean(), default=False, nullable=False),
        sa.Column('es_donante', sa.Boolean(), default=False, nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla campania
    op.create_table(
        'campania',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion_corta', sa.String(300), nullable=True),
        sa.Column('descripcion_larga', sa.Text(), nullable=True),
        sa.Column('tipo_campania_id', sa.Integer(), sa.ForeignKey('tipo_campania.id'), nullable=False),
        sa.Column('estado_campania_id', sa.Integer(), sa.ForeignKey('estado_campania.id'), nullable=False),
        sa.Column('fecha_inicio_plan', sa.Date(), nullable=True),
        sa.Column('fecha_fin_plan', sa.Date(), nullable=True),
        sa.Column('fecha_inicio_real', sa.Date(), nullable=True),
        sa.Column('fecha_fin_real', sa.Date(), nullable=True),
        sa.Column('objetivo_principal', sa.Text(), nullable=True),
        sa.Column('meta_recaudacion', sa.Numeric(12, 2), nullable=True),
        sa.Column('meta_participantes', sa.Integer(), nullable=True),
        sa.Column('responsable_id', sa.Integer(), sa.ForeignKey('miembro.id'), nullable=True),
        sa.Column('agrupacion_id', sa.Integer(), sa.ForeignKey('agrupacion_territorial.id'), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('created_by_id', sa.Integer(), sa.ForeignKey('usuario.id'), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=True),
    )

    # Tabla accion_campania
    op.create_table(
        'accion_campania',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('campania_id', sa.Integer(), sa.ForeignKey('campania.id'), nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text(), nullable=True),
        sa.Column('fecha', sa.Date(), nullable=False),
        sa.Column('hora_inicio', sa.String(5), nullable=True),
        sa.Column('hora_fin', sa.String(5), nullable=True),
        sa.Column('lugar', sa.String(200), nullable=True),
        sa.Column('direccion', sa.String(500), nullable=True),
        sa.Column('voluntarios_necesarios', sa.Integer(), default=0, nullable=False),
        sa.Column('voluntarios_confirmados', sa.Integer(), default=0, nullable=False),
        sa.Column('materiales_necesarios', sa.Text(), nullable=True),
        sa.Column('observaciones', sa.Text(), nullable=True),
        sa.Column('completada', sa.Boolean(), default=False, nullable=False),
    )

    # Tabla participante_campania (clave primaria compuesta)
    op.create_table(
        'participante_campania',
        sa.Column('campania_id', sa.Integer(), sa.ForeignKey('campania.id'), primary_key=True),
        sa.Column('miembro_id', sa.Integer(), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('rol_participante_id', sa.Integer(), sa.ForeignKey('rol_participante.id'), nullable=False),
        sa.Column('horas_aportadas', sa.Numeric(6, 2), nullable=True),
        sa.Column('donacion_asociada_id', sa.Integer(), sa.ForeignKey('donacion.id'), nullable=True),
        sa.Column('confirmado', sa.Boolean(), default=False, nullable=False),
        sa.Column('asistio', sa.Boolean(), nullable=True),
        sa.Column('fecha_inscripcion', sa.DateTime(), nullable=False),
        sa.Column('fecha_confirmacion', sa.DateTime(), nullable=True),
        sa.Column('observaciones', sa.Text(), nullable=True),
    )

    # Añadir campania_id a donacion
    op.add_column('donacion', sa.Column('campania_id', sa.Integer(), sa.ForeignKey('campania.id'), nullable=True))

    # Datos iniciales - Tipos de campaña
    op.execute("""
        INSERT INTO tipo_campania (codigo, nombre, descripcion, activo) VALUES
        ('RECAUDACION', 'Recaudación', 'Campañas para recaudar fondos', true),
        ('SENSIBILIZACION', 'Sensibilización', 'Campañas de concienciación y difusión', true),
        ('AYUDA_DIRECTA', 'Ayuda Directa', 'Campañas de ayuda directa a beneficiarios', true),
        ('VOLUNTARIADO', 'Voluntariado', 'Campañas que requieren principalmente voluntarios', true)
    """)

    # Datos iniciales - Estados de campaña
    op.execute("""
        INSERT INTO estado_campania (codigo, nombre, orden, color, activo) VALUES
        ('PLANIFICADA', 'Planificada', 1, '#6B7280', true),
        ('ACTIVA', 'Activa', 2, '#10B981', true),
        ('SUSPENDIDA', 'Suspendida', 3, '#F59E0B', true),
        ('FINALIZADA', 'Finalizada', 4, '#3B82F6', true),
        ('CANCELADA', 'Cancelada', 5, '#EF4444', true)
    """)

    # Datos iniciales - Roles de participante
    op.execute("""
        INSERT INTO rol_participante (codigo, nombre, es_voluntario, es_coordinador, es_donante, activo) VALUES
        ('VOLUNTARIO', 'Voluntario', true, false, false, true),
        ('COORDINADOR', 'Coordinador', true, true, false, true),
        ('DONANTE', 'Donante', false, false, true, true),
        ('COLABORADOR', 'Colaborador', true, false, false, true)
    """)


def downgrade() -> None:
    # Eliminar columna de donacion
    op.drop_column('donacion', 'campania_id')

    # Eliminar tablas en orden inverso
    op.drop_table('participante_campania')
    op.drop_table('accion_campania')
    op.drop_table('campania')
    op.drop_table('rol_participante')
    op.drop_table('estado_campania')
    op.drop_table('tipo_campania')
