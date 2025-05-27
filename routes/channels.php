<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{id}', function ($user, $id) {

    $participant = \App\Models\ChatParticipant::where([
        [
            'user_id',$user->id,
        ],
        [
            'chat_id',$id
        ]
    ])->first();

    return $participant !== null;
});