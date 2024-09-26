<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BaseCarControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->actingAs($this->user);

        $this->headers = [
            'Authorization' => 'Bearer ' . $this->user->createToken('testToken')->plainTextToken,
        ];
    }

    protected function getCarData(): array
    {
        return [
            'brand' => 'Tesla',
            'model' => 'Model S',
            'year' => 2020,
            'mileage' => 50000,
            'color' => 'White',
        ];
    }
}
