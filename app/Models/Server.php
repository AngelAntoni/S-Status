<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'type',
        'description',
        'is_active',
        'last_checked'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked' => 'datetime'
    ];

    // Relaciones
    public function reports()
    {
        return $this->hasMany(Report::class, 'server_id');
    }

    public function statuses()
    {
        return $this->hasMany(ServerStatus::class);
    }

    public function latestStatus()
    {
        return $this->hasOne(ServerStatus::class)->latest('checked_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
