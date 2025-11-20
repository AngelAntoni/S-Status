<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SlackController extends Controller
{
    public function enviar(Request $request)
    {
        $mensaje = $request->input('mensaje', 'Sin mensaje');

        $webhookUrl = env('SLACK_WEBHOOK_URL');

        try {
            $response = Http::asJson()->withoutVerifying()->post($webhookUrl, [
                'text' => $mensaje
            ]);

            if ($response->successful()) {
                return response()->json(['ok' => true]);
            } else {
                return response()->json([
                    'ok' => false,
                    'error' => 'Slack respondió con error ' . $response->status(),
                    'body' => $response->body()
                ], 500);
            }
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
