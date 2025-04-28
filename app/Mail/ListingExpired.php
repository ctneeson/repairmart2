<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Listing;
use App\Models\User;

class ListingExpired extends Mailable
{
    use Queueable, SerializesModels;

    public $listingOwner;
    public $listing;

    /**
     * Create a new message instance.
     */
    public function __construct(Listing $listing, User $listingOwner)
    {
        $this->listingOwner = $listingOwner;
        $this->listing = $listing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Listing Expired: {$this->listing->title}")
            ->view('mail.listing-expired')
            ->with([
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
            subject: 'Listing Expired',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.listing-expired',
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
