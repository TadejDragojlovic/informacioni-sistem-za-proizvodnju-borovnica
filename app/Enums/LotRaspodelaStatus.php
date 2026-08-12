<?php

namespace App\Enums;

enum LotRaspodelaStatus: string
{
    case REZERVISANO = 'rezervisano';
    case IZDATO = 'izdato';
    case OTKAZANO = 'otkazano';
}
