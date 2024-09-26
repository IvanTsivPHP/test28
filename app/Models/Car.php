<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{

    use HasFactory;

    protected $fillable = ['brand_id', 'model_id', 'user_id', 'year', 'mileage', 'color'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
