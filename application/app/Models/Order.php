<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['delivery_status', 'payment_status', 'paid_at', 'delivered_at', 'payment_method', 'check_pm'];

    protected $casts = [
        'address' => 'array',
        'quantity' => 'int',
        'price' => 'float'
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function request_type(): HasOne
    {
        return $this->hasOne(RequestType::class, 'id', 'request_type_id');
    }

    public function tanker(): HasOne
    {
        return $this->hasOne(Tanker::class, 'id', 'tanker_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getCreatedAtAttribute($value)
    {
        $createdAt = new Carbon($value);
        return $createdAt->format('Y-m-d H:i:s');
    }
}
