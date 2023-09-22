# API for Building routes with JWT authorization

## Описание проекта
**Проект включает в себя аутентификацию при помощи Access и Refresh токенов, а также возможность продления Access токена, используя Refresh токен. Построение маршрута включает в себя запрос на сохранение координат пользователя, а также запрос маршрута за определенный промежуток времени. Все ендпоинты для взаимодействия с API прописаны в Swagger json, который находится по пути  ```./storage/api-docs/api-docs.json```.**
## Требования к проекту
- docker
- docker-compose
## Установка и запуск проекта
-1. Создать docker volume:
```shell
    docker volume create pg_volume
```
-2. Развернуть приложение, используя docker-compose. В корне проекта набрать команду:
```shell
    docker-compose up
```
-3. В контейнере php-fpm или с использованием локального интерпретатора php установить зависимости composer:
```shell
    composer install
```
-4. Указать переменные окужения в файле .env c ЛК яндекса https://yandex.ru/dev/maps/geocoder/:
```YANDEX_GEOCODING_API_KEY=#setYourApiKeyHere```
-5. Запустить миграции в контейнере php-fpm:
```shell
    php artisan migrate
```

-6. Обращение к веб-серверу nginx, который развернут на хосту localhost по порту 8181:
