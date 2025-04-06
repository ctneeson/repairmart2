<x-app-layout title="{{ $email->subject }}" bodyClass="page-email-show">
    <main>
        <div class="container-small">
            <div class="card p-large">
                <!-- Header with Back Link -->
                <div class="d-flex justify-content-between align-items-center mb-medium">
                    <a href="{{ route('email.index') }}" class="btn btn-link text-decoration-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 5px; vertical-align: middle;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Back to Messages
                    </a>
                    
                    <div class="message-date text-muted">
                        {{ $email->created_at->format('F j, Y \a\t g:i A') }}
                    </div>
                </div>

                <!-- Message Header -->
                <div class="message-header mb-medium pb-medium" style="border-bottom: 1px solid #e2e8f0;">
                    <h1 class="message-subject">{{ $email->subject }}</h1>
                    
                    <div class="message-meta d-flex justify-content-between align-items-start mt-small">
                        <div class="message-participants" style="flex: 1;">
                            <div class="message-from mb-small">
                                <span class="font-weight-bold">From:</span> 
                                <span class="message-sender">{{ $email->sender->name }}</span>
                            </div>
                            
                            <div class="message-to">
                                <span class="font-weight-bold">To:</span> 
                                @foreach($email->recipients as $recipient)
                                    <span class="message-recipient">{{ $recipient->name }}{{ !$loop->last ? ',' : '' }}</span>
                                @endforeach
                            </div>
                        </div>
                        
                        @if($isSender)
                            <div class="message-status" style="min-width: 120px; text-align: right; margin-left: 15px;">
                                <span class="badge"
                                    style="background-color: {{ $email->read_at ? '#38a169' : '#718096' }};
                                        color: white;
                                        padding: 5px 10px;
                                        border-radius: 9999px;
                                        display: inline-block;">
                                    {{ $email->read_at ? 'Read ' . $email->read_at->format('M d, Y') : 'Unread' }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Message Content -->
                <div class="message-body mb-medium">
                    <div class="message-content p-medium" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        {!! nl2br(e($email->content)) !!}
                    </div>
                </div>

                <!-- Attachments Section (if any) -->
                @if($email->attachments->count() > 0)
                    <div class="message-attachments mb-medium">
                        <h3 class="mb-small">Attachments ({{ $email->attachments->count() }})</h3>
                        <div class="attachments-list" style="display: flex; flex-wrap: wrap; gap: 10px;">
                            @foreach($email->attachments as $attachment)
                                <div class="attachment-item" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; width: 200px;">
                                    <div class="attachment-preview" style="margin-bottom: 10px; text-align: center;">
                                        @if(Str::startsWith($attachment->mime_type, 'image/'))
                                            <img src="{{ Storage::url($attachment->path) }}" alt="{{ $attachment->filename }}" style="max-width: 100%; max-height: 150px; object-fit: contain;">
                                        @else
                                            <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                                {!! getFileIcon($attachment->mime_type) !!}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="attachment-info">
                                        <div class="attachment-name" style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $attachment->filename }}">
                                            {{ Str::limit($attachment->filename, 20) }}
                                        </div>
                                        <div class="attachment-meta text-muted" style="font-size: 0.875rem;">
                                            {{ formatFileSize($attachment->size) }}
                                        </div>
                                        <div class="attachment-actions mt-small">
                                            <a href="{{ route('email.attachment.download', $attachment->id) }}" class="btn btn-sm btn-primary" style="width: 100%;">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 14px; margin-right: 3px; vertical-align: middle;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                </svg>
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="message-actions" style="display: flex; gap: 10px; margin-top: 20px;">
                    <!-- Reply Button - only visible to recipients -->
                    @if($isRecipient)
                        <a href="{{ route('email.reply', $email->id) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 5px; vertical-align: middle;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                            </svg>
                            Reply
                        </a>
                        
                        <!-- Mark as Unread Button (only for recipients) -->
                        @if($email->read_at)
                            <form action="{{ route('email.mark-unread', $email->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 5px; vertical-align: middle;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V19.5z" />
                                    </svg>
                                    Mark as Unread & Return to Inbox
                                </button>
                            </form>
                        @endif
                        
                        <!-- Delete Button - only visible to recipients -->
                        <form action="{{ route('email.destroy', $email->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 5px; vertical-align: middle;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    @else
                        <!-- Message for senders - optional info text -->
                        <div class="text-muted">
                            <small>You sent this message. Recipients can reply to or delete it.</small>
                        </div>
                    @endif
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