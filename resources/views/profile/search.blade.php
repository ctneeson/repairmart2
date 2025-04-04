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
                  <tr style="border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Name</th>
                    <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Email</th>
                    <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Roles</th>
                    <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Created</th>
                    <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($users as $user)
                  <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">{{$user->name}}</td>
                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">{{$user->email}}</td>
                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                      @foreach($user->roles as $role)
                        <span class="role-badge" style="display: inline-block; background-color: #e2e8f0; color: #4a5568; padding: 2px 8px; border-radius: 9999px; font-size: 0.875rem; margin-right: 4px; margin-bottom: 4px;">
                          {{$role->name}}
                        </span>
                      @endforeach
                    </td>
                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">{{ $user->getCreatedDate() }}</td>
                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;" class="actions-cell">
                      <div class="flex flex-col space-y-2" style="display: flex; flex-direction: column; gap: 8px;">
                        <!-- Edit Button -->
                        <a
                          href="{{route('profile.admin.index', $user->id)}}"
                          class="btn btn-edit inline-flex items-center w-full"
                          style="width: 100%; justify-content: flex-start;"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            style="width: 12px; margin-right: 5px"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                            />
                          </svg>
                          edit
                        </a>
                        
                        <!-- Delete Button -->
                        <form action="{{ route('profile.admin.destroy', $user) }}"
                          method="POST" style="width: 100%;">
                        @csrf
                        @method('DELETE')

                        @if(Auth::id() === $user->id)
                          <!-- Disabled delete button for current user -->
                          <button type="button" 
                                  class="btn btn-delete inline-flex items-center w-full opacity-50 cursor-not-allowed"
                                  style="width: 100%; justify-content: flex-start; opacity: 0.5;" 
                                  disabled
                                  title="You cannot delete your own account">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke-width="1.5"
                              stroke="currentColor"
                              style="width: 12px; margin-right: 5px"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                              />
                            </svg>
                            delete
                          </button>
                        @else
                          <!-- Active delete button for other users -->
                          <button onclick="return confirm('Delete this user?')"
                                  class="btn btn-delete inline-flex items-center w-full"
                                  style="width: 100%; justify-content: flex-start;">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke-width="1.5"
                              stroke="currentColor"
                              style="width: 12px; margin-right: 5px"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                              />
                            </svg>
                            delete
                          </button>
                        @endif
                        </form>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="text-center p-large" style="padding: 24px; text-align: center;">
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