<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['date_of_appointment', 'type_id', 'completed', 'cancelled', 'no_show'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function getAllAppointments($params)
    {
        $appts = User::with('appointments', 'appointments.type');

        if ($name = Arr::get($params, 'name')) {
            $appts->where('name', 'like', '%' . $name . '%');
        }

        if ($dob = Arr::get($params, 'date_of_birth')) {
            $appts->where('date_of_birth', 'like', $dob . '%');
        }

        if ($phone = Arr::get($params, 'phone')) {
            $appts->where('phone', 'like', $phone . '%');
        }

        if ($email = Arr::get($params, 'email')) {
            $appts->where('email', 'like', $email . '%');
        }

        if ($appointment_date = Arr::get($params, 'date_of_appointment')) {
            // @todo Can I use the user appointments relationship to find the appointments?
            $appts = Appointment::with('user', 'type')->where('date_of_appointment', 'like', $appointment_date . '%');
        }

        return response()->json([$appts->get()]);
    }

    public function createAppointment($params)
    {
        // @todo Should those that are not users be able to create appointments?
        $user = User::find(Arr::get($params, 'user_id'));

        if (! $user) {
            return 'Unable to create appointment: User not found!';
        }

        if (! $type = Type::find(Arr::get($params, 'type_id'))) {
            return 'Unable to create appointment: Type does not exist!';
        }

        $createdAppointment = $user->appointments()->create([
            'date_of_appointment' => Arr::get($params, 'date_of_appointment'),
            // @todo make sure it's a valid type_id
            'type_id' => $type->id
        ]);

        return $createdAppointment;
    }
}
