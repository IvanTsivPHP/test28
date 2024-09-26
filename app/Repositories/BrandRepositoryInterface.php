<?php

namespace App\Repositories;

interface BrandRepositoryInterface
{
    public function firstOrCreate(string $name);
}
