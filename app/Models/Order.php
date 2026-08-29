<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'department',
        'phone',
        'status',
        'telegram_username',
        'total_amount',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
