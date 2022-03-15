# PHP клиент для API сервиса email рассылок [enKod](https://enkod.io).
Пакет предоставляет удобный интерфейс для интеграции с сервисом email рассылок через API. 
## Требования
* php ^7.1

## Установка
Вы можете установить данный пакет с помощью сomposer:

```
composer require brandshopru/enkod
```

## Использование
В конструктор отправляется api ключ и url сервиса <br>

Затем необходимо вызвать метод отправки письма:
* $enkod->sendOne("email@email.ru", 1); - отправить письмо конкретному получателю, используя шаблон '1' 
* $enkod->sendMany($recipients, 1); - отправить письмо нескольким получателям, используя шаблон '1'

```php
<?php
use Brandshopru\Enkod;

$apiKey = "apikey";
$url = "https://api.enkod.ru/v1";

$enkod = new Enkod\Client($apiKey, $url);

$enkod->sendOne("email@email.ru", 1);
```

* Третьим параметром можно передавать пользовательские данные для подстановки в тему или шаблон письма

```php
<?php
use Brandshopru\Enkod;

$apiKey = "apikey";
$url = "https://api.enkod.ru/v1";
$snippets = [
    'first_name' => 'Иванов',
    'last_name' => 'Иван',
];
$enkod = new Enkod\Client($apiKey, $url);

$enkod->sendOne("email@email.ru", 1, $snippets);
```

