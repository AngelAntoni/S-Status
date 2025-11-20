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

            <a href="/añadir_serv" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-plus-lg"></i>
            </a>

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

<script src="{{ asset('js/servers.js') }}"></script>

@endsection
