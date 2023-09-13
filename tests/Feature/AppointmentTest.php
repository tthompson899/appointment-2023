<?php

namespace Tests\Feature;

use App\Mail\AppointmentCreated;
use App\Models\Appointment;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
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
        $user = User::factory()->create();
        $type = Type::factory()->create();

        $data = [
            'user_id' => $user->id,
            'type_id' => $type->id,
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

    public function testSendEmailAppointmentCreated(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $type = Type::factory()->create();

        $data = [
            'user_id' => $user->id,
            'type_id' => $type->id,
            'date_of_appointment' => '2023-01-20 13:00:00',
        ];

        Mail::assertNothingSent();

        $response = $this->post('/api/appointment', $data);

        $response->assertStatus(201);

        $createdAppointment = Appointment::latest()->first();
 
        Mail::assertSent(function (AppointmentCreated $mail) use ($createdAppointment) {
            return $mail->appointment->id === $createdAppointment->id &&
                $mail->hasTo($createdAppointment->user->email) &&
                $mail->appointment->user->name === $createdAppointment->user->name &&
                $mail->appointment->type->name === $createdAppointment->type->name;
        });
    }

    public function testEmailNotSentNoTypeId(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'date_of_appointment' => '2023-03-07 15:30:00',
        ];

        Mail::assertNothingSent();

        $response = $this->post('/api/appointment', $data);

        $response->assertStatus(404);
        $response->assertContent('Unable to create appointment: Type does not exist.');

        Mail::assertNothingSent();
    }

    public function testEmailNotSentNoUserId(): void
    {
        Mail::fake();

        $type = Type::factory()->create();

        $data = [
            'type_id' => $type->id,
            'date_of_appointment' => '2023-10-15 08:30:00',
        ];

        Mail::assertNothingSent();

        $response = $this->post('/api/appointment', $data);

        $response->assertStatus(404);
        $response->assertContent('Unable to create appointment: User not found.');

        Mail::assertNothingSent();
    }
}
