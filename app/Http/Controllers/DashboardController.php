<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $perPage = (int) request()->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $servers = Server::where('is_active', true)
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard', compact('servers', 'perPage'));
    }
}
