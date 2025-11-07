<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'username',
        'password',
        'remember_token',
        'role',
        'id_unit',
        'contact',
        'last_seen_approve_at',
        'last_seen_notification_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_approve_at' => 'datetime',
        'last_seen_notification_at' => 'datetime',
    ];

    // 🔗 Relasi ke tabel Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    // 🔗 Relasi ke tabel Agenda
    public function agenda()
    {
        return $this->hasMany(Agenda::class, 'id_user', 'id_user');
    }

    // 🔗 Relasi ke agenda yang disetujui (optional)
    public function approvedAgenda()
    {
        return $this->hasMany(Agenda::class, 'approved_by', 'id_user');
    }
}
