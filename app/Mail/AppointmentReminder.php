<?php

namespace App\Mail;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('appointments@dentist.com', 'Dentisty'),
            subject: 'Test Appointment Dental',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $formatDateOfAppointment = Carbon::create($this->appointment->date_of_appointment)->toDayDateTimeString();

        return new Content(
            view: 'emails.appointment-remind',
            with: [
                'appointmentDetail' => $this->appointment,
                'formatAppt' => $formatDateOfAppointment,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
