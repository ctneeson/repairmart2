<x-app-layout title="Compose Message" bodyClass="page-email-create">
    <main>
        <div class="container-small">
            <div class="card p-large">
                <h1 class="page-title">Compose Message</h1>
                
                @if(isset($listing))
                <div class="alert alert-info mb-medium">
                    <p>You are sending a message regarding: <a href="{{ route('listings.show', $listing) }}" class="underline">{{ $listing->title }}</a></p>
                </div>
                @endif
                
                <form action="{{ route('email.store') }}" method="POST" enctype="multipart/form-data" class="email-form">
                    @csrf
                    
                    <!-- Recipient Selection -->
                    <div class="form-group @error('recipient_ids') has-error @enderror">
                        <label for="recipient_ids">Recipient(s)</label>
                        
                        @if($prefilled && isset($recipient))
                            <!-- Hidden input to submit the value for a prefilled form with single recipient -->
                            <input type="hidden" name="recipient_ids[]" value="{{ $recipient->id }}">
                            
                            <!-- Display-only field -->
                            <input type="text" value="{{ $recipient->name }}" 
                                   class="opacity-75" style="background-color: #f3f4f6;" disabled>
                        @elseif($prefilled && isset($recipients))
                            <!-- Hidden inputs for multiple prefilled recipients -->
                            @foreach($recipients as $recipient)
                                <input type="hidden" name="recipient_ids[]" value="{{ $recipient->id }}">
                            @endforeach
                            
                            <!-- Display-only field -->
                            <input type="text" value="{{ $recipients->pluck('name')->join(', ') }}" 
                                   class="opacity-75" style="background-color: #f3f4f6;" disabled>
                        @else
                            <select name="recipient_ids[]" id="recipient_ids" required multiple>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('recipient_ids', [])) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-hint">Hold Ctrl (or Cmd on Mac) to select multiple recipients</p>
                        @endif
                        
                        @error('recipient_ids')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                        
                        @error('recipient_ids.*')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Subject Line -->
                    <div class="form-group @error('subject') has-error @enderror">
                        <label for="subject">Subject</label>
                        
                        @if($prefilled && $subject)
                            <!-- Hidden input to submit the value -->
                            <input type="hidden" name="subject" value="{{ $subject }}">
                            
                            <!-- Display-only field -->
                            <input type="text" value="{{ $subject }}" 
                                   class="opacity-75" style="background-color: #f3f4f6;" disabled>
                            <span class="text-sm text-gray-500 mt-1 block">
                                This subject is pre-filled based on the listing.
                            </span>
                        @else
                            <input type="text" id="subject" name="subject" value="{{ old('subject', $subject) }}" required maxlength="100">
                        @endif
                        
                        @error('subject')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Rest of your form remains the same -->
                    <!-- Message Content -->
                    <div class="form-group @error('content') has-error @enderror">
                        <label for="content">Message</label>
                        <textarea id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Attachments section remains the same -->
                    <div class="form-group">
                        <label>Attachments</label>
                        
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
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 5px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Send Message
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            min-height: 38px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #edf2f7;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            padding: 2px 8px;
            margin: 3px 5px 3px 0;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 if the recipient selector exists
            if (document.getElementById('recipient_ids')) {
                $('#recipient_ids').select2({
                    placeholder: 'Select recipient(s)',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    </script>
    @endpush

</x-app-layout>