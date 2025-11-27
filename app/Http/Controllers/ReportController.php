<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|string',
            'http_status_code' => 'nullable|integer',
            'mensaje' => 'nullable|string',
            'error_description' => 'nullable|string',
        ]);

        // Buscar servidor por URL (tolerante con la barra final)
        $urlInput = rtrim($validated['url'], '/');
        $server = Server::where('user_id', Auth::id())
            ->where('url', $validated['url'])
            ->orWhere('url', $urlInput)
            ->orWhere('url', $urlInput . '/')
            ->first();

        if (!$server) {
            return response()->json([
                'ok' => false,
                'error' => 'Servidor no encontrado para la URL proporcionada'
            ], 404);
        }

        // Armar descripción de error
        $codigo = $validated['http_status_code'] ?? null;
        $mensaje = $validated['mensaje'] ?? null;
        $descripcion = $validated['error_description']
            ?? ($codigo ? "HTTP {$codigo}: " . ($mensaje ?? 'Error') : ($mensaje ?? 'Página caída'));

        Report::create([
            'server_id' => $server->id,
            'incident_at' => now(),
            'duration_minutes' => null,
            'error_description' => $descripcion,
            'resolved_by' => null,
            'status' => 'en_proceso',
            'notes' => $mensaje,
        ]);

        return response()->json(['ok' => true]);
    }

    public function destroy(\App\Models\Report $report)
    {
        $report->delete();
        return response()->json(['ok' => true]);
    }

    public function destroyMultiple(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        \App\Models\Report::whereIn('id', $validated['ids'])->delete();

        return response()->json(['ok' => true, 'deleted' => count($validated['ids'])]);
    }
}