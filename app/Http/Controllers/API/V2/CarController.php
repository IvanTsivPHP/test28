<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V2\CarRequest;
use App\Http\Resources\CarCollection;
use App\Http\Resources\CarResource;
use App\Services\V2\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    protected $carService;

    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }

    public function index(): CarCollection
    {
        $cars = $this->carService->getUserCars();
        return new CarCollection($cars);
    }

    public function store(CarRequest $request): CarResource
    {
        $carData = $request->all();
        $carData['user_id'] = Auth::user()->id;
        $car = $this->carService->create($carData);

        return new CarResource($car);
    }

    public function show($id): JsonResponse|CarResource
    {
        try {
            $car = $this->carService->getById($id);

            return new CarResource($car);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }   
       
    }

    public function update(CarRequest $request, $id): JsonResponse
    {
        try {
            $this->carService->update($id, $request->all());

            return response()->json(['message' => 'Car updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }   
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->carService->delete($id);

            return response()->json(['message' => 'Car deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }   
    }
}