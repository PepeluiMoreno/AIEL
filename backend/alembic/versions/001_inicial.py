"""Migración inicial - Todas las tablas del sistema AIEL.

Revision ID: 001_inicial
Revises:
Create Date: 2026-01-18
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import postgresql

revision: str = '001_inicial'
down_revision: Union[str, None] = None
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # =====================
    # ENUMS
    # =====================
    op.execute("CREATE TYPE tipotransaccion AS ENUM ('QUERY', 'MUTATION')")
    op.execute("CREATE TYPE estadocuota AS ENUM ('PENDIENTE', 'COBRADA', 'COBRADA_PARCIAL', 'EXENTO', 'DEVUELTA')")
    op.execute("CREATE TYPE modoingreso AS ENUM ('SEPA', 'TRANSFERENCIA', 'PAYPAL', 'EFECTIVO')")

    # =====================
    # CATÁLOGOS BÁSICOS
    # =====================

    op.create_table('pais',
        sa.Column('codpais', sa.String(3), primary_key=True),
        sa.Column('nombre', sa.String(100), nullable=False),
    )

    op.create_table('provincia',
        sa.Column('codprov', sa.String(2), primary_key=True),
        sa.Column('nombre', sa.String(100), nullable=False),
    )

    op.create_table('tipo_miembro',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('requiere_cuota', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('rol',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('transaccion',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(50), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('tipo', postgresql.ENUM('QUERY', 'MUTATION', name='tipotransaccion', create_type=False)),
        sa.Column('modulo', sa.String(50)),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('rol_transaccion',
        sa.Column('rol_id', sa.Integer, sa.ForeignKey('rol.id'), primary_key=True),
        sa.Column('transaccion_id', sa.Integer, sa.ForeignKey('transaccion.id'), primary_key=True),
    )

    # =====================
    # USUARIO
    # =====================

    op.create_table('usuario',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('email', sa.String(255), unique=True, index=True, nullable=False),
        sa.Column('password_hash', sa.String(255), nullable=False),
        sa.Column('activo', sa.Boolean, default=True),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('last_login', sa.DateTime),
    )

    # =====================
    # AGRUPACIÓN TERRITORIAL
    # =====================

    op.create_table('agrupacion_territorial',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('codigo', sa.String(10), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('es_nacional', sa.Boolean, default=False),
        sa.Column('portavoz_id', postgresql.UUID(as_uuid=True)),
        sa.Column('iban_principal', sa.String(34)),
        sa.Column('iban_secundario', sa.String(34)),
        sa.Column('observaciones', sa.Text),
        sa.Column('activo', sa.Boolean, default=True),
        # ContactoMixin
        sa.Column('direccion', sa.String(300)),
        sa.Column('codigo_postal', sa.String(10)),
        sa.Column('localidad', sa.String(100)),
        sa.Column('provincia', sa.String(100)),
        sa.Column('telefono', sa.String(20)),
        sa.Column('telefono2', sa.String(20)),
        sa.Column('email', sa.String(255)),
        sa.Column('web', sa.String(255)),
        # SoftDeleteMixin / AuditoriaMixin
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    # =====================
    # MIEMBRO
    # =====================

    op.create_table('miembro',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('usuario_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id'), unique=True),
        sa.Column('tipo_miembro_id', sa.Integer, sa.ForeignKey('tipo_miembro.id'), nullable=False),
        sa.Column('tipo_persona', sa.String(10), default='FISICA'),
        sa.Column('tipo_membresia', sa.String(10), default='DIRECTA'),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('apellido1', sa.String(100)),
        sa.Column('apellido2', sa.String(100)),
        sa.Column('fecha_nacimiento', sa.Date),
        sa.Column('tipo_documento', sa.String(10)),
        sa.Column('numero_documento', sa.String(20)),
        sa.Column('pais_documento_id', sa.String(3), sa.ForeignKey('pais.codpais')),
        sa.Column('direccion', sa.String(300)),
        sa.Column('codigo_postal', sa.String(10)),
        sa.Column('localidad', sa.String(100)),
        sa.Column('provincia_id', sa.String(2), sa.ForeignKey('provincia.codprov')),
        sa.Column('pais_domicilio_id', sa.String(3), sa.ForeignKey('pais.codpais')),
        sa.Column('telefono', sa.String(20)),
        sa.Column('telefono2', sa.String(20)),
        sa.Column('email', sa.String(255)),
        sa.Column('agrupacion_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('agrupacion_territorial.id')),
        sa.Column('iban', sa.String(34)),
        # Voluntariado
        sa.Column('es_voluntario', sa.Boolean, default=False),
        sa.Column('disponibilidad', sa.String(20)),
        sa.Column('horas_disponibles_semana', sa.Integer),
        sa.Column('experiencia_voluntariado', sa.Text),
        sa.Column('intereses', sa.Text),
        sa.Column('observaciones_voluntariado', sa.Text),
        sa.Column('puede_conducir', sa.Boolean, default=False),
        sa.Column('vehiculo_propio', sa.Boolean, default=False),
        sa.Column('disponibilidad_viajar', sa.Boolean, default=False),
        sa.Column('total_horas_voluntariado', sa.Numeric(8, 2), default=0),
        sa.Column('total_campanias_participadas', sa.Integer, default=0),
        sa.Column('fecha_ultimo_voluntariado', sa.Date),
        # Alta/baja
        sa.Column('fecha_alta', sa.Date, server_default=sa.func.current_date()),
        sa.Column('fecha_baja', sa.Date),
        # Mixins
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    # FK de portavoz a agrupacion
    op.create_foreign_key('fk_agrupacion_portavoz', 'agrupacion_territorial', 'miembro', ['portavoz_id'], ['id'])

    op.create_table('usuario_rol',
        sa.Column('usuario_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id'), primary_key=True),
        sa.Column('rol_id', sa.Integer, sa.ForeignKey('rol.id'), primary_key=True),
        sa.Column('agrupacion_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('agrupacion_territorial.id')),
    )

    # =====================
    # FINANCIERO
    # =====================

    op.create_table('importe_cuota_anio',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('anio', sa.Integer, unique=True, nullable=False),
        sa.Column('importe', sa.Numeric(10, 2), nullable=False),
    )

    op.create_table('cuota_anio',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), nullable=False),
        sa.Column('anio', sa.Integer, nullable=False),
        sa.Column('agrupacion_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('agrupacion_territorial.id'), nullable=False),
        sa.Column('importe', sa.Numeric(10, 2), nullable=False),
        sa.Column('importe_pagado', sa.Numeric(10, 2), default=0),
        sa.Column('estado', postgresql.ENUM('PENDIENTE', 'COBRADA', 'COBRADA_PARCIAL', 'EXENTO', 'DEVUELTA', name='estadocuota', create_type=False), default='PENDIENTE'),
        sa.Column('modo_ingreso', postgresql.ENUM('SEPA', 'TRANSFERENCIA', 'PAYPAL', 'EFECTIVO', name='modoingreso', create_type=False)),
        sa.Column('fecha_pago', sa.Date),
        sa.Column('observaciones', sa.Text),
    )

    op.create_table('donacion_concepto',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('remesa',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('fecha', sa.Date, server_default=sa.func.current_date()),
        sa.Column('importe_total', sa.Numeric(10, 2), nullable=False),
        sa.Column('gastos', sa.Numeric(10, 2), default=0),
        sa.Column('archivo_sepa', sa.String(255)),
        sa.Column('observaciones', sa.Text),
    )

    # =====================
    # CAMPAÑAS
    # =====================

    op.create_table('tipo_campania',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('estado_campania',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('orden', sa.Integer, default=0),
        sa.Column('color', sa.String(7)),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('rol_participante',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('es_voluntario', sa.Boolean, default=False),
        sa.Column('es_coordinador', sa.Boolean, default=False),
        sa.Column('es_donante', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('campania',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion_corta', sa.String(300)),
        sa.Column('descripcion_larga', sa.Text),
        sa.Column('tipo_campania_id', sa.Integer, sa.ForeignKey('tipo_campania.id'), nullable=False),
        sa.Column('estado_campania_id', sa.Integer, sa.ForeignKey('estado_campania.id'), nullable=False),
        sa.Column('fecha_inicio_plan', sa.Date),
        sa.Column('fecha_fin_plan', sa.Date),
        sa.Column('fecha_inicio_real', sa.Date),
        sa.Column('fecha_fin_real', sa.Date),
        sa.Column('objetivo_principal', sa.Text),
        sa.Column('meta_recaudacion', sa.Numeric(12, 2)),
        sa.Column('meta_participantes', sa.Integer),
        sa.Column('responsable_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id')),
        sa.Column('agrupacion_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('agrupacion_territorial.id')),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('created_by_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('updated_at', sa.DateTime),
    )

    op.create_table('accion_campania',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('campania_id', sa.Integer, sa.ForeignKey('campania.id'), nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('fecha', sa.Date, nullable=False),
        sa.Column('hora_inicio', sa.String(5)),
        sa.Column('hora_fin', sa.String(5)),
        sa.Column('lugar', sa.String(200)),
        sa.Column('direccion', sa.String(500)),
        sa.Column('voluntarios_necesarios', sa.Integer, default=0),
        sa.Column('voluntarios_confirmados', sa.Integer, default=0),
        sa.Column('materiales_necesarios', sa.Text),
        sa.Column('observaciones', sa.Text),
        sa.Column('completada', sa.Boolean, default=False),
    )

    op.create_table('donacion',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), nullable=False),
        sa.Column('concepto_id', sa.Integer, sa.ForeignKey('donacion_concepto.id')),
        sa.Column('campania_id', sa.Integer, sa.ForeignKey('campania.id')),
        sa.Column('importe', sa.Numeric(10, 2), nullable=False),
        sa.Column('gastos', sa.Numeric(10, 2), default=0),
        sa.Column('fecha', sa.Date, server_default=sa.func.current_date()),
        sa.Column('modo_ingreso', postgresql.ENUM('SEPA', 'TRANSFERENCIA', 'PAYPAL', 'EFECTIVO', name='modoingreso', create_type=False)),
        sa.Column('observaciones', sa.Text),
        sa.Column('deleted_at', sa.DateTime),
    )

    op.create_table('participante_campania',
        sa.Column('campania_id', sa.Integer, sa.ForeignKey('campania.id'), primary_key=True),
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('rol_participante_id', sa.Integer, sa.ForeignKey('rol_participante.id'), nullable=False),
        sa.Column('horas_aportadas', sa.Numeric(6, 2)),
        sa.Column('donacion_asociada_id', sa.Integer, sa.ForeignKey('donacion.id')),
        sa.Column('confirmado', sa.Boolean, default=False),
        sa.Column('asistio', sa.Boolean),
        sa.Column('fecha_inscripcion', sa.DateTime, server_default=sa.func.now()),
        sa.Column('fecha_confirmacion', sa.DateTime),
        sa.Column('observaciones', sa.Text),
    )

    op.create_table('orden_cobro',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('remesa_id', sa.Integer, sa.ForeignKey('remesa.id'), nullable=False),
        sa.Column('cuota_id', sa.Integer, sa.ForeignKey('cuota_anio.id'), nullable=False),
        sa.Column('importe', sa.Numeric(10, 2), nullable=False),
        sa.Column('estado', sa.String(20), default='PENDIENTE'),
    )

    # =====================
    # GRUPOS DE TRABAJO
    # =====================

    op.create_table('tipo_grupo',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('es_permanente', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('rol_grupo',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('es_coordinador', sa.Boolean, default=False),
        sa.Column('puede_editar', sa.Boolean, default=False),
        sa.Column('puede_aprobar_gastos', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('estado_tarea',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('orden', sa.Integer, default=0),
        sa.Column('color', sa.String(7)),
        sa.Column('es_final', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('grupo_trabajo',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('tipo_grupo_id', sa.Integer, sa.ForeignKey('tipo_grupo.id'), nullable=False),
        sa.Column('campania_id', sa.Integer, sa.ForeignKey('campania.id')),
        sa.Column('fecha_inicio', sa.Date),
        sa.Column('fecha_fin', sa.Date),
        sa.Column('objetivo', sa.Text),
        sa.Column('presupuesto_asignado', sa.Numeric(12, 2)),
        sa.Column('presupuesto_ejecutado', sa.Numeric(12, 2), default=0),
        sa.Column('activo', sa.Boolean, default=True),
        sa.Column('agrupacion_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('agrupacion_territorial.id')),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('created_by_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('updated_at', sa.DateTime),
    )

    op.create_table('miembro_grupo',
        sa.Column('grupo_id', sa.Integer, sa.ForeignKey('grupo_trabajo.id'), primary_key=True),
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('rol_grupo_id', sa.Integer, sa.ForeignKey('rol_grupo.id'), nullable=False),
        sa.Column('fecha_incorporacion', sa.Date, server_default=sa.func.current_date()),
        sa.Column('fecha_baja', sa.Date),
        sa.Column('activo', sa.Boolean, default=True),
        sa.Column('responsabilidades', sa.Text),
        sa.Column('observaciones', sa.Text),
    )

    op.create_table('tarea_grupo',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('grupo_id', sa.Integer, sa.ForeignKey('grupo_trabajo.id'), nullable=False),
        sa.Column('titulo', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('asignado_a_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id')),
        sa.Column('estado_id', sa.Integer, sa.ForeignKey('estado_tarea.id'), nullable=False),
        sa.Column('prioridad', sa.Integer, default=2),
        sa.Column('fecha_creacion', sa.DateTime, server_default=sa.func.now()),
        sa.Column('fecha_limite', sa.Date),
        sa.Column('fecha_completada', sa.DateTime),
        sa.Column('horas_estimadas', sa.Numeric(6, 2)),
        sa.Column('horas_reales', sa.Numeric(6, 2)),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('reunion_grupo',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('grupo_id', sa.Integer, sa.ForeignKey('grupo_trabajo.id'), nullable=False),
        sa.Column('titulo', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('fecha', sa.Date, nullable=False),
        sa.Column('hora_inicio', sa.String(5)),
        sa.Column('hora_fin', sa.String(5)),
        sa.Column('lugar', sa.String(200)),
        sa.Column('url_online', sa.String(500)),
        sa.Column('orden_del_dia', sa.Text),
        sa.Column('acta', sa.Text),
        sa.Column('realizada', sa.Boolean, default=False),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('created_by_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('asistente_reunion',
        sa.Column('reunion_id', sa.Integer, sa.ForeignKey('reunion_grupo.id'), primary_key=True),
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('confirmado', sa.Boolean, default=False),
        sa.Column('asistio', sa.Boolean),
        sa.Column('observaciones', sa.Text),
    )

    # =====================
    # VOLUNTARIADO
    # =====================

    op.create_table('categoria_competencia',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('competencia',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(30), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('categoria_id', sa.Integer, sa.ForeignKey('categoria_competencia.id'), nullable=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('nivel_competencia',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(50), nullable=False),
        sa.Column('orden', sa.Integer, default=0),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('miembro_competencia',
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('competencia_id', sa.Integer, sa.ForeignKey('competencia.id'), primary_key=True),
        sa.Column('nivel_id', sa.Integer, sa.ForeignKey('nivel_competencia.id'), nullable=False),
        sa.Column('verificado', sa.Boolean, default=False),
        sa.Column('fecha_verificacion', sa.Date),
        sa.Column('verificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('observaciones', sa.Text),
    )

    op.create_table('tipo_documento_voluntario',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('requiere_caducidad', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('documento_miembro',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), nullable=False),
        sa.Column('tipo_documento_id', sa.Integer, sa.ForeignKey('tipo_documento_voluntario.id'), nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('archivo_url', sa.String(500), nullable=False),
        sa.Column('archivo_nombre', sa.String(255), nullable=False),
        sa.Column('archivo_tipo', sa.String(50)),
        sa.Column('archivo_tamano', sa.Integer),
        sa.Column('fecha_subida', sa.DateTime, server_default=sa.func.now()),
        sa.Column('fecha_caducidad', sa.Date),
        sa.Column('activo', sa.Boolean, default=True),
        sa.Column('subido_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('tipo_formacion',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('formacion_miembro',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), nullable=False),
        sa.Column('tipo_formacion_id', sa.Integer, sa.ForeignKey('tipo_formacion.id'), nullable=False),
        sa.Column('titulo', sa.String(300), nullable=False),
        sa.Column('institucion', sa.String(200)),
        sa.Column('descripcion', sa.Text),
        sa.Column('fecha_inicio', sa.Date),
        sa.Column('fecha_fin', sa.Date),
        sa.Column('horas', sa.Integer),
        sa.Column('certificado', sa.Boolean, default=False),
        sa.Column('documento_id', sa.Integer, sa.ForeignKey('documento_miembro.id')),
        sa.Column('competencias_adquiridas', sa.Text),
        sa.Column('es_interna', sa.Boolean, default=False),
    )

    # =====================
    # PRESUPUESTO Y PLANIFICACIÓN
    # =====================

    op.create_table('estado_planificacion',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('orden', sa.Integer, default=0),
        sa.Column('color', sa.String(7)),
        sa.Column('es_final', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('categoria_partida',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('planificacion_anual',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('ejercicio', sa.Integer, unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('objetivos', sa.Text),
        sa.Column('estado_id', sa.Integer, sa.ForeignKey('estado_planificacion.id'), nullable=False),
        sa.Column('fecha_aprobacion', sa.Date),
        sa.Column('presupuesto_total', sa.Numeric(14, 2), default=0),
        # Mixins
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('partida_presupuestaria',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('codigo', sa.String(30), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('ejercicio', sa.Integer, nullable=False),
        sa.Column('tipo', sa.String(10), nullable=False),
        sa.Column('categoria_id', sa.Integer, sa.ForeignKey('categoria_partida.id')),
        sa.Column('importe_presupuestado', sa.Numeric(12, 2), default=0),
        sa.Column('importe_comprometido', sa.Numeric(12, 2), default=0),
        sa.Column('importe_ejecutado', sa.Numeric(12, 2), default=0),
        sa.Column('activo', sa.Boolean, default=True),
        sa.Column('planificacion_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('planificacion_anual.id')),
        # Mixins
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    # =====================
    # ACTIVIDADES, PROPUESTAS Y KPIs
    # =====================

    op.create_table('tipo_actividad',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('requiere_grupo', sa.Boolean, default=False),
        sa.Column('requiere_presupuesto', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('estado_actividad',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('orden', sa.Integer, default=0),
        sa.Column('color', sa.String(7)),
        sa.Column('es_final', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('estado_propuesta',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('orden', sa.Integer, default=0),
        sa.Column('color', sa.String(7)),
        sa.Column('es_final', sa.Boolean, default=False),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('tipo_recurso',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500)),
        sa.Column('requiere_importe', sa.Boolean, default=True),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('tipo_kpi',
        sa.Column('id', sa.Integer, primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('formato', sa.String(50)),
        sa.Column('activo', sa.Boolean, default=True),
    )

    op.create_table('kpi',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('codigo', sa.String(30), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('tipo_kpi_id', sa.Integer, sa.ForeignKey('tipo_kpi.id'), nullable=False),
        sa.Column('unidad', sa.String(50)),
        sa.Column('valor_objetivo_defecto', sa.Numeric(12, 2)),
        sa.Column('valor_minimo', sa.Numeric(12, 2)),
        sa.Column('formula', sa.String(500)),
        sa.Column('activo', sa.Boolean, default=True),
        sa.Column('deleted_at', sa.DateTime),
    )

    op.create_table('propuesta_actividad',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('codigo', sa.String(30), unique=True, nullable=False),
        sa.Column('titulo', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('justificacion', sa.Text),
        sa.Column('proponente_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), nullable=False),
        sa.Column('estado_id', sa.Integer, sa.ForeignKey('estado_propuesta.id'), nullable=False),
        sa.Column('fecha_presentacion', sa.Date),
        sa.Column('fecha_resolucion', sa.Date),
        sa.Column('motivo_resolucion', sa.Text),
        sa.Column('planificacion_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('planificacion_anual.id')),
        sa.Column('campania_id', sa.Integer, sa.ForeignKey('campania.id')),
        sa.Column('fecha_inicio_propuesta', sa.Date),
        sa.Column('fecha_fin_propuesta', sa.Date),
        sa.Column('presupuesto_solicitado', sa.Numeric(12, 2), default=0),
        sa.Column('presupuesto_aprobado', sa.Numeric(12, 2)),
        sa.Column('partida_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('partida_presupuestaria.id')),
        sa.Column('observaciones', sa.Text),
        # Mixins
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('tarea_propuesta',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('propuesta_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('propuesta_actividad.id'), nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('orden', sa.Integer, default=0),
        sa.Column('grupo_trabajo_id', sa.Integer, sa.ForeignKey('grupo_trabajo.id')),
        sa.Column('responsable_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id')),
        sa.Column('fecha_inicio_estimada', sa.Date),
        sa.Column('fecha_fin_estimada', sa.Date),
        sa.Column('horas_estimadas', sa.Numeric(6, 2)),
        sa.Column('deleted_at', sa.DateTime),
    )

    op.create_table('recurso_propuesta',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('propuesta_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('propuesta_actividad.id'), nullable=False),
        sa.Column('tipo_recurso_id', sa.Integer, sa.ForeignKey('tipo_recurso.id'), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=False),
        sa.Column('cantidad', sa.Integer, default=1),
        sa.Column('importe_unitario_estimado', sa.Numeric(10, 2), default=0),
        sa.Column('importe_total_estimado', sa.Numeric(10, 2), default=0),
        sa.Column('importe_aprobado', sa.Numeric(10, 2)),
        sa.Column('proveedor', sa.String(200)),
        sa.Column('observaciones', sa.Text),
        sa.Column('deleted_at', sa.DateTime),
    )

    op.create_table('grupo_propuesta',
        sa.Column('propuesta_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('propuesta_actividad.id'), primary_key=True),
        sa.Column('grupo_trabajo_id', sa.Integer, sa.ForeignKey('grupo_trabajo.id'), primary_key=True),
        sa.Column('tareas_asignadas', sa.Text),
        sa.Column('horas_estimadas', sa.Numeric(6, 2)),
    )

    op.create_table('actividad',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('codigo', sa.String(30), unique=True, nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('propuesta_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('propuesta_actividad.id')),
        sa.Column('tipo_actividad_id', sa.Integer, sa.ForeignKey('tipo_actividad.id'), nullable=False),
        sa.Column('estado_id', sa.Integer, sa.ForeignKey('estado_actividad.id'), nullable=False),
        sa.Column('prioridad', sa.Integer, default=2),
        sa.Column('fecha_inicio', sa.Date, nullable=False),
        sa.Column('fecha_fin', sa.Date, nullable=False),
        sa.Column('hora_inicio', sa.Time),
        sa.Column('hora_fin', sa.Time),
        sa.Column('es_todo_el_dia', sa.Boolean, default=True),
        sa.Column('lugar', sa.String(200)),
        sa.Column('direccion', sa.String(300)),
        sa.Column('es_online', sa.Boolean, default=False),
        sa.Column('url_online', sa.String(500)),
        sa.Column('campania_id', sa.Integer, sa.ForeignKey('campania.id')),
        sa.Column('planificacion_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('planificacion_anual.id')),
        sa.Column('coordinador_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), nullable=False),
        sa.Column('es_colectiva', sa.Boolean, default=False),
        sa.Column('partida_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('partida_presupuestaria.id')),
        sa.Column('dotacion_economica', sa.Numeric(10, 2), default=0),
        sa.Column('gasto_real', sa.Numeric(10, 2), default=0),
        sa.Column('voluntarios_necesarios', sa.Integer, default=0),
        sa.Column('voluntarios_confirmados', sa.Integer, default=0),
        sa.Column('completada', sa.Boolean, default=False),
        sa.Column('fecha_completada', sa.DateTime),
        sa.Column('resultados', sa.Text),
        sa.Column('observaciones', sa.Text),
        # Mixins
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('tarea_actividad',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('actividad_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('actividad.id'), nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text),
        sa.Column('orden', sa.Integer, default=0),
        sa.Column('grupo_trabajo_id', sa.Integer, sa.ForeignKey('grupo_trabajo.id')),
        sa.Column('responsable_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id')),
        sa.Column('estado_id', sa.Integer, sa.ForeignKey('estado_tarea.id'), nullable=False),
        sa.Column('fecha_limite', sa.Date),
        sa.Column('fecha_completada', sa.DateTime),
        sa.Column('horas_estimadas', sa.Numeric(6, 2)),
        sa.Column('horas_reales', sa.Numeric(6, 2)),
        # Mixins
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('recurso_actividad',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('actividad_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('actividad.id'), nullable=False),
        sa.Column('tipo_recurso_id', sa.Integer, sa.ForeignKey('tipo_recurso.id'), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=False),
        sa.Column('cantidad', sa.Integer, default=1),
        sa.Column('importe_presupuestado', sa.Numeric(10, 2), default=0),
        sa.Column('importe_real', sa.Numeric(10, 2), default=0),
        sa.Column('proveedor', sa.String(200)),
        sa.Column('factura_referencia', sa.String(100)),
        sa.Column('fecha_factura', sa.Date),
        sa.Column('pagado', sa.Boolean, default=False),
        sa.Column('fecha_pago', sa.Date),
        sa.Column('observaciones', sa.Text),
        # Mixins
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('grupo_actividad',
        sa.Column('actividad_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('actividad.id'), primary_key=True),
        sa.Column('grupo_trabajo_id', sa.Integer, sa.ForeignKey('grupo_trabajo.id'), primary_key=True),
        sa.Column('tareas_asignadas', sa.Text),
        sa.Column('horas_estimadas', sa.Numeric(6, 2)),
        sa.Column('horas_reales', sa.Numeric(6, 2)),
    )

    op.create_table('participante_actividad',
        sa.Column('actividad_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('actividad.id'), primary_key=True),
        sa.Column('miembro_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('rol', sa.String(20), default='VOLUNTARIO'),
        sa.Column('confirmado', sa.Boolean, default=False),
        sa.Column('asistio', sa.Boolean),
        sa.Column('horas_aportadas', sa.Numeric(5, 2), default=0),
        sa.Column('observaciones', sa.Text),
    )

    op.create_table('kpi_actividad',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('actividad_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('actividad.id'), nullable=False),
        sa.Column('kpi_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('kpi.id'), nullable=False),
        sa.Column('valor_objetivo', sa.Numeric(12, 2), nullable=False),
        sa.Column('peso', sa.Numeric(5, 2), default=1),
        sa.Column('valor_actual', sa.Numeric(12, 2)),
        sa.Column('fecha_ultima_medicion', sa.DateTime),
        sa.Column('porcentaje_logro', sa.Numeric(5, 2)),
        sa.Column('observaciones', sa.Text),
        # Mixins
        sa.Column('deleted_at', sa.DateTime),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    op.create_table('medicion_kpi',
        sa.Column('id', postgresql.UUID(as_uuid=True), primary_key=True, server_default=sa.text('gen_random_uuid()')),
        sa.Column('kpi_actividad_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('kpi_actividad.id'), nullable=False),
        sa.Column('valor_medido', sa.Numeric(12, 2), nullable=False),
        sa.Column('fecha_medicion', sa.DateTime, server_default=sa.func.now()),
        sa.Column('medido_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('miembro.id')),
        sa.Column('observaciones', sa.Text),
        sa.Column('created_at', sa.DateTime, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime),
        sa.Column('creado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
        sa.Column('modificado_por_id', postgresql.UUID(as_uuid=True), sa.ForeignKey('usuario.id')),
    )

    # =====================
    # DATOS INICIALES (SEEDING DE CATÁLOGOS)
    # =====================

    # Tipos de campaña
    op.execute("""
        INSERT INTO tipo_campania (codigo, nombre, descripcion, activo) VALUES
        ('RECAUDACION', 'Recaudación', 'Campañas para recaudar fondos', true),
        ('SENSIBILIZACION', 'Sensibilización', 'Campañas de concienciación y difusión', true),
        ('AYUDA_DIRECTA', 'Ayuda Directa', 'Campañas de ayuda directa a beneficiarios', true),
        ('VOLUNTARIADO', 'Voluntariado', 'Campañas que requieren principalmente voluntarios', true)
    """)

    # Estados de campaña
    op.execute("""
        INSERT INTO estado_campania (codigo, nombre, orden, color, activo) VALUES
        ('PLANIFICADA', 'Planificada', 1, '#6B7280', true),
        ('ACTIVA', 'Activa', 2, '#10B981', true),
        ('SUSPENDIDA', 'Suspendida', 3, '#F59E0B', true),
        ('FINALIZADA', 'Finalizada', 4, '#3B82F6', true),
        ('CANCELADA', 'Cancelada', 5, '#EF4444', true)
    """)

    # Roles de participante
    op.execute("""
        INSERT INTO rol_participante (codigo, nombre, es_voluntario, es_coordinador, es_donante, activo) VALUES
        ('VOLUNTARIO', 'Voluntario', true, false, false, true),
        ('COORDINADOR', 'Coordinador', true, true, false, true),
        ('DONANTE', 'Donante', false, false, true, true),
        ('COLABORADOR', 'Colaborador', true, false, false, true)
    """)

    # Tipos de grupo
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

    # Roles de grupo
    op.execute("""
        INSERT INTO rol_grupo (codigo, nombre, descripcion, es_coordinador, puede_editar, puede_aprobar_gastos, activo) VALUES
        ('COORDINADOR', 'Coordinador', 'Coordinador del grupo', true, true, true, true),
        ('SECRETARIO', 'Secretario', 'Secretario del grupo', false, true, false, true),
        ('MIEMBRO', 'Miembro', 'Miembro regular del grupo', false, false, false, true),
        ('COLABORADOR', 'Colaborador', 'Colaborador externo', false, false, false, true)
    """)

    # Estados de tarea
    op.execute("""
        INSERT INTO estado_tarea (codigo, nombre, orden, color, es_final, activo) VALUES
        ('PENDIENTE', 'Pendiente', 1, '#6B7280', false, true),
        ('EN_PROGRESO', 'En Progreso', 2, '#3B82F6', false, true),
        ('EN_REVISION', 'En Revisión', 3, '#F59E0B', false, true),
        ('COMPLETADA', 'Completada', 4, '#10B981', true, true),
        ('CANCELADA', 'Cancelada', 5, '#EF4444', true, true)
    """)

    # Estados de planificación
    op.execute("""
        INSERT INTO estado_planificacion (codigo, nombre, orden, color, es_final, activo) VALUES
        ('BORRADOR', 'Borrador', 1, '#6B7280', false, true),
        ('EN_REVISION', 'En Revisión', 2, '#F59E0B', false, true),
        ('APROBADO', 'Aprobado', 3, '#10B981', false, true),
        ('EN_EJECUCION', 'En Ejecución', 4, '#3B82F6', false, true),
        ('CERRADO', 'Cerrado', 5, '#9333EA', true, true)
    """)

    # Categorías de partida
    op.execute("""
        INSERT INTO categoria_partida (codigo, nombre, descripcion, activo) VALUES
        ('PERSONAL', 'Personal', 'Gastos de personal', true),
        ('INFRAESTRUCTURA', 'Infraestructura', 'Gastos de infraestructura y mantenimiento', true),
        ('CAMPANIAS', 'Campañas', 'Gastos de campañas', true),
        ('EVENTOS', 'Eventos', 'Gastos de eventos y actividades', true),
        ('FORMACION', 'Formación', 'Gastos de formación', true),
        ('COMUNICACION', 'Comunicación', 'Gastos de comunicación y difusión', true),
        ('DESPLAZAMIENTOS', 'Desplazamientos', 'Gastos de viajes y desplazamientos', true),
        ('OTROS', 'Otros', 'Otros gastos', true)
    """)

    # Tipos de actividad
    op.execute("""
        INSERT INTO tipo_actividad (codigo, nombre, descripcion, requiere_grupo, requiere_presupuesto, activo) VALUES
        ('EVENTO', 'Evento', 'Eventos y actos públicos', true, true, true),
        ('REUNION', 'Reunión', 'Reuniones internas', false, false, true),
        ('FORMACION', 'Formación', 'Actividades formativas', true, true, true),
        ('CAMPANIA', 'Campaña', 'Acciones de campaña', true, true, true),
        ('ADMINISTRATIVA', 'Administrativa', 'Tareas administrativas', false, false, true)
    """)

    # Estados de actividad
    op.execute("""
        INSERT INTO estado_actividad (codigo, nombre, orden, color, es_final, activo) VALUES
        ('PLANIFICADA', 'Planificada', 1, '#6B7280', false, true),
        ('EN_PREPARACION', 'En Preparación', 2, '#F59E0B', false, true),
        ('EN_CURSO', 'En Curso', 3, '#3B82F6', false, true),
        ('COMPLETADA', 'Completada', 4, '#10B981', true, true),
        ('CANCELADA', 'Cancelada', 5, '#EF4444', true, true),
        ('APLAZADA', 'Aplazada', 6, '#9333EA', false, true)
    """)

    # Estados de propuesta
    op.execute("""
        INSERT INTO estado_propuesta (codigo, nombre, orden, color, es_final, activo) VALUES
        ('BORRADOR', 'Borrador', 1, '#6B7280', false, true),
        ('PENDIENTE', 'Pendiente de Revisión', 2, '#F59E0B', false, true),
        ('EN_REVISION', 'En Revisión', 3, '#3B82F6', false, true),
        ('APROBADA', 'Aprobada', 4, '#10B981', true, true),
        ('RECHAZADA', 'Rechazada', 5, '#EF4444', true, true),
        ('MODIFICACIONES', 'Requiere Modificaciones', 6, '#9333EA', false, true)
    """)

    # Tipos de recurso
    op.execute("""
        INSERT INTO tipo_recurso (codigo, nombre, descripcion, requiere_importe, activo) VALUES
        ('LOCAL', 'Local/Espacio', 'Alquiler de espacios', true, true),
        ('DESPLAZAMIENTO', 'Desplazamiento', 'Gastos de viaje y transporte', true, true),
        ('MATERIAL', 'Material', 'Material fungible y consumibles', true, true),
        ('CATERING', 'Catering', 'Comida y bebida', true, true),
        ('ALOJAMIENTO', 'Alojamiento', 'Gastos de alojamiento', true, true),
        ('COMUNICACION', 'Comunicación', 'Gastos de difusión y publicidad', true, true),
        ('EQUIPAMIENTO', 'Equipamiento', 'Equipos y herramientas', true, true),
        ('HUMANO', 'Recurso Humano', 'Voluntarios y personal', false, true),
        ('OTRO', 'Otro', 'Otros recursos', true, true)
    """)

    # Tipos de KPI
    op.execute("""
        INSERT INTO tipo_kpi (codigo, nombre, formato, activo) VALUES
        ('NUMERICO', 'Numérico', '{value}', true),
        ('PORCENTAJE', 'Porcentaje', '{value}%', true),
        ('MONETARIO', 'Monetario', '€{value}', true),
        ('BOOLEANO', 'Sí/No', NULL, true)
    """)

    # KPIs base
    op.execute("""
        INSERT INTO kpi (codigo, nombre, descripcion, tipo_kpi_id, unidad, valor_objetivo_defecto, activo) VALUES
        ('KPI-PARTICIPANTES', 'Participantes', 'Número de participantes en la actividad', 1, 'personas', 50, true),
        ('KPI-ASISTENCIA', 'Tasa de Asistencia', 'Porcentaje de asistencia sobre inscritos', 2, '%', 80, true),
        ('KPI-SATISFACCION', 'Satisfacción', 'Puntuación media de satisfacción', 1, 'puntos (1-10)', 7, true),
        ('KPI-RECAUDACION', 'Recaudación', 'Importe total recaudado', 3, 'euros', 1000, true),
        ('KPI-VOLUNTARIOS', 'Voluntarios Activos', 'Número de voluntarios participantes', 1, 'personas', 10, true),
        ('KPI-DIFUSION', 'Alcance Difusión', 'Personas alcanzadas en redes/medios', 1, 'personas', 500, true)
    """)


def downgrade() -> None:
    # Tablas en orden inverso
    op.drop_table('medicion_kpi')
    op.drop_table('kpi_actividad')
    op.drop_table('participante_actividad')
    op.drop_table('grupo_actividad')
    op.drop_table('recurso_actividad')
    op.drop_table('tarea_actividad')
    op.drop_table('actividad')
    op.drop_table('grupo_propuesta')
    op.drop_table('recurso_propuesta')
    op.drop_table('tarea_propuesta')
    op.drop_table('propuesta_actividad')
    op.drop_table('kpi')
    op.drop_table('tipo_kpi')
    op.drop_table('tipo_recurso')
    op.drop_table('estado_propuesta')
    op.drop_table('estado_actividad')
    op.drop_table('tipo_actividad')
    op.drop_table('partida_presupuestaria')
    op.drop_table('planificacion_anual')
    op.drop_table('categoria_partida')
    op.drop_table('estado_planificacion')
    op.drop_table('formacion_miembro')
    op.drop_table('tipo_formacion')
    op.drop_table('documento_miembro')
    op.drop_table('tipo_documento_voluntario')
    op.drop_table('miembro_competencia')
    op.drop_table('nivel_competencia')
    op.drop_table('competencia')
    op.drop_table('categoria_competencia')
    op.drop_table('asistente_reunion')
    op.drop_table('reunion_grupo')
    op.drop_table('tarea_grupo')
    op.drop_table('miembro_grupo')
    op.drop_table('grupo_trabajo')
    op.drop_table('estado_tarea')
    op.drop_table('rol_grupo')
    op.drop_table('tipo_grupo')
    op.drop_table('orden_cobro')
    op.drop_table('participante_campania')
    op.drop_table('donacion')
    op.drop_table('accion_campania')
    op.drop_table('campania')
    op.drop_table('rol_participante')
    op.drop_table('estado_campania')
    op.drop_table('tipo_campania')
    op.drop_table('remesa')
    op.drop_table('donacion_concepto')
    op.drop_table('cuota_anio')
    op.drop_table('importe_cuota_anio')
    op.drop_table('usuario_rol')
    op.drop_constraint('fk_agrupacion_portavoz', 'agrupacion_territorial', type_='foreignkey')
    op.drop_table('miembro')
    op.drop_table('agrupacion_territorial')
    op.drop_table('usuario')
    op.drop_table('rol_transaccion')
    op.drop_table('transaccion')
    op.drop_table('rol')
    op.drop_table('tipo_miembro')
    op.drop_table('provincia')
    op.drop_table('pais')
    op.execute('DROP TYPE IF EXISTS tipotransaccion')
    op.execute('DROP TYPE IF EXISTS estadocuota')
    op.execute('DROP TYPE IF EXISTS modoingreso')
