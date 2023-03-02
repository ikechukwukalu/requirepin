<?php

namespace Ikechukwukalu\Requirepin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User as AppUser;

class User extends AppUser
{
    use HasApiTokens, HasFactory, Notifiable;

}
