<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Type;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder;

class AppointmentReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:appointment-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind user of upcoming appointment';

    /**
     * Execute the console command.
     */
    public function handle()
    {        
        // query the users with an appointment that is a week from today
        $currentDateTime = Carbon::now('America/Chicago');
        info("Current Date: " . $currentDateTime);

        // @todo - Need to decide if I'm doing every day reminders or every minute/every hour? because
        // this value would need to be adjusted if every minute/day/etc: ->endOfDay()

        // @todo ADJUSTMENTS may need to be made if there are too many appointments on one day
        $upcomingAppointments = Appointment::where('date_of_appointment', '>=', $currentDateTime->copy()->addDays(7)->startOfDay())
            ->where('date_of_appointment', '<=', $currentDateTime->copy()->addDays(7)->endOfDay())
            ->get();

        // $upcomingAppointments = Appointment::factory()->for(User::factory()->create())
        // ->for(Type::factory()->create())
        // ->create([
        //     'date_of_appointment' => $currentDateTime,
        // ]);

        info("Appointment Reminder: " . $currentDateTime->copy()->addDays(7)->endOfDay());

        if ($upcomingAppointments->count() > 0) {
            foreach($upcomingAppointments as $appointment) {
                Mail::to($appointment->user->email)->send(new AppointmentReminder($appointment));
            }
        }

        return 0;
    }
}
