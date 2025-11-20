(function () {

  // Eliminar seleccionados
  const btnEliminarSel = document.getElementById('btnEliminarSeleccionados');
  if (btnEliminarSel) {
    btnEliminarSel.addEventListener('click', async function () {
      try {
        const ids = Array.from(document.querySelectorAll('.chk-reporte:checked'))
          .map(chk => parseInt(chk.value, 10))
          .filter(Boolean);

        if (!ids.length) {
          if (window.Swal) Swal.fire('Selecciona reportes', 'No hay reportes seleccionados', 'info');
          return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const resp = await fetch('/reportes/eliminar-multiples', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify({ ids })
        });
        const data = await resp.json();

        if (data?.ok) {
          ids.forEach(id => {
            const fila = document.querySelector(`tr[data-reporte-id="${id}"]`);
            if (fila) fila.remove();
          });
          if (window.Swal) Swal.fire('Eliminados', 'Reportes eliminados', 'success');
        } else {
          if (window.Swal) Swal.fire('Error', 'No se pudieron eliminar', 'error');
        }
      } catch (e) {
        if (window.Swal) Swal.fire('Error', 'Ocurrió un error', 'error');
      }
    });
  }

  // Validar todas las vistas
  const btn = document.getElementById('btnValidarVistas');
  if (btn) {
    btn.addEventListener('click', async function () {
      try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const urlVista =
          document.querySelector('.url-container a')?.href
          || new URLSearchParams(window.location.search).get('url')
          || '';

        if (!urlVista) {
          if (window.Swal) Swal.fire('Sin URL', 'No se encontró la URL del servidor.', 'warning');
          return;
        }

        const modalEl = document.getElementById('validacionModal');
        const bsModal = new bootstrap.Modal(modalEl);
        const tbody = document.getElementById('tablaVistas');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Validando vistas...</td></tr>';
        bsModal.show();

        const resp = await fetch('/descubrir-y-validar-vistas', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
          body: JSON.stringify({ url: urlVista })
        });
        const data = await resp.json();

        tbody.innerHTML = '';
        (data?.vistas || []).forEach(v => {
          const estado = v.activo
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${v.ruta}</td>
            <td><a href="${v.url}" target="_blank" rel="noopener">${v.url}</a></td>
            <td>${estado}</td>
            <td>${v.codigo}</td>
          `;
          tbody.appendChild(tr);
        });

        if (!data?.vistas?.length) {
          tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No se encontraron vistas para mostrar.</td></tr>';
        }
      } catch (e) {
        const tbody = document.getElementById('tablaVistas');
        if (tbody) tbody.innerHTML =
          '<tr><td colspan="4" class="text-center text-danger">Error al validar vistas.</td></tr>';
      }
    });
  }

})();
