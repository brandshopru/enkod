<?php
declare(strict_types=1);

namespace Brandshopru\Enkod;

class Enkod
{
    private string $apikey; //ключ АПИ enKod
    private int $email_id; //id письма, требуется для отправки
    private array $email_body; //содержимое для отправки получателю
    private string $url;

    public bool $isTransaction; //Является ли сообщение транзакционным
    public string $subject; //Тема сообщения
    public string $fromEmail; //От какого емейла будет отправлено сообщение
    public string $fromName; //От какого имени будет отправлено сообщение
    public string $html; //HTML версия сообщения
    public string $plainText; //Текстовая версия сообщения
    public string $replyToEmail; //Емейл для ответа на сообщение
    public string $replyToName; //Имя для ответа на сообщение
    public array $tags; //Тэги сообщения
    public object $utm; //UTM метки для ссылок в сообщении
    public object $urlParams; //Параметры для передачи в ссылках

    public function __construct(string $key, string $url="")
    {
        $this->apikey = $key;
        $this->url = $url;
    }

    private function makeEmailTemplate(){
        if(!empty($this->isTransaction)) $this->email_body['isTransaction'] = $this->isTransaction;
        if(!empty($this->subject)) $this->email_body['subject'] = $this->subject;
        if(!empty($this->fromEmail)) $this->email_body['fromEmail'] = $this->fromEmail;
        if(!empty($this->fromName)) $this->email_body['fromName'] = $this->fromName;
        if(!empty($this->html)) $this->email_body['html'] = $this->html;
        if(!empty($this->plainText)) $this->email_body['plainText'] = $this->plainText;
        if(!empty($this->replyToEmail)) $this->email_body['replyToEmail'] = $this->replyToEmail;
        if(!empty($this->replyToName)) $this->email_body['replyToName'] = $this->replyToName;
        if(!empty($this->tags)) $this->email_body['tags'] = $this->tags;
        if(!empty($this->utm)) $this->email_body['utm'] = $this->utm;
        if(!empty($this->urlParams)) $this->email_body['urlParams'] = $this->urlParams;

        try {
            $this->request("/message/create/");
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function sendOne(string $email, array $snippets = [], array $attachments = []){
        $this->makeEmailTemplate();

        $this->email_body = [];
        $this->email_body['messageId'] = $this->email_id;
        $this->email_body['email'] = $email;
        $this->email_body['snippets'] = $snippets;
        $this->email_body['attachment'] = $attachments;

        try {
            $this->request("/mail/");
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function sendMany(array $recipients){
        $this->makeEmailTemplate();

        $this->email_body = [];
        $this->email_body['messageId'] = $this->email_id;
        $this->email_body['recipients'] = $recipients;

        try {
            $this->request("/mails/");
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function request(string $uri){
        try {
            $url = "$this->url"."$uri";
            $postdata = json_encode($this->email_body);

            $client = new \GuzzleHttp\Client();

            $request = $client->request('POST', $url,
                [
                    'headers' => [
                        'apiKey' => $this->apikey,
                        'content-type' => 'application/json'
                    ],
                    'body' => $postdata
                ],
            );
            $httpcode = $request->getStatusCode();
            $result = $request->getBody();

            if($httpcode === 201) $this->email_id = json_decode($result)['messageId'];
            if($httpcode === 400) throw new \Exception("Ошибка в запросе. Подробная информация в ответе в тэге message: ".json_decode($result)['message']."Код запроса: ".json_decode($result)['requestID']);
            if($httpcode === 401) throw new \Exception("У API ключа нет прав на выполнение этого действия");
            if($httpcode === 500) throw new \Exception("Что то пошло не так. Свяжитесь с менеджером. Код запроса: ".json_decode($result)['requestID']);

        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }
    }
}