```bash
export PGPASSWORD='postgres'
psql -Upostgres -h localhost

drop database lifts1; 
create database lifts1;
\q
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load -q
php bin/console fos:user:create admin admin@admin.com admin --super-admin


php bin/console server:run


php bin/console generate:bundle 
php bin/console make:entity 
php bin/console make:crud 
```

# чтобы работал эмулятор движения лифтов
```
php bin/console app:move-to-first 
php bin/console app:process
```
