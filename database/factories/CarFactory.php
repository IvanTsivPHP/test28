<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Car::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $carModel = CarModel::factory()->create();

        return [
            'brand_id' => $carModel->brand_id,  // Генерируем новую марку или можно использовать существующую
            'model_id' => $carModel->id,  // Генерируем новую модель
            'user_id' => User::factory(),  // Генерируем нового пользователя
            'year' => $this->faker->numberBetween(1990, 2024),  // Генерируем случайный год
            'mileage' => $this->faker->numberBetween(0, 300000),  // Пробег
            'color' => $this->faker->safeColorName(),  // Цвет
        ];
    }
}