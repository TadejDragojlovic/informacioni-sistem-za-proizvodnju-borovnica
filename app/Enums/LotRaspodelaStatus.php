<?php

namespace App\Enums;

enum LotRaspodelaStatus: string
{
    case REZERVISANO = 'rezervisano';
    case IZDATO = 'izdato';
    case OTKAZANO = 'otkazano';

    public function label(): string
    {
        return match ($this) {
            self::REZERVISANO => 'Rezervisano',
            self::IZDATO => 'Izdato',
            self::OTKAZANO => 'Otkazano',
        };
    }
}
