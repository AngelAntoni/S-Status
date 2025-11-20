<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'incident_at',
        'duration_minutes',
        'error_description',
        'resolved_by',
        'status',
        'notes'
    ];

    protected $casts = [
        'incident_at' => 'datetime'
    ];

    // Relación con Server
    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }
}