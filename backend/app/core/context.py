from dataclasses import dataclass
from functools import cached_property

from fastapi import Request
from sqlalchemy.ext.asyncio import AsyncSession
from strawberry.fastapi import BaseContext

from .auth import decode_token
from .database import async_session


@dataclass
class Context(BaseContext):
    request: Request

    @cached_property
    def user_id(self) -> int | None:
        auth = self.request.headers.get("Authorization", "")
        if not auth.startswith("Bearer "):
            return None
        token = auth[7:]
        payload = decode_token(token)
        if not payload:
            return None
        return int(payload["sub"])

    @cached_property
    def user_roles(self) -> list[str]:
        auth = self.request.headers.get("Authorization", "")
        if not auth.startswith("Bearer "):
            return []
        token = auth[7:]
        payload = decode_token(token)
        if not payload:
            return []
        return payload.get("roles", [])

    async def get_db(self) -> AsyncSession:
        return async_session()


async def get_context(request: Request) -> Context:
    return Context(request=request)
