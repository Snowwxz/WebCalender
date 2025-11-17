<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaLog extends Model
{
    protected $fillable = [
        'agenda_id',
        'user_id',
        'action',
        'description',
        'old_data',
        'new_data',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
