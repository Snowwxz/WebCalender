<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUnit extends Model
{
    use HasFactory;

    protected $table = 'group_units';
    protected $primaryKey = 'id_group';
    protected $guarded = [];

    protected $fillable = [
        'id_unit',
    ];

    /**
     * Relasi ke Unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    /**
     * Relasi ke Invitation (satu group bisa dipakai banyak sesi / agenda)
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'id_group', 'id_group');
    }
}
