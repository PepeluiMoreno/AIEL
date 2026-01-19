import uuid
from datetime import datetime
from sqlalchemy import String, Boolean, ForeignKey, DateTime, Uuid
from sqlalchemy.orm import Mapped, mapped_column, relationship

from ..core.database import Base


class Usuario(Base):
    __tablename__ = "usuario"

    id: Mapped[uuid.UUID] = mapped_column(Uuid, primary_key=True, default=uuid.uuid4)
    email: Mapped[str] = mapped_column(String(255), unique=True, index=True)
    password_hash: Mapped[str] = mapped_column(String(255))
    activo: Mapped[bool] = mapped_column(Boolean, default=True)

    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)
    last_login: Mapped[datetime | None] = mapped_column(DateTime)

    # Relaciones
    roles: Mapped[list["UsuarioRol"]] = relationship(back_populates="usuario")
    miembro: Mapped["Miembro | None"] = relationship(back_populates="usuario")


class UsuarioRol(Base):
    """Un usuario puede tener múltiples roles."""
    __tablename__ = "usuario_rol"

    usuario_id: Mapped[uuid.UUID] = mapped_column(Uuid, ForeignKey("usuario.id"), primary_key=True)
    rol_id: Mapped[int] = mapped_column(ForeignKey("rol.id"), primary_key=True)

    # Ámbito del rol (ej: coordinador de agrupación X)
    agrupacion_id: Mapped[uuid.UUID | None] = mapped_column(Uuid, ForeignKey("agrupacion_territorial.id"))

    usuario: Mapped["Usuario"] = relationship(back_populates="roles")
    rol: Mapped["Rol"] = relationship()
    agrupacion: Mapped["AgrupacionTerritorial | None"] = relationship()


# Forward refs para type hints
from .miembro import Miembro  # noqa: E402,F401
from .tipologias import Rol  # noqa: E402,F401
from .agrupacion import AgrupacionTerritorial  # noqa: E402,F401
