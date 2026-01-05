<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SemesterUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $studentEmail;
    public $newSemester;
    public $newAcademicYear;
    public $updateType;

    /**
     * Create a new message instance.
     */
    public function __construct($studentName, $studentEmail, $newSemester = null, $newAcademicYear = null, $updateType = 'semester')
    {
        $this->studentName = $studentName;
        $this->studentEmail = $studentEmail;
        $this->newSemester = $newSemester;
        $this->newAcademicYear = $newAcademicYear;
        $this->updateType = $updateType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->updateType === 'academic_year'
            ? 'New Academic Year - Scholarship Application Required'
            : 'New Semester - Scholarship Application Required';

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
            view: 'emails.semester-update',
            with: [
                'studentName' => $this->studentName,
                'newSemester' => $this->newSemester,
                'newAcademicYear' => $this->newAcademicYear,
                'updateType' => $this->updateType,
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
