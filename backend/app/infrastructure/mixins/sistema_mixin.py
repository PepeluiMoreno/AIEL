"""
Mixin para registros del sistema (catálogos, tipologías, estados).

Estos registros son críticos para el funcionamiento del sistema
y no deben poder ser eliminados por usuarios.
"""

from sqlalchemy import Boolean, String
from sqlalchemy.orm import Mapped, mapped_column


class RegistroSistemaMixin:
    """
    Mixin para marcar registros como "del sistema" (no eliminables).

    Propósito:
    - Proteger catálogos y tipologías críticas
    - Prevenir eliminación accidental de estados
    - Marcar registros que vienen pre-cargados con el sistema

    Campos:
    - es_sistema: Boolean que marca si el registro es del sistema

    Los registros del sistema:
    - NO pueden ser eliminados (ni soft delete)
    - NO pueden ser desactivados
    - Pueden ser actualizados pero con restricciones
    """

    es_sistema: Mapped[bool] = mapped_column(
        Boolean,
        nullable=False,
        default=False,
        index=True,
        comment="Indica si es un registro del sistema (no eliminable)"
    )

    def puede_eliminarse(self) -> bool:
        """
        Verifica si el registro puede ser eliminado.

        Returns:
            False si es registro del sistema, True en caso contrario
        """
        return not self.es_sistema

    def puede_desactivarse(self) -> bool:
        """
        Verifica si el registro puede ser desactivado.

        Returns:
            False si es registro del sistema, True en caso contrario
        """
        return not self.es_sistema

    def validar_eliminacion(self) -> None:
        """
        Valida que el registro pueda ser eliminado.

        Raises:
            ValueError: Si el registro es del sistema
        """
        if self.es_sistema:
            raise ValueError(
                "No se puede eliminar este registro porque es un registro del sistema."
            )

    def validar_desactivacion(self) -> None:
        """
        Valida que el registro pueda ser desactivado.

        Raises:
            ValueError: Si el registro es del sistema
        """
        if self.es_sistema:
            raise ValueError(
                "No se puede desactivar este registro porque es un registro del sistema."
            )


class CatalogoMixin(RegistroSistemaMixin):
    """
    Mixin especializado para catálogos (extends RegistroSistemaMixin).

    Añade campos adicionales comunes en catálogos:
    - nombre: Nombre descriptivo
    - descripcion: Descripción opcional
    - orden: Para ordenar registros
    - activo: Estado activo/inactivo
    """

    nombre: Mapped[str] = mapped_column(
        String(100),
        nullable=False,
        comment="Nombre descriptivo del registro"
    )

    descripcion: Mapped[str] = mapped_column(
        String(500),
        nullable=True,
        comment="Descripción opcional del registro"
    )

    orden: Mapped[int] = mapped_column(
        "orden",
        nullable=False,
        default=0,
        comment="Orden de presentación"
    )

    activo: Mapped[bool] = mapped_column(
        Boolean,
        nullable=False,
        default=True,
        index=True,
        comment="Indica si el registro está activo"
    )

    def __repr__(self) -> str:
        return f"<{self.__class__.__name__}(nombre='{self.nombre}')>"
