<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Notification extends Model
{
    use HasFactory;

    public function user(): MorphMany
    {
        return $this->morphMany(User::class, 'user');
    }

    public function sender(): MorphMany
    {
        return $this->morphMany(User::class, 'sender');
    }
}
