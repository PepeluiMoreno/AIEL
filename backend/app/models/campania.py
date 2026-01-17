from datetime import date, datetime
from decimal import Decimal

from sqlalchemy import String, ForeignKey, DateTime, Date, Numeric, Boolean, Text, Integer
from sqlalchemy.orm import Mapped, mapped_column, relationship

from ..core.database import Base


class TipoCampania(Base):
    """Tipos de campaña: RECAUDACION, SENSIBILIZACION, AYUDA_DIRECTA, etc."""
    __tablename__ = "tipo_campania"

    id: Mapped[int] = mapped_column(primary_key=True)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    descripcion: Mapped[str | None] = mapped_column(String(500))
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class EstadoCampania(Base):
    """Estados: PLANIFICADA, ACTIVA, SUSPENDIDA, FINALIZADA, CANCELADA."""
    __tablename__ = "estado_campania"

    id: Mapped[int] = mapped_column(primary_key=True)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    orden: Mapped[int] = mapped_column(Integer, default=0)
    color: Mapped[str | None] = mapped_column(String(7))  # Hex color: #FFFFFF
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class Campania(Base):
    """Campaña de la ONG."""
    __tablename__ = "campania"

    id: Mapped[int] = mapped_column(primary_key=True)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(200))
    descripcion_corta: Mapped[str | None] = mapped_column(String(300))
    descripcion_larga: Mapped[str | None] = mapped_column(Text)

    # Clasificación
    tipo_campania_id: Mapped[int] = mapped_column(ForeignKey("tipo_campania.id"))
    estado_campania_id: Mapped[int] = mapped_column(ForeignKey("estado_campania.id"))

    # Fechas planificadas
    fecha_inicio_plan: Mapped[date | None] = mapped_column(Date)
    fecha_fin_plan: Mapped[date | None] = mapped_column(Date)

    # Fechas reales
    fecha_inicio_real: Mapped[date | None] = mapped_column(Date)
    fecha_fin_real: Mapped[date | None] = mapped_column(Date)

    # Objetivos y metas
    objetivo_principal: Mapped[str | None] = mapped_column(Text)
    meta_recaudacion: Mapped[Decimal | None] = mapped_column(Numeric(12, 2))
    meta_participantes: Mapped[int | None] = mapped_column(Integer)

    # Responsable y agrupación
    responsable_id: Mapped[int | None] = mapped_column(ForeignKey("miembro.id"))
    agrupacion_id: Mapped[int | None] = mapped_column(ForeignKey("agrupacion_territorial.id"))

    # Auditoría
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    created_by_id: Mapped[int] = mapped_column(ForeignKey("usuario.id"))
    updated_at: Mapped[datetime | None] = mapped_column(DateTime, onupdate=datetime.utcnow)

    # Relaciones
    tipo_campania: Mapped["TipoCampania"] = relationship()
    estado_campania: Mapped["EstadoCampania"] = relationship()
    responsable: Mapped["Miembro | None"] = relationship(foreign_keys=[responsable_id])
    agrupacion: Mapped["AgrupacionTerritorial | None"] = relationship()
    creador: Mapped["Usuario"] = relationship(foreign_keys=[created_by_id])
    acciones: Mapped[list["AccionCampania"]] = relationship(back_populates="campania")
    participantes: Mapped[list["ParticipanteCampania"]] = relationship(back_populates="campania")


class AccionCampania(Base):
    """Acción/actividad dentro de una campaña."""
    __tablename__ = "accion_campania"

    id: Mapped[int] = mapped_column(primary_key=True)
    campania_id: Mapped[int] = mapped_column(ForeignKey("campania.id"))

    nombre: Mapped[str] = mapped_column(String(200))
    descripcion: Mapped[str | None] = mapped_column(Text)

    # Fecha y hora
    fecha: Mapped[date] = mapped_column(Date)
    hora_inicio: Mapped[str | None] = mapped_column(String(5))  # "09:00"
    hora_fin: Mapped[str | None] = mapped_column(String(5))     # "14:00"

    # Ubicación
    lugar: Mapped[str | None] = mapped_column(String(200))
    direccion: Mapped[str | None] = mapped_column(String(500))

    # Logística
    voluntarios_necesarios: Mapped[int] = mapped_column(Integer, default=0)
    voluntarios_confirmados: Mapped[int] = mapped_column(Integer, default=0)
    materiales_necesarios: Mapped[str | None] = mapped_column(Text)
    observaciones: Mapped[str | None] = mapped_column(Text)

    # Estado
    completada: Mapped[bool] = mapped_column(Boolean, default=False)

    # Relaciones
    campania: Mapped["Campania"] = relationship(back_populates="acciones")


class RolParticipante(Base):
    """Roles de participación: VOLUNTARIO, COORDINADOR, DONANTE, etc."""
    __tablename__ = "rol_participante"

    id: Mapped[int] = mapped_column(primary_key=True)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    es_voluntario: Mapped[bool] = mapped_column(Boolean, default=False)
    es_coordinador: Mapped[bool] = mapped_column(Boolean, default=False)
    es_donante: Mapped[bool] = mapped_column(Boolean, default=False)
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class ParticipanteCampania(Base):
    """Participación de un miembro en una campaña."""
    __tablename__ = "participante_campania"

    # Clave primaria compuesta
    campania_id: Mapped[int] = mapped_column(ForeignKey("campania.id"), primary_key=True)
    miembro_id: Mapped[int] = mapped_column(ForeignKey("miembro.id"), primary_key=True)

    rol_participante_id: Mapped[int] = mapped_column(ForeignKey("rol_participante.id"))

    # Datos de participación
    horas_aportadas: Mapped[Decimal | None] = mapped_column(Numeric(6, 2))
    donacion_asociada_id: Mapped[int | None] = mapped_column(ForeignKey("donacion.id"))

    # Estado
    confirmado: Mapped[bool] = mapped_column(Boolean, default=False)
    asistio: Mapped[bool | None] = mapped_column(Boolean)

    # Fechas
    fecha_inscripcion: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    fecha_confirmacion: Mapped[datetime | None] = mapped_column(DateTime)

    # Observaciones
    observaciones: Mapped[str | None] = mapped_column(Text)

    # Relaciones
    campania: Mapped["Campania"] = relationship(back_populates="participantes")
    miembro: Mapped["Miembro"] = relationship()
    rol_participante: Mapped["RolParticipante"] = relationship()
    donacion_asociada: Mapped["Donacion | None"] = relationship()


# Imports para evitar circular imports
from .miembro import Miembro  # noqa: E402,F401
from .agrupacion import AgrupacionTerritorial  # noqa: E402,F401
from .usuario import Usuario  # noqa: E402,F401
from .financiero import Donacion  # noqa: E402,F401
