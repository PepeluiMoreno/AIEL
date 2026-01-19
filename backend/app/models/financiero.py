import uuid
from datetime import date, datetime
from decimal import Decimal
from enum import Enum as PyEnum

from sqlalchemy import String, ForeignKey, DateTime, Date, Numeric, Enum, Boolean, Text, Uuid
from sqlalchemy.orm import Mapped, mapped_column, relationship

from ..core.database import Base


class EstadoCuota(PyEnum):
    PENDIENTE = "PENDIENTE"
    COBRADA = "COBRADA"
    COBRADA_PARCIAL = "COBRADA_PARCIAL"
    EXENTO = "EXENTO"
    DEVUELTA = "DEVUELTA"


class ModoIngreso(PyEnum):
    SEPA = "SEPA"
    TRANSFERENCIA = "TRANSFERENCIA"
    PAYPAL = "PAYPAL"
    EFECTIVO = "EFECTIVO"


class ImporteCuotaAnio(Base):
    """Importe de cuota por año."""
    __tablename__ = "importe_cuota_anio"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    anio: Mapped[int] = mapped_column(unique=True)
    importe: Mapped[Decimal] = mapped_column(Numeric(10, 2))


class CuotaAnio(Base):
    """Cuota anual de un miembro (socio)."""
    __tablename__ = "cuota_anio"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    miembro_id: Mapped[uuid.UUID] = mapped_column(Uuid, ForeignKey("miembro.id"))
    anio: Mapped[int]
    agrupacion_id: Mapped[uuid.UUID] = mapped_column(Uuid, ForeignKey("agrupacion_territorial.id"))

    importe: Mapped[Decimal] = mapped_column(Numeric(10, 2))
    importe_pagado: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    estado: Mapped[EstadoCuota] = mapped_column(Enum(EstadoCuota), default=EstadoCuota.PENDIENTE)
    modo_ingreso: Mapped[ModoIngreso | None] = mapped_column(Enum(ModoIngreso))

    fecha_pago: Mapped[date | None] = mapped_column(Date)
    observaciones: Mapped[str | None] = mapped_column(Text)

    miembro: Mapped["Miembro"] = relationship()
    agrupacion: Mapped["AgrupacionTerritorial"] = relationship()


class DonacionConcepto(Base):
    """Conceptos de donación predefinidos."""
    __tablename__ = "donacion_concepto"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    codigo: Mapped[str] = mapped_column(String(20), unique=True)
    nombre: Mapped[str] = mapped_column(String(200))
    activo: Mapped[bool] = mapped_column(Boolean, default=True)


class Donacion(Base):
    """Donaciones de miembros."""
    __tablename__ = "donacion"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    miembro_id: Mapped[uuid.UUID] = mapped_column(Uuid, ForeignKey("miembro.id"))
    concepto_id: Mapped[uuid.UUID | None] = mapped_column(Uuid, ForeignKey("donacion_concepto.id"))
    campania_id: Mapped[int | None] = mapped_column(ForeignKey("campania.id"))

    importe: Mapped[Decimal] = mapped_column(Numeric(10, 2))
    gastos: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    fecha: Mapped[date] = mapped_column(Date, default=date.today)
    modo_ingreso: Mapped[ModoIngreso | None] = mapped_column(Enum(ModoIngreso))
    observaciones: Mapped[str | None] = mapped_column(Text)

    deleted_at: Mapped[datetime | None] = mapped_column(DateTime)

    miembro: Mapped["Miembro"] = relationship()
    concepto: Mapped["DonacionConcepto | None"] = relationship()
    campania: Mapped["Campania | None"] = relationship()


class Remesa(Base):
    """Lote de cobros SEPA."""
    __tablename__ = "remesa"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    fecha: Mapped[date] = mapped_column(Date, default=date.today)
    importe_total: Mapped[Decimal] = mapped_column(Numeric(10, 2))
    gastos: Mapped[Decimal] = mapped_column(Numeric(10, 2), default=0)
    archivo_sepa: Mapped[str | None] = mapped_column(String(255))
    observaciones: Mapped[str | None] = mapped_column(Text)

    ordenes: Mapped[list["OrdenCobro"]] = relationship(back_populates="remesa")


class OrdenCobro(Base):
    """Orden individual dentro de remesa SEPA."""
    __tablename__ = "orden_cobro"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    remesa_id: Mapped[uuid.UUID] = mapped_column(Uuid, ForeignKey("remesa.id"))
    cuota_id: Mapped[uuid.UUID] = mapped_column(Uuid, ForeignKey("cuota_anio.id"))
    importe: Mapped[Decimal] = mapped_column(Numeric(10, 2))
    estado: Mapped[str] = mapped_column(String(20), default="PENDIENTE")

    remesa: Mapped["Remesa"] = relationship(back_populates="ordenes")
    cuota: Mapped["CuotaAnio"] = relationship()


from .miembro import Miembro  # noqa: E402,F401
from .agrupacion import AgrupacionTerritorial  # noqa: E402,F401
from .campania import Campania  # noqa: E402,F401
