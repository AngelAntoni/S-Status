@extends('layouts.app1')
@section('content')

<div class="container d-flex justify-content-center mt-5">
  <div class="card p-4 shadow-sm" style="max-width: 480px; width: 100%;">
    <h5 class="mb-3 fw-bold text-center">Crear cuenta</h5>

    <form method="POST" action="{{ route('register.post') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Tu nombre" required>
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Correo electrónico</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="usuario@ejemplo.com" required>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Repite tu contraseña" required>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Ya tengo cuenta</a>
        <button type="submit" class="btn btn-success">Registrar</button>
      </div>
    </form>
  </div>
</div>

@endsection