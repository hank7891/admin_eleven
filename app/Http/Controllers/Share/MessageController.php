<?php

namespace App\Http\Controllers\Share;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Share\MessageService;

class MessageController extends Controller
{
    /**
     * 取得 session message
     * @param Request $request
     */
    public function getMessage($type)
    {
        try {
            $data = MessageService::getMessages($type);
            return response()->json(['status' => 0, 'data' => $data]);

        } catch (\Exception $e) {

            return response()->json(['status' => 0, 'msg' => $e->getMessage()]);
        }
    }
}
