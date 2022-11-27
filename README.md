## Getting Started

1. Setup database configuration on file `.env`

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=jagoan123
```

2. Execute database migration

```cmd
php artisan migrate
```

3. Install dependencies

```
composer install && npm install
```

4. Run laravel

```
php artisan serve
```

4. Run vite

```
npm run dev
```

## About Application

#### Interface Book

`GET` localhost:8000/book

on this page, have function.

1. list books
2. create book
3. search book
4. delete multiple books
5. view detail book
6. edit book
7. delete book

## About API

#### Book

1. Get books

`POST` localhost:8000/api/book

```
{
  "title": "Traveling to Europe 123",
  "desc": "Traveling to Europe 123",
  "cat": "123",
  "keywords": "123,123,123",
  "price": 123123,
  "publisher": "qwe, 123",
}
```

2. Get one book

`GET` localhost:8000/api/book/{:id}

3. Add book

`PUT` localhost:8000/api/book

```
{
  "title": "Traveling to Europe 123",
  "desc": "Traveling to Europe 123",
  "keywords": "123,123,123",
  "price": 123123,
  "stock": 1,
  "publisher": "qwe, 123",
  "cat": [2,3,4,5]
}
```

4. Update book

`POST` localhost:8000/api/book/{:id}

```
{
  "title": "title",
  "desc": "desc",
  "keywords": "keywords",
  "price": "price",
  "stock": "stock",
  "publisher": "publisher",
}
```

5. Delete book

`DELETE` localhost:8000/api/book/{:id}
