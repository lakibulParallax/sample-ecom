<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Tanker extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $casts = [
        'route' => 'array',
        'assigned_amount' => 'float',
        'current_amount' => 'float',
    ];

    protected $fillable = [
        'driver',
        'email',
        'phone',
        'nid',
        'is_active',
        'is_phone_verified'
    ];

    protected $hidden = [
        'phone_verified_at',
        'created_at',
        'updated_at',
        'temporary_token',
        'otp',
        'otp_expires_at',
        'remember_token',
        'is_verified',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'tanker_id', 'id');
    }

    public function routeNotificationForFcm()
    {
        return $this->device_token;
    }

    public function deliveredOrdersCount()
    {
        return $this->orders->where('delivery_status', 4)->count();
    }

    public function pendingOrdersCount()
    {
        return $this->orders->whereIn('delivery_status', [0, 1, 2, 3])->count();
    }
}
