# Microservicio de Anonimización RGPD
# Imagen ligera basada en Python Alpine

FROM python:3.12-slim

# Metadatos
LABEL maintainer="SIGA Team"
LABEL description="Servicio de anonimización de datos personales (RGPD)"
LABEL version="1.0.0"

# Variables de entorno por defecto
ENV PYTHONUNBUFFERED=1 \
    PYTHONDONTWRITEBYTECODE=1 \
    PIP_NO_CACHE_DIR=1 \
    PIP_DISABLE_PIP_VERSION_CHECK=1

# Directorio de trabajo
WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Copiar requirements e instalar dependencias Python
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copiar código de la aplicación
COPY main.py .

# Usuario no-root para seguridad
RUN useradd --create-home --shell /bin/bash appuser
USER appuser

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD python -c "print('OK')" || exit 1

# Comando por defecto (una ejecución)
# Para modo scheduler usar: docker run ... --schedule
ENTRYPOINT ["python", "main.py"]
CMD []
