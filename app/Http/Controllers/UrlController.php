<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UrlController extends Controller
{
    /**
     * Verifica el estado de una URL usando cURL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificar(Request $request)
    {
        $url = trim($request->input('url'));

        if (empty($url)) {
            return response()->json([
                'activo' => false,
                'codigo' => null,
                'mensaje' => 'No se proporcionó una URL para verificar.',
                'url' => $url
            ], 400);
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'activo' => false,
                'codigo' => null,
                'mensaje' => 'La URL no tiene un formato válido.',
                'url' => $url
            ], 400);
        }

        // HEAD inicial
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120 Safari/537.36 S-StatusBot/1.0',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
        $error = curl_error($ch) ?: null;
        curl_close($ch);

        // Fallback: muchos sitios bloquean HEAD (403/405) o dan 0; intentar GET mínimo
        if ($response === false || $httpCode === 0 || in_array($httpCode, [403, 405])) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120 Safari/537.36 S-StatusBot/1.0',
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ]);
            curl_setopt($ch, CURLOPT_RANGE, '0-1024');
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
            $error = $error ?: (curl_error($ch) ?: null);
            curl_close($ch);
        }

        $activo = $httpCode >= 200 && $httpCode < 400;

        return response()->json([
            'activo' => $activo,
            'codigo' => $httpCode ?: 'Sin respuesta',
            'mensaje' => $error ?: ($activo ? 'OK' : 'Página caída o inaccesible'),
            'url' => $url
        ]);
    }

    /**
     * Descubre y valida vistas (pages) del sitio sin hardcode.
     */
    public function descubrirYValidarVistas(Request $request)
    {
        $inputUrl = trim($request->input('url', ''));
        if (empty($inputUrl) || !filter_var($inputUrl, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'URL inválida'], 400);
        }

        $parsed = parse_url($inputUrl);
        if (!$parsed || !isset($parsed['scheme'], $parsed['host'])) {
            return response()->json(['error' => 'URL inválida'], 400);
        }

        $base = $parsed['scheme'] . '://' . $parsed['host'];
        $baseWithPath = $base . (isset($parsed['path']) ? rtrim($parsed['path'], '/') : '');

        // Descargar HTML de la página base
        $htmlResp = $this->descargarHtml($inputUrl);
        $html = $htmlResp['html'];

        // Extraer y filtrar rutas tipo "view"
        $rutas = $this->extraerVistasDesdeHtml($html, $base);

        // Siempre incluir home
        array_unshift($rutas, $baseWithPath ?: $base);
        $rutas = array_values(array_unique($rutas));

        $vistas = [];
        $activas = 0;
        $caidas = 0;

        foreach ($rutas as $u) {
            $ver = $this->verificarLigero($u);
            $activo = !$ver['fallo'] && $ver['codigo'] > 0;
            $activo ? $activas++ : $caidas++;

            $vistas[] = [
                'url' => $u,
                'ruta' => parse_url($u, PHP_URL_PATH) ?: '/',
                'codigo' => $ver['codigo'] ?: 'Sin respuesta',
                'activo' => $activo,
            ];
        }

        return response()->json([
            'dominio' => $base,
            'resumen' => [
                'total' => count($vistas),
                'activas' => $activas,
                'caidas' => $caidas,
            ],
            'vistas' => $vistas,
        ]);
    }

    /**
     * GET ligero para obtener HTML inicial.
     */
    private function descargarHtml(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120 Safari/537.36 S-StatusBot/1.0',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        $html = curl_exec($ch) ?: '';
        curl_close($ch);

        return ['html' => $html];
    }

    /**
     * Extrae enlaces internos del HTML y filtra solo rutas de páginas.
     */
    private function extraerVistasDesdeHtml(string $html, string $base): array
    {
        if (empty($html)) return [];

        $host = parse_url($base, PHP_URL_HOST);
        $urls = [];

        // Capturar href en etiquetas <a>
        if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $html, $m)) {
            foreach ($m[1] as $href) {
                if (empty($href)) continue;

                // Normalizar absoluto vs relativo
                if (preg_match('#^https?://#i', $href)) {
                    $u = $href;
                } elseif (strpos($href, '//') === 0) {
                    $u = parse_url($base, PHP_URL_SCHEME) . ':' . $href;
                } else {
                    // relativo
                    $u = rtrim($base, '/') . '/' . ltrim($href, '/');
                }

                // Mismo dominio únicamente
                $h = parse_url($u, PHP_URL_HOST);
                if (!$h || strtolower($h) !== strtolower($host)) continue;

                // Limpiar fragmentos y query redundantes
                $u = preg_replace('/#.*$/', '', $u);

                // Filtrar recursos no-view por extensión y segmentos
                if ($this->esRecursoNoVista($u)) continue;

                $urls[] = $u;
            }
        }

        // Limitar para evitar escaneos masivos
        $urls = array_slice(array_values(array_unique($urls)), 0, 40);

        return $urls;
    }

    /**
     * Decide si un URL apunta a recurso estático o rutas excluidas.
     */
    private function esRecursoNoVista(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $pathLower = strtolower($path);

        $extNoVista = [
            '.css','.js','.map','.json','.xml','.txt','.csv',
            '.png','.jpg','.jpeg','.gif','.webp','.svg','.ico',
            '.pdf','.zip','.rar','.7z','.tar','.gz',
            '.woff','.woff2','.ttf','.eot',
        ];

        foreach ($extNoVista as $e) {
            if (str_ends_with($pathLower, $e)) return true;
        }

        $segmentosExcluidos = [
            '/vendor/','/assets/','/static/','/storage/','/node_modules/',
            '/api/','/wp-includes/','/wp-admin/','/favicon.ico',
        ];

        foreach ($segmentosExcluidos as $seg) {
            if (str_contains($pathLower, $seg)) return true;
        }

        return false;
    }

    /**
     * Verificación ligera: HEAD y fallback a GET mínimo.
     */
    private function verificarLigero(string $url): array
    {
        // HEAD
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120 Safari/537.36 S-StatusBot/1.0',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
        curl_close($ch);

        // Si falla o servidores que bloquean HEAD, intentar GET mínimo
        if ($resp === false || $code === 0 || in_array($code, [403,405])) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120 Safari/537.36 S-StatusBot/1.0',
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ]);
            // Descargar poco para no cargar el sitio
            curl_setopt($ch, CURLOPT_RANGE, '0-1024');
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
            curl_close($ch);
        }

        return [
            'codigo' => $code,
            'fallo' => ($resp === false || $code === 0),
        ];
    }

    public function verificarUrl(Request $request)
    {
        return $this->verificar($request);
    }
}
