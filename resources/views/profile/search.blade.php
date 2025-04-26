<x-app-layout title="Search Users" bodyClass="page-my-listings">
  <main>
      <div>
        <div class="container">
          <h1 class="listing-details-page-title">User Profiles</h1>
          
          <!-- Search Form -->
          <div class="card mb-medium p-medium">
            <form action="{{ route('profile.search') }}" method="GET" class="search-form">
              <div class="flex items-center" style="display: flex; align-items: center; gap: 10px;">
                <div class="flex-grow" style="flex-grow: 1;">
                  <input 
                    type="text" 
                    name="q" 
                    placeholder="Search by name or email..." 
                    value="{{ request('q') }}" 
                    class="form-control w-full"
                    style="width: 100%; padding: 8px 12px; border-radius: 4px; border: 1px solid #ddd;"
                  >
                </div>
                <div>
                  <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; margin-right: 4px; display: inline-block; vertical-align: middle;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Search
                  </button>
                </div>
                @if(request('q'))
                  <div>
                    <a href="{{ route('profile.search') }}" class="btn btn-default">
                      Clear
                    </a>
                  </div>
                @endif
              </div>
            </form>
          </div>
          
          <!-- Results Table -->
          <div class="card p-medium">
            <div class="table-responsive">
              <table class="table" style="border-collapse: collapse; width: 100%;">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Created</th>
                    <th style="text-align: center;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($users as $user)
                  <tr>
                    <td><a href="{{ route('profile.show', $user->id) }}">
                      {{$user->name}}
                      </a>
                    </td>
                    <td>{{$user->email}}</td>
                    <td>
                      @foreach($user->roles as $role)
                        <span class="badge role-badge bg-{{ strtolower(str_replace(' ', '-', $role->name)) }}">
                          {{$role->name}}
                        </span>
                      @endforeach
                    </td>
                    <td>{{ $user->getCreatedDate() }}</td>
                    <td class="actions-cell">
                      <div class="action-buttons-container">
                        <!-- Edit Button -->
                        <a
                          href="{{route('profile.admin.index', $user->id)}}"
                          class="btn btn-edit"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652
                              2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5
                              4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125">
                            </path>
                          </svg>
                          Edit
                        </a>
                        
                        <!-- Edit Button -->
                        <a
                          href="{{ route('email.create', ['recipient_ids' => [$user->id]]) }}"
                          class="btn btn-edit"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2
                              2-2z">
                            </path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                          </svg>
                          Message
                        </a>

                        <!-- Delete Button -->
                        <form action="{{ route('profile.admin.destroy', $user) }}"
                          method="POST" style="width: 100%;">
                        @csrf
                        @method('DELETE')

                        @if(Auth::id() === $user->id)
                          <!-- Disabled delete button for current user -->
                          <button type="button" class="btn btn-delete" disabled
                            title="You cannot delete your own account">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                              stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107
                                1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244
                                2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456
                                0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114
                                1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964
                                51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                              </path>
                            </svg>
                            Delete
                          </button>
                        @else
                          <!-- Active delete button for other users -->
                          <button onclick="return confirm('Delete this user?')"
                                  class="btn btn-delete">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                              stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107
                                1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244
                                2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456
                                0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114
                                1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964
                                51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                              </path>
                            </svg>
                            Delete
                          </button>
                        @endif
                        </form>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="text-center p-large"
                      style="padding: 24px; text-align: center;">
                      No users found.
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <div class="mt-medium">
              {{ $users->onEachSide(3)->links() }}
            </div>
          </div>
        </div>
      </div>
  </main>
</x-app-layout>