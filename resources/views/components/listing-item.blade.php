@props(['listing', 'isInWatchlist'=>false])

<div class="listing-item card">
    <a href="{{route('listings.show', $listing->id)}}">
      @php
      $attachmentUrl = $listing->primaryAttachment?->getUrl() ?: '/img/no-photo-available.jpg';
      $filePath = $listing->primaryAttachment ? Storage::disk('public')->path($listing->primaryAttachment->path) : public_path($attachmentUrl);
      $mimeType = $listing->primaryAttachment ? mime_content_type($filePath) : 'image/jpeg';
    @endphp
    @if (str_starts_with($mimeType, 'image/'))
      <img
        src="{{ $attachmentUrl }}"
        alt=""
        class="listing-item-img rounded-t"
      />
    @elseif (str_starts_with($mimeType, 'video/'))
      <video
        src="{{ $attachmentUrl }}"
        class="listing-item-img rounded-t"
      ></video>
    @else
      <img
        src="/img/no-photo-available.jpg"
        alt=""
        class="listing-item-img rounded-t"
      />
    @endif
    </a>
    <div class="p-medium">
      <div class="flex items-center justify-between">
        <small class="m-0 text-muted">
          @if($listing->use_default_location==0)
          {{$listing->city}}, {{$listing->country->name}}
          @else
          {{$listing->customer->city}}, {{$listing->customer->country->name}}
          @endif
      </small>
      <form action="{{ route('watchlist.storeDestroy', $listing) }}" method="POST">
        @csrf
        <button class="btn-heart text-primary">
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
      </form>
      </div>
      <h2 class="listing-item-title">{{$listing->manufacturer->name}} - {{$listing->title}}</h2>
      <p class="listing-item-price">{{$listing->currency->iso_code}} {{$listing->budget}}</p>
      <hr />
      <p class="m-0">
        @foreach($listing->products as $product)
          <span class="listing-item-badge">{{$product->subcategory}}</span>
        @endforeach
      </p>
    </div>
  </div>