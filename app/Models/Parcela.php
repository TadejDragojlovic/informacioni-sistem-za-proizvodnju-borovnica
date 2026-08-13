<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parcela extends Model
{
    use HasFactory;

    protected $table = 'parcelas';

    protected $fillable = [
        'oznaka',
        'povrsina_m2',
        'zemlja_porekla',
    ];

    protected function casts(): array
    {
        return [
            'povrsina_m2' => 'integer',
        ];
    }

    public function lotovi(): HasMany
    {
        return $this->hasMany(Lot::class, 'parcela_id');
    }
}
