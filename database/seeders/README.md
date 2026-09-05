# Demo podaci

`DatabaseSeeder` prvo kreira osnovne podatke, a zatim i demo scenario:

- nalozi: `admin@borovnica.com` / `admin`, `zaposleni@borovnica.com` / `zaposleni`, `zaposleni2@borovnica.com` / `zaposleni2`, `kupac@borovnica.com` / `kupac`, `kupac2@borovnica.com` / `kupac2`, `kupac3@borovnica.com` / `kupac3`;
- 8 lotova pokriva statuse `KREIRAN`, `USKLADISTEN`, `RASPOLOZIV`, `BLOKIRAN` i `POVUCEN`;
- 5 narudžbina obuhvata jednu potvrđenu, tri otpremljene i jednu otkazanu narudžbinu;
- raspodele prikazuju rezervaciju, izdavanje i oslobađanje rezervacije;
- događaji lota čuvaju prijem, kvalitet, odobrenje, blokiranje, povlačenje i promene količine;
- svaki lot ima najmanje jedan resurs, a otpremljene narudžbine koriste lotove iz oba skladišta.

Za pun demo finansijskog izveštaja izaberi jul 2026. godine.

Seederi koriste `updateOrCreate`, pa ponovljeno `php artisan db:seed` ne duplira demo zapise.
