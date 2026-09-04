# Cheatsheet za testove

## Standardni testovi

```powershell
docker compose exec app php artisan test --exclude-group=mysql-concurrency
```

Standardni testovi koriste SQLite bazu u memoriji definisanu u `phpunit.xml`, pa ne menjaju razvojnu MySQL bazu ni njene podatke.

## Konkurentna FIFO rezervacija

Bazu je potrebno napraviti samo prvi put:

```powershell
docker compose exec mysql mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS borovnice_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

Pre svakog pokretanja pripremi isključivo test bazu, pa pokreni test:

```powershell
docker compose exec -e DB_CONNECTION=mysql -e DB_HOST=mysql -e DB_PORT=3306 -e DB_DATABASE=borovnice_test -e DB_USERNAME=root -e DB_PASSWORD=root app php artisan migrate:fresh --force

docker compose exec -e DB_CONNECTION=mysql -e DB_HOST=mysql -e DB_PORT=3306 -e DB_DATABASE=borovnice_test -e DB_USERNAME=root -e DB_PASSWORD=root app php artisan test --group=mysql-concurrency
```

`migrate:fresh` iznad briše samo bazu `borovnice_test`. Test odbija izvršavanje nad bazom čiji se naziv ne završava sa `_test`; nemoj paralelno pokretati više njegovih lokalnih instanci nad istom bazom.

U GitHub Actions-u standardni SQLite testovi rade pri svakom pushu na bilo koju granu, dok se MySQL konkurentni test pokreće samo za pull request ili direktan push na `main`.
