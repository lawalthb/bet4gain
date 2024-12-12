<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use App\Models\Setting;

class ChatController extends Controller
{

    private $pusher;

    public function __construct()
    {
       // $this->middleware('auth');
      $this->pusher = new Pusher(
    Setting::get('pusher_key'),
    Setting::get('pusher_secret'),
    Setting::get('pusher_app_id'),
    [
        'cluster' => Setting::get('pusher_cluster'),
        'useTLS' => true
    ]);
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

    public function getMessages()
    {
        $messages = Message::with('user')
            ->latest()
            ->take(10)
            ->get();

        return response()->json($messages);
    }

}

