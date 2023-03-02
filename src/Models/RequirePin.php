<?php

namespace Ikechukwukalu\Requirepin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequirePin extends Model
{
    use HasFactory;

    protected $table = 'require_pins';

    protected $fillable = [
        'user_id',
        'uuid',
        'ip',
        'device',
        'payload',
        'method',
        'route_arrested',
        'redirect_to',
        'pin_validation_url',
        'approved_at',
        'cancelled_at',
        'expires_at',
        'retry',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
