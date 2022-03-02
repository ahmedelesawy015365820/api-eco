<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function active()
    {
        return $this->active? 'Active' : "Inactive";
    }

    // start Relation

    public function category() {
        return $this->belongsTo(Product::class,'category_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class,'mediable');
    }

}
