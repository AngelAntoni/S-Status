<!doctype html>
<html lang="en" data-theme="light">

<head>
    <!-- CSS personalizados -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/servidor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark.css') }}?v={{ filemtime(public_path('css/dark.css')) }}">



    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inicio De Sesion</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @stack('styles')

    <style>
      html, body {
        height: 100%;
        margin: 0;
      }

      body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }

      main {
        flex: 1;
      }

      html {
        scroll-behavior: smooth;
      }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-name" content="{{ config('app.name') }}">
</head>
<body>
<header class="mb-3">
  <nav class="navbar navbar-expand-lg shadow-sm py-1">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <div class="navbar-brand d-flex align-items-center">
          <img src="{{ asset('img/STATUS.png') }}" alt="Logo" width="180" height="90" class="me-1">
        </div>

        <button id="darkModeToggle" class="btn btn-outline-light ms-2">
        <i class="bi bi-moon-stars-fill"></i>
        </button>

        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral">
          <i class="bi bi-list"></i>
        </button>
      </div>
      @auth
      <div class="d-flex align-items-center">
        <form method="POST" action="{{ route('logout') }}" class="mb-0">
          @csrf
          <button type="submit" class="btn btn-outline-light" title="Cerrar sesión">
            <i class="bi bi-box-arrow-right"></i>
          </button>
        </form>
      </div>
      @endauth
    </div>
  </nav>
</header>


    <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="menuLateral" aria-labelledby="menuLateralLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="menuLateralLabel">Menú</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="list-unstyled">
          <li><a href="/hub" class="text-white d-block py-2 text-decoration-none"><i class="bi bi-house-door-fill me-2"></i>Inicio</a></li>
          <li><a href="/dashboard" class="text-white d-block py-2 text-decoration-none"><i class="bi bi-graph-up me-2"></i>Dashboard</a></li>
        </ul>
      </div>
    </div>

    <main class="container my-4">
      @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <footer class="py-2 text-start border-top mt-3">
      <div class="container d-flex justify-content-between">
        <div>18/06/2025 Angel Antonio CS</div>
        <div>Contacto: Angel@atura.mx</div>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/serverTable.js') }}"></script>
    <script src="{{ asset('js/darkmode.js') }}"></script>

    @yield('scripts') 

</body>
</html>


