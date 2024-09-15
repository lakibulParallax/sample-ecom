<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Product extends Model
{
    use HasFactory;

    public function fileManager(): MorphOne
    {
        return $this->morphOne(FileManager::class, 'origin');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function sub_category(): HasOne
    {
        return $this->hasOne(SubCategory::class, 'id', 'sub_category_id');
    }

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function getImageUrlAttribute()
    {
        if ($this->fileManager) {
            return $this->fileManager->getUrlAttribute;
        }
        return null;
    }

}
