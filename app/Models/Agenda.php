<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupUnit;

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
        'date',
        'start_time',
        'end_time',
        'location',
        'status',
        'is_public',
        'id_user',
        'approved_by',
        'id_unit',
        'notes',
        'reason',
    ];

    /**
     * ✅ Konversi otomatis tipe data tanggal & jam
     * Agar mudah diformat dan diolah di Blade dengan Carbon.
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
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

    // ini relasikan ke agenda_log buat terdaftar
    public function logs()
    {
        return $this->hasMany(AgendaLog::class, 'agenda_id');
    }

    //ini berfungsi dibagian untuk relasikan ke invitation
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'id_agenda', 'id_agenda');
    }

    /**
     * Get all units invited to this agenda
     */
    public function getInvitedUnitsAttribute()
    {
        $units = collect();
        foreach ($this->invitations as $invitation) {
            $groupUnits = GroupUnit::where('id_group', $invitation->id_group)
                ->with('unit')
                ->get();
            foreach ($groupUnits as $groupUnit) {
                if ($groupUnit->unit) {
                    $units->push($groupUnit->unit);
                }
            }
        }
        return $units->unique('id_unit')->values();
    }

    /**
     * Get invited unit names as comma-separated string (for backward compatibility)
     * Now includes session information if available
     */
    public function getInvitedUnitNamesAttribute()
    {
        // Use already loaded relations if available, otherwise load them
        $invitations = $this->relationLoaded('invitations') 
            ? $this->invitations 
            : $this->invitations()->with(['groupUnits.unit'])->get();
        
        if ($invitations->isEmpty()) {
            return '';
        }

        $sessionGroups = [];
        $hasAnySession = false;
        
        foreach ($invitations as $invitation) {
            // Ensure groupUnits are loaded
            if (!$invitation->relationLoaded('groupUnits')) {
                $invitation->load('groupUnits.unit');
            }
            
            $units = [];
            foreach ($invitation->groupUnits as $groupUnit) {
                // Ensure unit is loaded
                if (!$groupUnit->relationLoaded('unit')) {
                    $groupUnit->load('unit');
                }
                
                if ($groupUnit->unit) {
                    $units[] = $groupUnit->unit->unit_name;
                }
            }
            
            if (!empty($units)) {
                // Jika ada session_name, gunakan format dengan session
                if (!empty($invitation->session_name)) {
                    $hasAnySession = true;
                    $sessionGroups[] = 'Sesi ' . $invitation->session_name . '= ' . implode(', ', $units);
                } else {
                    // Jika tidak ada session_name, tambahkan ke array tanpa prefix
                    $sessionGroups[] = implode(', ', $units);
                }
            }
        }
        
        // Jika tidak ada session sama sekali, kembalikan format lama (backward compatibility)
        if (empty($sessionGroups)) {
            return $this->invitedUnits->pluck('unit_name')->implode(', ');
        }
        
        // Jika ada setidaknya satu session, gabungkan dengan line break untuk memisahkan session
        // Jika tidak ada session sama sekali, gabungkan dengan ', '
        if ($hasAnySession) {
            return implode("\n", $sessionGroups);
        } else {
            // Semua tanpa session, gabungkan dengan koma
            return implode(', ', $sessionGroups);
        }
    }
}
