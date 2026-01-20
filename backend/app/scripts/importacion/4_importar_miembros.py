"""
Script para importar miembros desde el dump SQL.

Importa las tablas:
- MIEMBRO → miembros
- MIEMBROELIMINADO5ANIOS → miembros (con fecha_baja)
- SOCIOSFALLECIDOS → miembros (con motivo_baja='Fallecido')

Este script implementa toda la lógica de mapeo confirmada en DECISIONES_MAPEO_CONFIRMADAS.md

IMPORTANTE:
- Encripta DNI/NIE e IBANs antes de almacenarlos
- Prioridad de teléfonos: móvil > fijo_casa > fijo_trabajo
- Profesión/estudios solo si es_voluntario=True
- Agrupación inferida de última cuota
- Provincia inferida por código postal

Este script debe ejecutarse DESPUÉS de importar agrupaciones.
"""
import asyncio
import uuid
import re
from typing import Optional, Dict, Tuple
from datetime import date, datetime
from sqlalchemy.ext.asyncio import AsyncSession, create_async_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy import select, text

from app.core.database import get_database_url
from app.infrastructure.services.encriptacion_service import get_encriptacion_service
from app.domains.miembros.models.miembro import Miembro, TipoMiembro
from app.domains.miembros.models.estado_miembro import EstadoMiembro
from app.domains.geografico.models.direccion import Provincia
from app.scripts.importacion.sql_dump_parser import SQLDumpParser


# Ruta al archivo dump SQL
DUMP_FILE_PATH = r"C:\Users\Jose\dev\SIGA\data\europalaica_com_2026_01_01 apertura de año.sql"


