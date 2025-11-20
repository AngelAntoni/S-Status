@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modal.css') }}">
<link rel="stylesheet" href="{{ asset('css/detalles-servidor.css') }}">
@endpush

@section('content')

<header class="print-header d-none">
  Detalles del Servidor
</header>

<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h3 class="mb-0">Detalles del Servidor</h3>
    <div class="d-flex gap-2">
      <a href="{{ route('dashboard') }}" class="btn btn-primary d-inline-flex align-items-center btn-volver">Volver</a>
      <button class="btn btn-primary" onclick="window.print()">Imprimir PDF</button>
    </div>
  </div>

  <div class="card detalles-card">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Tipo del Servidor</label>
          <div>{{ $server->type ?? '' }}</div>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">URL</label>
          @if(!empty($server->url))
            <div class="url-container">
              <a href="{{ $server->url }}" target="_blank" rel="noopener">{{ $server->url }}</a>
            </div>
          @endif
        </div>
      </div>

      <hr>

      <h5 class="mb-3 d-flex justify-content-between align-items-center">
        Información de Reportes
        <div class="d-flex gap-2">
          <button id="btnEliminarSeleccionados" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-trash"></i> Eliminar seleccionados
          </button>
        </div>
      </h5>

      <div class="table-responsive">
        <table class="table table-striped align-middle text-center">
          <thead>
            <tr>
              <th>Servidor/nombre</th>
              <th>Fecha/hora</th>
              <th>Error</th>
              <th>Seleccionar</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($reportes ?? []) as $reporte)
              <tr data-reporte-id="{{ $reporte->id }}">
                <td><div class="server-name">{{ $reporte->servidor_nombre }}</div></td>
                <td>
                  <div class="datetime-info">
                    <div class="date">{{ ($reporte->fecha ?? '') !== 'N/A' ? ($reporte->fecha ?? '') : '' }}</div>
                    <div class="time">{{ ($reporte->hora ?? '') !== 'N/A' ? ($reporte->hora ?? '') : '' }}</div>
                    <div class="duration">{{ ($reporte->duracion ?? '') !== 'N/A' ? ($reporte->duracion ?? '') : '' }}</div>
                  </div>
                </td>
                <td>
                  <span class="status-pill status-error">
                    {{ $reporte->error_descripcion }}
                  </span>
                </td>
                <td>
                  <input type="checkbox" class="chk-reporte" value="{{ $reporte->id }}">
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-muted">Sin datos de reportes para mostrar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles del Reporte</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-start">
        <p><strong>Nombre del servidor:</strong> <span id="modal-servidor"></span></p>
        <p><strong>URL:</strong> <a href="#" target="_blank" id="modal-url"></a></p>
        <p><strong>Fecha:</strong> <span id="modal-fecha"></span></p>
        <p><strong>Hora:</strong> <span id="modal-hora"></span></p>
        <p><strong>Causa:</strong> <span id="modal-causa"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal validación vistas -->
<div class="modal fade" id="validacionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Validación de Vistas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th>Vista</th>
                <th>URL</th>
                <th>Estado</th>
                <th>Código</th>
              </tr>
            </thead>
            <tbody id="tablaVistas"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/modalInfo.js') }}"></script>
<script src="{{ asset('js/detalles-servidor.js') }}"></script>
@endsection
