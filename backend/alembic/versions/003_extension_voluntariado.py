"""Extensión de voluntariado: competencias, documentos, formación

Revision ID: 003_voluntariado
Revises: 002_grupos_trabajo
Create Date: 2026-01-17
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '003_voluntariado'
down_revision: Union[str, None] = '002_grupos_trabajo'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # === EXTENSIÓN DE TABLA MIEMBRO ===
    # Campos de voluntariado
    op.add_column('miembro', sa.Column('es_voluntario', sa.Boolean(), default=False, nullable=False, server_default='false'))
    op.add_column('miembro', sa.Column('disponibilidad', sa.String(20), nullable=True))
    op.add_column('miembro', sa.Column('horas_disponibles_semana', sa.Integer(), nullable=True))
    op.add_column('miembro', sa.Column('experiencia_voluntariado', sa.Text(), nullable=True))
    op.add_column('miembro', sa.Column('intereses', sa.Text(), nullable=True))
    op.add_column('miembro', sa.Column('observaciones_voluntariado', sa.Text(), nullable=True))
    op.add_column('miembro', sa.Column('puede_conducir', sa.Boolean(), default=False, nullable=False, server_default='false'))
    op.add_column('miembro', sa.Column('vehiculo_propio', sa.Boolean(), default=False, nullable=False, server_default='false'))
    op.add_column('miembro', sa.Column('disponibilidad_viajar', sa.Boolean(), default=False, nullable=False, server_default='false'))
    op.add_column('miembro', sa.Column('total_horas_voluntariado', sa.Numeric(8, 2), default=0, nullable=False, server_default='0'))
    op.add_column('miembro', sa.Column('total_campanias_participadas', sa.Integer(), default=0, nullable=False, server_default='0'))
    op.add_column('miembro', sa.Column('fecha_ultimo_voluntariado', sa.Date(), nullable=True))

    # === COMPETENCIAS ===

    # Tabla categoria_competencia
    op.create_table(
        'categoria_competencia',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=True),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla competencia
    op.create_table(
        'competencia',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(30), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=True),
        sa.Column('categoria_id', sa.Integer(), sa.ForeignKey('categoria_competencia.id'), nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla nivel_competencia
    op.create_table(
        'nivel_competencia',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(50), nullable=False),
        sa.Column('orden', sa.Integer(), default=0, nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla miembro_competencia
    op.create_table(
        'miembro_competencia',
        sa.Column('miembro_id', sa.Integer(), sa.ForeignKey('miembro.id'), primary_key=True),
        sa.Column('competencia_id', sa.Integer(), sa.ForeignKey('competencia.id'), primary_key=True),
        sa.Column('nivel_id', sa.Integer(), sa.ForeignKey('nivel_competencia.id'), nullable=False),
        sa.Column('verificado', sa.Boolean(), default=False, nullable=False),
        sa.Column('fecha_verificacion', sa.Date(), nullable=True),
        sa.Column('verificado_por_id', sa.Integer(), sa.ForeignKey('usuario.id'), nullable=True),
        sa.Column('observaciones', sa.Text(), nullable=True),
    )

    # === DOCUMENTOS ===

    # Tabla tipo_documento_voluntario
    op.create_table(
        'tipo_documento_voluntario',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('descripcion', sa.String(500), nullable=True),
        sa.Column('requiere_caducidad', sa.Boolean(), default=False, nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla documento_miembro
    op.create_table(
        'documento_miembro',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('miembro_id', sa.Integer(), sa.ForeignKey('miembro.id'), nullable=False),
        sa.Column('tipo_documento_id', sa.Integer(), sa.ForeignKey('tipo_documento_voluntario.id'), nullable=False),
        sa.Column('nombre', sa.String(200), nullable=False),
        sa.Column('descripcion', sa.Text(), nullable=True),
        sa.Column('archivo_url', sa.String(500), nullable=False),
        sa.Column('archivo_nombre', sa.String(255), nullable=False),
        sa.Column('archivo_tipo', sa.String(50), nullable=True),
        sa.Column('archivo_tamano', sa.Integer(), nullable=True),
        sa.Column('fecha_subida', sa.DateTime(), nullable=False),
        sa.Column('fecha_caducidad', sa.Date(), nullable=True),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
        sa.Column('subido_por_id', sa.Integer(), sa.ForeignKey('usuario.id'), nullable=False),
    )

    # === FORMACIÓN ===

    # Tabla tipo_formacion
    op.create_table(
        'tipo_formacion',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('codigo', sa.String(20), unique=True, nullable=False),
        sa.Column('nombre', sa.String(100), nullable=False),
        sa.Column('activo', sa.Boolean(), default=True, nullable=False),
    )

    # Tabla formacion_miembro
    op.create_table(
        'formacion_miembro',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('miembro_id', sa.Integer(), sa.ForeignKey('miembro.id'), nullable=False),
        sa.Column('tipo_formacion_id', sa.Integer(), sa.ForeignKey('tipo_formacion.id'), nullable=False),
        sa.Column('titulo', sa.String(300), nullable=False),
        sa.Column('institucion', sa.String(200), nullable=True),
        sa.Column('descripcion', sa.Text(), nullable=True),
        sa.Column('fecha_inicio', sa.Date(), nullable=True),
        sa.Column('fecha_fin', sa.Date(), nullable=True),
        sa.Column('horas', sa.Integer(), nullable=True),
        sa.Column('certificado', sa.Boolean(), default=False, nullable=False),
        sa.Column('documento_id', sa.Integer(), sa.ForeignKey('documento_miembro.id'), nullable=True),
        sa.Column('competencias_adquiridas', sa.Text(), nullable=True),
        sa.Column('es_interna', sa.Boolean(), default=False, nullable=False),
    )

    # === DATOS INICIALES ===

    # Categorías de competencia
    op.execute("""
        INSERT INTO categoria_competencia (codigo, nombre, descripcion, activo) VALUES
        ('TECNICA', 'Técnica', 'Competencias técnicas y de oficio', true),
        ('IDIOMAS', 'Idiomas', 'Competencias lingüísticas', true),
        ('COMUNICACION', 'Comunicación', 'Competencias de comunicación y RRSS', true),
        ('GESTION', 'Gestión', 'Competencias de gestión y organización', true),
        ('JURIDICA', 'Jurídica', 'Competencias legales y jurídicas', true),
        ('EDUCATIVA', 'Educativa', 'Competencias educativas y formativas', true),
        ('SANITARIA', 'Sanitaria', 'Competencias sanitarias y de primeros auxilios', true)
    """)

    # Niveles de competencia
    op.execute("""
        INSERT INTO nivel_competencia (codigo, nombre, orden, activo) VALUES
        ('BASICO', 'Básico', 1, true),
        ('INTERMEDIO', 'Intermedio', 2, true),
        ('AVANZADO', 'Avanzado', 3, true),
        ('EXPERTO', 'Experto', 4, true)
    """)

    # Competencias predefinidas
    op.execute("""
        INSERT INTO competencia (codigo, nombre, descripcion, categoria_id, activo)
        SELECT 'DISENO_GRAFICO', 'Diseño Gráfico', 'Diseño de cartelería, flyers, etc.', id, true FROM categoria_competencia WHERE codigo = 'TECNICA'
        UNION ALL
        SELECT 'EDICION_VIDEO', 'Edición de Video', 'Edición y producción de vídeo', id, true FROM categoria_competencia WHERE codigo = 'TECNICA'
        UNION ALL
        SELECT 'DESARROLLO_WEB', 'Desarrollo Web', 'Programación y desarrollo web', id, true FROM categoria_competencia WHERE codigo = 'TECNICA'
        UNION ALL
        SELECT 'FOTOGRAFIA', 'Fotografía', 'Fotografía de eventos', id, true FROM categoria_competencia WHERE codigo = 'TECNICA'
        UNION ALL
        SELECT 'INGLES', 'Inglés', 'Idioma inglés', id, true FROM categoria_competencia WHERE codigo = 'IDIOMAS'
        UNION ALL
        SELECT 'FRANCES', 'Francés', 'Idioma francés', id, true FROM categoria_competencia WHERE codigo = 'IDIOMAS'
        UNION ALL
        SELECT 'REDES_SOCIALES', 'Redes Sociales', 'Gestión de redes sociales', id, true FROM categoria_competencia WHERE codigo = 'COMUNICACION'
        UNION ALL
        SELECT 'REDACCION', 'Redacción', 'Redacción de textos y comunicados', id, true FROM categoria_competencia WHERE codigo = 'COMUNICACION'
        UNION ALL
        SELECT 'ORATORIA', 'Oratoria', 'Hablar en público', id, true FROM categoria_competencia WHERE codigo = 'COMUNICACION'
        UNION ALL
        SELECT 'COORD_EQUIPOS', 'Coordinación de Equipos', 'Coordinación y liderazgo de equipos', id, true FROM categoria_competencia WHERE codigo = 'GESTION'
        UNION ALL
        SELECT 'GESTION_PROYECTOS', 'Gestión de Proyectos', 'Planificación y gestión de proyectos', id, true FROM categoria_competencia WHERE codigo = 'GESTION'
        UNION ALL
        SELECT 'DERECHO_ASOC', 'Derecho Asociativo', 'Legislación de asociaciones y ONGs', id, true FROM categoria_competencia WHERE codigo = 'JURIDICA'
        UNION ALL
        SELECT 'FORMACION', 'Formación', 'Capacidad para impartir formación', id, true FROM categoria_competencia WHERE codigo = 'EDUCATIVA'
        UNION ALL
        SELECT 'PRIMEROS_AUX', 'Primeros Auxilios', 'Conocimientos de primeros auxilios', id, true FROM categoria_competencia WHERE codigo = 'SANITARIA'
    """)

    # Tipos de documento
    op.execute("""
        INSERT INTO tipo_documento_voluntario (codigo, nombre, descripcion, requiere_caducidad, activo) VALUES
        ('CV', 'Currículum Vitae', 'Currículum del voluntario', false, true),
        ('CERTIFICADO', 'Certificado', 'Certificados de formación o competencia', true, true),
        ('DNI', 'DNI/NIE', 'Documento de identidad', true, true),
        ('FOTO', 'Fotografía', 'Fotografía del voluntario', false, true),
        ('ANTECEDENTES', 'Certificado Antecedentes', 'Certificado de antecedentes penales', true, true),
        ('AUTORIZACION', 'Autorización', 'Autorizaciones y consentimientos', false, true),
        ('SEGURO', 'Seguro', 'Póliza de seguro de voluntariado', true, true),
        ('OTRO', 'Otro', 'Otros documentos', false, true)
    """)

    # Tipos de formación
    op.execute("""
        INSERT INTO tipo_formacion (codigo, nombre, activo) VALUES
        ('CURSO', 'Curso', true),
        ('TALLER', 'Taller', true),
        ('SEMINARIO', 'Seminario', true),
        ('JORNADA', 'Jornada', true),
        ('CERTIFICACION', 'Certificación', true),
        ('TITULO_UNIV', 'Título Universitario', true),
        ('MASTER', 'Máster', true),
        ('OTRO', 'Otro', true)
    """)


def downgrade() -> None:
    # Eliminar tablas en orden inverso
    op.drop_table('formacion_miembro')
    op.drop_table('tipo_formacion')
    op.drop_table('documento_miembro')
    op.drop_table('tipo_documento_voluntario')
    op.drop_table('miembro_competencia')
    op.drop_table('nivel_competencia')
    op.drop_table('competencia')
    op.drop_table('categoria_competencia')

    # Eliminar columnas de miembro
    op.drop_column('miembro', 'fecha_ultimo_voluntariado')
    op.drop_column('miembro', 'total_campanias_participadas')
    op.drop_column('miembro', 'total_horas_voluntariado')
    op.drop_column('miembro', 'disponibilidad_viajar')
    op.drop_column('miembro', 'vehiculo_propio')
    op.drop_column('miembro', 'puede_conducir')
    op.drop_column('miembro', 'observaciones_voluntariado')
    op.drop_column('miembro', 'intereses')
    op.drop_column('miembro', 'experiencia_voluntariado')
    op.drop_column('miembro', 'horas_disponibles_semana')
    op.drop_column('miembro', 'disponibilidad')
    op.drop_column('miembro', 'es_voluntario')