class MapeadorMiembros:
    """Mapea miembros de MySQL a PostgreSQL."""

    def __init__(self, parser: SQLDumpParser):
        self.parser = parser
        self.mapeo_miembros: dict[int, uuid.UUID] = {}  # CODUSER → UUID
        self.servicio_encriptacion = get_encriptacion_service()
        self.tipo_miembro_socio_id: Optional[uuid.UUID] = None
        self.tipo_miembro_simpatizante_id: Optional[uuid.UUID] = None
        self.tipo_miembro_voluntario_id: Optional[uuid.UUID] = None
        self.estado_activo_id: Optional[uuid.UUID] = None
        self.estado_baja_id: Optional[uuid.UUID] = None
        self.cache_provincias_por_cp: dict[str, uuid.UUID] = {}
        self.cuotas_por_miembro: Dict[int, list] = {}  # Cache de cuotas por CODUSER

    @staticmethod
    def normalizar_nombre(nombre: Optional[str]) -> Optional[str]:
        """
        Normaliza nombres y apellidos: primera letra mayúscula, resto minúsculas.
        Ejemplos: 'JOSE LUIS' -> 'Jose Luis', 'GARCÍA' -> 'García'
        """
        if not nombre:
            return None

        nombre_str = str(nombre).strip()
        if not nombre_str or nombre_str.upper() in ('NULL', 'NONE'):
            return None

        # Convertir a minúsculas y capitalizar cada palabra
        return nombre_str.title()

    async def cargar_tipos_miembro(self, session: AsyncSession):
        """Carga los UUIDs de tipos de miembro."""
        print("  Ejecutando query para SOCIO...")
        result = await session.execute(
            select(TipoMiembro).where(TipoMiembro.codigo == 'SOCIO')
        )
        print("  Query SOCIO completada")
        tipo_socio = result.scalar_one_or_none()
        if tipo_socio:
            self.tipo_miembro_socio_id = tipo_socio.id

        result = await session.execute(
            select(TipoMiembro).where(TipoMiembro.codigo == 'SIMPATIZANTE')
        )
        tipo_simpatizante = result.scalar_one_or_none()
        if tipo_simpatizante:
            self.tipo_miembro_simpatizante_id = tipo_simpatizante.id

        result = await session.execute(
            select(TipoMiembro).where(TipoMiembro.codigo == 'VOLUNTARIO')
        )
        tipo_voluntario = result.scalar_one_or_none()
        if tipo_voluntario:
            self.tipo_miembro_voluntario_id = tipo_voluntario.id

        print(f"  Tipos de miembro cargados:")
        print(f"    SOCIO: {self.tipo_miembro_socio_id}")
        print(f"    SIMPATIZANTE: {self.tipo_miembro_simpatizante_id}")
        print(f"    VOLUNTARIO: {self.tipo_miembro_voluntario_id}")

    async def cargar_estados_miembro(self, session: AsyncSession):
        """Carga los UUIDs de estados de miembro."""

        result = await session.execute(
            select(EstadoMiembro).where(EstadoMiembro.codigo == 'ACTIVO')
        )
        estado_activo = result.scalar_one_or_none()
        if estado_activo:
            self.estado_activo_id = estado_activo.id

        result = await session.execute(
            select(EstadoMiembro).where(EstadoMiembro.codigo == 'BAJA')
        )
        estado_baja = result.scalar_one_or_none()
        if estado_baja:
            self.estado_baja_id = estado_baja.id

        print(f"  Estados de miembro cargados:")
        print(f"    ACTIVO: {self.estado_activo_id}")
        print(f"    BAJA: {self.estado_baja_id}")

    def cargar_cuotas_cache(self):
        """Carga todas las cuotas en memoria para búsqueda rápida de agrupación."""

        print("\nCargando cuotas en cache...")
        count = 0

        try:
            for row in self.parser.extraer_inserts('CUOTAANIOSOCIO'):
                if len(row) < 3:
                    continue

                # Estructura: ANIOCUOTA, CODSOCIO, CODAGRUPACION, ...
                aniocuota = row[0] if len(row) > 0 else None
                codsocio = row[1] if len(row) > 1 else None
                codagrupacion = row[2] if len(row) > 2 else None

                if codsocio:
                    if codsocio not in self.cuotas_por_miembro:
                        self.cuotas_por_miembro[codsocio] = []

                    self.cuotas_por_miembro[codsocio].append({
                        'anio': aniocuota,
                        'codagrupacion': codagrupacion
                    })
                    count += 1

        except Exception as e:
            print(f"  Advertencia: Error cargando cuotas: {e}")

        print(f"  {count} cuotas cargadas en cache")

    def mapear_tipo_miembro(self, tipo_mysql: Optional[str]) -> uuid.UUID:
        """Mapea TIPOMIEMBRO de MySQL a tipo_miembro_id."""

        if not tipo_mysql:
            return self.tipo_miembro_socio_id

        tipo_lower = str(tipo_mysql).lower().strip()

        if 'simpatizante' in tipo_lower:
            return self.tipo_miembro_simpatizante_id
        elif 'voluntario' in tipo_lower:
            return self.tipo_miembro_voluntario_id
        else:
            return self.tipo_miembro_socio_id

    def procesar_telefonos(self, movil: Optional[str], fijo_casa: Optional[str], fijo_trabajo: Optional[str]) -> tuple[Optional[str], Optional[str]]:
        """
        Prioriza teléfonos según decisión confirmada:
        móvil > fijo_casa > fijo_trabajo

        Retorna: (telefono, telefono2)
        """

        telefonos = []

        if movil and str(movil).strip():
            telefonos.append(str(movil).strip())

        if fijo_casa and str(fijo_casa).strip():
            telefonos.append(str(fijo_casa).strip())

        if fijo_trabajo and str(fijo_trabajo).strip():
            telefonos.append(str(fijo_trabajo).strip())

        telefono = telefonos[0] if len(telefonos) > 0 else None
        telefono2 = telefonos[1] if len(telefonos) > 1 else None

        return telefono, telefono2

    async def obtener_uuid_pais(self, session: AsyncSession, codigo_pais) -> Optional[uuid.UUID]:
        """Obtiene el UUID de un país desde temp_id_mapping o por código ISO."""

        if not codigo_pais:
            return None

        # Si es un string (código ISO), buscar directamente en paises
        if isinstance(codigo_pais, str):
            from app.domains.geografico.models.direccion import Pais
            result = await session.execute(
                select(Pais).where(Pais.codigo == str(codigo_pais).strip().upper())
            )
            pais = result.scalar_one_or_none()
            return pais.id if pais else None

        # Si es un entero, buscar en temp_id_mapping
        result = await session.execute(
            text("""
                SELECT new_uuid FROM temp_id_mapping
                WHERE tabla = 'PAIS' AND old_id = :old_id
            """),
            {"old_id": codigo_pais}
        )
        row = result.fetchone()
        return row[0] if row else None

    async def inferir_provincia_por_cp(self, session: AsyncSession, codigo_postal: Optional[str]) -> Optional[uuid.UUID]:
        """
        Infiere la provincia por código postal (Decisión 4).

        Lógica:
        - Los 2 primeros dígitos del CP español corresponden a la provincia
        - Busca en caché primero
        - Si no existe, busca en BD por código
        """

        if not codigo_postal or len(str(codigo_postal)) < 2:
            return None

        # Extraer los 2 primeros dígitos
        codigo_provincia = str(codigo_postal)[:2]

        # Verificar caché
        if codigo_provincia in self.cache_provincias_por_cp:
            return self.cache_provincias_por_cp[codigo_provincia]

        # Buscar en BD
        result = await session.execute(
            select(Provincia).where(Provincia.codigo == codigo_provincia)
        )
        provincia = result.scalar_one_or_none()

        if provincia:
            self.cache_provincias_por_cp[codigo_provincia] = provincia.id
            return provincia.id

        return None

    def obtener_agrupacion_por_ultima_cuota(self, coduser: int) -> Optional[str]:
        """
        Infiere la agrupación del miembro por su última cuota (Decisión 3).

        Retorna: CODAGRUPACION de la última CUOTAANIOSOCIO
        """

        if coduser not in self.cuotas_por_miembro:
            return None

        cuotas = self.cuotas_por_miembro[coduser]
        if not cuotas:
            return None

        # Ordenar por año descendente y tomar la primera
        cuotas_ordenadas = sorted(cuotas, key=lambda x: x['anio'] if x['anio'] else 0, reverse=True)

        if cuotas_ordenadas and cuotas_ordenadas[0]['codagrupacion']:
            codagrupacion = str(cuotas_ordenadas[0]['codagrupacion']).strip()
            return codagrupacion if codagrupacion else None

        return None

    async def obtener_uuid_agrupacion(self, session: AsyncSession, codigo_agrupacion: Optional[str]) -> Optional[uuid.UUID]:
        """Obtiene el UUID de una agrupación por su código."""

        if not codigo_agrupacion:
            return None

        result = await session.execute(
            text("""
                SELECT id FROM agrupaciones_territoriales
                WHERE codigo = :codigo
            """),
            {"codigo": codigo_agrupacion}
        )
        row = result.fetchone()
        return row[0] if row else None

    def determinar_es_voluntario(self, colabora: Optional[str]) -> bool:
        """
        Determina si el miembro es voluntario basado en el campo COLABORA.

        Si COLABORA tiene contenido significativo, se marca como voluntario.
        """

        if not colabora:
            return False

        colabora_limpio = str(colabora).strip().lower()

        # Palabras clave que indican que NO es voluntario
        palabras_negativas = ['no', 'n/a', 'ninguna', 'ninguno', 'nada']

        if colabora_limpio in palabras_negativas:
            return False

        # Si tiene contenido, consideramos que es voluntario
        return len(colabora_limpio) > 0

    def parse_fecha(self, fecha_val) -> Optional[date]:
        """Parsea una fecha de MySQL a date de Python."""

        if not fecha_val:
            return None

        if isinstance(fecha_val, date):
            return fecha_val

        if isinstance(fecha_val, datetime):
            return fecha_val.date()

        if isinstance(fecha_val, str):
            fecha_str = fecha_val.strip()

            # Fechas inválidas de MySQL
            if fecha_str in ('0000-00-00', '0000-00-00 00:00:00'):
                return None

            try:
                # Intentar ISO format
                return date.fromisoformat(fecha_str.split()[0])
            except:
                pass

        return None

    def procesar_motivo_baja_y_observaciones(self, motivo_baja: Optional[str]) -> Tuple[Optional[str], Optional[str]]:
        """
        Procesa el motivo de baja y observaciones.
        Si motivo_baja excede 500 caracteres, se guarda completo en observaciones
        y se trunca en motivo_baja.

        Returns:
            (motivo_baja_truncado, observaciones)
        """
        if not motivo_baja:
            return None, None

        motivo_text = str(motivo_baja).strip()

        if len(motivo_text) <= 500:
            return motivo_text, None

        # Si excede 500 caracteres, truncar motivo_baja y guardar completo en observaciones
        motivo_truncado = motivo_text[:497] + "..."
        observaciones = motivo_text

        return motivo_truncado, observaciones

    async def importar_miembros(self, session: AsyncSession):
        """Importa la tabla MIEMBRO."""

        print("\nImportando miembros...")

        # Estructura de MIEMBRO según el dump:
        # CODUSER, CODPAISDOC, TIPOMIEMBRO, NUMDOCUMENTOMIEMBRO, TIPODOCUMENTOMIEMBRO,
        # APE1, APE2, NOM, SEXO, FECHANAC, TELFIJOCASA, TELFIJOTRABAJO, TELMOVIL,
        # PROFESION, ESTUDIOS, EMAIL, EMAILERROR, INFORMACIONEMAIL, INFORMACIONCARTAS,
        # COLABORA, CODPAISDOM, DIRECCION, CP, LOCALIDAD, CODPROV, NOMPROVINCIA,
        # ARCHIVOFIRMAPD, PATH_ARCHIVO_FIRMAS, COMENTARIOSOCIO, OBSERVACIONES,
        # CUENTAMIEMBROIBAN

        importados = 0
        omitidos = 0

        for row in self.parser.extraer_inserts('MIEMBRO'):
            if len(row) < 8:  # Mínimo de campos indispensables
                omitidos += 1
                continue

            coduser = row[0]
            if not coduser:
                omitidos += 1
                continue

            # Extraer campos con acceso seguro
            codpaisdoc = row[1] if len(row) > 1 else None
            tipomiembro = row[2] if len(row) > 2 else None
            numdocumentomiembro = row[3] if len(row) > 3 else None
            tipodocumentomiembro = row[4] if len(row) > 4 else None
            ape1 = row[5] if len(row) > 5 else None
            ape2 = row[6] if len(row) > 6 else None
            nom = row[7] if len(row) > 7 else None
            sexo = row[8] if len(row) > 8 else None  # Ignorado
            fechanac = row[9] if len(row) > 9 else None
            telfijocasa = row[10] if len(row) > 10 else None
            telfijotrabajo = row[11] if len(row) > 11 else None
            telmovil = row[12] if len(row) > 12 else None
            profesion = row[13] if len(row) > 13 else None
            estudios = row[14] if len(row) > 14 else None
            email = row[15] if len(row) > 15 else None
            emailerror = row[16] if len(row) > 16 else None
            informacionemail = row[17] if len(row) > 17 else None
            informacioncartas = row[18] if len(row) > 18 else None
            colabora = row[19] if len(row) > 19 else None
            codpaisdom = row[20] if len(row) > 20 else None
            direccion = row[21] if len(row) > 21 else None
            cp = row[22] if len(row) > 22 else None
            localidad = row[23] if len(row) > 23 else None
            codprov = row[24] if len(row) > 24 else None
            nomprovincia = row[25] if len(row) > 25 else None  # Ignorado
            archivofirmapd = row[26] if len(row) > 26 else None  # Ignorado
            path_archivo_firmas = row[27] if len(row) > 27 else None  # Ignorado
            comentariosocio = row[28] if len(row) > 28 else None
            observaciones = row[29] if len(row) > 29 else None
            cuentamiembroiban = row[30] if len(row) > 30 else None

            # Mapear tipo de miembro
            tipo_miembro_id = self.mapear_tipo_miembro(tipomiembro)

            # Procesar teléfonos (Decisión 1)
            telefono, telefono2 = self.procesar_telefonos(
                telmovil,
                telfijocasa,
                telfijotrabajo
            )

            # Obtener país de documento
            pais_documento_id = await self.obtener_uuid_pais(session, codpaisdoc)

            # Obtener país de domicilio
            pais_domicilio_id = await self.obtener_uuid_pais(session, codpaisdom)

            # Inferir provincia por CP (Decisión 4)
            provincia_id = await self.inferir_provincia_por_cp(session, cp)

            # Si no se pudo inferir, intentar por CODPROV directo
            if not provincia_id and codprov:
                result = await session.execute(
                    text("""
                        SELECT new_uuid FROM temp_id_mapping
                        WHERE tabla = 'PROVINCIA' AND old_id = :old_id
                    """),
                    {"old_id": codprov}
                )
                provincia_row = result.fetchone()
                if provincia_row:
                    provincia_id = provincia_row[0]

            # Obtener agrupación por última cuota (Decisión 3)
            codigo_agrupacion = self.obtener_agrupacion_por_ultima_cuota(coduser)
            agrupacion_id = await self.obtener_uuid_agrupacion(session, codigo_agrupacion)

            # Determinar si es voluntario
            es_voluntario = self.determinar_es_voluntario(colabora)

            # Guardar DNI/NIE sin encriptar (se encriptará después)
            numero_documento_limpio = None
            if numdocumentomiembro:
                doc_limpio = str(numdocumentomiembro).strip().replace(' ', '').replace('-', '')
                if doc_limpio and doc_limpio.upper() != 'NULL':
                    numero_documento_limpio = doc_limpio

            # Guardar IBAN sin encriptar (se encriptará después)
            iban_limpio = None
            if cuentamiembroiban:
                iban_str = str(cuentamiembroiban).strip().replace(' ', '')
                if iban_str and iban_str.upper() != 'NULL':
                    iban_limpio = iban_str

            # Procesar fechas
            fecha_nacimiento = self.parse_fecha(fechanac)

            # Fecha de baja: detectar por EMAILERROR = 'BAJA'
            fecha_baja = None
            motivo_baja_raw = None
            if emailerror and str(emailerror).upper().strip() == 'BAJA':
                fecha_baja = date.today()  # Aproximación
                motivo_baja_raw = "Baja registrada"

            # Procesar motivo_baja y observaciones
            motivo_baja, observaciones_baja = self.procesar_motivo_baja_y_observaciones(motivo_baja_raw)

            # Determinar estado: BAJA si tiene fecha_baja, ACTIVO en caso contrario
            estado_id = self.estado_baja_id if fecha_baja else self.estado_activo_id

            # Concatenar observaciones
            obs_completas = []
            if observaciones_baja:
                obs_completas.append(observaciones_baja)
            if comentariosocio:
                obs_completas.append(str(comentariosocio).strip())
            if observaciones:
                obs_completas.append(str(observaciones).strip())
            observaciones_texto = ' | '.join(obs_completas) if obs_completas else None

            # Crear miembro
            miembro = Miembro(
                nombre=self.normalizar_nombre(nom) or "Sin nombre",
                apellido1=self.normalizar_nombre(ape1) or "Sin apellido",
                apellido2=self.normalizar_nombre(ape2),
                fecha_nacimiento=fecha_nacimiento,
                tipo_miembro_id=tipo_miembro_id,
                estado_id=estado_id,
                tipo_documento=str(tipodocumentomiembro).strip() if tipodocumentomiembro else None,
                numero_documento=numero_documento_limpio,
                pais_documento_id=pais_documento_id,
                direccion=str(direccion).strip() if direccion else None,
                codigo_postal=str(cp).strip() if cp else None,
                localidad=str(localidad).strip() if localidad else None,
                provincia_id=provincia_id,
                pais_domicilio_id=pais_domicilio_id,
                telefono=telefono,
                telefono2=telefono2,
                email=str(email).strip() if email else None,
                agrupacion_id=agrupacion_id,
                iban=iban_limpio,
                fecha_baja=fecha_baja,
                motivo_baja=motivo_baja,
                observaciones=observaciones_texto,
                activo=(fecha_baja is None),
                es_voluntario=es_voluntario,
                profesion=str(profesion).strip() if (es_voluntario and profesion) else None,
                nivel_estudios=str(estudios).strip() if (es_voluntario and estudios) else None,
                observaciones_voluntariado=str(colabora).strip() if colabora else None,
                intereses=str(colabora).strip() if colabora else None
            )

            session.add(miembro)
            self.mapeo_miembros[coduser] = miembro.id
            importados += 1

            if importados % 100 == 0:
                await session.flush()  # Flush cada 100 registros en vez de cada uno
                print(f"  Procesados {importados} miembros...", flush=True)

        # Flush final
        await session.flush()
        print(f"  [OK] {importados} miembros importados", flush=True)
        if omitidos > 0:
            print(f"  [WARN] {omitidos} miembros omitidos (sin CODUSER o campos insuficientes)")

    async def importar_miembros_eliminados(self, session: AsyncSession):
        """Importa la tabla MIEMBROELIMINADO5ANIOS (miembros con baja)."""

        print("\nImportando miembros eliminados...")

        importados = 0

        try:
            for row in self.parser.extraer_inserts('MIEMBROELIMINADO5ANIOS'):
                if len(row) < 8:
                    continue

                coduser = row[0]
                if not coduser or coduser in self.mapeo_miembros:
                    continue

                # Estructura probable: CODUSER, CODPAISDOC, TIPOMIEMBRO, ..., FECHABAJA, MOTIVOBAJA
                tipomiembro = row[2] if len(row) > 2 else None
                ape1 = row[5] if len(row) > 5 else None
                ape2 = row[6] if len(row) > 6 else None
                nom = row[7] if len(row) > 7 else None
                email = row[15] if len(row) > 15 else None
                fechabaja = row[-2] if len(row) > 1 else None  # Penúltimo campo
                motivobaja = row[-1] if len(row) > 0 else None  # Último campo

                # Mapear tipo de miembro
                tipo_miembro_id = self.mapear_tipo_miembro(tipomiembro)

                # Procesar fecha de baja
                fecha_baja = self.parse_fecha(fechabaja) or date.today()

                # Procesar motivo y observaciones
                motivo_text = str(motivobaja).strip() if motivobaja else "Baja registrada"
                motivo_baja, observaciones = self.procesar_motivo_baja_y_observaciones(motivo_text)

                # Crear miembro dado de baja (estado BAJA)
                miembro = Miembro(
                    nombre=str(nom).strip() if nom else "Sin nombre",
                    apellido1=str(ape1).strip() if ape1 else "Sin apellido",
                    apellido2=str(ape2).strip() if ape2 else None,
                    tipo_miembro_id=tipo_miembro_id,
                    estado_id=self.estado_baja_id,
                    email=str(email).strip() if email else None,
                    fecha_baja=fecha_baja,
                    motivo_baja=motivo_baja,
                    observaciones=observaciones,
                    activo=False,
                    es_voluntario=False
                )

                session.add(miembro)
                await session.flush()

                self.mapeo_miembros[coduser] = miembro.id
                importados += 1

        except Exception as e:
            print(f"  [WARN] Tabla MIEMBROELIMINADO5ANIOS no encontrada o error: {e}")

        print(f"  [OK] {importados} miembros eliminados importados")

    async def importar_miembros_fallecidos(self, session: AsyncSession):
        """Importa la tabla SOCIOSFALLECIDOS."""

        print("\nImportando miembros fallecidos...")

        importados = 0
        actualizados = 0

        try:
            for row in self.parser.extraer_inserts('SOCIOSFALLECIDOS'):
                if len(row) < 6:
                    continue

                coduser = row[0]
                if not coduser:
                    continue

                # Si ya existe, actualizar motivo de baja
                if coduser in self.mapeo_miembros:
                    miembro_uuid = self.mapeo_miembros[coduser]
                    result = await session.execute(
                        select(Miembro).where(Miembro.id == miembro_uuid)
                    )
                    miembro = result.scalar_one_or_none()

                    if miembro:
                        miembro.motivo_baja = "Fallecido"
                        miembro.estado_id = self.estado_baja_id
                        if not miembro.fecha_baja and len(row) > 4:
                            miembro.fecha_baja = self.parse_fecha(row[4])
                        miembro.activo = False
                        actualizados += 1
                    continue

                # Estructura: CODUSER, APE1, APE2, NOM, FECHABAJA, EMAIL
                ape1 = row[1] if len(row) > 1 else None
                ape2 = row[2] if len(row) > 2 else None
                nom = row[3] if len(row) > 3 else None
                fechabaja = row[4] if len(row) > 4 else None
                email = row[5] if len(row) > 5 else None

                fecha_baja = self.parse_fecha(fechabaja) or date.today()

                miembro = Miembro(
                    nombre=str(nom).strip() if nom else "Sin nombre",
                    apellido1=str(ape1).strip() if ape1 else "Sin apellido",
                    apellido2=str(ape2).strip() if ape2 else None,
                    tipo_miembro_id=self.tipo_miembro_socio_id,
                    estado_id=self.estado_baja_id,
                    email=str(email).strip() if email else None,
                    fecha_baja=fecha_baja,
                    motivo_baja="Fallecido",
                    activo=False,
                    es_voluntario=False
                )

                session.add(miembro)
                await session.flush()

                self.mapeo_miembros[coduser] = miembro.id
                importados += 1

        except Exception as e:
            print(f"  [WARN] Tabla SOCIOSFALLECIDOS no encontrada o error: {e}")

        print(f"  [OK] {importados} miembros fallecidos importados")
        print(f"  [OK] {actualizados} miembros existentes actualizados como fallecidos")

    async def guardar_mapeo_temporal(self, session: AsyncSession):
        """Guarda el mapeo de CODUSER en temp_id_mapping."""

        print("\nGuardando mapeo de miembros...")

        for coduser, uuid_val in self.mapeo_miembros.items():
            await session.execute(
                text("""
                    INSERT INTO temp_id_mapping (tabla, old_id, new_uuid)
                    VALUES ('MIEMBRO', :old_id, :new_uuid)
                    ON CONFLICT (tabla, old_id) DO UPDATE SET new_uuid = EXCLUDED.new_uuid
                """),
                {"old_id": coduser, "new_uuid": uuid_val}
            )

        print(f"  [OK] {len(self.mapeo_miembros)} mapeos guardados", flush=True)

    async def encriptar_dnis_en_lote(self, session: AsyncSession):
        """Encripta todos los DNIs/NIEs en lote."""
        print("\nEncriptando DNIs en lote...", flush=True)

        result = await session.execute(
            select(Miembro).where(
                Miembro.numero_documento.isnot(None),
                Miembro.numero_documento != ''
            )
        )
        miembros = result.scalars().all()

        encriptados = 0
        errores = 0

        for miembro in miembros:
            try:
                miembro.numero_documento = self.servicio_encriptacion.encriptar_dni(miembro.numero_documento)
                encriptados += 1

                if encriptados % 100 == 0:
                    await session.flush()
                    print(f"  Encriptados {encriptados} DNIs...", flush=True)
            except ValueError as e:
                # DNI inválido, dejar sin encriptar
                errores += 1
                if errores < 10:  # Solo mostrar primeros 10 errores
                    print(f"  [WARN] DNI inválido (ID={miembro.id}): {e}", flush=True)

        await session.flush()
        print(f"  [OK] {encriptados} DNIs encriptados", flush=True)
        if errores > 0:
            print(f"  [WARN] {errores} DNIs con errores (dejados sin encriptar)", flush=True)

    async def encriptar_ibans_en_lote(self, session: AsyncSession):
        """Encripta todos los IBANs en lote."""
        print("\nEncriptando IBANs en lote...", flush=True)

        result = await session.execute(
            select(Miembro).where(
                Miembro.iban.isnot(None),
                Miembro.iban != ''
            )
        )
        miembros = result.scalars().all()

        encriptados = 0
        errores = 0

        for miembro in miembros:
            try:
                miembro.iban = self.servicio_encriptacion.encriptar_iban(miembro.iban)
                encriptados += 1

                if encriptados % 100 == 0:
                    await session.flush()
                    print(f"  Encriptados {encriptados} IBANs...", flush=True)
            except (ValueError, Exception) as e:
                # IBAN inválido, dejar sin encriptar
                errores += 1
                if errores < 10:  # Solo mostrar primeros 10 errores
                    print(f"  [WARN] IBAN inválido (ID={miembro.id}): {e}", flush=True)

        await session.flush()
        print(f"  [OK] {encriptados} IBANs encriptados", flush=True)
        if errores > 0:
            print(f"  [WARN] {errores} IBANs con errores (dejados sin encriptar)", flush=True)


