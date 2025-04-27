<x-app-layout title="Messages" bodyClass="page-email-index">
    <main>
        <div class="container-small">
            <h1 class="email-index-page-title">Messages</h1>
            
            <div class="card p-medium">
                <div class="card-header" style="background-color: white; border-bottom: 1px solid #e2e8f0; padding: 0;">
                    <ul class="nav nav-tabs card-header-tabs" style="margin-left: 1rem;">
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab', 'inbox') === 'inbox' ? 'active' : '' }}" href="{{ route('email.index', ['tab' => 'inbox']) }}">
                                Inbox
                                @if($unreadCount > 0)
                                    <span class="badge rounded-pill" style="color: white; background-color: #3490dc; margin-left: 5px;">
                                        ({{ $unreadCount }})
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('tab') === 'sent' ? 'active' : '' }}" href="{{ route('email.index', ['tab' => 'sent']) }}">Sent</a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-medium">
                    <!-- Inbox Tab -->
                    <div class="tab-pane fade {{ request('tab', 'inbox') === 'inbox' ? 'show active' : '' }}" id="inbox" 
                         style="{{ request('tab', 'inbox') === 'inbox' ? '' : 'display: none;' }}">
                        @if($receivedEmails->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>From</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th style="text-align: center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($receivedEmails as $email)
                                            <tr style="{{ is_null($email->read_at) ? 'font-weight: bold; background-color: #f8fafc;' : '' }}">
                                                <td>{{ $email->sender->name }}</td>
                                                <td>
                                                    <a href="{{ route('email.show', $email->id) }}" class="text-decoration-none">
                                                        {{ $email->subject }}
                                                        @if($email->attachments_count > 0)
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 5px; vertical-align: middle;">
                                                                <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
                                                            </svg>
                                                        @endif
                                                    </a>
                                                </td>
                                                <td>{{ $email->created_at->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    @if(is_null($email->read_at))
                                                        <span class="badge"
                                                            style="background-color: #3490dc;
                                                                color: white;
                                                                padding: 3px 8px;
                                                                border-radius: 9999px;
                                                                font-size: 12px;">New</span>
                                                    @else
                                                        <span class="badge"
                                                            style="background-color: #718096;
                                                                color: white;
                                                                padding: 3px 8px;
                                                                border-radius: 9999px;
                                                                font-size: 12px;">Read</span>
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
                    <div class="tab-pane fade {{ request('tab') === 'sent' ? 'show active' : '' }}" id="sent" 
                         style="{{ request('tab') === 'sent' ? '' : 'display: none;' }}">
                        @if($sentEmails->count() > 0)
                            <div class="table-responsive">
                                <table class="table" style="border-collapse: collapse; width: 100%;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #e2e8f0;">
                                            <th>To</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th style="text-align: center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sentEmails as $email)
                                            <tr>
                                                <td>
                                                    @foreach($email->recipients as $recipient)
                                                        <span>{{ $recipient->name }}</span>@if(!$loop->last),@endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <a href="{{ route('email.show', $email->id) }}" class="text-decoration-none">
                                                        {{ $email->subject }}
                                                        @if($email->attachments_count > 0)
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 5px; vertical-align: middle;">
                                                                <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
                                                            </svg>
                                                        @endif
                                                    </a>
                                                </td>
                                                <td>{{ $email->created_at->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    @if(is_null($email->read_at))
                                                        <span class="badge"
                                                            style="background-color: #718096;
                                                                color: white;
                                                                padding: 3px 8px;
                                                                border-radius: 9999px;
                                                                font-size: 12px;">Unread</span>
                                                    @else
                                                        <span class="badge"
                                                            style="background-color: #38a169;
                                                                color: white;
                                                                padding: 3px 8px;
                                                                border-radius: 9999px;
                                                                font-size: 12px;">Read</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-medium">
                                {{ $sentEmails->appends(['tab' => 'sent'])->links() }}
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
</x-app-layout>