import uuid
from sqlalchemy import String, Boolean, ForeignKey, Uuid, Text
from sqlalchemy.orm import Mapped, mapped_column, relationship

from ..core.database import Base
from .mixins import SoftDeleteMixin, AuditoriaMixin, ContactoMixin


class AgrupacionTerritorial(Base, SoftDeleteMixin, AuditoriaMixin, ContactoMixin):
    __tablename__ = "agrupacion_territorial"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    codigo: Mapped[str] = mapped_column(String(10), unique=True)
    nombre: Mapped[str] = mapped_column(String(200))

    # Indica si es la agrupación nacional (Europa Laica)
    es_nacional: Mapped[bool] = mapped_column(Boolean, default=False)

    # Portavoz de la agrupación (referencia a miembro)
    portavoz_id: Mapped[uuid.UUID | None] = mapped_column(Uuid, ForeignKey("miembro.id"))

    # Cuentas bancarias (IBAN)
    iban_principal: Mapped[str | None] = mapped_column(String(34))
    iban_secundario: Mapped[str | None] = mapped_column(String(34))

    # Observaciones
    observaciones: Mapped[str | None] = mapped_column(Text)

    activo: Mapped[bool] = mapped_column(Boolean, default=True)

    # Relaciones
    miembros: Mapped[list["Miembro"]] = relationship(
        back_populates="agrupacion",
        foreign_keys="Miembro.agrupacion_id"
    )
    portavoz: Mapped["Miembro | None"] = relationship(
        foreign_keys=[portavoz_id]
    )


from .miembro import Miembro  # noqa: E402,F401
