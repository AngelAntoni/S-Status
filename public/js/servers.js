// Añadir servidor desde el modal
const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('#btnAñadir');
    if (!btn) return;

    const name = document.getElementById('nombreServidor')?.value.trim() || '';
    const url = document.getElementById('urlServidor')?.value.trim() || '';
    const typeRaw = document.getElementById('tipoServidor')?.value.trim() || '';
    const type = (typeRaw || '').toLowerCase();
    const description = document.getElementById('descripcionServidor')?.value.trim() || '';

    if (!name || !url || !type) {
      if (window.Swal) Swal.fire('Campos incompletos', 'Nombre, URL y Tipo son obligatorios', 'warning');
      return;
    }

    if (!/^https?:\/\//i.test(url)) {
      if (window.Swal) Swal.fire('URL inválida', 'La URL debe comenzar con http:// o https://', 'error');
      return;
    }

    const payload = { name, url, type, description, _token: csrf };

    try {
      const resp = await fetch('/add-server', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify(payload)
      });

      const ct = resp.headers.get('content-type') || '';
      const data = ct.includes('application/json') ? await resp.json() : null;

      if (resp.ok && data?.ok) {
        if (window.Swal) await Swal.fire('Éxito', 'Servidor agregado correctamente', 'success');
        window.location.reload();
      } else {
        if (window.Swal) Swal.fire('Error', (data && data.error) ? data.error : `Error ${resp.status}`, 'error');
      }
    } catch (e) {
      if (window.Swal) Swal.fire('Error', e?.message || 'Ocurrió un error al guardar', 'error');
    }
  });

  document.addEventListener('click', async (e) => {
    const delBtn = e.target.closest('.btn-eliminar-url');
    if (!delBtn) return;

    const url = delBtn.dataset.url || '';
    if (!url) return;

    try {
      const ok = await (window.Swal ? Swal.fire({
        icon: 'warning',
        title: 'Eliminar servidor',
        text: '¿Seguro que deseas eliminar este servidor y sus reportes?',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then(r => r.isConfirmed) : Promise.resolve(confirm('¿Eliminar servidor?')));

      if (!ok) return;

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
      const data = await resp.json().catch(() => null);

      if (resp.ok && data && data.ok) {
        const tr = delBtn.closest('tr');
        if (tr) tr.remove();
        if (window.Swal) Swal.fire('Eliminado', 'Servidor eliminado', 'success');
      } else {
        if (window.Swal) Swal.fire('Error', (data && data.error) ? data.error : `Error ${resp.status}`, 'error');
      }
    } catch (err) {
      if (window.Swal) Swal.fire('Error', err?.message || 'Ocurrió un error al eliminar', 'error');
    }
  });

  document.addEventListener('click', async (e) => {
    const editBtn = e.target.closest('.btn-editar-url');
    if (!editBtn) return;

    const row = editBtn.closest('tr');
    const currentName = row?.querySelector('td:nth-child(1)')?.textContent?.trim() || '';
    const currentType = row?.querySelector('td:nth-child(2)')?.textContent?.trim().toLowerCase() || '';
    const currentUrl = editBtn.dataset.url || row?.querySelector('td:nth-child(3)')?.textContent?.trim() || '';

    const html = `
      <div class="mb-2">
        <label class="form-label">Nombre</label>
        <input id="swal-name" class="form-control" type="text" value="${currentName}">
      </div>
      <div class="mb-2">
        <label class="form-label">Tipo</label>
        <select id="swal-type" class="form-select">
          <option value="web" ${currentType==='web'?'selected':''}>WEB</option>
          <option value="api" ${currentType==='api'?'selected':''}>API</option>
          <option value="ftp" ${currentType==='ftp'?'selected':''}>FTP</option>
          <option value="bd" ${currentType==='bd'?'selected':''}>Base de Datos</option>
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label">Nueva URL</label>
        <input id="swal-url" class="form-control" type="url" value="${currentUrl}">
      </div>
    `;

    const res = await (window.Swal ? Swal.fire({
      title: 'Editar servidor',
      html,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Guardar',
      cancelButtonText: 'Cancelar',
      preConfirm: () => {
        return {
          name: document.getElementById('swal-name')?.value?.trim() || '',
          type: (document.getElementById('swal-type')?.value?.trim() || '').toLowerCase(),
          new_url: document.getElementById('swal-url')?.value?.trim() || ''
        };
      }
    }) : Promise.resolve({ isConfirmed: true, value: { name: currentName, type: currentType, new_url: currentUrl } }));

    if (!res.isConfirmed) return;
    const payload = { url: currentUrl };
    if (res.value.name) payload.name = res.value.name;
    if (res.value.type) payload.type = res.value.type;
    if (res.value.new_url && res.value.new_url !== currentUrl) payload.new_url = res.value.new_url;

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
      const data = await resp.json().catch(() => null);
      if (resp.ok && data && data.ok) {
        if (window.Swal) await Swal.fire('Guardado', 'Servidor actualizado', 'success');
        window.location.reload();
      } else {
        if (window.Swal) Swal.fire('Error', (data && data.error) ? data.error : `Error ${resp.status}`, 'error');
      }
    } catch (err) {
      if (window.Swal) Swal.fire('Error', err?.message || 'Ocurrió un error al editar', 'error');
    }
  });
});
