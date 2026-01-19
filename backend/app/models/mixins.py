"""Mixins reutilizables para los modelos SQLAlchemy."""

import uuid
from datetime import datetime
from sqlalchemy import String, DateTime, Uuid, ForeignKey
from sqlalchemy.orm import Mapped, mapped_column, declared_attr, relationship


class SoftDeleteMixin:
    """Mixin para soft-delete. Añade campo deleted_at para marcar registros eliminados."""

    deleted_at: Mapped[datetime | None] = mapped_column(DateTime, default=None)

    @property
    def is_deleted(self) -> bool:
        return self.deleted_at is not None


class AuditoriaMixin:
    """Mixin de auditoría. Registra quién creó y modificó el registro."""

    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    updated_at: Mapped[datetime | None] = mapped_column(DateTime, onupdate=datetime.utcnow)

    @declared_attr
    def creado_por_id(cls) -> Mapped[uuid.UUID | None]:
        return mapped_column(Uuid, ForeignKey("usuario.id"), nullable=True)

    @declared_attr
    def modificado_por_id(cls) -> Mapped[uuid.UUID | None]:
        return mapped_column(Uuid, ForeignKey("usuario.id"), nullable=True)


class ContactoMixin:
    """Mixin para datos de contacto. Incluye dirección, teléfono, email, etc."""

    direccion: Mapped[str | None] = mapped_column(String(300))
    codigo_postal: Mapped[str | None] = mapped_column(String(10))
    localidad: Mapped[str | None] = mapped_column(String(100))
    provincia: Mapped[str | None] = mapped_column(String(100))
    telefono: Mapped[str | None] = mapped_column(String(20))
    telefono2: Mapped[str | None] = mapped_column(String(20))
    email: Mapped[str | None] = mapped_column(String(255))
    web: Mapped[str | None] = mapped_column(String(255))
