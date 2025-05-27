<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceInformation extends Model
{
    protected $fillable=[
        'service_id',
        'description',
        'user_id',
        'location_id',
        'start_price',
        'provider_price'

    ];

    public function service(): BelongsTo{
        return $this->belongsTo(Service::class);
    }
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function location(): BelongsTo{
        return $this->belongsTo(Location::class);
    }
    public function image(): HasMany{
        return $this->hasMany(Image::class);
    }
    public function video() : HasMany
    {
        return $this->hasMany(Video::class);
    }
        
}
