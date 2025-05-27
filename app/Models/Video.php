<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $fillable = [
        's_information_id',
        'user_id',
        'video',
    ];

        public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function Service_information(): BelongsTo
    {
        return $this->belongsTo(ServiceInformation::class);
    }
}
