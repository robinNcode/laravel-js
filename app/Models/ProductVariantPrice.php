<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariantPrice extends Model
{
    protected $fillable = [
        'product_variant_one',
        'product_variant_two',
        'product_variant_three',
        'price',
        'stock',
        'product_id'
    ];

    public function variantOne(): HasOne
    {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_one');
    }

    public function variantTwo(): HasOne
    {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_two');
    }

    public function variantThree(): HasOne
    {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_three');
    }


}
