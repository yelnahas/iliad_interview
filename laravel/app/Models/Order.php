<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'description', 'date'];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function searchableAs() : string
    {
        return 'orders_index';
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'date' => $this->date
        ];
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
