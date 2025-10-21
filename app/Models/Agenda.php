<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';
    protected $primaryKey = 'id_agenda';
    protected $guarded = [];

    /**
     * ✅ Kolom yang bisa diisi mass-assignment
     */
    protected $fillable = [
        'agenda_name',
        'description',
        'person_in_charge',
        'date',
        'start_time',
        'end_time',
        'location',
        'involved_institution',
        'status',
        'is_public',
        'id_user',
        'approved_by',
        'id_unit',
    ];

    /**
     * ✅ Konversi otomatis tipe data tanggal & jam
     * Agar mudah diformat dan diolah di Blade dengan Carbon.
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * ✅ Relasi ke user (pembuat agenda)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * ✅ Relasi ke user yang menyetujui (admin)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }
}
