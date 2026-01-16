<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $company; // ✅ Add this
    public $loginUrl;

    // ✅ FIXED: Constructor accepts both parameters
    public function __construct(Company $company, User $user)
    {
        $this->company = $company;
        $this->user = $user;
        $this->loginUrl = route('login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'company' => $this->company,
                'user' => $this->user,
                'loginUrl' => $this->loginUrl,
            ]
        );
    }
}