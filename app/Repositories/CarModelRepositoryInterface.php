<?php

namespace App\Repositories;

interface CarModelRepositoryInterface
{
    public function firstOrCreate(int $brand_id, string $name);
}
