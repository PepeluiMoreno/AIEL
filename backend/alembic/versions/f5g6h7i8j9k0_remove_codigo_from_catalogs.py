"""remove_codigo_from_catalogs

Eliminar el campo 'codigo' de todos los catálogos del sistema.
Con UUIDs como identificadores, el campo codigo es redundante.

Revision ID: f5g6h7i8j9k0
Revises: e4f5g6h7i8j9
Create Date: 2026-01-20 18:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa


revision: str = 'f5g6h7i8j9k0'
down_revision: Union[str, None] = 'e4f5g6h7i8j9'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


# Tablas de las que se eliminará el campo 'codigo'
TABLES_WITH_CODIGO = [
    'tipos_miembro',
    'estados_miembro',
    'motivos_baja',
    'estados_cuota',
    'estados_campania',
    'estados_tarea',
    'estados_actividad',
    'estados_participante',
    'estados_orden_cobro',
    'estados_remesa',
    'estados_donacion',
    'estados_notificacion',
    'tipos_campania',
    'roles_participante',
    'campanias',
    'tipos_actividad',
    'estados_propuesta',
    'tipos_recurso',
    'tipos_kpi',
    'tipos_grupo',
    'roles_grupo',
    'grupos_trabajo',
    'categorias_competencia',
    'competencias',
    'niveles_competencia',
    'tipos_documento_voluntario',
    'tipos_formacion',
    'donaciones_conceptos',
    'tipos_asociacion',
    'estados_convenio',
    'convenios',
    'agrupaciones_territoriales',
    'tipos_organizacion',
    'organizaciones',
]


def upgrade() -> None:
    conn = op.get_bind()

    # 1. Eliminar vistas materializadas que dependen de 'codigo'
    print("Eliminando vistas materializadas...")
    conn.execute(sa.text("DROP MATERIALIZED VIEW IF EXISTS vista_miembros_segmentacion CASCADE"))
    conn.execute(sa.text("DROP MATERIALIZED VIEW IF EXISTS vista_agrupaciones_territoriales CASCADE"))

    # 2. Eliminar columnas 'codigo' de todas las tablas
    for table in TABLES_WITH_CODIGO:
        # Verificar si la tabla existe
        result = conn.execute(sa.text(
            "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = :table)"
        ), {"table": table})
        table_exists = result.scalar()

        if not table_exists:
            print(f"  Tabla {table} no existe, saltando...")
            continue

        # Verificar si la columna codigo existe
        result = conn.execute(sa.text("""
            SELECT EXISTS (
                SELECT FROM information_schema.columns
                WHERE table_name = :table AND column_name = 'codigo'
            )
        """), {"table": table})
        column_exists = result.scalar()

        if not column_exists:
            print(f"  Columna 'codigo' no existe en {table}, saltando...")
            continue

        # Eliminar índice si existe (PostgreSQL)
        conn.execute(sa.text(f"DROP INDEX IF EXISTS ix_{table}_codigo"))

        # Eliminar constraint unique si existe
        conn.execute(sa.text(f"ALTER TABLE {table} DROP CONSTRAINT IF EXISTS {table}_codigo_key"))

        # Eliminar la columna
        conn.execute(sa.text(f"ALTER TABLE {table} DROP COLUMN IF EXISTS codigo CASCADE"))
        print(f"  Eliminada columna 'codigo' de {table}")

    # 3. Recrear vista materializada vista_agrupaciones_territoriales sin 'codigo'
    print("Recreando vista materializada vista_agrupaciones_territoriales...")

    conn.execute(sa.text("""
        CREATE MATERIALIZED VIEW vista_agrupaciones_territoriales AS
        SELECT
            o.id,
            o.nombre,
            o.nombre_corto,
            CASE
                WHEN LOWER(t.nombre) LIKE '%estatal%' THEN 'ESTATAL'
                WHEN LOWER(t.nombre) LIKE '%internacional%' THEN 'INTERNACIONAL'
                WHEN LOWER(t.nombre) LIKE '%autonóm%' OR LOWER(t.nombre) LIKE '%autonom%' THEN 'AUTONOMICA'
                WHEN LOWER(t.nombre) LIKE '%provincial%' THEN 'PROVINCIAL'
                WHEN LOWER(t.nombre) LIKE '%local%' THEN 'LOCAL'
                ELSE o.ambito
            END as tipo,
            o.organizacion_padre_id as agrupacion_padre_id,
            o.nivel,
            o.pais_id,
            o.provincia_id,
            o.municipio_id,
            o.direccion_id,
            o.email,
            COALESCE(o.telefono_movil, o.telefono_fijo) as telefono,
            o.web,
            o.descripcion,
            o.activo
        FROM organizaciones o
        INNER JOIN tipos_organizacion t ON o.tipo_id = t.id
        WHERE t.categoria = 'INTERNA'
          AND o.eliminado = FALSE
    """))

    # Índices para vista_agrupaciones_territoriales
    conn.execute(sa.text("CREATE UNIQUE INDEX idx_vista_agrup_id ON vista_agrupaciones_territoriales(id)"))
    conn.execute(sa.text("CREATE INDEX idx_vista_agrup_tipo ON vista_agrupaciones_territoriales(tipo)"))
    conn.execute(sa.text("CREATE INDEX idx_vista_agrup_padre ON vista_agrupaciones_territoriales(agrupacion_padre_id)"))
    conn.execute(sa.text("CREATE INDEX idx_vista_agrup_provincia ON vista_agrupaciones_territoriales(provincia_id)"))
    conn.execute(sa.text("CREATE INDEX idx_vista_agrup_activo ON vista_agrupaciones_territoriales(activo)"))

    print("Vista vista_agrupaciones_territoriales recreada exitosamente.")

    # 4. Recrear vista materializada vista_miembros_segmentacion sin 'codigo'
    print("Recreando vista materializada vista_miembros_segmentacion...")

    conn.execute(sa.text("""
        CREATE MATERIALIZED VIEW vista_miembros_segmentacion AS
        SELECT
            m.id,
            m.nombre,
            m.apellido1,
            m.apellido2,
            m.email,
            m.fecha_nacimiento,
            CASE
                WHEN m.fecha_nacimiento IS NOT NULL THEN
                    EXTRACT(YEAR FROM age(CURRENT_DATE, m.fecha_nacimiento))::integer
                ELSE NULL
            END as edad,
            tm.nombre as tipo_miembro_nombre,
            em.nombre as estado_nombre,
            m.agrupacion_id,
            a.nombre as agrupacion_nombre,
            CASE
                WHEN m.fecha_nacimiento IS NOT NULL
                     AND EXTRACT(YEAR FROM age(CURRENT_DATE, m.fecha_nacimiento)) < 30
                THEN true
                ELSE false
            END as es_joven,
            CASE
                WHEN LOWER(tm.nombre) = 'simpatizante' THEN true
                ELSE false
            END as es_simpatizante,
            CASE
                WHEN m.es_voluntario = true
                     AND m.fecha_baja IS NULL
                     AND (m.disponibilidad IS NOT NULL OR COALESCE(m.horas_disponibles_semana, 0) > 0)
                THEN true
                ELSE false
            END as es_voluntario_disponible,
            m.es_voluntario,
            m.disponibilidad,
            m.fecha_alta,
            m.fecha_baja
        FROM miembros m
        INNER JOIN tipos_miembro tm ON m.tipo_miembro_id = tm.id
        INNER JOIN estados_miembro em ON m.estado_id = em.id
        LEFT JOIN vista_agrupaciones_territoriales a ON m.agrupacion_id = a.id
        WHERE m.eliminado = FALSE
    """))

    # Crear índices uno por uno
    conn.execute(sa.text("CREATE UNIQUE INDEX idx_vista_miembro_seg_id ON vista_miembros_segmentacion(id)"))
    conn.execute(sa.text("CREATE INDEX idx_vista_miembro_seg_es_joven ON vista_miembros_segmentacion(es_joven) WHERE es_joven = true"))
    conn.execute(sa.text("CREATE INDEX idx_vista_miembro_seg_es_simpatizante ON vista_miembros_segmentacion(es_simpatizante) WHERE es_simpatizante = true"))
    conn.execute(sa.text("CREATE INDEX idx_vista_miembro_seg_es_voluntario ON vista_miembros_segmentacion(es_voluntario_disponible) WHERE es_voluntario_disponible = true"))
    conn.execute(sa.text("CREATE INDEX idx_vista_miembro_seg_estado ON vista_miembros_segmentacion(estado_nombre)"))
    conn.execute(sa.text("CREATE INDEX idx_vista_miembro_seg_agrupacion ON vista_miembros_segmentacion(agrupacion_id)"))
    conn.execute(sa.text("CREATE INDEX idx_vista_miembro_seg_email ON vista_miembros_segmentacion(email) WHERE email IS NOT NULL"))

    print("Vista materializada recreada exitosamente.")


def downgrade() -> None:
    conn = op.get_bind()

    # 1. Eliminar las vistas nuevas
    conn.execute(sa.text("DROP MATERIALIZED VIEW IF EXISTS vista_miembros_segmentacion CASCADE"))
    conn.execute(sa.text("DROP MATERIALIZED VIEW IF EXISTS vista_agrupaciones_territoriales CASCADE"))

    # 2. Restaurar columnas 'codigo'
    for table in reversed(TABLES_WITH_CODIGO):
        # Verificar si la tabla existe
        result = conn.execute(sa.text(
            "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = :table)"
        ), {"table": table})
        table_exists = result.scalar()

        if not table_exists:
            continue

        # Verificar si la columna ya existe
        result = conn.execute(sa.text("""
            SELECT EXISTS (
                SELECT FROM information_schema.columns
                WHERE table_name = :table AND column_name = 'codigo'
            )
        """), {"table": table})
        column_exists = result.scalar()

        if column_exists:
            continue

        # Restaurar la columna
        conn.execute(sa.text(f"ALTER TABLE {table} ADD COLUMN codigo VARCHAR(50)"))
        conn.execute(sa.text(f"CREATE INDEX IF NOT EXISTS ix_{table}_codigo ON {table}(codigo)"))

    # 3. Nota: La vista original con 'codigo' debería recrearse manualmente si se necesita
