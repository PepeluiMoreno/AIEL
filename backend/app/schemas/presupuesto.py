"""Schemas GraphQL para presupuesto y planificación."""

import strawberry
from datetime import date, datetime
from decimal import Decimal
from uuid import UUID


@strawberry.type
class EstadoPlanificacion:
    id: int
    codigo: str
    nombre: str
    orden: int
    color: str | None
    es_final: bool
    activo: bool


@strawberry.type
class CategoriaPartida:
    id: int
    codigo: str
    nombre: str
    descripcion: str | None
    activo: bool


@strawberry.type
class PartidaPresupuestaria:
    id: UUID
    codigo: str
    nombre: str
    descripcion: str | None
    ejercicio: int
    tipo: str  # INGRESO, GASTO
    categoria: CategoriaPartida | None
    importe_presupuestado: Decimal
    importe_comprometido: Decimal
    importe_ejecutado: Decimal
    activo: bool
    created_at: datetime
    updated_at: datetime | None

    @strawberry.field
    def importe_disponible(self) -> Decimal:
        return self.importe_presupuestado - self.importe_comprometido

    @strawberry.field
    def porcentaje_ejecutado(self) -> Decimal:
        if self.importe_presupuestado == 0:
            return Decimal("0")
        return (self.importe_ejecutado / self.importe_presupuestado) * 100


@strawberry.type
class PlanificacionAnual:
    id: UUID
    ejercicio: int
    nombre: str
    descripcion: str | None
    objetivos: str | None
    estado: EstadoPlanificacion
    fecha_aprobacion: date | None
    presupuesto_total: Decimal
    partidas: list[PartidaPresupuestaria]
    created_at: datetime
    updated_at: datetime | None

    @strawberry.field
    def total_presupuestado(self) -> Decimal:
        return sum((p.importe_presupuestado for p in self.partidas), Decimal("0"))

    @strawberry.field
    def total_ejecutado(self) -> Decimal:
        return sum((p.importe_ejecutado for p in self.partidas), Decimal("0"))


# --- Input Types ---

@strawberry.input
class PlanificacionAnualInput:
    ejercicio: int
    nombre: str
    descripcion: str | None = None
    objetivos: str | None = None
    estado_id: int | None = None  # Por defecto BORRADOR
    presupuesto_total: Decimal = Decimal("0")


@strawberry.input
class PlanificacionAnualUpdateInput:
    nombre: str | None = None
    descripcion: str | None = None
    objetivos: str | None = None
    estado_id: int | None = None
    fecha_aprobacion: date | None = None
    presupuesto_total: Decimal | None = None


@strawberry.input
class PartidaPresupuestariaInput:
    codigo: str
    nombre: str
    descripcion: str | None = None
    ejercicio: int
    tipo: str  # INGRESO, GASTO
    categoria_id: int | None = None
    importe_presupuestado: Decimal = Decimal("0")
    planificacion_id: UUID | None = None


@strawberry.input
class PartidaPresupuestariaUpdateInput:
    nombre: str | None = None
    descripcion: str | None = None
    tipo: str | None = None
    categoria_id: int | None = None
    importe_presupuestado: Decimal | None = None
    importe_comprometido: Decimal | None = None
    importe_ejecutado: Decimal | None = None
    activo: bool | None = None


@strawberry.input
class CategoriaPartidaInput:
    codigo: str
    nombre: str
    descripcion: str | None = None
