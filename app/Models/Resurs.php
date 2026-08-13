<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resurs extends Model
{
    use HasFactory;

    protected $table = 'resurs';

    protected $fillable = [
        'lot_id',
        'naziv',
        'kolicina',
        'jedinica_mere',
        'cena_po_jedinici',
        'datum_upotrebe',
        'evidentirao_user_id',
    ];

    protected function casts(): array
    {
        return [
            'lot_id' => 'integer',
            'kolicina' => 'decimal:2',
            'cena_po_jedinici' => 'decimal:2',
            'datum_upotrebe' => 'date',
            'evidentirao_user_id' => 'integer',
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }

    public function evidentiraoUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evidentirao_user_id');
    }
}
