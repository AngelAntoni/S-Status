<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Models\Server;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\ServerStatusController;
use App\Http\Controllers\ReportController;


Route::get('/', function () {
    return view('splash'); 
});

Route::get('/hub', function () {
    return view('hub');
})->name('hub');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/añadir_serv', function () {
    return view('añadir_serv');
})->name('añadir_serv');


Route::get('/servidor/{id}/detalles', [ServerController::class, 'detalles'])->name('servidor.detalles');

Route::post('/add-server', function (Request $request) {
    $server = new Server();
    $server->name = $request->name;
    $server->url = $request->url;
    $server->type = $request->type;
    $server->description = $request->description;
    $server->is_active = true;
    $server->save();
    
    return response()->json(['success' => true]);
});


Route::post('/enviarSlack', [SlackController::class, 'enviar']);
Route::post('/verificar', [UrlController::class, 'verificar']);
Route::post('/verificar-url', [UrlController::class, 'verificarUrl']);
Route::post('/descubrir-y-validar-vistas', [UrlController::class, 'descubrirYValidarVistas']);
Route::post('/guardar-status', [ServerStatusController::class, 'store']);
Route::post('/guardar-reporte', [ReportController::class, 'store']);
Route::delete('/reportes/{report}', [ReportController::class, 'destroy'])->name('reportes.destroy');
Route::post('/reportes/eliminar-multiples', [ReportController::class, 'destroyMultiple'])->name('reportes.destroyMultiple');

Route::delete('/servidores', [ServerController::class, 'destroyByUrl'])->name('servidores.destroyByUrl');

Route::put('/servidores', [ServerController::class, 'updateByUrl'])->name('servidores.updateByUrl');


Route::get('/detalles/{tipo}', [ServerController::class, 'detalles'])
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
