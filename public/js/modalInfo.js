// modalInfo.js - Versión navegador usando endpoint Laravel para Slack

console.log('Script modalInfo.js cargado');

/**
 * Envía alerta a Slack mediante el endpoint de Laravel (/enviarSlack)
 */
async function enviarAlertaSlack(mensaje) {
  try {
    // Obtener el token CSRF de la meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
      console.error('No se encontró el token CSRF. Asegúrate de incluir <meta name="csrf-token" content="{{ csrf_token() }}"> en tu layout');
      return;
    }
    
    const response = await fetch('/enviarSlack', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ mensaje })
    });

    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
      const data = await response.json();
      
      if (response.ok && data.ok) {
        console.log('Alerta enviada a Slack vía servidor Laravel');
        return true;
      } else {
        console.error('Error en el servidor al enviar alerta:', data.error || 'Desconocido');
        return false;
      }
    } else {
      console.error('La respuesta no es JSON válido. Status:', response.status);
      const textResponse = await response.text();
      console.log('Respuesta del servidor:', textResponse.substring(0, 150) + '...');
      return false;
    }
  } catch (error) {
    console.error('Error enviando alerta a Slack:', error);
    return false;
  }
}

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM completamente cargado');

  const modalElement = document.getElementById('infoModal');

  if (!modalElement) {
    console.error('No se encontró el modal con id="infoModal"');
    return;
  }

  console.log('Modal encontrado correctamente');

  const botones = document.querySelectorAll('.info-btn');
  console.log(`Botones encontrados: ${botones.length}`);

  modalElement.addEventListener('show.bs.modal', (event) => {
    console.log('Modal activándose...');

    const btn = event.relatedTarget;
    if (!btn) {
      console.error('No se pudo obtener el botón que activó el modal');
      return;
    }

    // Solo los campos que quedan en el modal y sin “N/A”
    const datos = {
      servidor: btn.dataset.servidor || '',
      url: btn.dataset.url || '#',
      fecha: btn.dataset.fecha || '',
      hora: btn.dataset.hora || '',
      causa: btn.dataset.causa || ''
    };

    const elementos = {
      servidor: document.getElementById('modal-servidor'),
      url: document.getElementById('modal-url'),
      fecha: document.getElementById('modal-fecha'),
      hora: document.getElementById('modal-hora'),
      causa: document.getElementById('modal-causa')
    };

    const faltantes = Object.entries(elementos)
      .filter(([_, el]) => !el)
      .map(([key]) => key);

    if (faltantes.length > 0) {
      console.error('Elementos faltantes en el modal:', faltantes);
      // No cortamos el llenado, se llenan los que existan
    }

    if (elementos.servidor) elementos.servidor.textContent = datos.servidor;
    if (elementos.url) { elementos.url.textContent = datos.url; elementos.url.href = datos.url; }
    if (elementos.fecha) elementos.fecha.textContent = datos.fecha;
    if (elementos.hora) elementos.hora.textContent = datos.hora;
    if (elementos.causa) elementos.causa.textContent = datos.causa;

    console.log('Modal llenado exitosamente');

    // Se elimina el envío automático a Slack al abrir el modal en "Detalles"
    // antes se enviaba si la causa incluía "error"
  });

  console.log('Event listener del modal configurado correctamente');

  let lastInfoData = null;

  function fillModal(datos) {
    const elementos = {
      servidor: document.getElementById('modal-servidor'),
      url: document.getElementById('modal-url'),
      fecha: document.getElementById('modal-fecha'),
      hora: document.getElementById('modal-hora'),
      causa: document.getElementById('modal-causa')
    };

    if (elementos.servidor) elementos.servidor.textContent = datos.servidor || '';
    if (elementos.url) { 
      elementos.url.textContent = datos.url || ''; 
      elementos.url.href = datos.url || '#'; 
    }
    if (elementos.fecha) elementos.fecha.textContent = datos.fecha || '';
    if (elementos.hora) elementos.hora.textContent = datos.hora || '';
    if (elementos.causa) elementos.causa.textContent = datos.causa || '';
  }

  // Llenar al hacer clic en el botón Información (más fiable)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.info-btn');
    if (!btn) return;

    lastInfoData = {
      servidor: btn.dataset.servidor || '',
      url: btn.dataset.url || '#',
      fecha: btn.dataset.fecha || '',
      hora: btn.dataset.hora || '',
      causa: btn.dataset.causa || ''
    };
    fillModal(lastInfoData);
  });

  // También llenar al mostrar el modal (siempre que haya datos)
  modalElement.addEventListener('show.bs.modal', (event) => {
    const btn = event.relatedTarget;
    const datos = btn ? {
      servidor: btn.dataset.servidor || '',
      url: btn.dataset.url || '#',
      fecha: btn.dataset.fecha || '',
      hora: btn.dataset.hora || '',
      causa: btn.dataset.causa || ''
    } : lastInfoData;

    if (!datos) {
      console.warn('No hay datos para llenar el modal todavía');
      return;
    }

    fillModal(datos);
  });
});
