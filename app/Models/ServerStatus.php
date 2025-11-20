<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerStatus extends Model
{
    use HasFactory;

    protected $table = 'server_status';

    protected $fillable = [
        'server_id',
        'status',
        'response_time',
        'http_status_code',
        'error_message',
        'checked_at'
    ];

    protected $casts = [
        'checked_at' => 'datetime'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
