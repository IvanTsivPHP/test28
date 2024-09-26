<?php

namespace App\Services\V2;

use App\Models\CarModel;
use App\Repositories\BrandRepositoryInterface;
use App\Repositories\CarModelRepositoryInterface;
use App\Repositories\CarRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CarService
{
   

    public function __construct(
        protected CarRepositoryInterface $carRepository,
        protected CarModelRepositoryInterface $carModelRepository,
        protected BrandRepositoryInterface $brandRepository
        ) {}

    public function getUserCars()
    {
        return $this->carRepository->allByUser(Auth::id());
    }

    private function formatData(array $data, CarModel $carModel) {
        return [
            'brand_id' => $carModel->brand_id,
            'model_id' => $carModel->id,
            'user_id' => Auth::id(),
            'year' => $data['year'],
            'mileage' => $data['mileage'],
            'color' => $data['color']
        ];
    }

    public function create(array $data)
    {
        $brand = $this->brandRepository->firstOrCreate($data['brand']);
        $carModel = $this->carModelRepository->firstOrCreate($brand->id, $data['model']);

        return $this->carRepository->create($this->formatData($data, $carModel));
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

        $brand = $this->brandRepository->firstOrCreate($data['brand']);
        $carModel = $this->carModelRepository->firstOrCreate($brand->id, $data['model']);

        return $this->carRepository->update($car, $this->formatData($data, $carModel));
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
