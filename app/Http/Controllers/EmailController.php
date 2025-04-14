<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Listing;
use App\Models\Email;
use App\Models\Quote;
use App\Models\Order;
use App\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    /**
     * Display a listing of the emails.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        // Get all users that can receive messages
        $users = User::whereNotNull('email')
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        // Initialize variables for pre-filling
        $prefilled = false;
        $recipient = null;
        $recipients = null;
        $subject = '';
        $listing = null;
        $quote = null;
        $order = null;

        // Check if recipient_ids are provided in the request
        if ($request->has('recipient_ids')) {
            $recipientIds = $request->input('recipient_ids');

            // Handle both array and single value
            if (is_array($recipientIds)) {
                if (count($recipientIds) === 1) {
                    // Single recipient
                    $recipient = User::find($recipientIds[0]);
                    $prefilled = true;
                } else {
                    // Multiple recipients
                    $recipients = User::whereIn('id', $recipientIds)->get();
                    $prefilled = true;
                }
            } else {
                // Single recipient passed as non-array
                $recipient = User::find($recipientIds);
                $prefilled = true;
            }
        }

        // Check if listing_id is provided
        if ($request->has('listing_id')) {
            $listing = Listing::find($request->input('listing_id'));

            if ($listing) {
                // Pre-fill subject if listing exists
                $subject = "RE: {$listing->title}";
                $prefilled = true;

                // If no recipient specified but listing exists, set recipient to listing owner
                if (!$recipient && !$recipients && !$request->has('recipient_ids')) {
                    $recipient = $listing->user;
                    $prefilled = true;
                }
            }
        }

        // Check if quote_id is provided
        if ($request->has('quote_id')) {
            $quote = Quote::find($request->input('quote_id'));

            if ($quote) {
                // Pre-fill subject if quote exists
                $subject = "RE: Quote #{$quote->id} - {$quote->listing->title}";
                $prefilled = true;

                // If no recipient specified but quote exists, set recipient based on context
                if (!$recipient && !$recipients && !$request->has('recipient_ids')) {
                    if (auth()->id() === $quote->user_id) {
                        // If the current user is the quote creator, set recipient to listing owner
                        $recipient = $quote->listing->user;
                    } else {
                        // Otherwise, set recipient to quote creator
                        $recipient = $quote->user;
                    }
                    $prefilled = true;
                }
            }
        }

        // Check if order_id is provided
        if ($request->has('order_id')) {
            $order = Order::find($request->input('order_id'));

            if ($order) {
                // Pre-fill subject if order exists
                $subject = "RE: Order #{$order->id} - {$order->quote->listing->title}";
                $prefilled = true;

                // If no recipient specified but order exists, set recipient based on context
                if (!$recipient && !$recipients && !$request->has('recipient_ids')) {
                    if (auth()->id() === $order->quote->user_id) {
                        // If current user is specialist, set recipient to customer
                        $recipient = $order->customer;
                    } else {
                        // Otherwise, set recipient to specialist
                        $recipient = $order->quote->user;
                    }
                    $prefilled = true;
                }
            }
        }

        return view('email.create', compact(
            'users',
            'prefilled',
            'recipient',
            'recipients',
            'subject',
            'listing',
            'quote',
            'order'
        ));
    }

    /**
     * Store a newly created email in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_ids' => 'required|array|min:1',
            'recipient_ids.*' => 'exists:users,id',
            'subject' => 'required|string|max:100',
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,txt,mp4,mov,ogg,qt',
        ]);

        DB::beginTransaction();
        try {
            $email = Email::create([
                'sender_id' => auth()->id(),
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'read_at' => null,
            ]);

            // Attach all recipients
            $email->recipients()->attach($validated['recipient_ids']);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $i => $file) {
                    $path = $file->store('attachments', 'public');

                    // Create the attachment record with the correct file information
                    // and add the current user's ID - no filename column
                    $email->attachments()->create([
                        'user_id' => auth()->id(),
                        'path' => $path,
                        'position' => $i + 1,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('email.index')->with('success', 'Message sent successfully to ' . count($validated['recipient_ids']) . ' recipient' . (count($validated['recipient_ids']) > 1 ? 's' : ''));

        } catch (\Exception $e) {
            \Log::error('Error creating message: ' . $e->getMessage());

            // Check if it's a file size issue
            if ($e instanceof \Symfony\Component\HttpFoundation\File\Exception\FileException) {
                return redirect()->back()->withErrors([
                    'attachments' => 'There was an issue with your file uploads. Please check the file sizes and formats.'
                ])->withInput();
            }

            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the emails.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get received emails (where the current user is a recipient)
        $receivedEmails = Email::whereHas('recipients', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->with(['sender', 'recipients'])
            ->withCount('attachments')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'received');

        // Get sent emails (where the current user is the sender)
        $sentEmails = Email::where('sender_id', $user->id)
            ->with('recipients')
            ->withCount('attachments')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'sent');

        // Count unread messages
        $unreadCount = Email::whereHas('recipients', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->whereNull('read_at')
            ->count();

        return view('email.index', compact('receivedEmails', 'sentEmails', 'unreadCount'));
    }

    /**
     * Display the specified email.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = auth()->user();
        $email = Email::with(['sender', 'recipients', 'attachments'])
            ->findOrFail($id);

        // Check if user is authorized to view this email
        $isRecipient = $email->recipients->contains('id', $user->id);
        $isSender = $email->sender_id === $user->id;

        if (!$isRecipient && !$isSender) {
            abort(403, 'You do not have permission to view this message.');
        }

        // Mark as read if user is a recipient and email is unread
        if ($isRecipient && is_null($email->read_at)) {
            $email->read_at = now();
            $email->save();
        }

        return view('email.show', compact('email', 'isRecipient', 'isSender'));
    }

    /**
     * Show reply form for an email
     *
     * @param Email $email
     * @return \Illuminate\View\View
     */
    public function reply(Email $email)
    {
        // Check if user is authorized to reply (must be sender or recipient)
        if (!$email->recipients->contains('id', auth()->id()) && $email->sender_id !== auth()->id()) {
            abort(403, 'You do not have permission to reply to this message.');
        }

        // Set recipient to original sender
        $recipient = $email->sender;

        // Prefill subject with Re: if it doesn't already start with Re:
        $subject = Str::startsWith($email->subject, 'Re:') ? $email->subject : 'Re: ' . $email->subject;

        $prefilled = true;
        $listing = null; // No listing context for replies
        $users = []; // Not needed for prefilled form

        return view('email.create', compact('recipient', 'subject', 'prefilled', 'listing', 'users', 'email'));
    }

    /**
     * Mark an email as unread
     *
     * @param Email $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markUnread(Email $email)
    {
        // Check if user is a recipient
        if (!$email->recipients->contains('id', auth()->id())) {
            abort(403, 'You do not have permission to mark this message.');
        }

        $email->read_at = null;
        $email->save();

        // Redirect to inbox instead of going back to the message
        return redirect()->route('email.index')->with('success', 'Message marked as unread');
    }

    /**
     * Delete an email (for the current user only)
     *
     * @param Email $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Email $email)
    {
        // Check if user is authorized to delete (must be sender or recipient)
        if (!$email->recipients->contains('id', auth()->id()) && $email->sender_id !== auth()->id()) {
            abort(403, 'You do not have permission to delete this message.');
        }

        // For a real system, you might want to implement soft deletes or user-specific delete flags
        // instead of completely removing the email

        // For simplicity in this example, we'll just delete the email
        $email->delete();

        return redirect()->route('email.index')->with('success', 'Message deleted successfully');
    }

    /**
     * Download an email attachment
     *
     * @param int $attachmentId
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadAttachment($attachmentId)
    {
        // Load attachment with its related email including recipients and sender
        $attachment = Attachment::with(['email.recipients', 'email.sender'])->findOrFail($attachmentId);

        // Make sure the attachment has an associated email
        if (!$attachment->email) {
            abort(404, 'Attachment not found or email has been deleted.');
        }

        // Check if user is authorized to download this attachment
        $email = $attachment->email;
        $currentUserId = auth()->id();

        // Check if current user is sender or a recipient
        $isAuthorized = ($email->sender_id === $currentUserId) ||
            $email->recipients->contains('id', $currentUserId);

        if (!$isAuthorized) {
            abort(403, 'You do not have permission to download this attachment.');
        }

        // Check if file exists in storage
        if (!Storage::disk('public')->exists($attachment->path)) {
            abort(404, 'The attachment file could not be found.');
        }

        // Log successful download attempt
        \Log::info("User {$currentUserId} downloaded attachment {$attachmentId} from email {$email->id}");

        // Extract a filename from the path instead of using a filename column
        $generatedFilename = basename($attachment->path);

        return Storage::disk('public')->download($attachment->path, $generatedFilename);
    }
}
