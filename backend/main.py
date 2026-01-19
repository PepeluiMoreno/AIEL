from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import strawberry
from strawberry.fastapi import GraphQLRouter

from app.core.context import get_context
from app.resolvers.queries import Query
from app.resolvers.mutations import Mutation

schema = strawberry.Schema(query=Query, mutation=Mutation)
graphql_app = GraphQLRouter(schema, context_getter=get_context)

app = FastAPI(title="AIEL API")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Ajustar en producción
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(graphql_app, prefix="/graphql")


@app.get("/health")
async def health():
    return {"status": "ok"}
