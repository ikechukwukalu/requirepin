<?php

namespace Ikechukwukalu\Requirepin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OldPin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pin',
    ];

    protected $hidden = [
        'pin'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
