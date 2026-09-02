<?php

namespace App\Enums;

enum LotDogadjajTip: string
{
    case LOT_KREIRAN = 'lot_kreiran';
    case PRIJEM_U_SKLADISTE = 'prijem_u_skladiste';
    case KLASA_KVALITETA_DODELJENA = 'klasa_kvaliteta_dodeljena';
    case PREMESTANJE = 'premestanje';
    case ODOBREN_ZA_PRODAJU = 'odobren_za_prodaju';
    case KOLICINA_REZERVISANA = 'kolicina_rezervisana';
    case REZERVACIJA_OSLOBODJENA = 'rezervacija_oslobodjena';
    case KOLICINA_IZDATA = 'kolicina_izdata';
    case KOREKCIJA_KOLICINE = 'korekcija_kolicine';
    case LOT_BLOKIRAN = 'lot_blokiran';
    case LOT_ODBLOKIRAN = 'lot_odblokiran';
    case LOT_POVUCEN = 'lot_povucen';

    public function label(): string
    {
        return match ($this) {
            self::LOT_KREIRAN => 'Lot kreiran',
            self::PRIJEM_U_SKLADISTE => 'Prijem u skladište',
            self::KLASA_KVALITETA_DODELJENA => 'Dodeljena klasa kvaliteta',
            self::PREMESTANJE => 'Premeštanje lota',
            self::ODOBREN_ZA_PRODAJU => 'Odobren za prodaju',
            self::KOLICINA_REZERVISANA => 'Količina rezervisana',
            self::REZERVACIJA_OSLOBODJENA => 'Rezervacija oslobođena',
            self::KOLICINA_IZDATA => 'Količina izdata',
            self::KOREKCIJA_KOLICINE => 'Korekcija količine',
            self::LOT_BLOKIRAN => 'Lot blokiran',
            self::LOT_ODBLOKIRAN => 'Lot odblokiran',
            self::LOT_POVUCEN => 'Lot povučen',
        };
    }
}
