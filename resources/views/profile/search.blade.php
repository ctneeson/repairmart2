<x-app-layout bodyClass="page-my-listings">
    <main>
        <div>
          <div class="container">
            <h1 class="listing-details-page-title">User Profiles</h1>
            <div class="card p-medium">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Roles</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($users as $user)
                    <tr>
                      <td>{{$user->name}}</td>
                      <td>{{$user->email}}</td>
                      <td>
                        @foreach($user->roles as $role)
                        <div>{{$role->name}}</div><br>
                        @endforeach
                      </td>
                      <td>{{ $user->getCreatedDate() }}</td>
                      <td class="actions-cell">
                        <div class="flex flex-col space-y-2" style="display: flex; flex-direction: column; gap: 8px;">
                          <!-- Edit Button -->
                          <a
                            href="{{route('profile.edit', $user->id)}}"
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
                          <form action="{{ route('profile.destroy', $user) }}"
                            method="POST" style="width: 100%;">
                            @csrf
                            @method('DELETE')
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
                          </form>
                        </div>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="7" class="text-center p-large">
                        No users found.
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
  
              {{ $users->onEachSide(3)->links() }}
            </div>
          </div>
        </div>
    </main>
  </x-app-layout>