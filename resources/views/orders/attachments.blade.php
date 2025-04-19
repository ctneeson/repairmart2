<x-app-layout title="Order Attachments" bodyClass="page-my-orders">
    <main>
        <div>
            <div class="container">
                <h1 class="listing-details-page-title">Manage attachments for Order #{{ $order->id }}</h1>
                <div class="listing-attachments-wrapper">
                    <form action="{{ route('orders.updateAttachments', $order) }}"
                        method="POST"
                        class="card p-medium form-update-attachments">
                        @csrf
                        @method('PUT')
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Delete</th>
                                        <th>Attachment</th>
                                        <th>Added by</th>
                                        <th>Date added</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($order->attachments as $attachment)
                                    <tr>
                                        <td>
                                            <input
                                                type="checkbox"
                                                name="delete_attachments[]"
                                                id="delete_attachment_{{ $attachment->id }}"
                                                value="{{ $attachment->id }}"
                                                {{ $attachment->user_id === auth()->id() || auth()->user()->hasRole('admin') ? '' : 'disabled' }}
                                                />
                                            @if($attachment->user_id !== auth()->id() && !auth()->user()->hasRole('admin'))
                                                <span class="text-muted small d-block">
                                                    <i class="bi bi-lock-fill"></i> Only uploader can delete
                                                </span>
                                            @endif
                                        </td>
                                        <td class="listing-form-attachments">
                                            @if(Str::startsWith($attachment->mime_type, 'image/'))
                                            <img
                                                src="{{ Storage::url($attachment->path) }}"
                                                alt=""
                                                class="order-form-attachment-preview"
                                            />
                                            @elseif(Str::startsWith($attachment->mime_type, 'video/'))
                                            <video
                                                src="{{ Storage::url($attachment->path) }}"
                                                class="order-form-attachment-preview"
                                                controls
                                                muted>
                                            </video>
                                            @elseif(Str::startsWith($attachment->mime_type, 'application/pdf'))
                                            <div class="document-preview">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="48"
                                                    height="48"
                                                    fill="currentColor"
                                                    class="bi bi-file-pdf"
                                                    viewBox="0 0 16 16"
                                                    >
                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2
                                                        2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1
                                                        1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                                    <path d="M4.603 14.087a.81.81 0 0
                                                        1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68
                                                        7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0
                                                        1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7
                                                        0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27
                                                        1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1
                                                        1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04
                                                        1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712
                                                        5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02
                                                        1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266
                                                        0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71
                                                        12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5
                                                        1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0
                                                        .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0
                                                        0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7
                                                        6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0
                                                        0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                                                </svg>
                                                <p>{{ $attachment->filename }}</p>
                                            </div>
                                            @else
                                            <div class="document-preview">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="48"
                                                    height="48"
                                                    fill="currentColor"
                                                    class="bi bi-file-text"
                                                    viewBox="0 0 16 16"
                                                    >
                                                    <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5
                                                        0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5
                                                        0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
                                                        <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0
                                                        1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0
                                                        0-1-1z"/>
                                                </svg>
                                                <p>{{ $attachment->filename }}</p>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attachment->user_id === auth()->id())
                                            <span>You</span>
                                            @elseif ($attachment->user_id)
                                            <a href="{{ route('profile.show', $attachment->user) }}">
                                                {{ $attachment->user->name }}
                                            </a>
                                            @else
                                            <span>System</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $attachment->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="position-controls">
                                            <input
                                                type="hidden"
                                                name="positions[{{ $attachment->id }}]"
                                                value="{{ old('positions.'.$attachment->id, $attachment->position) }}"
                                                class="position-input"
                                            />
                                            <div class="position-buttons">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm move-up"
                                                    title="Move up"
                                                    data-id="{{ $attachment->id }}"
                                                    >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        >
                                                        <path d="M18 15l-6-6-6 6"/>
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm move-down"
                                                    title="Move down"
                                                    data-id="{{ $attachment->id }}"
                                                    >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        >
                                                        <path d="M6 9l6 6 6-6"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center p-large">
                                            No attachments were uploaded for this order.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-medium">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('orders.show', $order) }}"
                                    class="btn btn-default"
                                    style="margin: 0.1rem">
                                    Back to Order
                                </a>
                                <button type="submit" class="btn btn-primary" style="margin: 0.1rem">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('orders.addAttachments', $order) }}"
                        enctype="multipart/form-data"
                        method="POST"
                        class="card form-attachments p-medium mb-large"
                        >
                        @csrf
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
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 8v8m-4-4h8"
                                        />
                                </svg>
                            </div>
                            <input id="orderFormAttachmentUpload"
                                type="file"
                                name="attachments[]"
                                multiple
                                accept="image/*,video/*,application/pdf,application/msword,
                                        application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain"
                                />
                        </div>

                        @error('attachments')
                        <p class="error-message">{{ $message }}</p>
                        @enderror
                            
                        @error('attachments.*')
                        <p class="error-message">{{ $message }}</p>
                        @enderror
                
                        <div id="attachmentPreviews" class="order-form-attachments"></div>
                        <div id="attachmentsList" style="margin-top: 20px;"></div>
                            
                        <p class="info-message">
                            <small>
                                Supported formats: Images, Videos, PDF, DOC, DOCX, TXT<br>
                                Maximum total upload size: {{ ini_get('post_max_size') }}<br>
                                Maximum individual file size: {{ ini_get('upload_max_filesize') }}
                            </small>
                        </p>
                        <div class="p-medium">
                            <div class="flex justify-end">
                                <button id="addAttachmentsButton" class="btn btn-primary" disabled>Add Attachments</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
  
@vite(['resources/js/listings-attachments.js'])