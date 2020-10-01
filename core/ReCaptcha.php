<?php


trait ReCaptcha
{
    private static string $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    private static string $recaptcha_secret ;

    /**
     * Виконує POST запит
     * @param string $url
     * @param array $params
     * @return false|string
     */
    private static function request_post(string $url, array $params)
    {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($params)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /**
     * Встановлює секретний ключ для recaptcha
     * @param string $secret_key
     */
    public static function setRecaptchaSecret(string $secret_key):void
    {
        self::$recaptcha_secret = $secret_key;
    }

    /**
     * Поаертає коректний ip користувача
     * @return string
     */
    private static function getIp():string
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip_list = explode(',', $_SERVER[$key]);
                $ip = trim(end($ip_list));
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '';
    }

    /**
     * Перевіряє чи коректно пройшла рекапча
     * @param string $token
     * @return bool
     */
    public static function reVerify(string $token):bool
    {
        if (self::$recaptcha_secret && OneClick::getConfig('ok_recaptcha_trigger')){
            $res = self::request_post(self::$recaptcha_url, [
                'secret' => self::$recaptcha_secret,
                'response' => $token,
                'remoteip' => self::getIp()
            ]);
            if ($res){
                $res = json_decode($res, true);
                // todo recaptcha debug var_dump response
//                var_dump($res);
//                die();
                return $res['success'] ?? false;
            }
            return false;
        }else{
            return false;
        }
    }
}