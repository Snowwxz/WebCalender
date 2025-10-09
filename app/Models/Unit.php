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
    ];

    // Relasi ke user (One to Many)
    public function users()
    {
        return $this->hasMany(User::class, 'id_unit', 'id_unit');
    }
}
