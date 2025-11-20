## Objetivo
Hacer que al presionar el botón `Enviar alertas`, después de que termine la animación del SweetAlert2, la vista se recargue de forma confiable.

## Cambios Propuestos
1. Reemplazar el uso de `didClose` por la resolución de la promesa de `Swal.fire()`:
   - Ubicación: `public/js/serverTable.js` líneas 330–345.
   - Código: `Swal.fire({...}).then(() => window.location.reload());`
   - Motivo: La promesa de `Swal.fire()` se resuelve siempre al cerrar el modal (incluyendo cierre por `timer`), lo que resulta más robusto que `didClose` según la práctica común.

2. Mantener respaldo si SweetAlert2 no está disponible:
   - Conservar el `setTimeout(() => location.reload(), 2000)` en el branch de fallback.

## Verificación
1. Abrir `Dashboard` y pulsar `Enviar alertas`.
2. Verificar que aparece el SweetAlert durante ~2s y, al cerrar, la página se recarga automáticamente.

## Notas
- No se cambian tiempos ni textos; solo el mecanismo de disparo del reload.
- No afecta el monitoreo ni la paginación añadida previamente.