<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'stock',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
}
