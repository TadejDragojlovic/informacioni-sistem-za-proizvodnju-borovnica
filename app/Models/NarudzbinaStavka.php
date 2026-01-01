<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NarudzbinaStavka extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'narudzbina_id',
        'proizvod_id',
        'kolicina',
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
            'narudzbina_id' => 'integer',
            'proizvod_id' => 'integer',
        ];
    }

    public function narudzbina(): BelongsTo
    {
        return $this->belongsTo(Narudzbina::class);
    }

    public function proizvod(): BelongsTo
    {
        return $this->belongsTo(Proizvod::class, 'proizvod_id');
    }
}
