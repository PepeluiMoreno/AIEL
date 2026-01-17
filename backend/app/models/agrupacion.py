from sqlalchemy import String, Boolean, ForeignKey
from sqlalchemy.orm import Mapped, mapped_column, relationship

from ..core.database import Base


class AgrupacionTerritorial(Base):
    __tablename__ = "agrupacion_territorial"

    id: Mapped[int] = mapped_column(primary_key=True)
    codigo: Mapped[str] = mapped_column(String(10), unique=True)
    nombre: Mapped[str] = mapped_column(String(200))

    # Emails de contacto
    email_coordinador: Mapped[str | None] = mapped_column(String(255))
    email_secretario: Mapped[str | None] = mapped_column(String(255))
    email_tesorero: Mapped[str | None] = mapped_column(String(255))

    # Cuentas bancarias (IBAN)
    iban_principal: Mapped[str | None] = mapped_column(String(34))
    iban_secundario: Mapped[str | None] = mapped_column(String(34))

    activo: Mapped[bool] = mapped_column(Boolean, default=True)

    # Relaciones
    miembros: Mapped[list["Miembro"]] = relationship(back_populates="agrupacion")


from .miembro import Miembro  # noqa: E402,F401
