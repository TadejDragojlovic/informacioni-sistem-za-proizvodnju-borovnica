# Informacioni sistem "Proizvodnja Borovnica"
Ovaj sistem omogućava upravljanje zalihama, resursima i prodajom borovnica uz automatizovano generisanje finansijskih izveštaja.

# Uputstvo za instalaciju
Pratite ove korake kako biste pokrenuli projekat lokalno:

1. Kloniranje projekta
``` sh
git clone https://github.com/tvoj-username/proizvodnja-borovnica-USI.git
cd proizvodnja-borovnica-USI
```

2. Instalacija zavisnosti \
Instalirajte PHP i JavaScript pakete:
``` sh
composer install
npm install
```
<br>

3. Podešavanje okruženja \
Kreirajte .env fajl i generišite ključ aplikacije:
``` sh
cp .env.example .env
php artisan key:generate
```
<br>

4. Migracije i Baza podataka \
Pokrenite migracije sa početnim podacima (seed):
``` sh
# Pokretanje migracija
php artisan migrate --seed
```
<br>

5. Kompajliranje i pokretanje \
Pokrenite Vite (za CSS/JS) i lokalni server:
``` sh
npm run build
php artisan serve
```
<br>

6. Testiranje sistema \
Da biste se uverili da sve radi ispravno (uključujući finansijsku logiku i bezbednost), pokrenite testove:
``` sh
php artisan test
```
<br>