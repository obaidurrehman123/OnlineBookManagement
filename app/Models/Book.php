<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'author',
        'description',
        'price',
        'quantity'
    ];
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function bookImages()
    {
        return $this->hasMany(BookImage::class);
    }

    public function reviews(){
        return $this->hasMany(Reviews::class);
    }
}
