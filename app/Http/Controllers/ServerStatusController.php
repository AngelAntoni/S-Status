<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\ServerStatus;
use Illuminate\Support\Facades\Auth;

class ServerStatusController extends Controller
{
    public function store(Request $request)
    {
        // ... existing code ...
        $validated = $request->validate([
            'url' => 'required|string',
            'status' => 'required|in:online,offline,maintenance,error',
            'response_time' => 'nullable|integer',
            'http_status_code' => 'nullable|integer',
            'error_message' => 'nullable|string'
        ]);

        // Buscar el servidor por URL (tolerante con la barra final)
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

        // Guardar estado
        ServerStatus::create([
            'server_id' => $server->id,
            'status' => $validated['status'],
            'response_time' => $validated['response_time'] ?? null,
            'http_status_code' => $validated['http_status_code'] ?? null,
            'error_message' => $validated['error_message'] ?? null,
            'checked_at' => now()
        ]);

        return response()->json(['ok' => true]);
        // ... existing code ...
    }
}