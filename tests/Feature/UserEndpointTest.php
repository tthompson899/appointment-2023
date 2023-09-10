<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserEndpointTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function __setup()
    {
        $this->setupFaker();
    }

    /**
     * A basic feature test example.
     */
    public function testGetUsers(): void
    {
        $users = User::factory()->count(15)->create();
        $userIds = $users->pluck('id');

        $response = $this->get('/api/users');
        $jsonData = $response->json();

        $response->assertStatus(200);
        $this->assertNotEmpty($jsonData);

        $usersFound = User::whereIn('id', $userIds)->get();
        $this->assertNotEmpty($usersFound);
        $this->assertEquals(15, $usersFound->count());
    }

    public function testCreateUser(): void
    {
        $birthDate = $this->faker->dateTimeBetween('+10 years', '+40 years');

        $data = [
            'name' => $this->faker->unique()->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->phoneNumber,
            'date_of_birth' => Carbon::parse($birthDate)->format('Y-m-d'),
        ];

        $response = $this->post('/api/user', $data);

        $response->assertStatus(200);

        $createdUser = User::latest()->first();

        $this->assertEquals($createdUser->name, $data['name']);
        $this->assertEquals($createdUser->email, $data['email']);
        $this->assertEquals($createdUser->phone, $data['phone']);
        $this->assertEquals($createdUser->date_of_birth, $data['date_of_birth']);
    }

    public function testUpdateUser(): void
    {
        $user = User::factory()->create();

        $data = [
            'phone' => '+1-800-222-3333',
        ];

        $response = $this->put('/api/user/' . $user->id, $data);

        $response->assertStatus(200);

        $updatedUser = User::find($user->id);

        $this->assertEquals($updatedUser->id, $user->id);
        $this->assertEquals($updatedUser->name, $user->name);
        $this->assertEquals($updatedUser->email, $user->email);
        $this->assertEquals($updatedUser->phone, $data['phone']);
        $this->assertEquals($updatedUser->date_of_birth, Carbon::parse($user->date_of_birth)->toDateString());
    }

    public function testDeleteUser(): void
    {
        $user = User::factory()->create();

        $existingUser = User::find($user->id);
        $this->assertNotEmpty($existingUser);

        $response = $this->delete('/api/user/' . $user->id);

        $response->assertStatus(200);

        $deletedUser = User::find($user->id);
        $this->assertEmpty($deletedUser);
    }

    public function testUnableToUpdateUserNotFound(): void
    {
        $user = User::factory()->create();

        $this->assertNotEmpty($user);
        $user->delete();

        $data = [
            'email' => $this->faker->unique()->safeEmail(),
        ];

        $response = $this->put('/api/user/' . $user->id, $data);

        $response->assertStatus(404);
    }

    public function testUnableToDeleteUserNotFound(): void
    {
        $user = User::factory()->create();

        $existingUser = User::find($user->id);
        $this->assertNotEmpty($existingUser);

        $existingUser->delete();

        $response = $this->delete('/api/user/' . $user->id);

        $response->assertStatus(404);
    }
}
