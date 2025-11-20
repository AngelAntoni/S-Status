$(document).ready(function() {
    const dt = $('#serverTable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        retrieve: true,
        paging: false,
        info: false,
        dom: '<"row"<"col-sm-12"f>><"row"<"col-sm-12"tr>'
    });

    const perPageSelect = $('<select class="form-select form-select-sm w-auto ms-2"></select>')
        .append('<option value="10">10</option>')
        .append('<option value="25">25</option>')
        .append('<option value="50">50</option>')
        .append('<option value="100">100</option>');

    try {
        const currentPerPage = Number(new URLSearchParams(window.location.search).get('per_page')) || 10;
        perPageSelect.val(String(currentPerPage));
    } catch (_) {}

    const lengthContainer = document.querySelector('.dataTables_length_container');
    if (lengthContainer) {
        const label = document.createElement('label');
        label.className = 'me-2 text-white-50';
        label.textContent = 'Mostrar';
        lengthContainer.appendChild(label);
        lengthContainer.appendChild(perPageSelect[0]);

        perPageSelect.on('change', function() {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', this.value);
            params.set('page', '1');
            window.location.search = params.toString();
        });
    }

    // Define enviarAlertaSlack aquí para que el Dashboard pueda notificar
    if (typeof window.enviarAlertaSlack !== 'function') {
        window.enviarAlertaSlack = async function(mensaje) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    console.error('Falta CSRF token para enviar a Slack');
                    return false;
                }
                const resp = await fetch('/enviarSlack', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ mensaje })
                });
                if (!resp.ok) {
                    console.error('Falló el envío a Slack. Status:', resp.status);
                    return false;
                }
                return true;
            } catch (e) {
                console.error('Error enviando a Slack:', e);
                return false;
            }
        };
    }

    // [Eliminado: inserción automática del "Link general"]
    // const normaliza = (u) => (u || '').trim().replace(/\/+$/, '/') + (/(\/)$/.test((u || '').trim()) ? '' : '');
    // const linkGeneral = normaliza(`${window.location.origin}/`);
    // const existeFilaConUrl = (url) => {
    // let existe = false;
    // $('#serverTable tbody tr').each(function() {
    // const u = $(this).find('td:eq(2)').text().trim();
    // if (normaliza(u) === normaliza(url)) {
    // existe = true;
    // return false;
    // }
    // })
    // return existe;
    // }
    // const agregarLinkGeneralSiFalta = async () => {
    // if (existeFilaConUrl(linkGeneral)) return;
    // try {
    // await $.ajax({
    // url: '/add-server',
    // method: 'POST',
    // data: {
    // name: 'Link general',
    // url: linkGeneral,
    // type: 'web',
    // description: 'Link general del sitio',
    // _token: $('meta[name="csrf-token"]').attr('content')
    // }
    // });
    // // Recargar para que aparezca la nueva fila y se monitorice
    // window.location.reload();
    // } catch (err) {
    // console.error('No se pudo crear Link general:', err);
    // }
    // }
    // // Llama a la inserción automática
    // agregarLinkGeneralSiFalta();
    // // Revisar si hay datos en sessionStorage para añadir un nuevo servidor
    const raw = sessionStorage.getItem('nuevoServidor');
    if (raw) {
        try {
            const n = JSON.parse(raw);
            const tipo = (n?.tipo || '').trim();
            const url = (n?.url || '').trim();
            
            // Enviar datos al servidor mediante AJAX
            $.ajax({
                url: '/add-server',
                method: 'POST',
                data: {
                    name: 'Servidor ' + tipo,
                    url: url,
                    type: tipo,
                    description: 'Servidor añadido desde el formulario',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Limpiar sessionStorage después de procesar
                    sessionStorage.removeItem('nuevoServidor');
                    // Recargar la página para mostrar el nuevo servidor
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error al guardar el servidor:', error);
                }
            });
        } catch (e) {
            console.error('Error al procesar nuevoServidor:', e);
        }
    }
    
    // Sistema de monitoreo TRAE Monitor
    const monitorearServidores = async () => {
        console.log('TRAE Monitor: Iniciando monitoreo de servidores...');
    
        // Obtiene el nombre del servicio desde meta o usa el hostname
        const SERVICIO_NOMBRE =
            document.querySelector('meta[name="app-name"]')?.getAttribute('content')
            || window.location.hostname
            || 'Aplicación';
    
        // Nuevo: guardar resultado de verificación en server_status
        const guardarEstadoServidor = async (url, activo, codigoHttp, mensaje) => {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const payload = {
                    url,
                    status: activo ? 'online' : (codigoHttp === 'Sin respuesta' ? 'offline' : 'error'),
                    http_status_code: codigoHttp === 'Sin respuesta' ? null : Number(codigoHttp) || null,
                    error_message: activo ? null : (mensaje || 'Error')
                    // response_time: null // si luego medimos tiempo, aquí se envía
                };
                const resp = await fetch('/guardar-status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                if (!resp.ok) {
                    console.error('No se pudo guardar estado:', resp.status, await resp.text());
                }
            } catch (e) {
                console.error('Error guardando estado:', e);
            }
        };
    
        const guardarReporte = async (url, codigoHttp, mensaje) => {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const payload = {
                    url,
                    http_status_code: codigoHttp === 'Sin respuesta' ? null : Number(codigoHttp) || null,
                    mensaje,
                    error_description: mensaje || null
                };
                const resp = await fetch('/guardar-reporte', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                if (!resp.ok) {
                    console.error('No se pudo guardar reporte:', resp.status, await resp.text());
                }
            } catch (e) {
                console.error('Error guardando reporte:', e);
            }
        };
    
        // Mapa de descripciones HTTP en español
        const descripcionHttp = (codigo) => {
            const code = Number(codigo);
            if (!code || isNaN(code)) return 'Sin respuesta';
            const map = {
                200: 'OK',
                201: 'Creado',
                202: 'Aceptado',
                204: 'Sin contenido',
                301: 'Movido permanentemente',
                302: 'Encontrado / Redirección temporal',
                304: 'No modificado',
                400: 'Solicitud inválida',
                401: 'No autorizado',
                403: 'Prohibido',
                404: 'No encontrado',
                405: 'Método no permitido',
                408: 'Tiempo de espera agotado',
                409: 'Conflicto',
                415: 'Tipo de contenido no soportado',
                418: 'Soy una tetera',
                429: 'Demasiadas solicitudes',
                500: 'Error interno del servidor',
                501: 'No implementado',
                502: 'Puerta de enlace incorrecta',
                503: 'Servicio no disponible',
                504: 'Tiempo de espera de la puerta de enlace'
            };
            return map[code] || 'Código HTTP desconocido';
        };
    
        const verificarServidor = async (url, nombre, fila, reportar = false) => {
            try {
                if (!url || !url.startsWith('http')) {
                    console.error(`URL inválida para ${nombre}: ${url}`);
                    return;
                }
    
                console.log(` Verificando: ${url}`);
    
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/verificar-url', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ url: url })
                });
    
                const resultado = await response.json();
                const activo = resultado.activo;
                const codigoHttp = resultado.codigo || 'Sin respuesta';
    
                // Guardar en BD
                await guardarEstadoServidor(url, activo, codigoHttp, resultado.mensaje);
    
                const celdaEstado = $(fila).find('td:eq(3)');
                if (activo) {
                    celdaEstado.html(`<span class="badge bg-success">Activo (${codigoHttp})</span>`);
                } else {
                    celdaEstado.html(`<span class="badge bg-danger">Caído (${codigoHttp})</span>`);
                    if (reportar) {
                        // Crear mensaje más detallado con información de la vista
                        let mensaje = `ALERTA: ${nombre} está CAÍDO\n`;
                        mensaje += `URL: ${url}\n`;
                        if (resultado.vista) {
                            mensaje += `Vista: ${resultado.vista} caída\n`;
                        }
                        const descHttp = descripcionHttp(codigoHttp);
                        mensaje += `Código HTTP: ${codigoHttp} (${descHttp})\n`;
                        mensaje += `Detalle: ${resultado.mensaje || 'Sin detalle'}`;

                        await enviarAlertaSlack(mensaje);

                        // Guardar reporte con mensaje enriquecido
                        const mensajeFinal = `${resultado.mensaje || 'Error'}${
                            codigoHttp && codigoHttp !== 'Sin respuesta'
                                ? ` - HTTP ${codigoHttp}: ${descHttp}`
                                : ''
                        }`;
                        await guardarReporte(url, codigoHttp, mensajeFinal);
                    }
                }
            } catch (error) {
                console.error(`Error al verificar ${url}:`, error);
                const celdaEstado = $(fila).find('td:eq(3)');
                celdaEstado.html('<span class="badge bg-danger">Error</span>');
    
                // Guardar error genérico
                await guardarEstadoServidor(url, false, 'Sin respuesta', error?.message || 'Error de verificación');
    
                if (reportar) {
                    // Detectar vista del URL para mensaje más claro
                    let vista = '';
                    if (url.includes('/detalles')) {
                        vista = 'Detalles';
                    } else if (url.includes('/reportes')) {
                        vista = 'Reportes';
                    } else if (url.includes('/hub')) {
                        vista = 'Hub';
                    } else if (url.includes('/dashboard')) {
                        vista = 'Dashboard';
                    }
                    
                    let mensaje = `ALERTA: ${nombre} está CAÍDO\n`;
                    mensaje += `URL: ${url}\n`;
                    if (vista) {
                        mensaje += `Vista: ${vista} caída\n`;
                    }
                    mensaje += `Código: Sin respuesta\n`;
                    mensaje += `Error: ${error?.message || 'Error de verificación'}`;
                    
                    await enviarAlertaSlack(mensaje);
                    await guardarReporte(url, 'Sin respuesta', error?.message || 'Error de verificación');
                }
            }
        };
    
        // Función principal de monitoreo (visual, sin Slack) - Optimizada para ejecución en paralelo
        const ejecutarMonitoreo = async (reportar = false) => {
            const promesas = [];
            $('#serverTable tbody tr').each(function() {
                const nombre = $(this).find('td:eq(0)').text().trim();
                const url = $(this).find('td:eq(2)').text().trim(); // con la columna Tipo presente, la URL sigue en índice 2
                promesas.push(verificarServidor(url, nombre, this, reportar));
            });
            
            // Ejecutar todas las verificaciones en paralelo
            await Promise.allSettled(promesas);
        };
    
        // Ejecutar monitoreo para actualizar la tabla (sin Slack)
        await ejecutarMonitoreo(false);
    
        // Actualizar cada 5 minutos (sin Slack)
        setInterval(() => ejecutarMonitoreo(false), 300000);
    
        // Botón para enviar alertas manualmente
        $('#btnEnviarAlertas').off('click').on('click', async function () {
            await ejecutarMonitoreo(true);
            const recargar = () => window.location.reload();
            if (window.Swal && typeof Swal.fire === 'function') {
                Swal.fire({
                    icon: 'info',
                    title: 'Alertas enviadas',
                    text: 'Se enviaron mensajes por las páginas caídas detectadas.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(recargar);
            } else {
                setTimeout(recargar, 2000);
            }
        });
    };

    // Iniciar el sistema de monitoreo después de cargar la página
    setTimeout(monitorearServidores, 500);
});
