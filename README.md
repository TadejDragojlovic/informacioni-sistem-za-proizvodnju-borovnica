# Informacioni sistem za proizvodnju i sledljivost borovnica

Laravel aplikacija razvijena za diplomski rad. Sistem povezuje proizvodnju, skladištenje, sledljivost lotova, prodaju i finansijski pregled.

## Glavne funkcionalnosti

- evidencija sorti, parcela, proizvoda, skladišta i skladišnih lokacija;
- praćenje lota od berbe do izdavanja ili povlačenja;
- evidencija kvaliteta, promena količine i korišćenih resursa;
- korpa i narudžbine kupaca;
- FIFO rezervacija raspoloživih lotova;
- otprema i otkazivanje narudžbina;
- mesečni finansijski izveštaj prema stvarnom vremenu otpreme;
- pristup funkcionalnostima prema ulozi korisnika.

## Tehnologije

- PHP 8.3 i Laravel 12;
- MySQL 8.4;
- Blade, Tailwind CSS, Alpine.js i Vite;
- PHPUnit;
- Docker Compose.

## Pokretanje pomoću Dockera

Potrebni su Git i pokrenut Docker Desktop.

```powershell
git clone https://github.com/TadejDragojlovic/informacioni-sistem-za-proizvodnju-borovnica.git
cd informacioni-sistem-za-proizvodnju-borovnica
docker compose up -d --build
```

Pri prvom pokretanju kontejneri instaliraju PHP i JavaScript zavisnosti. Sačekaj da se aplikacioni kontejner pokrene, a zatim pripremi bazu:

```powershell
docker compose exec app php artisan migrate --seed
```

Aplikacija je dostupna na [http://localhost:8000](http://localhost:8000), a phpMyAdmin na [http://localhost:8080](http://localhost:8080). Za phpMyAdmin koristi server `mysql`, korisnika `borovnice` i lozinku `borovnice`.

Kontejnere možeš zaustaviti bez brisanja podataka:

```powershell
docker compose down
```

Za potpuno ponovno kreiranje razvojne baze koristi sledeću komandu samo kada postojeći podaci nisu potrebni:

```powershell
docker compose exec app php artisan migrate:fresh --seed
```

## Demo nalozi

| Uloga | Email | Lozinka |
|---|---|---|
| Administrator | `admin@borovnica.com` | `admin` |
| Zaposleni | `zaposleni@borovnica.com` | `zaposleni` |
| Kupac | `kupac@borovnica.com` | `kupac` |

Svi demo nalozi i pripremljeni podaci opisani su u [dokumentaciji seedera](database/seeders/README.md).

## Provera projekta

```powershell
docker compose exec app php artisan test --exclude-group=mysql-concurrency
docker compose exec app ./vendor/bin/pint --test
docker compose exec node npm run build
```

Konkurentni MySQL test i smoke-test koraci opisani su u [TESTIRANJE.md](TESTIRANJE.md). Uloge i glavni poslovni tokovi opisani su u [docs/FUNKCIONALNOSTI.md](docs/FUNKCIONALNOSTI.md).
