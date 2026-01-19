import strawberry
from strawberry.types import Info
from sqlalchemy import select
from sqlalchemy.orm import selectinload
from datetime import date

from ..core.context import Context
from ..models.voluntariado import (
    CategoriaCompetencia as CategoriaCompetenciaModel,
    Competencia as CompetenciaModel,
    NivelCompetencia as NivelCompetenciaModel,
    MiembroCompetencia as MiembroCompetenciaModel,
    TipoDocumento as TipoDocumentoModel,
    DocumentoMiembro as DocumentoMiembroModel,
    TipoFormacion as TipoFormacionModel,
    FormacionMiembro as FormacionMiembroModel,
)
from ..models.miembro import Miembro as MiembroModel
from ..schemas.voluntariado import (
    CategoriaCompetencia, Competencia, NivelCompetencia, MiembroCompetencia,
    TipoDocumentoVoluntario, DocumentoMiembro, TipoFormacion, FormacionMiembro,
    PerfilVoluntario,
    VoluntariadoInput, CompetenciaInput, MiembroCompetenciaInput,
    DocumentoMiembroInput, FormacionMiembroInput, FormacionMiembroUpdateInput,
    BusquedaVoluntariosInput,
)


# --- Funciones de conversión ---

def model_to_categoria_competencia(m: CategoriaCompetenciaModel) -> CategoriaCompetencia:
    return CategoriaCompetencia(
        id=m.id, codigo=m.codigo, nombre=m.nombre,
        descripcion=m.descripcion, activo=m.activo
    )


def model_to_competencia(m: CompetenciaModel) -> Competencia:
    return Competencia(
        id=m.id, codigo=m.codigo, nombre=m.nombre, descripcion=m.descripcion,
        categoria=model_to_categoria_competencia(m.categoria), activo=m.activo
    )


def model_to_nivel_competencia(m: NivelCompetenciaModel) -> NivelCompetencia:
    return NivelCompetencia(
        id=m.id, codigo=m.codigo, nombre=m.nombre, orden=m.orden, activo=m.activo
    )


def model_to_miembro_competencia(m: MiembroCompetenciaModel) -> MiembroCompetencia:
    return MiembroCompetencia(
        competencia=model_to_competencia(m.competencia),
        nivel=model_to_nivel_competencia(m.nivel),
        verificado=m.verificado, fecha_verificacion=m.fecha_verificacion,
        observaciones=m.observaciones
    )


def model_to_tipo_documento(m: TipoDocumentoModel) -> TipoDocumentoVoluntario:
    return TipoDocumentoVoluntario(
        id=m.id, codigo=m.codigo, nombre=m.nombre, descripcion=m.descripcion,
        requiere_caducidad=m.requiere_caducidad, activo=m.activo
    )


def model_to_documento_miembro(m: DocumentoMiembroModel) -> DocumentoMiembro:
    return DocumentoMiembro(
        id=m.id, tipo_documento=model_to_tipo_documento(m.tipo_documento),
        nombre=m.nombre, descripcion=m.descripcion,
        archivo_url=m.archivo_url, archivo_nombre=m.archivo_nombre,
        archivo_tipo=m.archivo_tipo, archivo_tamano=m.archivo_tamano,
        fecha_subida=m.fecha_subida, fecha_caducidad=m.fecha_caducidad,
        activo=m.activo
    )


def model_to_tipo_formacion(m: TipoFormacionModel) -> TipoFormacion:
    return TipoFormacion(
        id=m.id, codigo=m.codigo, nombre=m.nombre, activo=m.activo
    )


def model_to_formacion_miembro(m: FormacionMiembroModel) -> FormacionMiembro:
    return FormacionMiembro(
        id=m.id, tipo_formacion=model_to_tipo_formacion(m.tipo_formacion),
        titulo=m.titulo, institucion=m.institucion, descripcion=m.descripcion,
        fecha_inicio=m.fecha_inicio, fecha_fin=m.fecha_fin, horas=m.horas,
        certificado=m.certificado, competencias_adquiridas=m.competencias_adquiridas,
        es_interna=m.es_interna
    )


