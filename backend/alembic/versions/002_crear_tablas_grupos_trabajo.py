"""Crear tablas de grupos de trabajo

Revision ID: 002_grupos_trabajo
Revises: 001_campania
Create Date: 2026-01-17
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '002_grupos_trabajo'
down_revision: Union[str, None] = '001_campania'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # Tabla tipo_grupo
    op.create_table(
        'tipo_grupo',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=True),
        sa.Column('es_permanente', sa.Boolean(), default=False, nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla rol_grupo
    op.create_table(
        'rol_grupo',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=True),
        sa.Column('es_coordinador', sa.Boolean(), default=False, nullable=False),
        sa.Column('puede_editar', sa.Boolean(), default=False, nullable=False),
        sa.Column('puede_aprobar_gastos', sa.Boolean(), default=False, nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla estado_tarea
    op.create_table(
        'estado_tarea',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('orden', sa.Integer(), default=0, nullable=False),
        sa.Column('color', sa.String(7), nullable=True),
        sa.Column('es_final', sa.Boolean(), default=False, nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla grupo_trabajo
    op.create_table(
        'grupo_trabajo',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text(), nullable=True),
        sa.Column('tipo_grupo_id', sa.Integer(), sa.ForeignKey('tipo_grupo.id'), nullable=False),
        sa.Column('campania_id', sa.Integer(), sa.ForeignKey('campania.id'), nullable=True),
        sa.Column('fecha_inicio', sa.Date(), nullable=True),
        sa.Column('fecha_fin', sa.Date(), nullable=True),
        sa.Column('objetivo', sa.Text(), nullable=True),
        sa.Column('presupuesto_asignado', sa.Numeric(12, 2), nullable=True),
        sa.Column('presupuesto_ejecutado', sa.Numeric(12, 2), default=0, nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
        sa.Column('agrupacion_id', sa.Integer(), sa.ForeignKey('agrupacion_territorial.id'), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('created_by_id', sa.Integer(), sa.ForeignKey('usuario.id'), nullable=False),
        sa.Column('updated_at', sa.DateTime(), nullable=True),
    )

    # Tabla miembro_grupo (clave primaria compuesta)
    op.create_table(
        'miembro_grupo',
        sa.Column('grupo_id', sa.Integer(), sa.ForeignKey('grupo_trabajo.id'), primary_key=True),
        sa.Column('miembro_id', sa.Integer(), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('rol_grupo_id', sa.Integer(), sa.ForeignKey('rol_grupo.id'), nullable=False),
        sa.Column('fecha_incorporacion', sa.Date(), nullable=False),
        sa.Column('fecha_baja', sa.Date(), nullable=True),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
        sa.Column('responsabilidades', sa.Text(), nullable=True),
        sa.Column('observaciones', sa.Text(), nullable=True),
    )

    # Tabla tarea_grupo
    op.create_table(
        'tarea_grupo',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('grupo_id', sa.Integer(), sa.ForeignKey('grupo_trabajo.id'), nullable=False),
        sa.Column('titulo', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text(), nullable=True),
        sa.Column('asignado_a_id', sa.Integer(), sa.ForeignKey('miembro.id'), nullable=True),
        sa.Column('estado_id', sa.Integer(), sa.ForeignKey('estado_tarea.id'), nullable=False),
        sa.Column('prioridad', sa.Integer(), default=2, nullable=False),
        sa.Column('fecha_creacion', sa.DateTime(), nullable=False),
        sa.Column('fecha_limite', sa.Date(), nullable=True),
        sa.Column('fecha_completada', sa.DateTime(), nullable=True),
        sa.Column('horas_estimadas', sa.Numeric(6, 2), nullable=True),
        sa.Column('horas_reales', sa.Numeric(6, 2), nullable=True),
        sa.Column('creado_por_id', sa.Integer(), sa.ForeignKey('usuario.id'), nullable=False),
    )

    # Tabla reunion_grupo
    op.create_table(
        'reunion_grupo',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('grupo_id', sa.Integer(), sa.ForeignKey('grupo_trabajo.id'), nullable=False),
        sa.Column('titulo', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text(), nullable=True),
        sa.Column('fecha', sa.Date(), nullable=False),
        sa.Column('hora_inicio', sa.String(5), nullable=True),
        sa.Column('hora_fin', sa.String(5), nullable=True),
        sa.Column('lugar', sa.String(200), nullable=True),
        sa.Column('url_online', sa.String(500), nullable=True),
        sa.Column('orden_del_dia', sa.Text(), nullable=True),
        sa.Column('acta', sa.Text(), nullable=True),
        sa.Column('realizada', sa.Boolean(), default=False, nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False),
        sa.Column('created_by_id', sa.Integer(), sa.ForeignKey('usuario.id'), nullable=False),
    )

    # Tabla asistente_reunion (clave primaria compuesta)
    op.create_table(
        'asistente_reunion',
        sa.Column('reunion_id', sa.Integer(), sa.ForeignKey('reunion_grupo.id'), primary_key=True),
        sa.Column('miembro_id', sa.Integer(), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('confirmado', sa.Boolean(), default=False, nullable=False),
        sa.Column('asistio', sa.Boolean(), nullable=True),
        sa.Column('observaciones', sa.Text(), nullable=True),
    )

    # Datos iniciales - Tipos de grupo
    op.execute("""
        INSERT INTO tipo_grupo (codigo, nombre, descripcion, es_permanente, activo) VALUES
        ('TECNICO', 'Grupo Técnico', 'Grupos de soporte técnico y desarrollo', true, true),
        ('COMUNICACION', 'Comunicación', 'Grupos de comunicación y redes sociales', true, true),
        ('FORMACION', 'Formación', 'Grupos de formación y capacitación', true, true),
        ('JURIDICO', 'Jurídico', 'Grupos de asesoría legal', true, true),
        ('CAMPANIA', 'Campaña', 'Grupos temporales para campañas específicas', false, true),
        ('EVENTO', 'Evento', 'Grupos temporales para eventos', false, true),
        ('PROYECTO', 'Proyecto', 'Grupos temporales para proyectos específicos', false, true)
    """)

    # Datos iniciales - Roles de grupo
    op.execute("""
        INSERT INTO rol_grupo (codigo, nombre, descripcion, es_coordinador, puede_editar, puede_aprobar_gastos, activo) VALUES
        ('COORDINADOR', 'Coordinador', 'Coordinador del grupo', true, true, true, true),
        ('SECRETARIO', 'Secretario', 'Secretario del grupo', false, true, false, true),
        ('MIEMBRO', 'Miembro', 'Miembro regular del grupo', false, false, false, true),
        ('COLABORADOR', 'Colaborador', 'Colaborador externo', false, false, false, true)
    """)

    # Datos iniciales - Estados de tarea
    op.execute("""
        INSERT INTO estado_tarea (codigo, nombre, orden, color, es_final, activo) VALUES
        ('PENDIENTE', 'Pendiente', 1, '#6B7280', false, true),
        ('EN_PROGRESO', 'En Progreso', 2, '#3B82F6', false, true),
        ('EN_REVISION', 'En Revisión', 3, '#F59E0B', false, true),
        ('COMPLETADA', 'Completada', 4, '#10B981', true, true),
        ('CANCELADA', 'Cancelada', 5, '#EF4444', true, true)
    """)


def downgrade() -> None:
    op.drop_table('asistente_reunion')
    op.drop_table('reunion_grupo')
    op.drop_table('tarea_grupo')
    op.drop_table('miembro_grupo')
    op.drop_table('grupo_trabajo')
    op.drop_table('estado_tarea')
    op.drop_table('rol_grupo')
    op.drop_table('tipo_grupo')
