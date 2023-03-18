<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static inRandomOrder()
 * @method static where(string $string, $slug)
 */
class Product extends Model
{
    public function getPrice()
    {
        $price = $this->price / 100;

        return number_format($price, 2, ',', ' ') . ' FCFA';
    }
}
