<?php


trait TelegramTrait
{
    /**
     * Відправляє в телеграм повідомлення.
     * @param string $message
     * @return array - масив відповіді сервісу API telegram
     */
    private static function sendTelegram(string $message):array
    {
        $text = urlencode($message);
        $url = 'https://api.telegram.org/bot' . get_option('telegram_bot_token')['input'] .
            '/sendMessage?chat_id=' . get_option('telegram_chat_id')['input'] . '&text='.$text;
        $res = file_get_contents($url);
        if ($res){
            return json_decode($res, true);
        }else{
            return ['error' => 'error sent to telegram!'];
        }
    }
}