<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TankerTransaction extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function tanker(): HasOne
    {
        return $this->hasOne(Tanker::class, 'id', 'tanker_id');
    }

    public function orders(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
