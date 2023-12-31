<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ReportFilter;

class Expense extends Model
{
    use ReportFilter,HasFactory;
    protected $casts = [
        'id' => 'integer',
        'order_id' => 'integer',
        'restaurant_id' => 'integer',
        'amount' => 'float',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];


    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function delivery_man()
    {
        return $this->belongsTo(DeliveryMan::class,'delivery_man_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