def model_to_perfil_voluntario(m: MiembroModel) -> PerfilVoluntario:
    nombre_completo = f"{m.nombre} {m.apellido1}"
    if m.apellido2:
        nombre_completo += f" {m.apellido2}"

    return PerfilVoluntario(
        miembro_id=m.id,
        nombre_completo=nombre_completo,
        es_voluntario=m.es_voluntario,
        disponibilidad=m.disponibilidad,
        horas_disponibles_semana=m.horas_disponibles_semana,
        experiencia_voluntariado=m.experiencia_voluntariado,
        intereses=m.intereses,
        puede_conducir=m.puede_conducir,
        vehiculo_propio=m.vehiculo_propio,
        disponibilidad_viajar=m.disponibilidad_viajar,
        total_horas_voluntariado=m.total_horas_voluntariado,
        total_campanias_participadas=m.total_campanias_participadas,
        fecha_ultimo_voluntariado=m.fecha_ultimo_voluntariado,
        competencias=[model_to_miembro_competencia(c) for c in m.competencias],
        formaciones=[model_to_formacion_miembro(f) for f in m.formaciones]
    )


# --- Queries ---

@strawberry.type
class VoluntariadoQuery:
    @strawberry.field
    async def categorias_competencia(self, info: Info[Context, None]) -> list[CategoriaCompetencia]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(CategoriaCompetenciaModel).where(CategoriaCompetenciaModel.activo == True)
            )
            return [model_to_categoria_competencia(c) for c in result.scalars()]

    @strawberry.field
    async def competencias(
        self, info: Info[Context, None], categoria_id: int | None = None
    ) -> list[Competencia]:
        async with await info.context.get_db() as db:
            query = select(CompetenciaModel).options(
                selectinload(CompetenciaModel.categoria)
            ).where(CompetenciaModel.activo == True)

            if categoria_id:
                query = query.where(CompetenciaModel.categoria_id == categoria_id)

            result = await db.execute(query)
            return [model_to_competencia(c) for c in result.scalars()]

    @strawberry.field
    async def niveles_competencia(self, info: Info[Context, None]) -> list[NivelCompetencia]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(NivelCompetenciaModel)
                .where(NivelCompetenciaModel.activo == True)
                .order_by(NivelCompetenciaModel.orden)
            )
            return [model_to_nivel_competencia(n) for n in result.scalars()]

    @strawberry.field
    async def tipos_documento_voluntario(self, info: Info[Context, None]) -> list[TipoDocumentoVoluntario]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(TipoDocumentoModel).where(TipoDocumentoModel.activo == True)
            )
            return [model_to_tipo_documento(t) for t in result.scalars()]

    @strawberry.field
    async def tipos_formacion(self, info: Info[Context, None]) -> list[TipoFormacion]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(TipoFormacionModel).where(TipoFormacionModel.activo == True)
            )
            return [model_to_tipo_formacion(t) for t in result.scalars()]

    @strawberry.field
    async def perfil_voluntario(
        self, info: Info[Context, None], miembro_id: int
    ) -> PerfilVoluntario | None:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(MiembroModel).options(
                    selectinload(MiembroModel.competencias).selectinload(MiembroCompetenciaModel.competencia).selectinload(CompetenciaModel.categoria),
                    selectinload(MiembroModel.competencias).selectinload(MiembroCompetenciaModel.nivel),
                    selectinload(MiembroModel.formaciones).selectinload(FormacionMiembroModel.tipo_formacion),
                ).where(MiembroModel.id == miembro_id, MiembroModel.deleted_at == None)
            )
            miembro = result.scalar_one_or_none()
            if not miembro:
                return None
            return model_to_perfil_voluntario(miembro)

    @strawberry.field
    async def voluntarios_disponibles(
        self, info: Info[Context, None], filtros: BusquedaVoluntariosInput | None = None
    ) -> list[PerfilVoluntario]:
        """Busca voluntarios por competencias y disponibilidad (RF-VC002)."""
        async with await info.context.get_db() as db:
            query = select(MiembroModel).options(
                selectinload(MiembroModel.competencias).selectinload(MiembroCompetenciaModel.competencia).selectinload(CompetenciaModel.categoria),
                selectinload(MiembroModel.competencias).selectinload(MiembroCompetenciaModel.nivel),
                selectinload(MiembroModel.formaciones).selectinload(FormacionMiembroModel.tipo_formacion),
            ).where(MiembroModel.es_voluntario == True)

            if filtros:
                if filtros.solo_activos:
                    query = query.where(MiembroModel.deleted_at == None)
                if filtros.disponibilidad:
                    query = query.where(MiembroModel.disponibilidad == filtros.disponibilidad)
                if filtros.puede_conducir:
                    query = query.where(MiembroModel.puede_conducir == True)
                if filtros.vehiculo_propio:
                    query = query.where(MiembroModel.vehiculo_propio == True)
                if filtros.disponibilidad_viajar:
                    query = query.where(MiembroModel.disponibilidad_viajar == True)
                if filtros.agrupacion_id:
                    query = query.where(MiembroModel.agrupacion_id == filtros.agrupacion_id)

            result = await db.execute(query)
            voluntarios = []

            for m in result.scalars():
                # Filtrar por competencias si se especificaron
                if filtros and filtros.competencia_ids:
                    competencia_ids_miembro = {c.competencia_id for c in m.competencias}
                    if not all(cid in competencia_ids_miembro for cid in filtros.competencia_ids):
                        continue
                voluntarios.append(model_to_perfil_voluntario(m))

            return voluntarios

    @strawberry.field
    async def documentos_miembro(
        self, info: Info[Context, None], miembro_id: int, solo_activos: bool = True
    ) -> list[DocumentoMiembro]:
        async with await info.context.get_db() as db:
            query = select(DocumentoMiembroModel).options(
                selectinload(DocumentoMiembroModel.tipo_documento)
            ).where(DocumentoMiembroModel.miembro_id == miembro_id)

            if solo_activos:
                query = query.where(DocumentoMiembroModel.activo == True)

            result = await db.execute(query)
            return [model_to_documento_miembro(d) for d in result.scalars()]

    @strawberry.field
    async def formaciones_miembro(
        self, info: Info[Context, None], miembro_id: int
    ) -> list[FormacionMiembro]:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(FormacionMiembroModel).options(
                    selectinload(FormacionMiembroModel.tipo_formacion)
                ).where(FormacionMiembroModel.miembro_id == miembro_id)
                .order_by(FormacionMiembroModel.fecha_fin.desc())
            )
            return [model_to_formacion_miembro(f) for f in result.scalars()]

    @strawberry.field
    async def documentos_por_caducar(
        self, info: Info[Context, None], dias: int = 30
    ) -> list[DocumentoMiembro]:
        """Documentos que caducan en los próximos N días."""
        from datetime import timedelta
        async with await info.context.get_db() as db:
            fecha_limite = date.today() + timedelta(days=dias)
            result = await db.execute(
                select(DocumentoMiembroModel).options(
                    selectinload(DocumentoMiembroModel.tipo_documento)
                ).where(
                    DocumentoMiembroModel.activo == True,
                    DocumentoMiembroModel.fecha_caducidad != None,
                    DocumentoMiembroModel.fecha_caducidad <= fecha_limite,
                    DocumentoMiembroModel.fecha_caducidad >= date.today()
                ).order_by(DocumentoMiembroModel.fecha_caducidad)
            )
            return [model_to_documento_miembro(d) for d in result.scalars()]


