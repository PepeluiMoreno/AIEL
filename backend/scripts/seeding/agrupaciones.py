"""Seeding de agrupaciones territoriales de Europa Laica."""

import asyncio
from sqlalchemy import select

from app.core.database import async_session
from app.models import AgrupacionTerritorial


# Agrupaciones territoriales de Europa Laica (según laicismo.org)
# La primera es la nacional (es_nacional=True), para miembros con membresía directa
AGRUPACIONES = [
    {"codigo": "EL", "nombre": "Europa Laica", "es_nacional": True},
    {"codigo": "AND", "nombre": "Andalucía Laica", "es_nacional": False},
    {"codigo": "ARA", "nombre": "Aragón Laico", "es_nacional": False},
    {"codigo": "AST", "nombre": "Asturias Laica", "es_nacional": False},
    {"codigo": "CAN", "nombre": "Canarias Laica", "es_nacional": False},
    {"codigo": "CAT", "nombre": "Catalunya Laica", "es_nacional": False},
    {"codigo": "EUS", "nombre": "Euskadi Laica", "es_nacional": False},
    {"codigo": "EXT", "nombre": "Extremadura Laica", "es_nacional": False},
    {"codigo": "GAL", "nombre": "Galicia Laica", "es_nacional": False},
    {"codigo": "MAD", "nombre": "Madrid Laica", "es_nacional": False},
    {"codigo": "MUR", "nombre": "Murcia Laica", "es_nacional": False},
    {"codigo": "VAL", "nombre": "País Valenciano Laico", "es_nacional": False},
]


async def seed_agrupaciones():
    async with async_session() as db:
        for agrup_data in AGRUPACIONES:
            result = await db.execute(
                select(AgrupacionTerritorial).where(
                    AgrupacionTerritorial.codigo == agrup_data["codigo"]
                )
            )
            if not result.scalar_one_or_none():
                agrupacion = AgrupacionTerritorial(**agrup_data, activo=True)
                db.add(agrupacion)
                print(f"  + Agrupación: {agrup_data['nombre']}")
            else:
                print(f"  = Agrupación ya existe: {agrup_data['nombre']}")

        await db.commit()
        print("Agrupaciones territoriales completadas.")


if __name__ == "__main__":
    asyncio.run(seed_agrupaciones())
