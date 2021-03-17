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
В конструктор отправлется api ключ и url сервиса <br>
Заполняются параметры письма, на сонове которых будет создан шаблон. Звездочкой отмечены обязательные параметры <br>
Затем необходимо вызвать метод отправки письма:
* $enkod->sendOne("email@email.ru"); - отправить письмо конкретному получателю
* $enkod->sendMany($recipients); - отправить письмо нескольким получателям

```php
<?php
use Brandshopru\enKod;

$apiKey = "apikey";
$url = "https://api.enkod.ru/v1/";

$enkod = new enKod\Enkod($apiKey, $url);

$enkod->isTransaction = false;
$enkod->subject = "test"; //*
$enkod->fromEmail = "test@test.ru"; //*
$enkod->fromName = "test@test.ru"; //*
$enkod->html = "<p><h1>Привет</h1></br>Это письмо для теста!</p>"; //*
$enkod->plainText = "Привет! Это письмо для теста"; //*
$enkod->replyToEmail = "germansobol@yandex.ru";
$enkod->replyToName = "test";
$enkod->tags = [
    "Тестовое сообщение",
    "АПИ",
    "Test"
];

$enkod->sendOne("email@email.ru");
```

$recipients используется при отправке сообщения несколькм получателям и представляет собой массив: <br>
Звездочкой помечены обязательные поля
```php
[
  "recipients" => [
    [
      "email" => "ivan@test.com", //*
      "snippets" => [
        "snippet1" => "value1",
        "snippet2" => "value2"
      ],
      "attachments" => [
        [
          "fileName" => "test.pdf",
          "mimeType" => "application/pdf",
          "content" => "JVBERi0xLjUNCiW1tbW1DQoxIDAgb2JqDQo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFIvTGFu\nZyhwbC1QTCkgL1N0cnVjdFRyZWVSb290IDggMCBSL01hcmtJbmZvPDwvTWFya2VkIHRydWU+Pj4+\nDQplbmRvYmoNCjIgMCBvYmoNCjw8L1R5cGUvUGFnZXMvQ291bnQgMS9LaWRzWyAzIDAgUl0gPj4N\nCmVuZG9iag0KMyAwIG9iag0KPDwvVHlwZS9QYWdlL1BhcmVudCAyIDAgUi9SZXNvdXJjZXM8PC9G\n...\nODlFOENBQTNGMTY5NzFBRTU+XSAvUHJldiA4MjU0MS9YUmVmU3RtIDgyMjcwPj4NCnN0YXJ0eHJl\nZg0KODMwNTcNCiUlRU9G\n"
        ]
      ]
    ],
    [
      "email" => "petr@test.com", //*
      "snippets" => [
        "snippet1" => "value3",
        "snippet2" => "value4"
      ]
    ]
  ]
];
```

