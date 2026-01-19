import uuid
from datetime import date
from decimal import Decimal
from sqlalchemy import String, ForeignKey, Date, Boolean, Text, Numeric, Uuid
from sqlalchemy.orm import Mapped, mapped_column, relationship

from ..core.database import Base
from .mixins import SoftDeleteMixin, AuditoriaMixin


class Miembro(Base, SoftDeleteMixin, AuditoriaMixin):
    __tablename__ = "miembro"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    usuario_id: Mapped[uuid.UUID | None] = mapped_column(Uuid, ForeignKey("usuario.id"), unique=True)

    # Tipo: SOCIO, SIMPATIZANTE, etc.
    tipo_miembro_id: Mapped[int] = mapped_column(ForeignKey("tipo_miembro.id"))

    # Tipo de persona: FISICA (persona individual) o JURIDICA (asociación)
    tipo_persona: Mapped[str] = mapped_column(String(10), default="FISICA")  # FISICA, JURIDICA

    # Tipo de membresía: DIRECTA (Europa Laica Nacional) o INDIRECTA (a través de agrupación territorial)
    tipo_membresia: Mapped[str] = mapped_column(String(10), default="DIRECTA")  # DIRECTA, INDIRECTA

    # Datos personales (para persona física) o razón social (para persona jurídica)
    nombre: Mapped[str] = mapped_column(String(100))  # Nombre o Razón Social
    apellido1: Mapped[str | None] = mapped_column(String(100))  # Null para personas jurídicas
    apellido2: Mapped[str | None] = mapped_column(String(100))
    fecha_nacimiento: Mapped[date | None] = mapped_column(Date)

    # Documento
    tipo_documento: Mapped[str | None] = mapped_column(String(10))  # DNI, NIE, PASAPORTE, CIF
    numero_documento: Mapped[str | None] = mapped_column(String(20))
    pais_documento_id: Mapped[str | None] = mapped_column(ForeignKey("pais.codpais"))

    # Domicilio
    direccion: Mapped[str | None] = mapped_column(String(300))
    codigo_postal: Mapped[str | None] = mapped_column(String(10))
    localidad: Mapped[str | None] = mapped_column(String(100))
    provincia_id: Mapped[str | None] = mapped_column(ForeignKey("provincia.codprov"))
    pais_domicilio_id: Mapped[str | None] = mapped_column(ForeignKey("pais.codpais"))

    # Contacto
    telefono: Mapped[str | None] = mapped_column(String(20))
    telefono2: Mapped[str | None] = mapped_column(String(20))
    email: Mapped[str | None] = mapped_column(String(255))

    # Agrupación territorial
    agrupacion_id: Mapped[uuid.UUID | None] = mapped_column(Uuid, ForeignKey("agrupacion_territorial.id"))

    # Datos bancarios (solo si es socio con cuota)
    iban: Mapped[str | None] = mapped_column(String(34))

    # --- CAMPOS DE VOLUNTARIADO (RF-VC001 a RF-VC005) ---

    # Disponibilidad (RF-VC003)
    es_voluntario: Mapped[bool] = mapped_column(Boolean, default=False)
    disponibilidad: Mapped[str | None] = mapped_column(String(20))  # COMPLETA, FINES_SEMANA, TARDES, MANANAS, PUNTUAL
    horas_disponibles_semana: Mapped[int | None]  # Horas semanales disponibles

    # Experiencia y preferencias
    experiencia_voluntariado: Mapped[str | None] = mapped_column(Text)
    intereses: Mapped[str | None] = mapped_column(Text)  # Áreas de interés
    observaciones_voluntariado: Mapped[str | None] = mapped_column(Text)

    # Movilidad
    puede_conducir: Mapped[bool] = mapped_column(Boolean, default=False)
    vehiculo_propio: Mapped[bool] = mapped_column(Boolean, default=False)
    disponibilidad_viajar: Mapped[bool] = mapped_column(Boolean, default=False)

    # Estadísticas de participación (RF-VC005)
    total_horas_voluntariado: Mapped[Decimal] = mapped_column(Numeric(8, 2), default=0)
    total_campanias_participadas: Mapped[int] = mapped_column(default=0)
    fecha_ultimo_voluntariado: Mapped[date | None] = mapped_column(Date)

    # --- FIN CAMPOS DE VOLUNTARIADO ---

    # Fechas de alta/baja en la organización
    fecha_alta: Mapped[date] = mapped_column(Date, default=date.today)
    fecha_baja: Mapped[date | None] = mapped_column(Date)

    # Relaciones
    usuario: Mapped["Usuario"] = relationship(back_populates="miembro")
    tipo_miembro: Mapped["TipoMiembro"] = relationship()
    agrupacion: Mapped["AgrupacionTerritorial | None"] = relationship(back_populates="miembros")
    provincia: Mapped["Provincia | None"] = relationship()
    pais_domicilio: Mapped["Pais | None"] = relationship(foreign_keys=[pais_domicilio_id])
    pais_documento: Mapped["Pais | None"] = relationship(foreign_keys=[pais_documento_id])
    competencias: Mapped[list["MiembroCompetencia"]] = relationship(back_populates="miembro")
    documentos: Mapped[list["DocumentoMiembro"]] = relationship(back_populates="miembro")
    formaciones: Mapped[list["FormacionMiembro"]] = relationship(back_populates="miembro")


from .usuario import Usuario  # noqa: E402,F401
from .tipologias import TipoMiembro  # noqa: E402,F401
from .agrupacion import AgrupacionTerritorial  # noqa: E402,F401
from .catalogos import Pais, Provincia  # noqa: E402,F401
from .voluntariado import MiembroCompetencia, DocumentoMiembro, FormacionMiembro  # noqa: E402,F401
