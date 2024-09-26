<?php

namespace Tests\Feature\API\V1;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\API\BaseCarControllerTest;
use Tests\TestCase;



class CarControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $headers;
    protected $carModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->carModel = CarModel::factory()->create();

        $this->actingAs($this->user);

        $this->headers = [
            'Authorization' => 'Bearer ' . $this->user->createToken('testToken')->plainTextToken,
        ];
    }

    protected function assertResponseStatusAndStructure($response, int $status)
    {
        $response->assertStatus($status);
       
        $response->assertJsonStructure([
            'data' => [
                'id',
                'brand',
                'model',
                'year',
                'mileage',
                'color',
            ],
        ]);
    }

    protected function assertCarInDatabase(array $carData)
    {
        $this->assertDatabaseHas('cars', [
            'brand_id' => $carData['brand_id'],
            'model_id' => $carData['model_id'],
            'year' => $carData['year'],
            'mileage' => $carData['mileage'],
            'color' => $carData['color'],
            'user_id' => $this->user->id,
        ]);
    }

    protected function assertResponseData($response, array $carData)
    {
        $response->assertJson([
            'data' => [
                'brand' => $this->carModel->brand->name,
                'model' => $this->carModel->name,
                'year' => $carData['year'],
                'mileage' => $carData['mileage'],
                'color' => $carData['color'],
            ],
        ]);
    }
    

    protected function getCarData(): array
    {
        return [
            'brand_id' => $this->carModel->brand_id,
            'model_id' => $this->carModel->id,
            'year' => 2020,
            'mileage' => 50000,
            'color' => 'White',
        ];
    }

    public function test_can_store_car_v1()
    {
        $carData = $this->getCarData();
        
        $response = $this->postJson('/api/v1/cars', $carData, $this->headers);
        $this->assertResponseStatusAndStructure($response, 201);
        $this->assertCarInDatabase($carData);
        $this->assertResponseData($response, $carData);
 
    }

    public function test_can_validate_store_car_v1() {
        $carData = $this->getCarData();
        $carData['brand_id'] = 'some string';
        $response = $this->postJson('/api/v1/cars', $carData, $this->headers);
        
        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'The selected brand id is invalid.',
            'errors' => [
                'brand_id' => [
                    'The selected brand id is invalid.'
                ]
            ]
        ]);  
    }

    public function test_can_fetch_user_cars()
    {
        // Создаем несколько машин для текущего пользователя
        $car1 = Car::factory()->create(['user_id' => $this->user->id]);
        $car2 = Car::factory()->create(['user_id' => $this->user->id]);
        
        $otherUser = User::factory()->create();
        $car3 = Car::factory()->create(['user_id' => $otherUser->id]);
        // Отправляем запрос на получение машин
        $response = $this->getJson('/api/v1/cars', $this->headers);

        // Проверяем статус ответа
        $response->assertStatus(200);

        // Проверяем структуру ответа
        $response->assertJsonStructure([
            'data' => [
                '*' => [ // Используем * для проверки множества машин
                    'id',
                    'brand',
                    'model',
                    'year',
                    'mileage',
                    'color',
                    
                ],
            ],
        ]);

        // Проверяем, что в ответе содержатся созданные машины
        $this->assertCount(2, $response->json('data')); // Проверяем, что 2 машины в ответе
        $this->assertEquals($car1->id, $response->json('data.0.id')); // Проверяем первую машину
        $this->assertEquals($car2->id, $response->json('data.1.id')); // Проверяем вторую машину
        $this->assertNotContains($car3->id, array_column($response->json('data'), 'id'));

        $this->assertDatabaseHas('cars', [
            'id' => $car3->id, // Проверяем, что ID третьей машины присутствует в базе
            'user_id' => $otherUser->id // Убедимся, что она принадлежит другому пользователю
        ]);
    }

    public function test_can_show_car()
        {
        // Создаем машину для текущего пользователя
        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $car->load(['brand', 'model']);

        // Отправляем запрос на получение машины по ID
        $response = $this->getJson("/api/v1/cars/{$car->id}", $this->headers);


        // Проверяем статус ответа
        $this->assertResponseStatusAndStructure($response, 200);
        $this->assertCarInDatabase($car->toArray());
        $response->assertJson([
            'data' => [
                'brand' => $car->model->brand->name,
                'model' => $car->model->name,
                'year' => $car->year,
                'mileage' => $car->mileage,
                'color' => $car->color,
            ],
        ]);
    }

    public function test_show_car_not_found()
    {
        // Отправляем запрос на получение машины по несуществующему ID
        $response = $this->getJson('/api/v1/cars/9999', $this->headers); // 9999 - предполагаемый несуществующий ID

        // Проверяем статус ответа
        $response->assertStatus(403);

        // Проверяем структуру ответа
        $response->assertJson([
            'error' => 'Unauthorized access to the car', 
        ]);
    }

    public function test_show_car_not_belong_to_user()
    {

        $otherUser = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $otherUser->id]);
        // Отправляем запрос на получение машины по несуществующему ID
        $response = $this->getJson("/api/v1/cars/{$car->id}", $this->headers);

        // Проверяем статус ответа
        $response->assertStatus(403);

        // Проверяем структуру ответа
        $response->assertJson([
            'error' => 'Unauthorized access to the car', 
        ]);
    }

    public function test_can_update_car()
{
    
    $car = Car::factory()->create(['user_id' => $this->user->id]);
    
    // Подготовка заголовков и данных для обновления

    $updatedData = [
        'brand_id' => $car->brand_id,
        'model_id' => $car->model_id,
        'year' => 2021,
        'mileage' => 40000,
        'color' => 'Blue',
    ];

    // Отправляем запрос на обновление
    $response = $this->putJson("/api/v1/cars/{$car->id}", $updatedData);

    // Проверка успешного обновления
    $response->assertStatus(200)
             ->assertJson([
                'message' => 'Car updated successfully',
             ]);

    // Проверка, что данные обновлены в базе
    $this->assertDatabaseHas('cars', [
        'id' => $car->id,
        'year' => 2021,
        'mileage' => 40000,
        'color' => 'Blue',
    ]);
}

