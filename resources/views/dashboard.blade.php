@extends('layouts.app')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="align-self-center">
            <h2>Lista de Servidores</h2>
        </div>

        <div class="d-flex align-items-center">
            <div class="dataTables_filter_container me-3"></div>

            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAgregarServidor">
                <i class="bi bi-plus-lg"></i>
            </button>


            <button id="btnEnviarAlertas" class="btn btn-outline-primary ms-2" title="Enviar alertas de páginas caídas">
                <i class="bi bi-check2-circle"></i>
            </button>
        </div>
    </div>

    <div class="dataTables_length_container mb-2"></div>
    
    <table id="serverTable" class="display">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>URL</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servers as $server)
            <tr>
                <td>{{ $server->name }}</td>
                <td>{{ strtoupper($server->type) }}</td>
                <td>{{ $server->url }}</td>
                <td>
                    @if($server->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-danger">Inactivo</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{ route('detalles', ['tipo' => $server->type]) }}?url={{ urlencode($server->url) }}" 
                       class="btn btn-sm btn-info" title="Ver detalles">
                        <i class="bi bi-eye"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-editar-url" data-url="{{ $server->url ?? '' }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-eliminar-url" data-url="{{ $server->url }}" title="Eliminar URL">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Modal: Añadir Servidor -->
<div class="modal fade" id="modalAgregarServidor" tabindex="-1" aria-labelledby="modalAgregarServidorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalAgregarServidorLabel">Añadir Servidor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formAgregarServidor">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nombre del Servidor</label>
                        <input type="text" class="form-control" id="nombreServidor" placeholder="Servidor Web Principal">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL del Servidor</label>
                        <input type="url" class="form-control" id="urlServidor" placeholder="https://example.com">
                        <div class="form-text">Ingrese una URL válida (ejemplo: https://example.com)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Servidor</label>
                        <select class="form-select" id="tipoServidor">
                            <option value="web">WEB</option>
                            <option value="bd">Base de Datos</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionServidor" rows="3" placeholder="Descripción del servidor"></textarea>
                    </div>

                </form>
            </div>

            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnAñadir" class="btn btn-success">Añadir</button>
            </div>

        </div>
    </div>
</div>


    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info small">
            @if($servers->count())
                Mostrando {{ $servers->total() }} registros
            @else
                No hay registros para mostrar
            @endif  
        </div>
        <div>
            {{ $servers->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="{{ asset('js/servers.js') }}?v={{ filemtime(public_path('js/servers.js')) }}"></script>

@endsection
