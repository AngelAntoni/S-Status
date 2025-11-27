<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServerController extends Controller
{
    public function detalles($tipo)
    {
        // Tomar la URL desde query string y decodificarla
        $url = urldecode(request()->query('url'));

        // Buscar el servidor por tipo y URL
        $servidor = Server::where('type', $tipo)
            ->where('user_id', Auth::id())
            ->where('url', $url)
            ->firstOrFail();

        // Obtener reportes relacionados y mapear a stdClass
        $reportes = Report::where('server_id', $servidor->id)
            ->orderBy('incident_at', 'desc')
            ->get()
            ->map(function($reporte) use ($servidor) {
                return (object)[
                    'id'                => $reporte->id,
                    'servidor_nombre'   => $servidor->name ?? 'N/A',
                    'fecha'             => $reporte->incident_at ? $reporte->incident_at->format('Y-m-d') : 'N/A',
                    'hora'              => $reporte->incident_at ? $reporte->incident_at->format('H:i:s') : 'N/A',
                    'duracion'          => $reporte->duration_minutes ? $reporte->duration_minutes . ' min' : 'N/A',
                    'error_descripcion' => $reporte->error_description ?? 'N/A',
                    'resuelto_por'      => $reporte->resolved_by ?? 'Pendiente'
                ];
            });

        return view('detalles', [
            'server' => $servidor,
            'reportes' => $reportes
        ]);
    }

    // Eliminar servidor por URL (tolerante con barra final)
    public function destroyByUrl(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|string'
        ]);

        $input = rtrim($validated['url'], '/');

        $server = Server::where('user_id', Auth::id())
            ->where('url', $validated['url'])
            ->orWhere('url', $input)
            ->orWhere('url', $input . '/')
            ->first();

        if (!$server) {
            return response()->json(['ok' => false, 'error' => 'Servidor no encontrado'], 404);
        }

        Report::where('server_id', $server->id)->delete();
        $server->delete();

        return response()->json(['ok' => true]);
    }

    // Editar servidor por URL (nombre, tipo, descripción y URL)
    public function updateByUrl(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|string',
            'name' => 'nullable|string',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'new_url' => 'nullable|string',
        ]);

        $input = rtrim($validated['url'], '/');

        $server = Server::where('user_id', Auth::id())
            ->where('url', $validated['url'])
            ->orWhere('url', $input)
            ->orWhere('url', $input . '/')
            ->first();

        if (!$server) {
            return response()->json(['ok' => false, 'error' => 'Servidor no encontrado'], 404);
        }

        if (array_key_exists('name', $validated)) {
            $server->name = $validated['name'];
        }
        if (array_key_exists('type', $validated)) {
            $server->type = $validated['type'];
        }
        if (array_key_exists('description', $validated)) {
            $server->description = $validated['description'];
        }
        if (!empty($validated['new_url'])) {
            $server->url = $validated['new_url'];
        }

        $server->save();

        return response()->json(['ok' => true]);
    }
}
