<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'email', 'phone', 'date_of_birth'
    ];

    public function appointments()
    {
        return $this->hasMany('App\Models\Appointment');
    }
}
