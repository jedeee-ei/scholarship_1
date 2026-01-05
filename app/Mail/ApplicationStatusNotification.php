<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ScholarshipApplication;

class ApplicationStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $status;
    public $remarks;

    /**
     * Create a new message instance.
     */
    public function __construct(ScholarshipApplication $application, $status, $remarks = null)
    {
        $this->application = $application;
        $this->status = $status;
        $this->remarks = $remarks;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'Approved' => 'Scholarship Application Approved - Congratulations!',
            'Rejected' => 'Scholarship Application Update',
            default => 'Scholarship Application Status Update'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-status',
            with: [
                'application' => $this->application,
                'status' => $this->status,
                'remarks' => $this->remarks,
                'studentName' => $this->application->first_name . ' ' . $this->application->last_name,
                'scholarshipType' => ucfirst($this->application->scholarship_type),
            ]
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
