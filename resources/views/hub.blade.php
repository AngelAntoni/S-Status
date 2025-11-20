@extends('layouts.app1')

@section('content')

<link rel="stylesheet" href="{{ asset('css/hub.css') }}">

<div class="container text-center my-3 hub-container">
    <h2 class="mb-3 title">Bienvenido a las Fases de S-Status</h2>

    <div class="row justify-content-center hub-cards">

        <div class="col-md-3 col-sm-4 mb-3">
            <a href="/dashboard" class="text-decoration-none">
                <div class="card custom-card">
                    <div class="card-body">
                        <i class="bi bi-bar-chart-line-fill icon"></i>
                        <h5 class="card-title">Dashboard</h5>
                        <p class="card-text"></p>
                    </div>
                </div>
            </a>
        </div>
        
    </div>
</div>

<script>
    (function() {
        if (document && document.body) {
            document.body.classList.add('hub-alt-noscroll', 'hub-alt-footer-fixed');
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('hub-alt-noscroll', 'hub-alt-footer-fixed');
            });
        }
    })();
</script>

@endsection
