<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;


class ChatController extends Controller
{

    private $pusher;

    public function __construct()
    {
        $this->middleware('auth');
        $this->pusher = new Pusher(
            '87892ed076b91483ee2a',
            '1043bfa797b5c0b09de5',
            '1769030',
            [
                'cluster' => 'mt1',
                'useTLS' => true
            ]
        );
        Log::info('Chat initialized');
    }


    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $chat = $request->message;

        $message = Message::create([
            'user_id' => $user->id,
            'chats' => $chat,
        ]);
        // Broadcast the message
        // broadcast(new MessageSent($user, $message))->toOthers();

        $this->pusher->trigger('public-chat', 'Chats', [
            'user' => $user,
            'message' => $message
        ]);

        return response()->json(['status' => 'Message Sent!']);
    }
}
