import uuid
from enum import Enum as PyEnum

from sqlalchemy import String, Boolean, ForeignKey, Enum, Uuid
from sqlalchemy.orm import Mapped, mapped_column, relationship

from ..core.database import Base


class TipoTransaccion(PyEnum):
    QUERY = "QUERY"
    MUTATION = "MUTATION"


class TipoMiembro(Base):
    """SOCIO, SIMPATIZANTE, etc."""
    __tablename__ = "tipo_miembro"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    requiere_cuota: Mapped[bool] = mapped_column(Boolean, default=False)
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class Rol(Base):
    """PRESIDENTE, TESORERO, COORDINADOR, Admin, GESTOR_SIMPS, etc."""
    __tablename__ = "rol"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    descripcion: Mapped[str | None] = mapped_column(String(500))
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class Transaccion(Base):
    """Operaciones del sistema: ALTA_SOCIO, BAJA_SOCIO, COBRAR_CUOTA, etc."""
    __tablename__ = "transaccion"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    codigo: Mapped[str] = mapped_column(String(50), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    tipo: Mapped[TipoTransaccion] = mapped_column(Enum(TipoTransaccion), default=TipoTransaccion.QUERY)
    modulo: Mapped[str | None] = mapped_column(String(50))
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class RolTransaccion(Base):
    """Permisos: qué roles pueden ejecutar qué transacciones."""
    __tablename__ = "rol_transaccion"

    rol_id: Mapped[uuid.UUID] = mapped_column(Uuid, ForeignKey("rol.id"), primary_key=True)
    transaccion_id: Mapped[uuid.UUID] = mapped_column(Uuid, ForeignKey("transaccion.id"), primary_key=True)

    rol: Mapped["Rol"] = relationship()
    transaccion: Mapped["Transaccion"] = relationship()
