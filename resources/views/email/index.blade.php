<x-app-layout title="Messages" bodyClass="page-email-index">
    <main>
        <div class="container-small">
            <h1 class="email-index-page-title">Messages</h1>
            
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('email.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 5px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Message
                </a>
            @endif

            <div class="card p-medium">
                <div class="card-header" style="background-color: white; border-bottom: 1px solid #e2e8f0; padding: 0;">
                    <ul class="nav nav-tabs card-header-tabs" style="margin-left: 1rem;">
                        <li class="nav-item">
                            <a class="nav-link active" href="#inbox">
                                Inbox
                                @if($unreadCount > 0)
                                    <span class="badge rounded-pill" style="color: white; background-color: #3490dc; margin-left: 5px;">
                                        ({{ $unreadCount }})
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#sent">Sent</a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-medium">
                    <!-- Inbox Tab -->
                    <div class="tab-pane fade show active" id="inbox">
                        @if($receivedEmails->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #e2e8f0;">
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 30%;">From</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 45%;">Subject</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 15%;">Date</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 10%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($receivedEmails as $email)
                                            <tr style="border-bottom: 1px solid #e2e8f0; {{ is_null($email->read_at) ? 'font-weight: bold; background-color: #f8fafc;' : '' }}">
                                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">{{ $email->sender->name }}</td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <a href="{{ route('email.show', $email->id) }}" class="text-decoration-none">
                                                        {{ $email->subject }}
                                                        @if($email->attachments_count > 0)
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 5px; vertical-align: middle;">
                                                                <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
                                                            </svg>
                                                        @endif
                                                    </a>
                                                </td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">{{ $email->created_at->format('M d, Y') }}</td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    @if(is_null($email->read_at))
                                                        <span class="badge" style="background-color: #3490dc; color: white; padding: 3px 8px; border-radius: 9999px; font-size: 12px;">New</span>
                                                    @else
                                                        <span class="badge" style="background-color: #718096; color: white; padding: 3px 8px; border-radius: 9999px; font-size: 12px;">Read</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-medium">
                                {{ $receivedEmails->links() }}
                            </div>
                        @else
                            <div class="text-center py-large">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="#718096" style="width: 64px; height: 64px; margin: 0 auto 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V19.5z" />
                                </svg>
                                <h3>Your inbox is empty</h3>
                                <p class="text-muted">You don't have any messages yet.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Sent Tab -->
                    <div class="tab-pane fade" id="sent">
                        @if($sentEmails->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #e2e8f0;">
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 30%;">To</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 45%;">Subject</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 15%;">Date</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 10%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sentEmails as $email)
                                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    @foreach($email->recipients as $recipient)
                                                        <span>{{ $recipient->name }}</span>@if(!$loop->last),@endif
                                                    @endforeach
                                                </td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    <a href="{{ route('email.show', $email->id) }}" class="text-decoration-none">
                                                        {{ $email->subject }}
                                                        @if($email->attachments_count > 0)
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 5px; vertical-align: middle;">
                                                                <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
                                                            </svg>
                                                        @endif
                                                    </a>
                                                </td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">{{ $email->created_at->format('M d, Y') }}</td>
                                                <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                                    @if(is_null($email->read_at))
                                                        <span class="badge" style="background-color: #718096; color: white; padding: 3px 8px; border-radius: 9999px; font-size: 12px;">Unread</span>
                                                    @else
                                                        <span class="badge" style="background-color: #38a169; color: white; padding: 3px 8px; border-radius: 9999px; font-size: 12px;">Read</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-medium">
                                {{ $sentEmails->links() }}
                            </div>
                        @else
                            <div class="text-center py-large">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="#718096" style="width: 64px; height: 64px; margin: 0 auto 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                                <h3>No sent messages</h3>
                                <p class="text-muted">You haven't sent any messages yet.</p>
                                
                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('email.create') }}" class="btn btn-primary mt-medium">
                                        Compose New Message
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all elements
            const tabs = document.querySelectorAll('.nav-link');
            const tabContents = document.querySelectorAll('.tab-pane');
            
            // Make sure "Inbox" tab is active by default
            showTab('inbox');
            
            // Add click handlers to each tab
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get the target tab ID from the href attribute
                    const targetId = this.getAttribute('href').substring(1);
                    showTab(targetId);
                });
            });
            
            function showTab(tabId) {
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.style.display = 'none';
                    content.classList.remove('show', 'active');
                });
                
                // Remove active class from all tabs
                tabs.forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Show the selected tab content
                const selectedTab = document.getElementById(tabId);
                selectedTab.style.display = 'block';
                selectedTab.classList.add('show', 'active');
                
                // Add active class to the selected tab
                const activeTabLink = document.querySelector(`.nav-link[href="#${tabId}"]`);
                activeTabLink.classList.add('active');
            }
        });
    </script>
    @endpush

</x-app-layout>