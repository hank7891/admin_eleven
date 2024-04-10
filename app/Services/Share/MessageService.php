<?php

namespace App\Services\Share;

class MessageService
{
    const SUCCESS = 'success';
    const DANGER  = 'danger';
    const INFO    = 'info';
    const WARNING = 'warning';

    /**
     * 取得 message 資訊
     * @param int $sessionKey
     *
     * @return array
     */
    public static function getMessages(string $sessionKey): array
    {
        $messages = session($sessionKey);
        $messages = (is_array($messages)) ? $messages : [];

        session()->forget($sessionKey);
        return $messages;
    }

    /**
     * 存放 message session
     * @param string $sessionKey
     * @param string $type
     * @param string $msg
     */
    public static function setMessage(string $sessionKey, string $type, string $msg): void
    {
        $messages = session($sessionKey);
        $messages = (is_array($messages)) ? $messages : [];

        $messages[] = [
            'type'    => $type,
            'message' => $msg,
        ];

        session([$sessionKey => $messages]);
    }
}
