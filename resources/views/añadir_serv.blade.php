@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/dark.css') }}">

<div class="container mt-5 d-flex justify-content-center">
    <div class="card p-4 shadow-sm" style="max-width: 600px; width: 100%;">
        <h5 class="mb-4 text-center fw-bold">Añadir Servidor</h5>

        <form id="formAgregarServidor">
            @csrf


            <div class="mb-3">
                <label for="nombreServidor" class="form-label">Nombre del Servidor</label>
                <input type="text" class="form-control" id="nombreServidor" placeholder="Servidor Web Principal">
            </div>

            <div class="mb-3">
                <label for="urlServidor" class="form-label">URL del Servidor</label>
                <input type="url" class="form-control" id="urlServidor" placeholder="https://example.com">
                <div class="form-text">Ingrese una URL válida (ejemplo: https://example.com)</div>
            </div>
            
            <div class="mb-4">
                <label for="tipoServidor" class="form-label">Tipo de Servidor</label>
                <select class="form-select" id="tipoServidor">
                    <option value="WEB">WEB</option>
                    <option value="bd">Base de Datos</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="descripcionServidor" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcionServidor" rows="3" placeholder="Descripción del servidor"></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" id="btnCancelar" class="btn btn-outline-secondary">Cancelar</button>
                <button type="button" id="btnAñadir" class="btn btn-success">Añadir</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/inputFiltering.js') }}"></script>

@endsection
