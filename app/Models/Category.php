<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function active()
    {
        return $this->active? 'Active' : "Inactive";
    }

    // start relation

    public function media()
    {
        return $this->morphOne(Media::class,'mediable');
    }

    public function product() {
        return $this->hasMany(Product::class,'category_id');
    }

}