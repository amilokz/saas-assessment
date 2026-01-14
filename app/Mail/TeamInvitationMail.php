<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $acceptUrl;
    public $declineUrl;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->acceptUrl = route('invitation.accept', $invitation->token);
        $this->declineUrl = route('invitation.decline', $invitation->token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re Invited to Join ' . $this->invitation->company->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}