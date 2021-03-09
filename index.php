<?php
include "src/enkod.php";

use Brandshopru\enKod;

$apiKey = "uL3saDVIel+LGKFBN9aI9kWW+tUcWWwftHBekLqycK8";
$url = "";

$enkod = new enKod\Enkod($apiKey, $url);

$enkod->isTransaction = false;
$enkod->subject = "test";
$enkod->fromEmail = "test@test.ru";
$enkod->fromName = "test@test.ru";
$enkod->html = "<p><h1>Привет</h1></br>Это письмо для теста!</p>";
$enkod->plainText = "Привет! Это письмо для теста";
$enkod->replyToEmail = "germansobol@yandex.ru";
$enkod->replyToName = "test";
$enkod->tags = [
    "Тестовое сообщение",
    "АПИ",
    "Test"
];

$enkod->sendOne("germansobol@yandex.ru");