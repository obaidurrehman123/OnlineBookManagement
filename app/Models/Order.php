<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'u_id',
        'order_date',
        'total_amount',
        'status',
    ];

    public function user(){
        return $this->hasOne(User::class);
    }
}