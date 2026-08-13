<?php

namespace App\Models;

use App\Enums\LotDogadjajTip;
use App\Enums\LotStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotDogadjaj extends Model
{
    use HasFactory;

    protected $table = 'lot_dogadjajs';

    protected $fillable = [
        'lot_id',
        'lot_raspodela_id',
        'tip',
        'kolicina_g',
        'vreme_dogadjaja',
        'evidentirao_user_id',
        'prethodni_status',
        'novi_status',
        'prethodna_skladisna_lokacija_id',
        'nova_skladisna_lokacija_id',
        'razlog',
    ];

    protected function casts(): array
    {
        return [
            'lot_id' => 'integer',
            'lot_raspodela_id' => 'integer',
            'tip' => LotDogadjajTip::class,
            'kolicina_g' => 'integer',
            'vreme_dogadjaja' => 'datetime',
            'evidentirao_user_id' => 'integer',
            'prethodni_status' => LotStatus::class,
            'novi_status' => LotStatus::class,
            'prethodna_skladisna_lokacija_id' => 'integer',
            'nova_skladisna_lokacija_id' => 'integer',
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }

    public function raspodela(): BelongsTo
    {
        return $this->belongsTo(LotRaspodela::class, 'lot_raspodela_id');
    }

    public function evidentiraoUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evidentirao_user_id');
    }

    public function prethodnaSkladisnaLokacija(): BelongsTo
    {
        return $this->belongsTo(SkladisnaLokacija::class, 'prethodna_skladisna_lokacija_id');
    }

    public function novaSkladisnaLokacija(): BelongsTo
    {
        return $this->belongsTo(SkladisnaLokacija::class, 'nova_skladisna_lokacija_id');
    }
}
