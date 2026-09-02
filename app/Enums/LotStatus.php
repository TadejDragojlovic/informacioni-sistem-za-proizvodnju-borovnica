<?php

namespace App\Enums;

enum LotStatus: string
{
    case KREIRAN = 'kreiran';
    case USKLADISTEN = 'uskladisten';
    case RASPOLOZIV = 'raspoloziv';
    case BLOKIRAN = 'blokiran';
    case ISCRPLJEN = 'iscrpljen';
    case POVUCEN = 'povucen';

    public function label(): string
    {
        return match ($this) {
            self::KREIRAN => 'Kreiran',
            self::USKLADISTEN => 'Uskladišten',
            self::RASPOLOZIV => 'Raspoloživ',
            self::BLOKIRAN => 'Blokiran',
            self::ISCRPLJEN => 'Iscrpljen',
            self::POVUCEN => 'Povučen',
        };
    }
}
