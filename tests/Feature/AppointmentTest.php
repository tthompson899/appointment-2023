<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testGetAllAppointments(): void
    {
        $user = User::factory()->create();
        $type = Type::factory()->create();
        $apointment = Appointment::factory()->for($user)->for($type)->create();

        $response = $this->get('/api/appointments');
        dd(Arr::get($response->json(), 'data'));
        $response->assertStatus(200);
    }
}
