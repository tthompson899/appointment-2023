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

        if (is_string($newAppt)) {
            return response($newAppt, 404)
            ->header('Content-Type', 'text/plain');
        }

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
            return response('Unable to find appointment.', 404)
            ->header('Content-Type', 'text/plain');
        }

        $updatedAppointment = $appt->update($params);
        $badResponse = response('Appointment Update has failed: Appointment not updated', 400)
        ->header('Content-Type', 'text/plain');

        return $updatedAppointment ? 'Appointment has been updated.' : $badResponse;
    }

    public function delete($id)
    {
        $appt = $this->appointment->find($id);

        if (empty($appt)) {
            return response('Appointment not found.', 404)
            ->header('Content-Type', 'text/plain');
        }

        return $appt->delete();
    }

    private function sendEmail(Appointment $appointmentDetails)
    {
        Mail::to($appointmentDetails->user->email)->send(new AppointmentCreated($appointmentDetails));
    }
}
