<?php

namespace App\Repositories;

use App\Models\CarModel;

class CarModelRepository implements CarModelRepositoryInterface
{
    public function firstOrCreate(int $brand_id, string $name)
    {
        return CarModel::firstOrCreate(['name' => $name, 'brand_id' => $brand_id]);
    }
}
