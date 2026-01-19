"""Cliente GraphQL para seeding usando mutations."""

import httpx
from typing import Any

# URL del servidor GraphQL (ajustar según configuración)
GRAPHQL_URL = "http://localhost:8000/graphql"


async def execute_mutation(query: str, variables: dict | None = None) -> dict[str, Any]:
    """Ejecuta una mutation GraphQL."""
    async with httpx.AsyncClient() as client:
        response = await client.post(
            GRAPHQL_URL,
            json={"query": query, "variables": variables or {}},
            headers={"Content-Type": "application/json"},
            timeout=30.0
        )
        response.raise_for_status()
        result = response.json()

        if "errors" in result:
            errors = result["errors"]
            raise Exception(f"GraphQL Error: {errors}")

        return result.get("data", {})


async def execute_query(query: str, variables: dict | None = None) -> dict[str, Any]:
    """Ejecuta una query GraphQL."""
    return await execute_mutation(query, variables)
