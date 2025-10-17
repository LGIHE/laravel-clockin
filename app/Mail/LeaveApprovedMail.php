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

class LeaveApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Leave $leave;
    public User $applicant;
    public User $approver;

    /**
     * Create a new message instance.
     */
    public function __construct(Leave $leave, User $applicant, User $approver)
    {
        $this->leave = $leave;
        $this->applicant = $applicant;
        $this->approver = $approver;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Leave Request Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-approved',
            with: [
                'leave' => $this->leave,
                'applicant' => $this->applicant,
                'approver' => $this->approver,
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
