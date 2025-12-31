<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resurs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'naziv',
        'kolicina',
        'trosak',
        'proizvod_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'kolicina' => 'decimal:2',
            'trosak' => 'decimal:2',
            'proizvod_id' => 'integer',
        ];
    }

    public function proizvod(): BelongsTo
    {
        return $this->belongsTo(Proizvod::class);
    }
}
