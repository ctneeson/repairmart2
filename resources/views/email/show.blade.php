<x-app-layout title="{{ $email->subject }}" bodyClass="page-email-show">
    <main>
        <div class="container-small" style="width: 75%; max-width: 800px;">
            <h1 class="email-show-page-title">{{ $email->subject }}</h1>
            <div class="card p-large my-medium">
                <div class="d-flex text-right mb-medium">
                    <div class="message-date text-muted">
                        {{ $email->created_at->format('F j, Y \a\t g:i A') }}
                    </div>

                    @if($isSender)
                    <div class="message-status" style="min-width: 120px; margin-left: 15px;">
                        <span class="badge my-small"
                            style="background-color: {{ $email->read_at ? '#38a169' : '#718096' }};
                                color: white;
                                padding: 5px 10px;
                                border-radius: 9999px;
                                display: inline-block;">
                            {{ $email->read_at ? 'Read: ' . $email->read_at->format('M d, Y') : 'Unread' }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-12 pe-md-4">
                        <div class="message-details">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-group-auto">
                                        <label style="font-weight: bold">From:</label>
                                        <div class="detail-value quote-description-text">
                                            <a href="{{ route('profile.show', $email->sender->id) }}">
                                                {{ $email->sender->name }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-group-auto">
                                        <label style="font-weight: bold">To:</label> 
                                        <div class="detail-value quote-description-text">
                                            @foreach($email->recipients as $recipient)
                                                <span class="recipient-badge">
                                                    {{ $recipient->name }}{{ !$loop->last ? ',' : '' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="detail-group">
                                        <label style="font-weight: bold">Content:</label>
                                        <div class="detail-value quote-description-text">
                                            {!! nl2br(e($email->content)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($email->attachments->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h3 class="mb-small">Attachments</h3>
                                    <div class="attachment-grid">
                                        @foreach($email->attachments as $attachment)
                                            <div class="attachment-item">
                                                <a href="{{ Storage::url($attachment->path) }}"
                                                    target="_blank" class="attachment-link">
                                                    @if(Str::contains($attachment->mime_type, 'image'))
                                                        <img src="{{ Storage::url($attachment->path) }}"
                                                            alt="{{ $attachment->filename }}"
                                                            class="attachment-thumbnail">
                                                    @elseif(Str::startsWith($attachment->mime_type, 'video/'))
                                                        <div class="video-player-container">
                                                            <video 
                                                                src="{{ Storage::url($attachment->path) }}" 
                                                                class="video-preview" 
                                                                muted 
                                                                loop 
                                                                preload="metadata"
                                                                onclick="event.preventDefault(); this.paused ? this.play() : this.pause();">
                                                            </video>
                                                            <div class="video-overlay">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" 
                                                                        class="bi bi-play-circle" viewBox="0 0 16 16">
                                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                                    <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445z"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    @elseif(Str::contains($attachment->mime_type, 'pdf'))
                                                        <div class="document-thumbnail">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                width="24" height="24" fill="currentColor"
                                                                class="bi bi-file-pdf" viewBox="0 0 16 16">
                                                                <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2
                                                                    2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5
                                                                    0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0
                                                                    1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                                                <path d="M4.603 14.087a.81.81 0 0
                                                                    1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68
                                                                    7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0
                                                                    1.062-2.227 7.269 7.269 0 0
                                                                    1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7
                                                                    0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27
                                                                    1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1
                                                                    1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04
                                                                    1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712
                                                                    5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307
                                                                    0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0
                                                                    1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266
                                                                    0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0
                                                                    .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858
                                                                    20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107
                                                                    0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876
                                                                    3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613
                                                                    0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="document-thumbnail">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                width="24" height="24" fill="currentColor"
                                                                class="bi bi-file-text" viewBox="0 0 16 16">
                                                                <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5
                                                                    0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0
                                                                    0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0
                                                                    0 0-1H5z"/>
                                                                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2
                                                                    2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1
                                                                    1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div class="attachment-name">{{ Str::limit($attachment->filename, 15) }}</div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-medium text-right">
                    <div class="d-flex justify-end gap-1 my-medium">
                        <a href="{{ route('email.index') }}" class="btn btn-link">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 5px; vertical-align: middle;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Back to Messages
                        </a>
                    </div>
                    <div class="d-flex justify-end gap-1 my-medium">
                        <!-- Reply Button - only visible to recipients -->
                        @if($isRecipient)

                            <form action="{{ route('email.destroy', $email->id) }}"
                                method="POST" style="display: inline-block;"
                                onsubmit="return confirm('Are you sure you want to delete this message?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary">
                                    Delete
                                </button>
                            </form>

                            @if($email->read_at)
                                <form action="{{ route('email.mark-unread', $email->id) }}"
                                    method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary">
                                        Mark as Unread & Return to Inbox
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('email.reply', $email->id) }}"
                                class="btn btn-primary" style="display: inline-block;">
                                Reply
                            </a>
                            
                        @else
                            <!-- Message for senders - optional info text -->
                            <div class="text-muted">
                                <small>You sent this message. Recipients can reply to or delete it.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>

@php
// Helper functions for blade template
function formatFileSize($bytes) {
    if ($bytes < 1024) return $bytes . ' bytes';
    else if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    else return round($bytes / 1048576, 1) . ' MB';
}

function getFileIcon($mimeType) {
    if (Str::startsWith($mimeType, 'image/')) {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="green" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/></svg>';
    } else if ($mimeType === 'application/pdf') {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="red" viewBox="0 0 16 16"><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/><path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/></svg>';
    } else if ($mimeType === 'application/msword' || $mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="blue" viewBox="0 0 16 16"><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/><path d="M4.5 12.5A.5.5 0 0 1 5 12h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 10h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/></svg>';
    } else if ($mimeType === 'text/plain') {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="gray" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/><path d="M4.5 8a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zm0 2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zm0 2a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1h-4z"/></svg>';
    } else {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/></svg>';
    }
}
@endphp