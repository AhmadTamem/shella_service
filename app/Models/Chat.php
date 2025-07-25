<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    protected $table='chats';
    protected $fillable = [
        'created_by',
        'is_private'
    ];

    public function participants():HasMany
    {
        return $this->hasMany(ChatParticipant::class, 'chat_id');
    }
    public function messages():HasMany{
        return $this->hasMany(ChatMessage::class,'chat_id');
    }
    public function lastMessage():HasOne{
        return $this->hasOne(ChatMessage::class,'chat_id')->latest('updated_at');
    }
    public function scopeHasParticipant($query , int $userId) : mixed{
        return $query->wherehas('participants',function($q) use ($userId){
            $q->where('user_id',$userId);
        } );
    }
}
