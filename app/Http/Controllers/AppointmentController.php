<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentCreated;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    private $appointment;

    public function __construct()
    {
        $this->appointment = new Appointment();
    }
    public function index(Request $request)
    {
        $params = $request->query();

        $userAppts = $this->appointment->getAllAppointments($params);

        return $userAppts;
    }

    public function create(Request $request)
    {
        $params = $request->only('user_id', 'type_id', 'date_of_appointment');

        $newAppt = $this->appointment->createAppointment($params);

        $this->sendEmail($newAppt);

        return $newAppt;
    }

    public function update($id, Request $request)
    {
        $params = $request->only('user_id', 'type_id', 'date_of_appointment', 'completed', 'cancelled', 'no_show');
        Log::info('update_params', [
            'params' => $params
        ]);
        $appt = $this->appointment->find($id);

        if (! $appt) {
            return 'Unable to find appointment.';
        }

        $updatedAppointment = $appt->update($params);

        return $updatedAppointment ? 'Appointment has been updated.' : 'Update Appointment failed.';
    }

    public function delete($id)
    {
        $appt = $this->appointment->find($id);

        if (! $appt) {
            return 'Appointment not found.';
        }

        return $appt->delete();
    }

    private function sendEmail(Appointment $appointmentDetails)
    {
        Mail::to($appointmentDetails->user->email)->send(new AppointmentCreated($appointmentDetails));
    }
}
