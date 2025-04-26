<x-app-layout title="Compose Message" bodyClass="page-email-create">
    <main>
        <div class="container-small">
            <h1 class="email-create-page-title">Compose Message</h1>
            {{-- <div class="card p-large"> --}}
                
                <!-- Context Alerts -->
                @if(isset($listing))
                <div class="alert alert-info mb-medium">
                    <p>You are sending a message regarding: <a href="{{ route('listings.show', $listing) }}" class="underline">{{ $listing->title }}</a></p>
                </div>
                @elseif(isset($quote))
                <div class="alert alert-info mb-medium">
                    <p>You are sending a message regarding Quote #{{ $quote->id }} for: <a href="{{ route('listings.show', $quote->listing) }}" class="underline">{{ $quote->listing->title }}</a></p>
                </div>
                @elseif(isset($order))
                <div class="alert alert-info mb-medium">
                    <p>You are sending a message regarding Order #{{ $order->id }} for: <a href="{{ route('listings.show', $order->listing) }}" class="underline">{{ $order->listing->title }}</a></p>
                </div>
                @endif
                
                <form action="{{ route('email.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="card email-form">
                    @csrf
                    
                    <div class="form-content">
                    <!-- Recipient Selection -->
                        <div class="form-details">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('recipient_ids') has-error @enderror">
                                        <label for="recipient_ids">Recipient(s)</label>
                                        
                                        @if($prefilled && isset($recipient))
                                            <!-- Hidden input to submit the value for a prefilled form with single recipient -->
                                            <input type="hidden" name="recipient_ids[]" value="{{ $recipient->id }}">
                                            
                                            <!-- Display-only field -->
                                            <div class="recipient-badge-container">
                                                <span class="recipient-badge">
                                                    {{ $recipient->name }}
                                                </span>
                                            </div>
                                        @elseif($prefilled && isset($recipients))
                                            <!-- Hidden inputs for multiple prefilled recipients -->
                                            <div class="recipient-badge-container">
                                                @foreach($recipients as $recipient)
                                                    <span class="recipient-badge">
                                                        {{ $recipient->name }}
                                                        <input type="hidden" name="recipient_ids[]" value="{{ $recipient->id }}">
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <!-- If we reach here, redirect to profile search - this should not happen with controller validation -->
                                            <div class="alert alert-danger">
                                                No recipients selected. <a href="{{ route('profile.search') }}">Please select recipients</a> before creating a message.
                                            </div>
                                            <script>
                                                // Redirect to profile search if no recipients provided
                                                window.location.href = "{{ route('profile.search') }}?error=no_recipients";
                                            </script>
                                        @endif
                                        
                                        @error('recipient_ids')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                        
                                        @error('recipient_ids.*')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Subject Line -->
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('subject') has-error @enderror">
                                        <!-- Keep your existing subject implementation -->
                                        <label for="subject">Subject</label>
                                        
                                        @if($prefilled && $subject)
                                            <!-- Hidden input to submit the value -->
                                            <input type="hidden" name="subject" value="{{ $subject }}">
                                            
                                            <!-- Display-only field -->
                                            <input type="text" value="{{ $subject }}" 
                                                class="opacity-75" style="background-color: #f3f4f6;" disabled>
                                            <span class="text-sm text-gray-500 mt-1 block">
                                                @if(isset($listing))
                                                    This subject is pre-filled based on the listing.
                                                @elseif(isset($quote))
                                                    This subject is pre-filled based on the quote.
                                                @elseif(isset($order))
                                                    This subject is pre-filled based on the order.
                                                @else
                                                    This subject is pre-filled.
                                                @endif
                                            </span>
                                        @else
                                            <input type="text" id="subject" name="subject"
                                                value="{{ old('subject', $subject) }}" required maxlength="100">
                                        @endif
                                        
                                        @error('subject')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Message Content -->
                            <div class="row">
                                <div class="col">
                                    <div class="form-group @error('content') has-error @enderror">
                                        <label for="content">Message</label>
                                        <textarea id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Attachments section remains the same -->
                        <div class="form-attachments">
                            <div class="form-attachment-upload">
                                <div class="upload-placeholder">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        style="width: 48px; height: 48px;"
                                    >
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 8v8m-4-4h8"
                                        />
                                    </svg>
                                </div>
                                <input id="emailFormAttachmentUpload" type="file" name="attachments[]" multiple 
                                    data-max-post-size="{{ ini_get('post_max_size') }}"
                                    data-max-file-size="{{ ini_get('upload_max_filesize') }}"
                                    accept="image/*,video/*,application/pdf,application/msword,
                                            application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                            text/plain" />
                            </div>
                        
                            @error('attachments')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                            
                            @error('attachments.*')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        
                            <div id="attachmentPreviews" class="email-form-attachments"></div>
                            <div id="attachmentsList" style="margin-top: 20px;"></div>
                            
                            <p class="info-message">
                                <small>
                                    Supported formats: JPEG, PNG, JPG, GIF, PDF, DOC, DOCX, TXT, MP4, MOV, OGG, QT<br>
                                    Maximum total upload size: {{ ini_get('post_max_size') }}<br>
                                    Maximum individual file size: {{ ini_get('upload_max_filesize') }}
                                </small>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="p-medium text-right">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 5px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Send Message
                        </button>
                    </div>
                </form>
            {{-- </div> --}}
        </div>
    </main>

    @push('styles')
    <style>
        .recipient-badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.5rem;
            background-color: #f3f4f6;
            border-radius: 0.25rem;
            border: 1px solid #e2e8f0;
        }
        
        .recipient-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.65rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #fff;
            background-color: #3490dc;
            border-radius: 0.25rem;
        }
    </style>
    @endpush
</x-app-layout>