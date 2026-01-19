"""Schemas GraphQL para actividades, propuestas y KPIs."""

import strawberry
from datetime import date, datetime, time
from decimal import Decimal
from uuid import UUID

from .miembro import Miembro
from .presupuesto import PartidaPresupuestaria, PlanificacionAnual


# =====================
# CATÁLOGOS
# =====================

@strawberry.type
class TipoActividad:
    id: int
    codigo: str
    nombre: str
    descripcion: str | None
    requiere_grupo: bool
    requiere_presupuesto: bool
    activo: bool


@strawberry.type
class EstadoActividad:
    id: int
    codigo: str
    nombre: str
    orden: int
    color: str | None
    es_final: bool
    activo: bool


@strawberry.type
class EstadoPropuesta:
    id: int
    codigo: str
    nombre: str
    orden: int
    color: str | None
    es_final: bool
    activo: bool


@strawberry.type
class TipoRecurso:
    id: int
    codigo: str
    nombre: str
    descripcion: str | None
    requiere_importe: bool
    activo: bool


@strawberry.type
class TipoKPI:
    id: int
    codigo: str
    nombre: str
    formato: str | None
    activo: bool


@strawberry.type
class EstadoTarea:
    id: int
    codigo: str
    nombre: str
    orden: int
    color: str | None
    es_final: bool
    activo: bool


# =====================
# GRUPO DE TRABAJO (referencia simplificada)
# =====================

@strawberry.type
class GrupoTrabajoRef:
    id: int
    codigo: str
    nombre: str


# =====================
# CAMPAÑA (referencia simplificada)
# =====================

@strawberry.type
class CampaniaRef:
    id: int
    codigo: str
    nombre: str


# =====================
# PROPUESTAS
# =====================

@strawberry.type
class TareaPropuesta:
    id: UUID
    nombre: str
    descripcion: str | None
    orden: int
    grupo_trabajo: GrupoTrabajoRef | None
    responsable: Miembro | None
    fecha_inicio_estimada: date | None
    fecha_fin_estimada: date | None
    horas_estimadas: Decimal | None


@strawberry.type
class RecursoPropuesta:
    id: UUID
    tipo_recurso: TipoRecurso
    descripcion: str
    cantidad: int
    importe_unitario_estimado: Decimal
    importe_total_estimado: Decimal
    importe_aprobado: Decimal | None
    proveedor: str | None
    observaciones: str | None


@strawberry.type
class GrupoPropuesta:
    grupo_trabajo: GrupoTrabajoRef
    tareas_asignadas: str | None
    horas_estimadas: Decimal | None


@strawberry.type
class PropuestaActividad:
    id: UUID
    codigo: str
    titulo: str
    descripcion: str | None
    justificacion: str | None
    proponente: Miembro
    estado: EstadoPropuesta
    fecha_presentacion: date | None
    fecha_resolucion: date | None
    motivo_resolucion: str | None
    campania: CampaniaRef | None
    fecha_inicio_propuesta: date | None
    fecha_fin_propuesta: date | None
    presupuesto_solicitado: Decimal
    presupuesto_aprobado: Decimal | None
    partida: PartidaPresupuestaria | None
    observaciones: str | None
    tareas: list[TareaPropuesta]
    recursos: list[RecursoPropuesta]
    grupos_asignados: list[GrupoPropuesta]
    created_at: datetime
    updated_at: datetime | None


# =====================
# ACTIVIDADES
# =====================

@strawberry.type
class TareaActividad:
    id: UUID
    nombre: str
    descripcion: str | None
    orden: int
    grupo_trabajo: GrupoTrabajoRef | None
    responsable: Miembro | None
    estado: EstadoTarea
    fecha_limite: date | None
    fecha_completada: datetime | None
    horas_estimadas: Decimal | None
    horas_reales: Decimal | None


