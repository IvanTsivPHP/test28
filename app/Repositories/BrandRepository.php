<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandRepository implements BrandRepositoryInterface
{
    public function firstOrCreate(string $name)
    {
        return Brand::firstOrCreate(['name' => $name]);
    }
}
