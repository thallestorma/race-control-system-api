# How to set up this project


First, start Docker containers:

```
docker-compose up -d
```

Then, execute:

```
docker exec -it laravel-api bash
composer install
php artisan migrate
```