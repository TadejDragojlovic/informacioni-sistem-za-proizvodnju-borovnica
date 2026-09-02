<?php

namespace App\Enums;

enum KlasaKvaliteta: string
{
    case KLASA_I = 'klasa_i';
    case KLASA_II = 'klasa_ii';

    public function label(): string
    {
        return match ($this) {
            self::KLASA_I => 'Klasa I',
            self::KLASA_II => 'Klasa II',
        };
    }
}
