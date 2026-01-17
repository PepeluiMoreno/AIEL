import strawberry
from datetime import date, datetime
from decimal import Decimal


@strawberry.type
class CategoriaCompetencia:
    id: int
    codigo: str
    nombre: str
    descripcion: str | None
    activo: bool


@strawberry.type
class Competencia:
    id: int
    codigo: str
    nombre: str
    descripcion: str | None
    categoria: CategoriaCompetencia
    activo: bool


@strawberry.type
class NivelCompetencia:
    id: int
    codigo: str
    nombre: str
    orden: int
    activo: bool


@strawberry.type
class MiembroCompetencia:
    competencia: Competencia
    nivel: NivelCompetencia
    verificado: bool
    fecha_verificacion: date | None
    observaciones: str | None


@strawberry.type
class TipoDocumentoVoluntario:
    id: int
    codigo: str
    nombre: str
    descripcion: str | None
    requiere_caducidad: bool
    activo: bool


@strawberry.type
class DocumentoMiembro:
    id: int
    tipo_documento: TipoDocumentoVoluntario
    nombre: str
    descripcion: str | None
    archivo_url: str
    archivo_nombre: str
    archivo_tipo: str | None
    archivo_tamano: int | None
    fecha_subida: datetime
    fecha_caducidad: date | None
    activo: bool

    @strawberry.field
    def esta_caducado(self) -> bool:
        if self.fecha_caducidad is None:
            return False
        return self.fecha_caducidad < date.today()


@strawberry.type
class TipoFormacion:
    id: int
    codigo: str
    nombre: str
    activo: bool


@strawberry.type
class FormacionMiembro:
    id: int
    tipo_formacion: TipoFormacion
    titulo: str
    institucion: str | None
    descripcion: str | None
    fecha_inicio: date | None
    fecha_fin: date | None
    horas: int | None
    certificado: bool
    competencias_adquiridas: str | None
    es_interna: bool


@strawberry.enum
class Disponibilidad:
    COMPLETA = "COMPLETA"
    FINES_SEMANA = "FINES_SEMANA"
    TARDES = "TARDES"
    MANANAS = "MANANAS"
    PUNTUAL = "PUNTUAL"


@strawberry.type
class PerfilVoluntario:
    """Información de voluntariado de un miembro."""
    miembro_id: int
    nombre_completo: str
    es_voluntario: bool
    disponibilidad: str | None
    horas_disponibles_semana: int | None
    experiencia_voluntariado: str | None
    intereses: str | None
    puede_conducir: bool
    vehiculo_propio: bool
    disponibilidad_viajar: bool
    total_horas_voluntariado: Decimal
    total_campanias_participadas: int
    fecha_ultimo_voluntariado: date | None
    competencias: list[MiembroCompetencia]
    formaciones: list[FormacionMiembro]


# --- Input Types ---

@strawberry.input
class VoluntariadoInput:
    """Datos de voluntariado para actualizar un miembro."""
    es_voluntario: bool | None = None
    disponibilidad: str | None = None
    horas_disponibles_semana: int | None = None
    experiencia_voluntariado: str | None = None
    intereses: str | None = None
    observaciones_voluntariado: str | None = None
    puede_conducir: bool | None = None
    vehiculo_propio: bool | None = None
    disponibilidad_viajar: bool | None = None


@strawberry.input
class CompetenciaInput:
    codigo: str
    nombre: str
    descripcion: str | None = None
    categoria_id: int


@strawberry.input
class MiembroCompetenciaInput:
    miembro_id: int
    competencia_id: int
    nivel_id: int
    observaciones: str | None = None


@strawberry.input
class DocumentoMiembroInput:
    miembro_id: int
    tipo_documento_id: int
    nombre: str
    descripcion: str | None = None
    archivo_url: str
    archivo_nombre: str
    archivo_tipo: str | None = None
    archivo_tamano: int | None = None
    fecha_caducidad: date | None = None


@strawberry.input
class FormacionMiembroInput:
    miembro_id: int
    tipo_formacion_id: int
    titulo: str
    institucion: str | None = None
    descripcion: str | None = None
    fecha_inicio: date | None = None
    fecha_fin: date | None = None
    horas: int | None = None
    certificado: bool = False
    competencias_adquiridas: str | None = None
    es_interna: bool = False


@strawberry.input
class FormacionMiembroUpdateInput:
    titulo: str | None = None
    institucion: str | None = None
    descripcion: str | None = None
    fecha_inicio: date | None = None
    fecha_fin: date | None = None
    horas: int | None = None
    certificado: bool | None = None
    competencias_adquiridas: str | None = None


@strawberry.input
class BusquedaVoluntariosInput:
    """Filtros para buscar voluntarios por competencias."""
    competencia_ids: list[int] | None = None
    disponibilidad: str | None = None
    puede_conducir: bool | None = None
    vehiculo_propio: bool | None = None
    disponibilidad_viajar: bool | None = None
    agrupacion_id: int | None = None
    solo_activos: bool = True
