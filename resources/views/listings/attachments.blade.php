<x-app-layout bodyClass="page-my-listings">
  <main>
    <div>
      <div class="container">
        <h1 class="listing-details-page-title">Manage attachments for: {{ $listing->title }}</h1>
        <div class="listing-attachments-wrapper">
          <form action="{{ route('listings.updateAttachments', $listing) }}"
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
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($listing->attachments as $attachment)
                  <tr>
                    <td>
                      <input type="checkbox"
                            name="delete_attachments[]"
                            id="delete_attachment_{{ $attachment->id }}"
                            value="{{ $attachment->id }}"
                      />
                    </td>
                    <td  class="listing-form-attachments">
                      @if(Str::startsWith($attachment->mime_type, 'image/'))
                        <img src="{{ $attachment->getUrl() }}" alt="" class="listing-form-attachment-preview" />
                      @elseif(Str::startsWith($attachment->mime_type, 'video/'))
                        <video src="{{ $attachment->getUrl() }}" class="listing-form-attachment-preview" controls muted></video>
                      @else
                        <div>Unknown type: {{ $attachment->mime_type }}</div>
                      @endif
                    </td>
                    <td class="position-controls">
                      <input type="hidden" name="positions[{{ $attachment->id }}]" value="{{ old('positions.'.$attachment->id, $attachment->position) }}" class="position-input" />
                      <div class="position-buttons">
                        <button type="button" class="btn btn-sm move-up" title="Move up" data-id="{{ $attachment->id }}">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 15l-6-6-6 6"/>
                          </svg>
                        </button>
                        <button type="button" class="btn btn-sm move-down" title="Move down" data-id="{{ $attachment->id }}">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="3" class="text-center p-large">
                      No attachments were uploaded for this listing.
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            <div class="p-medium">
              <div class="flex justify-end">
              <button class="btn btn-primary">Save Changes</button>
              </div>
            </div>
          </form>
          <form action="{{ route('listings.addAttachments', $listing) }}"
                enctype="multipart/form-data"
                method ="POST"
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
                  <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 8v8m-4-4h8"
                    />
                </svg>
              </div>
              <input id="listingFormAttachmentUpload" type="file" name="attachments[]" multiple accept="image/*,video/*" />
            </div>

            @error('attachments')
            <p class="error-message">{{ $message }}</p>
            @enderror
            
            @error('attachments.*')
                <p class="error-message">{{ $message }}</p>
            @enderror

            <div id="attachmentPreviews" class="listing-form-attachments"></div>
            <div id="attachmentsList" style="margin-top: 20px;"></div>
            
            <p class="info-message">
                <small>
                    Supported formats: JPEG, PNG, JPG, GIF, SVG, MP4, MOV, OGG, QT<br>
                    Maximum total upload size: {{ ini_get('post_max_size') }}<br>
                    Maximum individual file size: {{ ini_get('upload_max_filesize') }}
                </small>
            </p>
            <div class="p-medium">
              <div class="flex justify-end">
              <button class="btn btn-primary">Add Attachments</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</x-app-layout>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Get all attachments
    const rows = Array.from(document.querySelectorAll('tr')).filter(row => {
      return row.querySelector('.position-input');
    });
    
    // Sort rows by position
    rows.sort((a, b) => {
      const posA = parseInt(a.querySelector('.position-input').value);
      const posB = parseInt(b.querySelector('.position-input').value);
      return posA - posB;
    });
    
    // Move Up button click handler
    document.querySelectorAll('.move-up').forEach(button => {
      button.addEventListener('click', function() {
        const currentRow = this.closest('tr');
        const currentPos = parseInt(currentRow.querySelector('.position-input').value);
        const currentIndex = rows.indexOf(currentRow);
        
        // Don't move if already at the top
        if (currentIndex === 0) return;
        
        // Swap with the row above
        const prevRow = rows[currentIndex - 1];
        const prevPos = parseInt(prevRow.querySelector('.position-input').value);
        
        // Update hidden input values only (no display elements to update)
        currentRow.querySelector('.position-input').value = prevPos;
        prevRow.querySelector('.position-input').value = currentPos;
        
        // Reorder in the array
        rows[currentIndex] = prevRow;
        rows[currentIndex - 1] = currentRow;
        
        // Reorder in the DOM
        const tbody = currentRow.parentNode;
        tbody.insertBefore(currentRow, prevRow);
        
        // Update visual states
        updateButtonStates();
      });
    });
    
    // Move Down button click handler
    document.querySelectorAll('.move-down').forEach(button => {
      button.addEventListener('click', function() {
        const currentRow = this.closest('tr');
        const currentPos = parseInt(currentRow.querySelector('.position-input').value);
        const currentIndex = rows.indexOf(currentRow);
        
        // Don't move if already at the bottom
        if (currentIndex === rows.length - 1) return;
        
        // Swap with the row below
        const nextRow = rows[currentIndex + 1];
        const nextPos = parseInt(nextRow.querySelector('.position-input').value);
        
        // Update hidden input values only (no display elements to update)
        currentRow.querySelector('.position-input').value = nextPos;
        nextRow.querySelector('.position-input').value = currentPos;
        
        // Reorder in the array
        rows[currentIndex] = nextRow;
        rows[currentIndex + 1] = currentRow;
        
        // Reorder in the DOM - this is what visually moves the rows
        const tbody = currentRow.parentNode;
        tbody.insertBefore(nextRow, currentRow);
        
        // Update visual states
        updateButtonStates();
      });
    });
    
    // Function to update which buttons should be enabled/disabled
    function updateButtonStates() {
      rows.forEach((row, index) => {
        const upButton = row.querySelector('.move-up');
        const downButton = row.querySelector('.move-down');
        
        // Disable up button for first row
        if (index === 0) {
          upButton.classList.add('disabled');
          upButton.setAttribute('disabled', 'disabled');
        } else {
          upButton.classList.remove('disabled');
          upButton.removeAttribute('disabled');
        }
        
        // Disable down button for last row
        if (index === rows.length - 1) {
          downButton.classList.add('disabled');
          downButton.setAttribute('disabled', 'disabled');
        } else {
          downButton.classList.remove('disabled');
          downButton.removeAttribute('disabled');
        }
      });
    }
    
    // Initialize button states
    updateButtonStates();
  });
  </script>