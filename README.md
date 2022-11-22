## Getting Started

1. Change database configuration on file `.env`

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
2. search books
3. delete multiple books
4. view detail book
5. edit book
6. delete one book

#### Interface Category

`GET` localhost:8000/category

on this page, have function.

1. list categorys
2. search categorys
3. delete multiple categorys
4. view detail category
5. edit category
6. delete one category

## About API Application

#### Book

1. Get all books

`GET` localhost:8000/api/book

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

1. Get one book

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

4. Get update book

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

1. Get delete book

`DELET` localhost:8000/api/book/{:id}

#### Category

1. Get all categories

`GET` localhost:8000/api/book

```
{
  "name": "name",
  "desc": "desc"
}
```

2. Get one category

`GET` localhost:8000/api/book/{:id}

3. Add category

`PUT` localhost:8000/api/book

```
{
  "name": "name",
  "desc": "desc",
  "parent": "parent"
}
```

1. Get update category

`POST` localhost:8000/api/book/{:id}

```
{
  "name": "name",
  "desc": "desc"
}
```

1. Get delete category

`DELET` localhost:8000/api/book/{:id}
