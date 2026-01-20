# Análisis de Delete Cascade para Campañas

## Contexto

Al eliminar una campaña, hay múltiples tablas relacionadas que deben ser actualizadas o eliminadas. Este documento describe las relaciones y la estrategia de eliminación.

## Tablas Relacionadas con Campañas

### 1. Relaciones Directas (FK a `campanias.id`)

| Tabla | Campo FK | Nullable | Acción en DELETE |
|-------|----------|----------|------------------|
| `participantes_campania` | `campania_id` | NO | **CASCADE DELETE** |
| `firmas_campania` | `campania_id` | NO | **CASCADE DELETE** |
| `actividades` | `campania_id` | SÍ | **SET NULL** |
| `donaciones` | `campania_id` | SÍ | **SET NULL** |
| `propuestas_actividad` | `campania_id` | SÍ | **SET NULL** |
| `grupos_trabajo` | `campania_id` | SÍ | **SET NULL** |

### 2. Relaciones Indirectas (cascada desde tablas hijas)

Cuando se elimina `participantes_campania`:
- No tiene tablas hijas

Cuando se elimina `firmas_campania`:
- No tiene tablas hijas

Cuando se hace SET NULL en `actividades`:
- `participantes_actividad` → permanecen (actividad sigue existiendo)
- `tareas_actividad` → permanecen
- `recursos_actividad` → permanecen
- `grupos_actividad` → permanecen
- `kpis_actividad` → permanecen
- `mediciones_kpi` → permanecen

## Estrategia de Eliminación

### Opción A: Soft Delete (Recomendada)

```python
# En Campania
def soft_delete(self, usuario_id: Optional[uuid.UUID] = None) -> None:
    """Marca la campaña como eliminada sin borrar datos."""
    self.eliminado = True
    self.fecha_eliminacion = datetime.utcnow()
    if usuario_id:
        self.modificado_por_id = usuario_id
```

**Ventajas:**
- Preserva histórico de participantes y firmas
- Preserva relación con donaciones (importante para contabilidad)
- Permite restaurar la campaña
- Cumple RGPD (los datos se conservan pero no se muestran)

**Implementación:**
- Todas las queries deben filtrar por `eliminado = false`
- Las relaciones permanecen intactas

### Opción B: Hard Delete con CASCADE

Si se requiere eliminar completamente, la migración Alembic debe configurar:

```sql
-- Migración para configurar ON DELETE CASCADE
ALTER TABLE participantes_campania
DROP CONSTRAINT participantes_campania_campania_id_fkey,
ADD CONSTRAINT participantes_campania_campania_id_fkey
    FOREIGN KEY (campania_id) REFERENCES campanias(id) ON DELETE CASCADE;

ALTER TABLE firmas_campania
DROP CONSTRAINT firmas_campania_campania_id_fkey,
ADD CONSTRAINT firmas_campania_campania_id_fkey
    FOREIGN KEY (campania_id) REFERENCES campanias(id) ON DELETE CASCADE;

-- Para campos nullable, SET NULL
ALTER TABLE actividades
DROP CONSTRAINT actividades_campania_id_fkey,
ADD CONSTRAINT actividades_campania_id_fkey
    FOREIGN KEY (campania_id) REFERENCES campanias(id) ON DELETE SET NULL;

ALTER TABLE donaciones
DROP CONSTRAINT donaciones_campania_id_fkey,
ADD CONSTRAINT donaciones_campania_id_fkey
    FOREIGN KEY (campania_id) REFERENCES campanias(id) ON DELETE SET NULL;
```

### Opción C: Eliminación Manual Controlada (Service Layer)

```python
class CampaniaService:
    async def eliminar_campania(
        self,
        db: AsyncSession,
        campania_id: uuid.UUID,
        hard_delete: bool = False
    ) -> dict:
        """
        Elimina una campaña con control de cascada.

        Args:
            campania_id: ID de la campaña a eliminar
            hard_delete: Si True, elimina físicamente. Si False, soft delete.

        Returns:
            dict con conteo de registros afectados
        """
        resultado = {
            'participantes_eliminados': 0,
            'firmas_eliminadas': 0,
            'actividades_desvinculadas': 0,
            'donaciones_desvinculadas': 0
        }

        if hard_delete:
            # 1. Eliminar participantes
            stmt = delete(ParticipanteCampania).where(
                ParticipanteCampania.campania_id == campania_id
            )
            result = await db.execute(stmt)
            resultado['participantes_eliminados'] = result.rowcount

            # 2. Eliminar firmas
            stmt = delete(FirmaCampania).where(
                FirmaCampania.campania_id == campania_id
            )
            result = await db.execute(stmt)
            resultado['firmas_eliminadas'] = result.rowcount

            # 3. Desvincular actividades (SET NULL)
            stmt = update(Actividad).where(
                Actividad.campania_id == campania_id
            ).values(campania_id=None)
            result = await db.execute(stmt)
            resultado['actividades_desvinculadas'] = result.rowcount

            # 4. Desvincular donaciones (SET NULL)
            stmt = update(Donacion).where(
                Donacion.campania_id == campania_id
            ).values(campania_id=None)
            result = await db.execute(stmt)
            resultado['donaciones_desvinculadas'] = result.rowcount

            # 5. Eliminar la campaña
            stmt = delete(Campania).where(Campania.id == campania_id)
            await db.execute(stmt)

        else:
            # Soft delete
            campania = await db.get(Campania, campania_id)
            if campania:
                campania.soft_delete()

        await db.commit()
        return resultado
```

## Recomendación

**Usar Soft Delete por defecto**, con opción de Hard Delete solo para administradores y con confirmación explícita.

Razones:
1. **Cumplimiento PGC ESFL**: Las donaciones vinculadas a campañas deben conservarse 6 años
2. **Trazabilidad**: Poder auditar qué campañas existieron y cuándo se "eliminaron"
3. **Seguridad**: Evitar pérdida accidental de datos
4. **RGPD**: Los datos de participantes pueden necesitar anonimización diferida

## Mutation GraphQL Propuesta

```graphql
type Mutation {
  # Soft delete (por defecto)
  eliminar_campania(filter: CampaniaFilter!): [Campania!]!

  # Hard delete (solo admin, con confirmación)
  eliminar_campania_permanente(
    campania_id: UUID!
    confirmar_eliminacion: Boolean!
  ): ResultadoEliminacion!
}

type ResultadoEliminacion {
  exito: Boolean!
  mensaje: String
  participantes_eliminados: Int
  firmas_eliminadas: Int
  actividades_desvinculadas: Int
  donaciones_desvinculadas: Int
}
```

## Tablas Afectadas - Resumen Visual

```
                    ┌─────────────────────┐
                    │     campanias       │
                    └─────────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
        ▼                     ▼                     ▼
┌───────────────┐   ┌───────────────┐   ┌───────────────┐
│ participantes │   │    firmas     │   │  actividades  │
│   _campania   │   │   _campania   │   │ (campania_id) │
│  CASCADE DEL  │   │  CASCADE DEL  │   │   SET NULL    │
└───────────────┘   └───────────────┘   └───────────────┘
                                                │
                    ┌───────────────────────────┤
                    ▼                           ▼
            ┌───────────────┐         ┌───────────────┐
            │  donaciones   │         │grupos_trabajo │
            │ (campania_id) │         │ (campania_id) │
            │   SET NULL    │         │   SET NULL    │
            └───────────────┘         └───────────────┘
```
