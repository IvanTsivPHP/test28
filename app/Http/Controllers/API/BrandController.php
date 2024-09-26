<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandCollection;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index(): BrandCollection
    {
        $brands = Brand::all();
        return new BrandCollection($brands);
    }
}
