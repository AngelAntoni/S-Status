document.addEventListener('DOMContentLoaded', function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // Editar servidor
  document.querySelectorAll('.btn-editar-url').forEach(btn => {
    btn.addEventListener('click', async function () {
      const row = this.closest('tr');
      const nombre = row?.querySelector('td:nth-child(1)')?.textContent.trim() || '';
      const tipo = row?.querySelector('td:nth-child(2)')?.textContent.trim() || '';
      const urlActual = this.dataset.url || row?.querySelector('td:nth-child(3)')?.textContent.trim() || '';

      if (!urlActual) return;

      const result = await Swal.fire({
        title: 'Editar servidor',
        width: '36rem',
        html: `
          <div class="mb-3">
            <label for="swal-name" class="form-label">Nombre</label>
            <input id="swal-name" type="text" class="form-control" value="${nombre}">
          </div>
          <div class="mb-3">
            <label for="swal-type" class="form-label">Tipo</label>
            <input id="swal-type" type="text" class="form-control" value="${tipo}">
          </div>
          <div class="mb-3">
            <label for="swal-url" class="form-label">Nueva URL (opcional)</label>
            <input id="swal-url" type="url" class="form-control" placeholder="https://ejemplo.com/" value="${urlActual}">
          </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false,
        customClass: {
          confirmButton: 'btn btn-primary me-2',
          cancelButton: 'btn btn-secondary'
        },
        didOpen: () => {
          const el = document.getElementById('swal-name');
          if (el) el.focus();
        },
        preConfirm: () => {
          const name = document.getElementById('swal-name').value.trim();
          const type = document.getElementById('swal-type').value.trim();
          const new_url = document.getElementById('swal-url').value.trim();
          if (!name) {
            Swal.showValidationMessage('El nombre es obligatorio');
            return false;
          }
          if (new_url && !/^https?:\/\//i.test(new_url)) {
            Swal.showValidationMessage('La URL debe empezar con http:// o https://');
            return false;
          }
          return { name, type, new_url };
        }
      });

      if (!result.isConfirmed) return;

      const payload = { url: urlActual, ...result.value };

      try {
        const resp = await fetch('/servidores', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify(payload)
        });

        const ct = resp.headers.get('content-type') || '';
        const data = ct.includes('application/json') ? await resp.json() : { ok: false, error: `HTTP ${resp.status}` };

        if (resp.ok && data?.ok) {
          await Swal.fire('Listo', 'Servidor actualizado correctamente', 'success');
          window.location.reload();
        } else {
          Swal.fire('Error', data?.error || `Error ${resp.status}`, 'error');
        }
      } catch (e) {
        Swal.fire('Error', e?.message || 'Ocurrió un error al editar', 'error');
      }
    });
  });

  // Eliminar URL
  document.querySelectorAll('.btn-eliminar-url').forEach(btn => {
    btn.addEventListener('click', async function () {
      const url = this.dataset.url;
      if (!url) return;

      const confirmResult = await Swal.fire({
        title: 'Eliminar URL',
        text: `¿Deseas eliminar este enlace?\n${url}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      });

      if (!confirmResult.isConfirmed) return;

      try {
        const resp = await fetch('/servidores', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify({ url })
        });

        let data = null;
        const ct = resp.headers.get('content-type') || '';
        if (ct.includes('application/json')) {
          data = await resp.json();
        } else {
          const text = await resp.text();
          throw new Error(`HTTP ${resp.status} - Respuesta no JSON: ${text.slice(0, 160)}`);
        }

        if (resp.ok && data?.ok) {
          await Swal.fire('Eliminado', 'URL eliminada correctamente', 'success');
          window.location.reload();
        } else {
          Swal.fire('Error', data?.error || `Error ${resp.status}`, 'error');
        }
      } catch (e) {
        Swal.fire('Error', e?.message || 'Ocurrió un error al eliminar', 'error');
      }
    });
  });
});
