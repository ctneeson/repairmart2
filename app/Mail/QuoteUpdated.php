<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Quote;
use App\Models\User;

class QuoteUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    public $listingOwner;
    public $listing;

    /**
     * Create a new message instance.
     *
     * @param  Quote  $quote
     * @param  User  $listingOwner
     * @return void
     */
    public function __construct(Quote $quote, User $listingOwner)
    {
        $this->quote = $quote;
        $this->listingOwner = $listingOwner;
        $this->listing = $quote->listing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Quote Updated for {$this->listing->title}")
            ->view('mail.quote-updated')
            ->with([
                'quote' => $this->quote,
                'listingOwner' => $this->listingOwner,
                'listing' => $this->listing,
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Quote Updated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.quote-updated',
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
