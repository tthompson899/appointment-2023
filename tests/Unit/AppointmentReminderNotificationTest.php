<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Type;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use App\Mail\AppointmentReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AppointmentReminderNotificationTest extends TestCase
{
    use DatabaseTransactions;

    public function testCanSendReminderEmail(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $type = Type::factory()->create();
        $appointmentDate = Carbon::now()->addDays(7);
        $appointment = Appointment::factory()->for($user)->for($type)->create([
            'date_of_appointment' => Carbon::parse($appointmentDate->format('Y-m-d') . ' 05:30:00', 'America/Chicago')->format('Y-m-d H:i A'),
        ]);

        $this->artisan("app:appointment-reminder")
            ->assertExitCode(0);

        Mail::assertSent(function (AppointmentReminder $mail) use ($appointment) {
            return $mail->appointment->id === $appointment->id &&
                $mail->hasTo($appointment->user->email) &&
                $mail->appointment->user->name === $appointment->user->name &&
                $mail->appointment->type->name === $appointment->type->name;
        });

        Mail::assertSentCount(1);
    }
}