from sqlalchemy import String
from sqlalchemy.orm import Mapped, mapped_column

from ..core.database import Base


class Pais(Base):
    __tablename__ = "pais"

    codpais: Mapped[str] = mapped_column(String(3), primary_key=True)
    nombre: Mapped[str] = mapped_column(String(100))


class Provincia(Base):
    __tablename__ = "provincia"

    codprov: Mapped[str] = mapped_column(String(2), primary_key=True)
    nombre: Mapped[str] = mapped_column(String(100))
