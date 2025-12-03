<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $table = 'invitation';
    protected $primaryKey = 'id_invitation';
    protected $guarded = [];

    protected $fillable = [
        'session_name',
        'id_agenda',
        'id_group',
    ];

    /**
     * Relasi ke Agenda
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'id_agenda', 'id_agenda');
    }

    /**
     * Relasi ke GroupUnit (kelompok unit) - mendapatkan semua unit dalam group ini
     */
    public function groupUnits()
    {
        return $this->hasMany(GroupUnit::class, 'id_group', 'id_group');
    }
}