@strawberry.type
class RecursoActividad:
    id: UUID
    tipo_recurso: TipoRecurso
    descripcion: str
    cantidad: int
    importe_presupuestado: Decimal
    importe_real: Decimal
    proveedor: str | None
    factura_referencia: str | None
    fecha_factura: date | None
    pagado: bool
    fecha_pago: date | None
    observaciones: str | None


@strawberry.type
class GrupoActividad:
    grupo_trabajo: GrupoTrabajoRef
    tareas_asignadas: str | None
    horas_estimadas: Decimal | None
    horas_reales: Decimal | None


@strawberry.type
class ParticipanteActividad:
    miembro: Miembro
    rol: str
    confirmado: bool
    asistio: bool | None
    horas_aportadas: Decimal
    observaciones: str | None


@strawberry.type
class MedicionKPI:
    id: UUID
    valor_medido: Decimal
    fecha_medicion: datetime
    medido_por: Miembro | None
    observaciones: str | None


@strawberry.type
class KPI:
    id: UUID
    codigo: str
    nombre: str
    descripcion: str | None
    tipo_kpi: TipoKPI
    unidad: str | None
    valor_objetivo_defecto: Decimal | None
    valor_minimo: Decimal | None
    formula: str | None
    activo: bool


@strawberry.type
class KPIActividad:
    id: UUID
    kpi: KPI
    valor_objetivo: Decimal
    peso: Decimal
    valor_actual: Decimal | None
    fecha_ultima_medicion: datetime | None
    porcentaje_logro: Decimal | None
    observaciones: str | None
    mediciones: list[MedicionKPI]


@strawberry.type
class Actividad:
    id: UUID
    codigo: str
    nombre: str
    descripcion: str | None
    propuesta: PropuestaActividad | None
    tipo_actividad: TipoActividad
    estado: EstadoActividad
    prioridad: int
    fecha_inicio: date
    fecha_fin: date
    hora_inicio: time | None
    hora_fin: time | None
    es_todo_el_dia: bool
    lugar: str | None
    direccion: str | None
    es_online: bool
    url_online: str | None
    campania: CampaniaRef | None
    coordinador: Miembro
    es_colectiva: bool
    partida: PartidaPresupuestaria | None
    dotacion_economica: Decimal
    gasto_real: Decimal
    voluntarios_necesarios: int
    voluntarios_confirmados: int
    completada: bool
    fecha_completada: datetime | None
    resultados: str | None
    observaciones: str | None
    tareas: list[TareaActividad]
    recursos: list[RecursoActividad]
    participantes: list[ParticipanteActividad]
    grupos_trabajo: list[GrupoActividad]
    kpis: list[KPIActividad]
    created_at: datetime
    updated_at: datetime | None

    @strawberry.field
    def porcentaje_voluntarios(self) -> Decimal:
        if self.voluntarios_necesarios == 0:
            return Decimal("100")
        return (Decimal(self.voluntarios_confirmados) / Decimal(self.voluntarios_necesarios)) * 100


# =====================
# INPUT TYPES
# =====================

@strawberry.input
class PropuestaActividadInput:
    codigo: str
    titulo: str
    descripcion: str | None = None
    justificacion: str | None = None
    proponente_id: UUID
    campania_id: int | None = None
    planificacion_id: UUID | None = None
    fecha_inicio_propuesta: date | None = None
    fecha_fin_propuesta: date | None = None
    presupuesto_solicitado: Decimal = Decimal("0")
    partida_id: UUID | None = None
    observaciones: str | None = None


@strawberry.input
class PropuestaActividadUpdateInput:
    titulo: str | None = None
    descripcion: str | None = None
    justificacion: str | None = None
    estado_id: int | None = None
    fecha_inicio_propuesta: date | None = None
    fecha_fin_propuesta: date | None = None
    presupuesto_solicitado: Decimal | None = None
    presupuesto_aprobado: Decimal | None = None
    partida_id: UUID | None = None
    motivo_resolucion: str | None = None
    observaciones: str | None = None


