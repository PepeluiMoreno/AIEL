from enum import Enum as PyEnum

from sqlalchemy import String, Boolean, ForeignKey, Enum
from sqlalchemy.orm import Mapped, mapped_column, relationship

from ..core.database import Base


class TipoTransaccion(PyEnum):
    QUERY = "QUERY"
    MUTATION = "MUTATION"


class TipoMiembro(Base):
    """SOCIO, SIMPATIZANTE, etc."""
    __tablename__ = "tipo_miembro"

    id: Mapped[int] = mapped_column(primary_key=True)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    requiere_cuota: Mapped[bool] = mapped_column(Boolean, default=False)
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class Rol(Base):
    """PRESIDENTE, TESORERO, COORDINADOR, ADMIN, GESTOR_SIMPS, etc."""
    __tablename__ = "rol"

    id: Mapped[int] = mapped_column(primary_key=True)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    descripcion: Mapped[str | None] = mapped_column(String(500))
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class Transaccion(Base):
    """Operaciones del sistema: ALTA_SOCIO, BAJA_SOCIO, COBRAR_CUOTA, etc."""
    __tablename__ = "transaccion"

    id: Mapped[int] = mapped_column(primary_key=True)
    codigo: Mapped[str] = mapped_column(String(50), unique=True)
    nombre: Mapped[str] = mapped_column(String(100))
    tipo: Mapped[TipoTransaccion] = mapped_column(Enum(TipoTransaccion), default=TipoTransaccion.QUERY)
    modulo: Mapped[str | None] = mapped_column(String(50))
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class RolTransaccion(Base):
    """Permisos: qué roles pueden ejecutar qué transacciones."""
    __tablename__ = "rol_transaccion"

    rol_id: Mapped[int] = mapped_column(ForeignKey("rol.id"), primary_key=True)
    transaccion_id: Mapped[int] = mapped_column(ForeignKey("transaccion.id"), primary_key=True)

    rol: Mapped["Rol"] = relationship()
    transaccion: Mapped["Transaccion"] = relationship()
