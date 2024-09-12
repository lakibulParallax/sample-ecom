<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Operator extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'phone_code',
        'password',
        'nid',
        'date_of_birth',
        'marital_status',
        'religion',
        'age',
        'passport',
        'father_name',
        'mother_name',
        'is_active',
        'login_medium',
        'is_phone_verified',
        'is_email_verified',
        'temporary_token',
        'permanent_village',
        'permanent_district',
        'permanent_thana',
        'permanent_union',
        'occupation',
        'educational_qualification',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function fileManager(): MorphMany
    {
        return $this->morphMany(FileManager::class, 'origin');
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
