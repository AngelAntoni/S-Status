<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Models\Server;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\ServerStatusController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('splash');
});

Route::get('/hub', function () {
    return view('hub');
})->name('hub');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/añadir_serv', function () {
    return view('añadir_serv');
})->name('añadir_serv');


Route::get('/servidor/{id}/detalles', [ServerController::class, 'detalles'])->name('servidor.detalles');

Route::post('/add-server', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string',
        'url' => 'required|url',
        'type' => 'required|string|in:web,api,ftp,bd',
        'description' => 'nullable|string'
    ]);

    $server = new Server();
    $server->name = $validated['name'];
    $server->url = $validated['url'];
    $server->type = strtolower($validated['type']);
    $server->description = $validated['description'] ?? null;
    $server->is_active = true;
    $server->user_id = \Illuminate\Support\Facades\Auth::id();
    $server->save();

    return response()->json(['ok' => true]);
})->middleware('auth');


Route::post('/enviarSlack', [SlackController::class, 'enviar']);
Route::post('/verificar', [UrlController::class, 'verificar']);
Route::post('/verificar-url', [UrlController::class, 'verificarUrl']);
Route::post('/descubrir-y-validar-vistas', [UrlController::class, 'descubrirYValidarVistas']);
Route::post('/guardar-status', [ServerStatusController::class, 'store'])->middleware('auth');
Route::post('/guardar-reporte', [ReportController::class, 'store'])->middleware('auth');
Route::delete('/reportes/{report}', [ReportController::class, 'destroy'])->name('reportes.destroy');
Route::post('/reportes/eliminar-multiples', [ReportController::class, 'destroyMultiple'])->name('reportes.destroyMultiple');

Route::delete('/servidores', [ServerController::class, 'destroyByUrl'])->middleware('auth')->name('servidores.destroyByUrl');

Route::put('/servidores', [ServerController::class, 'updateByUrl'])->middleware('auth')->name('servidores.updateByUrl');


Route::get('/detalles/{tipo}', [ServerController::class, 'detalles'])
    ->middleware('auth')
    ->name('detalles');

Route::get('/db-health', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return response()->json([
            'ok' => true,
            'connection' => \Illuminate\Support\Facades\DB::getDatabaseName(),
            'servers' => \App\Models\Server::count(),
            'server_status' => \App\Models\ServerStatus::count(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});