@strawberry.input
class TareaPropuestaInput:
    propuesta_id: UUID
    nombre: str
    descripcion: str | None = None
    orden: int = 0
    grupo_trabajo_id: int | None = None
    responsable_id: UUID | None = None
    fecha_inicio_estimada: date | None = None
    fecha_fin_estimada: date | None = None
    horas_estimadas: Decimal | None = None


@strawberry.input
class RecursoPropuestaInput:
    propuesta_id: UUID
    tipo_recurso_id: int
    descripcion: str
    cantidad: int = 1
    importe_unitario_estimado: Decimal = Decimal("0")
    proveedor: str | None = None
    observaciones: str | None = None


@strawberry.input
class GrupoPropuestaInput:
    propuesta_id: UUID
    grupo_trabajo_id: int
    tareas_asignadas: str | None = None
    horas_estimadas: Decimal | None = None


@strawberry.input
class ActividadInput:
    codigo: str
    nombre: str
    descripcion: str | None = None
    propuesta_id: UUID | None = None
    tipo_actividad_id: int
    prioridad: int = 2
    fecha_inicio: date
    fecha_fin: date
    hora_inicio: time | None = None
    hora_fin: time | None = None
    es_todo_el_dia: bool = True
    lugar: str | None = None
    direccion: str | None = None
    es_online: bool = False
    url_online: str | None = None
    campania_id: int | None = None
    planificacion_id: UUID | None = None
    coordinador_id: UUID
    es_colectiva: bool = False
    partida_id: UUID | None = None
    dotacion_economica: Decimal = Decimal("0")
    voluntarios_necesarios: int = 0
    observaciones: str | None = None


@strawberry.input
class ActividadUpdateInput:
    nombre: str | None = None
    descripcion: str | None = None
    tipo_actividad_id: int | None = None
    estado_id: int | None = None
    prioridad: int | None = None
    fecha_inicio: date | None = None
    fecha_fin: date | None = None
    hora_inicio: time | None = None
    hora_fin: time | None = None
    es_todo_el_dia: bool | None = None
    lugar: str | None = None
    direccion: str | None = None
    es_online: bool | None = None
    url_online: str | None = None
    coordinador_id: UUID | None = None
    es_colectiva: bool | None = None
    partida_id: UUID | None = None
    dotacion_economica: Decimal | None = None
    gasto_real: Decimal | None = None
    voluntarios_necesarios: int | None = None
    completada: bool | None = None
    resultados: str | None = None
    observaciones: str | None = None


@strawberry.input
class TareaActividadInput:
    actividad_id: UUID
    nombre: str
    descripcion: str | None = None
    orden: int = 0
    grupo_trabajo_id: int | None = None
    responsable_id: UUID | None = None
    fecha_limite: date | None = None
    horas_estimadas: Decimal | None = None


@strawberry.input
class RecursoActividadInput:
    actividad_id: UUID
    tipo_recurso_id: int
    descripcion: str
    cantidad: int = 1
    importe_presupuestado: Decimal = Decimal("0")
    proveedor: str | None = None
    observaciones: str | None = None


@strawberry.input
class ParticipanteActividadInput:
    actividad_id: UUID
    miembro_id: UUID
    rol: str = "VOLUNTARIO"
    observaciones: str | None = None


@strawberry.input
class KPIInput:
    codigo: str
    nombre: str
    descripcion: str | None = None
    tipo_kpi_id: int
    unidad: str | None = None
    valor_objetivo_defecto: Decimal | None = None
    valor_minimo: Decimal | None = None
    formula: str | None = None


@strawberry.input
class KPIActividadInput:
    actividad_id: UUID
    kpi_id: UUID
    valor_objetivo: Decimal
    peso: Decimal = Decimal("1")
    observaciones: str | None = None


@strawberry.input
class MedicionKPIInput:
    kpi_actividad_id: UUID
    valor_medido: Decimal
    medido_por_id: UUID | None = None
    observaciones: str | None = None
