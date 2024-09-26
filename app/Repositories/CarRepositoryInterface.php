<?php

namespace App\Repositories;

use App\Models\Car;

interface CarRepositoryInterface
{
    public function allByUser(int $user_id);
    public function getByUser($user_id, $car_id);
    public function get(int $id);
    public function create(array $data);
    public function update(Car $car, array $data);
    public function delete(int $id);
    public function belongsToUser($carId, $userId);
}
