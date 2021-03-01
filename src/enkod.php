<?php

declare(strict_types=1);

namespace Brandshopru\Enkod;


class Enkod
{
    private string $apikey;
    private string $env = "dev";
    private array $emailBody;
    private int $email_id;

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

    private function makeEmailTemplate(){
        if(!empty($this->isTransaction)) $this->emailBody['isTransaction'] = $this->isTransaction;
        if(!empty($this->subject)) $this->emailBody['subject'] = $this->subject;;
        if(!empty($this->fromEmail)) $this->emailBody['fromEmail'] = $this->fromEmail;
        if(!empty($this->fromName)) $this->emailBody['fromName'] = $this->fromName;
        if(!empty($this->html)) $this->emailBody['html'] = $this->html;
        if(!empty($this->plainText)) $this->emailBody['plainText'] = $this->plainText;
        if(!empty($this->replyToEmail)) $this->emailBody['replyToEmail'] = $this->replyToEmail;
        if(!empty($this->replyToName)) $this->emailBody['replyToName'] = $this->replyToName;
        if(!empty($this->tags)) $this->emailBody['tags'] = $this->tags;
        if(!empty($this->utm)) $this->emailBody['utm'] = $this->utm;
        if(!empty($this->urlParams)) $this->emailBody['urlParams'] = $this->urlParams;

        echo "<pre>";
        var_dump($this->emailBody);
        echo "</pre>";

        $this->request("message", "create");
    }

    public function sendOne(string $email, array $snippets = [], array $attachments = []){
        $this->makeEmailTemplate();

        $this->emailBody = [];
        $this->emailBody['messageId'] = $this->email_id;
        $this->emailBody['email'] = $email;
        $this->emailBody['snippets'] = $snippets;
        $this->emailBody['attachment'] = $attachments;

        $this->request("mail", "");
    }

    public function sendMany(array $recipients){
        $this->makeEmailTemplate();

        $this->emailBody = [];
        $this->emailBody['messageId'] = $this->email_id;
        $this->emailBody['recipients'] = $recipients;

        $this->request("mails", "");
    }

    private function request(string $controller, string $method){
        if($this->env === "dev")
            $url = "https://dev.api.enkod.ru/v1/$controller/$method";

        if($this->env === "prod")
            $url = "https://api.enkod.ru/v1/$controller/$method";

        $postdata = json_encode($this->emailBody);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['apiKey: 0']);
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode === 201) $this->email_id = json_decode($result)['messageId'];
        if($httpcode === 400) exit("Ошибка в запросе. Подробная информация в ответе в тэге message: ".$result['message']."Код запроса: ".$result['requestID']);
        if($httpcode === 401) exit("У API ключа нет прав на выполнение этого действия");
        if($httpcode === 500) exit("Что то пошло не так. Свяжитесь с менеджером. Код запроса: ".$result['requestID']);
    }
}