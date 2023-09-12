<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetAllAppointments(): void
    {
        $user = User::factory()->create();
        $type = Type::factory()->create();
        $appointment = Appointment::factory()->for($user)->for($type)->create();

        $response = $this->get('/api/appointments');
        $jsonData = $response->json();

        $response->assertStatus(200);
        $this->assertNotEmpty($jsonData);

        $appointmentFound = Appointment::find($appointment->id);
        $this->assertNotEmpty($appointmentFound);

        $userFound = User::find($user->id);
        $this->assertNotEmpty($userFound);
    }

    public function testCreateAppointment():void
    {
        $data = [
            'user_id' => 1,
            'type_id' => 5,
            'date_of_appointment' => '2023-01-20 13:00:00',
        ];

        $response = $this->post('/api/appointment', $data);

        $response->assertStatus(201);

        $createdAppointment = Appointment::latest()->first();
        $this->assertEquals($createdAppointment->user_id, $data['user_id']);
        $this->assertEquals($createdAppointment->type_id, $data['type_id']);
        $this->assertEquals($createdAppointment->date_of_appointment, $data['date_of_appointment']);
        $this->assertFalse($createdAppointment->completed);
        $this->assertFalse($createdAppointment->cancelled);
        $this->assertFalse($createdAppointment->no_show);
    }

    public function testUpdateAppointment(): void
    {
        $user = User::factory()->create();
        $type = Type::factory()->create();
        $appointment = Appointment::factory()->for($user)->for($type)->create();

        $data = [
            'completed' => true,
        ];

        $response = $this->put('/api/appointment/' . $appointment->id, $data);

        $response->assertStatus(200);

        $updatedAppointment = Appointment::find($appointment->id);

        $this->assertNotEmpty($updatedAppointment->user_id);
        $this->assertNotEmpty($updatedAppointment->type_id);
        $this->assertNotEmpty($updatedAppointment->date_of_appointment);
        $this->assertTrue($updatedAppointment->completed);
        $this->assertFalse($updatedAppointment->cancelled);
        $this->assertFalse($updatedAppointment->no_show);
    }

    public function testDeleteAppointment(): void
    {
        $user = User::factory()->create();
        $type = Type::factory()->create();
        $appointment = Appointment::factory()->for($user)->for($type)->create();

        $existingAppointment = Appointment::find($appointment->id);
        $this->assertNotEmpty($existingAppointment);

        $response = $this->delete('/api/appointment/' . $appointment->id);

        $response->assertStatus(200);

        $deletedAppointment = Appointment::find($appointment->id);
        $this->assertEmpty($deletedAppointment);
    }

    public function testUnableToUpdateAppointmentNotFound(): void
    {
        $appointment = Appointment::factory()
        ->for(User::factory()->create())
        ->for(Type::factory()->create())
        ->create();

        $this->assertNotEmpty($appointment);
        $appointment->delete();

        $data = [
            'no_show' => true,
        ];

        $response = $this->put('/api/appointment/' . $appointment->id, $data);

        $response->assertStatus(404);
        $response->assertContent('Unable to find appointment.');
    }

    public function testUnableToDeleteAppointmentNotFound(): void
    {
        $appointment = Appointment::factory()
            ->for(User::factory()->create())
            ->for(Type::factory()->create())
            ->create();

        $existingAppointment = Appointment::find($appointment->id);
        $this->assertNotEmpty($existingAppointment);

        $existingAppointment->delete();

        $response = $this->delete('/api/appointment/' . $existingAppointment->id);

        $response->assertStatus(404);
        $response->assertContent('Appointment not found.');
    }
}
