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
}
