<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Product extends Model
{
    use HasFactory;

    public function fileManager(): MorphOne
    {
        return $this->morphOne(FileManager::class, 'origin');
    }

    public function getImageUrlAttribute()
    {
        if ($this->fileManager) {
            return $this->fileManager->getUrlAttribute;
        }
        return null;
    }

}
