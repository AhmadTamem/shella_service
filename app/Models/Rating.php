<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'rating_value',
        'user_id',
        'service_id',
        'comment'
    ];
    public function service():BelongsTo{
        return $this->belongsTo(Service::class);
    }
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
            
}
