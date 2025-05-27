<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    protected $fillable = [
        'image',
        'service_id',
        'user_id',
        'service_information_id'
    ];



    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function Service_information(): BelongsTo
    {
        return $this->belongsTo(ServiceInformation::class);
    }
        
}
