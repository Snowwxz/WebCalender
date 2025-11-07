<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';
    protected $primaryKey = 'id_unit';
    protected $fillable = [
        'unit_name',
        'address',
        'email',
        'password',
        'role',
    ];

    protected $hidden = ['password'];

    // Relasi ke user (One to Many)
    public function users()
    {
        return $this->hasMany(User::class, 'id_unit', 'id_unit');
    }

    // Relasi ke agenda (One to Many)
    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'id_unit', 'id_unit');
    }
}
