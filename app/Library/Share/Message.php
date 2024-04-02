<?php

namespace App\Library\Share;

class Message
{
    const SUCCESS = 'success';
    const DANGER  = 'danger';
    const INFO    = 'info';
    const WARNING = 'warning';

    /**
     * 取得 message 資訊
     * 取出訊息後會清空 session
     * @param $sessionKey
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public static function getMessages($sessionKey)
    {
        $messages = session($sessionKey);
        $messages = (is_array($messages)) ? $messages : [];

        session()->forget($sessionKey);
        return $messages;
    }

    /**
     * 存放 message session
     *
     * @param $sessionKey
     * @param $type
     * @param $msg
     */
    public static function setMessage($sessionKey, $type, $msg)
    {
        $messages = session($sessionKey);


        if (is_array($messages)) {
            $messages[] = [
                'type'    => $type,
                'message' => $msg,
            ];
        } else {
            $messages[] = [
                'type'    => $type,
                'message' => $msg,
            ];
        }

        session([$sessionKey => $messages]);
    }
}
