<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarModelCollection;
use App\Models\CarModel;

class CarModelController extends Controller
{
    public function index(): CarModelCollection
    {
        $models = CarModel::with('brand')->get();
        return new CarModelCollection($models);
    }
}