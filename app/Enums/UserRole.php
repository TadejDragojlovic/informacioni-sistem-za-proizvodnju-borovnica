<?php

namespace App\Enums;

enum UserRole: string
{
    case KUPAC = 'kupac';
    case ZAPOSLENI = 'zaposleni';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::KUPAC => 'Kupac',
            self::ZAPOSLENI => 'Zaposleni',
            self::ADMIN => 'Administrator',
        };
    }
}
