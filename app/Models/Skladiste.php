<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skladiste extends Model
{
    use HasFactory;

    protected $table = 'skladistes';

    protected $fillable = [
        'naziv',
        'lokacija',
        'mesecni_trosak',
        'aktivan',
    ];

    protected function casts(): array
    {
        return [
            'mesecni_trosak' => 'decimal:2',
            'aktivan' => 'boolean',
        ];
    }

    public function skladisneLokacije(): HasMany
    {
        return $this->hasMany(SkladisnaLokacija::class, 'skladiste_id');
    }
}
