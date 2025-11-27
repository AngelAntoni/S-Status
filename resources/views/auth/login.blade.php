@extends('layouts.app1')
@section('content')

<div class="container d-flex justify-content-center mt-5">
  <div class="card p-4 shadow-sm" style="max-width: 420px; width: 100%;">
    <h5 class="mb-3 fw-bold text-center">Iniciar sesión</h5>

    <form method="POST" action="{{ route('login.post') }}">
      @csrf

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

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember">Recordarme</label>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <a href="/" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Acceder</button>
      </div>

      <div class="text-center mt-3">
        <a href="{{ route('register') }}" class="text-decoration-none">Crear cuenta</a>
      </div>
    </form>
  </div>
</div>

@endsection