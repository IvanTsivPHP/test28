<?php

namespace App\Repositories;

use App\Models\Car;

class CarRepository implements CarRepositoryInterface
{
    public function allByUser($user_id)
    {
        return Car::where('user_id', $user_id)->with(['brand', 'model'])->get();
    }

    public function getByUser($user_id, $car_id)
    {
        return Car::where('user_id', $user_id)->with(['brand', 'model'])->findOrFail($car_id);
    }

    public function get($car_id)
    {
        return Car::with(['brand', 'model'])->findOrFail($car_id);
    }
    public function create(array $data)
    {
        return Car::create($data);
    }

    public function update(Car $car, array $data)
    {
        return $car->update($data);
    }

    public function delete($id)
    {
        $car = $this->get($id);
        $car->delete();
    }

    public function belongsToUser($carId, $userId)
    {
        return Car::where('id', $carId)->where('user_id', $userId)->exists();
    }
}
