# Demo podaci

`DatabaseSeeder` kreira povezan scenario za ručnu proveru aplikacije:

- 6 korisnika: jedan administrator, dva zaposlena i tri kupca;
- 3 sorte, 3 parcele, 2 skladišta i 5 skladišnih lokacija;
- 4 proizvoda i 8 lotova u različitim fazama obrade;
- 5 narudžbina: jedna potvrđena, tri otpremljene i jedna otkazana;
- 7 raspodela koje prikazuju rezervaciju, izdavanje i oslobađanje robe;
- 12 resursa, pri čemu svaki demo lot ima najmanje jedan resurs;
- događaje prijema, kvaliteta, odobrenja, blokiranja, povlačenja i promene količine.

## Nalozi

| Uloga | Email | Lozinka |
|---|---|---|
| Administrator | `admin@borovnica.com` | `admin` |
| Zaposleni | `zaposleni@borovnica.com` | `zaposleni` |
| Zaposleni | `zaposleni2@borovnica.com` | `zaposleni2` |
| Kupac | `kupac@borovnica.com` | `kupac` |
| Kupac | `kupac2@borovnica.com` | `kupac2` |
| Kupac | `kupac3@borovnica.com` | `kupac3` |

Za pun demo finansijskog izveštaja izaberi jul 2026. godine. Otpremljene narudžbine u tom mesecu koriste lotove iz oba skladišta.

Seederi koriste ponovljive upise, pa `php artisan db:seed` ne duplira demo zapise.
