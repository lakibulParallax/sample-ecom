<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function sub_categories(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }
}
