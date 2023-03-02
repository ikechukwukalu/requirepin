<?php

namespace Ikechukwukalu\Requirepin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'isbn',
        'authors',
        'country',
        'number_of_pages',
        'publisher',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    public function setReleaseDateAttribute($value) {
        return $this->attributes['release_date'] = date('Y-m-d', strtotime($value));
    }

    public function getReleaseDateAttribute($value) {
        return date('Y-m-d', strtotime($value));
    }
}
