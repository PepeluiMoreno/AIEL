"""Helper para conexión a MySQL (base de datos legacy)."""
import os
import aiomysql
from typing import AsyncIterator
from contextlib import asynccontextmanager
from dotenv import load_dotenv

# Cargar variables de entorno
load_dotenv()


def get_mysql_config() -> dict:
    """Obtiene la configuración de MySQL desde variables de entorno."""
    return {
        'host': os.getenv('MYSQL_HOST', 'localhost'),
        'port': int(os.getenv('MYSQL_PORT', '3306')),
        'user': os.getenv('MYSQL_USER', 'root'),
        'password': os.getenv('MYSQL_PASSWORD', ''),
        'db': os.getenv('MYSQL_DATABASE', 'europalaica_db'),
        'charset': 'utf8mb4',
        'autocommit': True
    }


@asynccontextmanager
async def get_mysql_connection():
    """
    Context manager para obtener una conexión a MySQL.

    Uso:
        async with get_mysql_connection() as conn:
            async with conn.cursor() as cursor:
                await cursor.execute("SELECT * FROM MIEMBRO")
                rows = await cursor.fetchall()
    """
    config = get_mysql_config()
    conn = await aiomysql.connect(**config)
    try:
        yield conn
    finally:
        conn.close()
