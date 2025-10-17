<?php

namespace App\Mail;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public Leave $leave;
    public User $applicant;
    public User $supervisor;

    /**
     * Create a new message instance.
     */
    public function __construct(Leave $leave, User $applicant, User $supervisor)
    {
        $this->leave = $leave;
        $this->applicant = $applicant;
        $this->supervisor = $supervisor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Leave Request - ' . $this->applicant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-request',
            with: [
                'leave' => $this->leave,
                'applicant' => $this->applicant,
                'supervisor' => $this->supervisor,
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
