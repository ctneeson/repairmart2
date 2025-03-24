<x-app-layout bodyClass="page-my-listings">
  <main>
      <div>
        <div class="container">
          <h1 class="listing-details-page-title">My Listings</h1>
          <div class="card p-medium">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Manufacturer</th>
                    <th>Product Categories</th>
                    <th>Date</th>
                    <th>Published</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($listings as $listing)
                  <tr>
                    <td>
                      @php
                        $attachmentUrl = $listing->primaryAttachment?->getUrl() ?: '/img/no-photo-available.jpg';
                        $filePath = $listing->primaryAttachment ? Storage::disk('public')->path($listing->primaryAttachment->path) : public_path($attachmentUrl);
                        $mimeType = $listing->primaryAttachment ? mime_content_type($filePath) : 'image/jpeg';
                      @endphp
                      @if (str_starts_with($mimeType, 'image/'))
                        <img
                          src="{{ $attachmentUrl }}"
                          alt=""
                          class="my-listings-img-thumbnail"
                        />
                      @elseif (str_starts_with($mimeType, 'video/'))
                        <video
                          src="{{ $attachmentUrl }}"
                          class="my-listings-img-thumbnail"
                          {{-- controls
                          style="max-width: 100px; max-height: 100px;" --}}
                        ></video>
                      @else
                        <img
                          src="/img/no-photo-available.jpg"
                          alt=""
                          class="my-listings-img-thumbnail"
                        />
                      @endif
                    </td>
                    <td>{{$listing->title}}</td>
                    <td>{{$listing->manufacturer->name}}</td>
                    <td>
                      @foreach($listing->products as $product)
                      <div>{{$product->category}} > {{$product->subcategory}}</div><br>
                      @endforeach
                    </td>
                    <td>{{ $listing->getCreatedDate() }}</td>
                    <td>{{$listing->published_at ? 'Yes' : 'No'}}</td>
                    <td class="">
                      <a
                        href="{{route('listings.edit', $listing->id)}}"
                        class="btn btn-edit inline-flex items-center"
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
                      <a
                        href="{{ route('listings.attachments', $listing) }}"
                        class="btn btn-edit inline-flex items-center"
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
                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                        />
                      </svg>
                      attachments
                      </a>
                      <form action="{{ route('listings.destroy', $listing) }}"
                        method="POST" class="inline-flex">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Delete this listing?')"
                                class="btn btn-delete inline-flex items-center">
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
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center p-large">
                      No listings found. <a href="{{route('listings.create')}}">Create a new listing</a>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            {{ $listings->onEachSide(3)->links() }}
          </div>
        </div>
      </div>
  </main>
</x-app-layout>