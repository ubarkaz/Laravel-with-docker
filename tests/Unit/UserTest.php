<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testUserRegistration()
    {
        // Mock the User model
        $userMock = Mockery::mock('alias:' . User::class);
        $userMock->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password123'),
            ])
            ->andReturn((object) [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password123'),
            ]);

        // Mock the Validator
        Validator::shouldReceive('make')
            ->once()
            ->with([
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ], [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ])
            ->andReturn(Mockery::mock(\Illuminate\Validation\Validator::class, function ($mock) {
                $mock->shouldReceive('fails')->andReturn(false);
            }));

        // Call the register method
        $response = $this->post('/api/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'User registered successfully!',
            'user' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
        ]);
    }
}