public function test_update_car_fails_with_invalid_data()
{
    $car = Car::factory()->create(['user_id' => $this->user->id]);

    $invalidData = [
        'brand_id' => 9999, // Не существует
        'model_id' => $car->model_id,
        'year' => 2021,
        'mileage' => 40000,
        'color' => 'Blue',
    ];

    // Отправляем запрос на обновление
    $response = $this->putJson("/api/v1/cars/{$car->id}", $invalidData);

    // Проверка, что ответ с ошибкой и правильный код статуса
    $response->assertStatus(422)
             ->assertJson([
                 'message' => 'The selected brand id is invalid.',
                 'errors' => [
                     'brand_id' => ['The selected brand id is invalid.'],
                 ],
             ]);
}
public function test_can_delete_car()
{
    
    $car = Car::factory()->create(['user_id' => $this->user->id]);


    // Отправляем запрос на удаление
    $response = $this->deleteJson("/api/v1/cars/{$car->id}");

    // Проверка успешного удаления
    $response->assertStatus(200)
             ->assertJson(['message' => 'Car deleted successfully']);

    // Проверка, что автомобиль удален из базы
    $this->assertDatabaseMissing('cars', ['id' => $car->id]);
}

public function test_delete_car_fails_when_not_found()
{
    // Отправляем запрос на удаление несуществующего автомобиля
    $response = $this->deleteJson("/api/v1/cars/9999");

    // Проверка, что ответ с ошибкой и правильный код статуса
    $response->assertStatus(403)
             ->assertJson(['error' => 'Unauthorized access to the car']);
}

}
