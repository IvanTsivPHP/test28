<?php

namespace App\Services\V1;

use App\Repositories\CarRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CarService
{
    protected $carRepository;

    public function __construct(CarRepositoryInterface $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function getUserCars()
    {
        return $this->carRepository->allByUser(Auth::id());
    }

    public function create(array $data)
    {
        $data['user_id'] = Auth::id();
        return $this->carRepository->create($data);
    }

    public function getById($id)
    {
        $this->checkCarOwnership($id, Auth::id());

        return $this->carRepository->get($id);
    }

    public function update(int $id, array $data)
    {
        $car = $this->carRepository->get($id);
        if($car['user_id'] != Auth::id()) {
            throw new \Exception('Unauthorized access to the car');
        }

        return $this->carRepository->update($car, $data);
    }

    public function delete($id)
    {
        $this->checkCarOwnership($id, Auth::id());

        return $this->carRepository->delete($id);
    }

    public function checkCarOwnership($carId, $userId)
    {
        if (!$this->carRepository->belongsToUser($carId, $userId)) {
            throw new \Exception('Unauthorized access to the car');
        }
    }
}
