<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Listing;
use App\Models\Email;
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
        $listing = null;
        $recipient = null;
        $subject = null;
        $prefilled = false;

        // Check if we're coming from a listing
        if ($request->has('listing_id') && $request->has('recipient_id')) {
            $listing = Listing::findOrFail($request->listing_id);
            $recipient = User::findOrFail($request->recipient_id);
            $subject = "RepairMart Listing {$listing->id}: {$listing->title}";
            $prefilled = true;
        }

        // Get all users for dropdown (if not prefilled)
        $users = !$prefilled ? User::where('id', '!=', auth()->id())->orderBy('name')->get() : [];

        return view(
            'email.create',
            compact('users', 'recipient', 'subject', 'prefilled', 'listing')
        );
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
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:100',
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,txt',
        ]);

        // Use a transaction for atomicity
        DB::beginTransaction();
        try {
            // Create the email
            $email = Email::create([
                'sender_id' => auth()->id(),
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'read_at' => null,
            ]);

            // Attach the recipient
            $email->recipients()->attach($validated['recipient_id']);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('message-attachments', 'public');

                    Attachment::create([
                        'email_id' => $email->id,
                        'path' => $path,
                        'filename' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('email.index')->with('success', 'Message sent successfully');

        } catch (\Exception $e) {
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

        return view('email.index', compact('receivedEmails', 'sentEmails'));
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

        return back()->with('success', 'Message marked as unread');
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
     * @param Attachment $attachment
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadAttachment($attachmentId)
    {
        $attachment = Attachment::findOrFail($attachmentId);

        // Check if user is authorized to download this attachment
        $email = $attachment->email;
        if (!$email->recipients->contains('id', auth()->id()) && $email->sender_id !== auth()->id()) {
            abort(403, 'You do not have permission to download this attachment.');
        }

        return Storage::download($attachment->path, $attachment->filename);
    }
}
