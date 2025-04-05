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
                    <div class="form-group @error('recipient_id') has-error @enderror">
                        <label for="recipient_id">Recipient</label>
                        
                        @if($prefilled && $recipient)
                            <!-- Hidden input to submit the value -->
                            <input type="hidden" name="recipient_id" value="{{ $recipient->id }}">
                            
                            <!-- Display-only field -->
                            <input type="text" value="{{ $recipient->name }} ({{ $recipient->email }})" 
                                   class="opacity-75" style="background-color: #f3f4f6;" disabled>
                        @else
                            <select name="recipient_id" id="recipient_id" required>
                                <option value="">Select a recipient</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('recipient_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        
                        @error('recipient_id')
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
                            <!-- Your attachment upload code -->
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

    @push('scripts')
    <!-- Your JavaScript for handling attachments remains the same -->
    @endpush
</x-app-layout>