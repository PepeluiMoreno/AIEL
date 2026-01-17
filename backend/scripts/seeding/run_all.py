"""Ejecuta todos los seeders en orden."""

import asyncio
import sys
from pathlib import Path

# Añadir el directorio raíz al path
sys.path.insert(0, str(Path(__file__).parent.parent.parent))

from scripts.seeding.roles import seed_roles
from scripts.seeding.tipos_miembro import seed_tipos_miembro
from scripts.seeding.transacciones import seed_transacciones, seed_permisos


async def main():
    print("=" * 50)
    print("SEEDING AIEL DATABASE")
    print("=" * 50)

    print("\n[1/4] Roles...")
    await seed_roles()

    print("\n[2/4] Tipos de miembro...")
    await seed_tipos_miembro()

    print("\n[3/4] Transacciones...")
    await seed_transacciones()

    print("\n[4/4] Permisos rol-transacción...")
    await seed_permisos()

    print("\n" + "=" * 50)
    print("SEEDING COMPLETADO")
    print("=" * 50)


if __name__ == "__main__":
    asyncio.run(main())
