@php
  $isInWatchlist = $listing->isInWatchlist(Auth::user())
@endphp

<x-app-layout>
  <main>
      <div class="container">
        <h1 class="listing-details-page-title">{{$listing->title}}</h1>
        <div class="listing-details-region">
          {{$listing->city}}, {{$listing->country->name}}
           - {{$listing->published_at}}
        </div>

        <div class="listing-details-content">
          <div class="listing-attachments-and-description">
            <div class="listing-attachments-carousel">
              <div class="listing-attachment-wrapper">
                @php
                $attachmentUrl = $listing->primaryAttachment?->getUrl() ?: '/img/no-photo-available.jpg';
                $filePath = $listing->primaryAttachment ? Storage::disk('public')->path($listing->primaryAttachment->path) : public_path($attachmentUrl);
                $mimeType = $listing->primaryAttachment ? mime_content_type($filePath) : 'image/jpeg';
              @endphp
              @if (str_starts_with($mimeType, 'image/'))
                <img
                  src="{{ $attachmentUrl }}"
                  alt=""
                  class="listing-active-attachment"
                  id="activeAttachment"
                />
              @elseif (str_starts_with($mimeType, 'video/'))
                <video
                  src="{{ $attachmentUrl }}"
                  class="listing-active-attachment"
                  id="activeAttachment"
                  controls
                ></video>
              @else
                <img
                  src="/img/no-photo-available.jpg"
                  alt=""
                  class="listing-active-attachment"
                  id="activeAttachment"
                />
              @endif

              </div>
              @if($listing->attachments->count() > 1)
                <div class="listing-attachment-thumbnails">
                  @foreach ($listing->attachments as $attachment)
                    @php
                      $attachmentUrl = $attachment->getUrl();
                      $filePath = Storage::disk('public')->path($attachment->path);
                      $mimeType = mime_content_type($filePath);
                    @endphp
                    @if (str_starts_with($mimeType, 'image/'))
                      <img src="{{$attachmentUrl}}" alt="" data-mime-type="{{$mimeType}}" />
                    @elseif (str_starts_with($mimeType, 'video/'))
                      <video src="{{$attachmentUrl}}" class="listing-form-attachment-preview" muted data-mime-type="{{$mimeType}}" ></video>
                    @else
                      <img src="/img/no-photo-available.jpg" alt="" data-mime-type="image/jpeg" />
                    @endif
                  @endforeach
                </div>
                <button class="carousel-button prev-button" id="prevButton">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    style="width: 64px"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15.75 19.5 8.25 12l7.5-7.5"
                    />
                  </svg>
                </button>
                <button class="carousel-button next-button" id="nextButton">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    style="width: 64px"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m8.25 4.5 7.5 7.5-7.5 7.5"
                    />
                  </svg>
                </button>
              @endif
            </div>

            <div class="card listing-detailed-description">
              <h2 class="listing-details-title">Detailed Description</h2>
                {!!$listing->description!!}
            </div>
          </div>
          <div class="listing-details card">
            <div class="flex items-center justify-between">
              <p class="listing-details-price">{{$listing->currency->iso_code}} {{$listing->budget}}</p>

              @auth
              <button class="btn-heart text-primary"
                data-url="{{ route('watchlist.storeDestroy', $listing) }}">
                <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                style="width: 16px"
                @class([
                  'hidden' => $isInWatchlist
                ])
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.563.563 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"
                />
              </svg>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                style="width: 16px"
                @class([
                  'hidden' => !$isInWatchlist
                ])
              >
                <path
                  fill-rule="evenodd"
                  d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006Z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            @endauth
            </div>

            <hr />
            <table class="listing-details-table">
              <tbody>
                <tr>
                  <th>Manufacturer</th>
                    <td>{{$listing->manufacturer->name}}</td>
                </tr>
                <tr>
                  <th>Product Categories</th>
                  @foreach($listing->products as $product)
                  <tr>
                    <td colspan="2">{{$product->category}} > {{$product->subcategory}}</td>
                  </tr>
                  @endforeach
                </tr>
                <tr>
                  <th>Location</th>
                  <td>{{$listing->city}}, {{$listing->country->name}}</td>
                </tr>
              </tbody>
            </table>

            <hr />

            <div class="flex gap-1 my-medium">
              <img
                src="/img/avatar.png"
                alt=""
                class="listing-details-owner-image"
              />
              <div>
                <h3 class="listing-details-owner">{{$listing->customer->name}}</h3>
                <div class="text-muted">{{$listing->customer->listingsCreated()->count()}} listings</div>
              </div>
            </div>
            {{-- Hide if current listing has no phone number --}}
            @if ($listing->phone && !empty($listing->phone))
            <div class="listing-details-phone">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79
                    0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 
                    2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0
                    1 22 16.92z"></path>
                </svg>
                <span id="phone-number" class="listing-details-phone-number">{{Str::mask($listing->phone, '*', -5)}}</span>
                <span class="listing-details-phone-view"
                    data-url="{{ route('listings.showPhone', $listing) }}">
                    view full number
                </span>
            </div>
            @endif
            {{-- Hide if current user is looking at their own listing --}}
            @if (auth()->id() !== $listing->user_id)
            <a href="mailto:{{$listing->customer->email}}" class="listing-details-email btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
              Message Customer
            </a>
            @endif
            {{-- Hide unless current user is a specialist or admin --}}
            @if (auth()->check() && auth()->user()->roles->whereIn('name', ['specialist', 'admin'])->count() > 0
                && auth()->id() !== $listing->user_id)
            <a href="{{route('quotes.create', $listing->id)}}" class="listing-details-createquote btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
              </svg>
              Create Quote
            </a>
            @endif
            {{-- Hide unless current user is looking at their own listing --}}
            @if (auth()->id() === $listing->user_id)
            <a href="{{route('listings.edit', $listing->id)}}" class="listing-details-edit btn">
              <svg xmlns="http://www.w3.org/2000/svg"
               width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round">
               <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
               <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
              </svg>
              Edit Listing
            </a>
            @endif
          </div>
        </div>
      </div>
  </main>
</x-app-layout>