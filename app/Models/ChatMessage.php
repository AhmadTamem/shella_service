<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    protected $fillable = [
        'chat_id',
        'user_id',
        'message'
    ];
    protected $touches = ['chat'];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class,'user_id');
    }
     public function chat() : BelongsTo{
        return $this->belongsTo(Chat::class,'chat_id');
     }
}