async def main():
    """Función principal."""
    import sys

    print("\n" + "="*80, flush=True)
    print("IMPORTACION DE MIEMBROS", flush=True)
    print("="*80 + "\n", flush=True)
    sys.stdout.flush()

    # Cargar parser SQL
    print("Cargando parser SQL...", flush=True)
    sys.stdout.flush()
    try:
        parser = SQLDumpParser(DUMP_FILE_PATH)
        print(f"  [OK] Parser cargado: {DUMP_FILE_PATH}", flush=True)
        sys.stdout.flush()
    except Exception as e:
        print(f"  [ERROR] No se pudo cargar el dump: {e}", flush=True)
        sys.stdout.flush()
        return

    # Conectar a PostgreSQL
    print("\nConectando a PostgreSQL...", flush=True)
    sys.stdout.flush()
    database_url = get_database_url()
    engine = create_async_engine(database_url, echo=False)
    async_session = sessionmaker(engine, class_=AsyncSession, expire_on_commit=False)
    print("  [OK] SessionMaker creado", flush=True)
    sys.stdout.flush()

    async with async_session() as session:
        try:
            print("\nCreando mapeador...", flush=True)
            sys.stdout.flush()
            mapeador = MapeadorMiembros(parser)
            print("  [OK] Mapeador creado", flush=True)
            sys.stdout.flush()

            # Cargar tipos de miembro
            print("\nCargando tipos de miembro...", flush=True)
            sys.stdout.flush()
            await mapeador.cargar_tipos_miembro(session)

            # Cargar estados de miembro
            print("\nCargando estados de miembro...")
            await mapeador.cargar_estados_miembro(session)

            # Cargar cuotas en cache (para inferir agrupación)
            # TEMPORALMENTE DESHABILITADO para acelerar la importación
            # mapeador.cargar_cuotas_cache()

            # Importar miembros activos
            await mapeador.importar_miembros(session)

            # Importar miembros eliminados
            await mapeador.importar_miembros_eliminados(session)

            # Importar miembros fallecidos
            await mapeador.importar_miembros_fallecidos(session)

            # Guardar mapeos
            await mapeador.guardar_mapeo_temporal(session)

            # Encriptar DNIs en lote
            await mapeador.encriptar_dnis_en_lote(session)

            # Encriptar IBANs en lote
            await mapeador.encriptar_ibans_en_lote(session)

            # Commit
            await session.commit()

            print("\n" + "="*80, flush=True)
            print("[OK] IMPORTACION COMPLETADA", flush=True)
            print("="*80, flush=True)
            print(f"\nMiembros importados: {len(mapeador.mapeo_miembros)}", flush=True)

        except Exception as e:
            await session.rollback()
            print(f"\n[ERROR] {e}")
            import traceback
            traceback.print_exc()
            raise
        finally:
            await engine.dispose()


if __name__ == "__main__":
    asyncio.run(main())
