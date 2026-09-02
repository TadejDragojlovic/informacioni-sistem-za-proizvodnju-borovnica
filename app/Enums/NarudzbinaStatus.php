<?php

namespace App\Enums;

enum NarudzbinaStatus: string
{
    case POTVRDJENA = 'potvrdjena';
    case OTPREMLJENA = 'otpremljena';
    case OTKAZANA = 'otkazana';

    public function label(): string
    {
        return match ($this) {
            self::POTVRDJENA => 'Potvrđena',
            self::OTPREMLJENA => 'Otpremljena',
            self::OTKAZANA => 'Otkazana',
        };
    }
}
