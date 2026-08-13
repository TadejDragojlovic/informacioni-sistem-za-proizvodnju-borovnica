<?php

namespace App\Enums;

enum UserRole: string
{
    case KUPAC = 'kupac';
    case ZAPOSLENI = 'zaposleni';
    case ADMIN = 'admin';
}
