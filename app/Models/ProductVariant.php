<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'attributes',
        'images'
    ];

    protected $casts = [
        'attributes' => 'array' ,
        'images' => 'array' ,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlsAttribute()
    {
        $urls = [];
        if($this->images)
        {
            foreach($this->images as $img){
               $urls[] = asset('storage/'.$img);
            }
            return $urls;
        }
        
    }

}