# --- Mutations ---

@strawberry.type
class VoluntariadoMutation:
    @strawberry.mutation
    async def actualizar_voluntariado_miembro(
        self, info: Info[Context, None], miembro_id: int, input: VoluntariadoInput
    ) -> PerfilVoluntario:
        """Actualiza los datos de voluntariado de un miembro."""
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(MiembroModel).where(MiembroModel.id == miembro_id)
            )
            miembro = result.scalar_one_or_none()
            if not miembro:
                raise Exception("Miembro no encontrado")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(miembro, field, value)

            await db.commit()
            await db.refresh(miembro, ["competencias", "formaciones"])
            for c in miembro.competencias:
                await db.refresh(c, ["competencia", "nivel"])
                await db.refresh(c.competencia, ["categoria"])
            for f in miembro.formaciones:
                await db.refresh(f, ["tipo_formacion"])

            return model_to_perfil_voluntario(miembro)

    @strawberry.mutation
    async def crear_competencia(
        self, info: Info[Context, None], input: CompetenciaInput
    ) -> Competencia:
        async with await info.context.get_db() as db:
            competencia = CompetenciaModel(
                codigo=input.codigo,
                nombre=input.nombre,
                descripcion=input.descripcion,
                categoria_id=input.categoria_id,
            )
            db.add(competencia)
            await db.commit()
            await db.refresh(competencia, ["categoria"])
            return model_to_competencia(competencia)

    @strawberry.mutation
    async def asignar_competencia_miembro(
        self, info: Info[Context, None], input: MiembroCompetenciaInput
    ) -> MiembroCompetencia:
        """Asigna una competencia a un miembro (RF-VC001)."""
        async with await info.context.get_db() as db:
            # Verificar si ya existe
            result = await db.execute(
                select(MiembroCompetenciaModel).where(
                    MiembroCompetenciaModel.miembro_id == input.miembro_id,
                    MiembroCompetenciaModel.competencia_id == input.competencia_id
                )
            )
            existing = result.scalar_one_or_none()
            if existing:
                raise Exception("El miembro ya tiene esta competencia asignada")

            mc = MiembroCompetenciaModel(
                miembro_id=input.miembro_id,
                competencia_id=input.competencia_id,
                nivel_id=input.nivel_id,
                observaciones=input.observaciones,
            )
            db.add(mc)
            await db.commit()
            await db.refresh(mc, ["competencia", "nivel"])
            await db.refresh(mc.competencia, ["categoria"])
            return model_to_miembro_competencia(mc)

    @strawberry.mutation
    async def actualizar_nivel_competencia_miembro(
        self, info: Info[Context, None],
        miembro_id: int, competencia_id: int, nivel_id: int
    ) -> MiembroCompetencia:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(MiembroCompetenciaModel).where(
                    MiembroCompetenciaModel.miembro_id == miembro_id,
                    MiembroCompetenciaModel.competencia_id == competencia_id
                )
            )
            mc = result.scalar_one_or_none()
            if not mc:
                raise Exception("Competencia no encontrada para este miembro")

            mc.nivel_id = nivel_id
            await db.commit()
            await db.refresh(mc, ["competencia", "nivel"])
            await db.refresh(mc.competencia, ["categoria"])
            return model_to_miembro_competencia(mc)

    @strawberry.mutation
    async def verificar_competencia_miembro(
        self, info: Info[Context, None], miembro_id: int, competencia_id: int
    ) -> MiembroCompetencia:
        """Marca una competencia como verificada."""
        user_id = info.context.user_id
        if not user_id:
            raise Exception("No autenticado")

        async with await info.context.get_db() as db:
            result = await db.execute(
                select(MiembroCompetenciaModel).where(
                    MiembroCompetenciaModel.miembro_id == miembro_id,
                    MiembroCompetenciaModel.competencia_id == competencia_id
                )
            )
            mc = result.scalar_one_or_none()
            if not mc:
                raise Exception("Competencia no encontrada para este miembro")

            mc.verificado = True
            mc.fecha_verificacion = date.today()
            mc.verificado_por_id = user_id
            await db.commit()
            await db.refresh(mc, ["competencia", "nivel"])
            await db.refresh(mc.competencia, ["categoria"])
            return model_to_miembro_competencia(mc)

    @strawberry.mutation
    async def eliminar_competencia_miembro(
        self, info: Info[Context, None], miembro_id: int, competencia_id: int
    ) -> bool:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(MiembroCompetenciaModel).where(
                    MiembroCompetenciaModel.miembro_id == miembro_id,
                    MiembroCompetenciaModel.competencia_id == competencia_id
                )
            )
            mc = result.scalar_one_or_none()
            if not mc:
                raise Exception("Competencia no encontrada para este miembro")

            await db.delete(mc)
            await db.commit()
            return True

    @strawberry.mutation
    async def crear_documento_miembro(
        self, info: Info[Context, None], input: DocumentoMiembroInput
    ) -> DocumentoMiembro:
        user_id = info.context.user_id
        if not user_id:
            raise Exception("No autenticado")

        async with await info.context.get_db() as db:
            doc = DocumentoMiembroModel(
                miembro_id=input.miembro_id,
                tipo_documento_id=input.tipo_documento_id,
                nombre=input.nombre,
                descripcion=input.descripcion,
                archivo_url=input.archivo_url,
                archivo_nombre=input.archivo_nombre,
                archivo_tipo=input.archivo_tipo,
                archivo_tamano=input.archivo_tamano,
                fecha_caducidad=input.fecha_caducidad,
                subido_por_id=user_id,
            )
            db.add(doc)
            await db.commit()
            await db.refresh(doc, ["tipo_documento"])
            return model_to_documento_miembro(doc)

    @strawberry.mutation
    async def desactivar_documento_miembro(
        self, info: Info[Context, None], documento_id: int
    ) -> bool:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(DocumentoMiembroModel).where(DocumentoMiembroModel.id == documento_id)
            )
            doc = result.scalar_one_or_none()
            if not doc:
                raise Exception("Documento no encontrado")

            doc.activo = False
            await db.commit()
            return True

    @strawberry.mutation
    async def crear_formacion_miembro(
        self, info: Info[Context, None], input: FormacionMiembroInput
    ) -> FormacionMiembro:
        async with await info.context.get_db() as db:
            formacion = FormacionMiembroModel(
                miembro_id=input.miembro_id,
                tipo_formacion_id=input.tipo_formacion_id,
                titulo=input.titulo,
                institucion=input.institucion,
                descripcion=input.descripcion,
                fecha_inicio=input.fecha_inicio,
                fecha_fin=input.fecha_fin,
                horas=input.horas,
                certificado=input.certificado,
                competencias_adquiridas=input.competencias_adquiridas,
                es_interna=input.es_interna,
            )
            db.add(formacion)
            await db.commit()
            await db.refresh(formacion, ["tipo_formacion"])
            return model_to_formacion_miembro(formacion)

    @strawberry.mutation
    async def actualizar_formacion_miembro(
        self, info: Info[Context, None], id: int, input: FormacionMiembroUpdateInput
    ) -> FormacionMiembro:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(FormacionMiembroModel).where(FormacionMiembroModel.id == id)
            )
            formacion = result.scalar_one_or_none()
            if not formacion:
                raise Exception("Formación no encontrada")

            for field, value in vars(input).items():
                if value is not None:
                    setattr(formacion, field, value)

            await db.commit()
            await db.refresh(formacion, ["tipo_formacion"])
            return model_to_formacion_miembro(formacion)

    @strawberry.mutation
    async def eliminar_formacion_miembro(self, info: Info[Context, None], id: int) -> bool:
        async with await info.context.get_db() as db:
            result = await db.execute(
                select(FormacionMiembroModel).where(FormacionMiembroModel.id == id)
            )
            formacion = result.scalar_one_or_none()
            if not formacion:
                raise Exception("Formación no encontrada")

            await db.delete(formacion)
            await db.commit()
            return True
