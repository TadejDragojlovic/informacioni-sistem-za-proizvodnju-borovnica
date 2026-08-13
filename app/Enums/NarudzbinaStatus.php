<?php

namespace App\Enums;

enum NarudzbinaStatus: string
{
    case POTVRDJENA = 'potvrdjena';
    case OTPREMLJENA = 'otpremljena';
    case OTKAZANA = 'otkazana';
}
