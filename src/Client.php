<?php
declare(strict_types=1);

namespace Brandshopru\Enkod;


class Client
{
    private string $apikey; //ключ АПИ enKod
    private array $body; //содержимое
    private string $url;
    private string $method;

    private $content;
    private $error;

    public function __construct($apikey, $url)
    {
        $this->apikey = $apikey;
        $this->url = $url;

        $this->content = '';
        $this->error = '';
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getError()
    {
        return $this->error;
    }

    public function sendOne(string $email, int $email_id, array $snippets = [])
    {
        $this->body = [];
        $this->body['messageId'] = $email_id;
        $this->body['email'] = $email;

        if (is_array($snippets) && !empty($snippets)) {
            foreach ($snippets as $key => $val) $this->body['snippets'][(string)$key] = (string)$val;
        }

        $this->method = 'POST';

        try {
            $this->content = $this->request("/mail/");

            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
        return false;
    }

//    public function sendMany(array $recipients, string $email_id)
//    {
//        $this->body = [];
//        $this->body['messageId'] = $email_id;
//        $this->body['recipients'] = $recipients;
//
//        $this->method = 'POST';
//
//        try {
//            return $this->request("/mails/");
//        } catch (\Exception $e) {
//            echo $e->getMessage();
//        }
//        return false;
//    }

    public function insertTable(string $table, array $rows)
    {
        $this->body = [];
        $this->body['table'] = $table;
        $this->body['columns'] = $rows;

        $this->method = 'POST';

        try {
            $this->content = $this->request("/table/insert/");

            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
        return false;
    }

    /**
     * @param array $subscriber_info
     *
    {
    "firstName": "Иван",
    "lastName": "Иванов",
    "email": "test@test.test",
    "phone": "79009009090",
    "mainChannel": "email",
    "method": "addAndUpdate",
    "groups": [
    "newsletter",
    "blog",
    "recommendations"
    ],
    "extraFields": {
    "city": "Москва",
    "middleName": "Иванович"
    }
    }
     *
     * @return object
     */
    public function addEditSubscriber(array $subscriber_info = [])
    {
        $this->body = $subscriber_info;

        $this->method = 'POST';

        try {
            $this->content = $this->request("/person/");

            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
        return false;
    }


    /*
     *
     {
      "email": "test@test.test",
      "groups": [
        "newsletter",
        "blog",
        "recommendations"
      ]
    }
     */
    public function unsubscribe(array $subscriber_info = [])
    {
        $this->body = $subscriber_info;
        $this->method = 'POST';

        try {
            return $this->request("/unsubscribe/");
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     * @param string $email
     * @param string $phone
     *
     */
    public function getSubscriberInfo(string $email = '', string $phone = '')
    {
        $this->body = [];

        if (!empty($email)) $url = '?email=' . $email;
        else if (!empty($phone)) $url = '?phone=' . $phone;
        else return false;

        $this->method = 'GET';

        try {
            $this->content = json_decode($this->request("/isSubscriber/" . $url));

            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
        return false;
    }

    /**
     *
     */
    public function getGroupsInfo()
    {
        $this->body = [];

        $this->method = 'GET';

        try {
            $groups = [];

            $groups_data = json_decode($this->request("/groups/"));
            if ($groups_data) foreach ($groups_data as $group) $groups[$group->id] = $group;

            return $groups;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return false;
    }

    /**
     *
     */
    public function getGroupInfo($group_id)
    {
        $this->body = [];

        $this->method = 'GET';

        try {
            $groups_data = json_decode($this->request("/groups/"));
            if ($groups_data) foreach ($groups_data as $group) if ($group->id == $group_id) return $group;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return false;
    }

    private function request(string $uri)
    {
        try {
            $url = "$this->url" . "$uri";

            $client = new \GuzzleHttp\Client();
            $request = $client->request($this->method, $url,
                [
                    'headers' => [
                        'apiKey' => $this->apikey,
                        'content-type' => 'application/json'
                    ],
                    'body' => (!empty($this->body) ? json_encode($this->body) : null),
                ]
            );
            $httpcode = $request->getStatusCode();
            $result = $request->getBody();

            if ($httpcode === 201) return $result;
            else if ($httpcode === 400) throw new \Exception("Ошибка в запросе. Подробная информация в ответе в тэге message: ".json_decode($result)['message']."Код запроса: ".json_decode($result)['requestID']);
            else if ($httpcode === 401) throw new \Exception("У API ключа нет прав на выполнение этого действия");
            else if ($httpcode === 500) throw new \Exception("Что то пошло не так. Свяжитесь с менеджером. Код запроса: ".json_decode($result)['requestID']);
            else return $result->getContents();

        } catch (GuzzleException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}