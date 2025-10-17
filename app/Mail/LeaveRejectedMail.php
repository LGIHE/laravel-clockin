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

class LeaveRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Leave $leave;
    public User $applicant;
    public User $rejector;

    /**
     * Create a new message instance.
     */
    public function __construct(Leave $leave, User $applicant, User $rejector)
    {
        $this->leave = $leave;
        $this->applicant = $applicant;
        $this->rejector = $rejector;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Leave Request Rejected',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-rejected',
            with: [
                'leave' => $this->leave,
                'applicant' => $this->applicant,
                'rejector' => $this->rejector,
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
