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

    // Relasi ke user (Many to One)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi ke user yang menyetujui (optional)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }
}

