<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'nid',
        'age',
        'passport',
        'is_active',
        'is_phone_verified',
        'is_email_verified',
        'plot_no',
        'block_id',
        'road_id',
        'building_name',
        'address'
    ];

    protected $hidden = [
        'email_verified_at',
        'phone_verified_at',
        'temporary_token',
        'otp',
        'otp_expires_at',
        'created_at',
        'updated_at',
        'remember_token',
    ];

    protected $casts = [
        'address' => 'array',
        'block_id' => 'int',
        'road_id' => 'int',
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function fileManager(): MorphMany
    {
        return $this->morphMany(FileManager::class, 'origin');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
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

    public function getImagesAttribute()
    {
        $images = array();
        if ($this->fileManager) {
            foreach ($this->fileManager as $file) {
                $obj = new \stdClass();
                $obj->id = $file->id;
                $obj->url = $file->url;
                $images[] = $obj;
            }
            return $images;
        }
        return asset('application/public/storage/no_image.jpg');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }
    public function getAvatarAttribute(): ?string
    {
        if ($this->hasMedia('avatar')) {
            return $this->getFirstMediaUrl('avatar');
        }
        return asset('blank.png');
    }
}
