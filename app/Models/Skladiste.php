<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skladiste extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lokacija',
        'kapacitet',
        'temperatura',
        'trosak',
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
            'temperatura' => 'decimal:2',
            'trosak' => 'decimal:2',
        ];
    }

    public function proizvods(): HasMany
    {
        return $this->hasMany(Proizvod::class);
    }
}
