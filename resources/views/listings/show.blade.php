<x-app-layout>
  <main>
      <div class="container">
        <h1 class="listing-details-page-title">{{$listing->title}}</h1>
        <div class="listing-details-region">
          @if($listing->use_default_location==0)
          {{$listing->city}}, {{$listing->country->name}}
          @else
          {{$listing->customer->city}}, {{$listing->customer->country->name}}
          @endif
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
              <button class="btn-heart">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  style="width: 20px"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
                  />
                </svg>
              </button>
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
                  @if($listing->use_default_location==0)
                  <td>{{$listing->city}}, {{$listing->country->name}}</td>
                  @else
                  <td>{{$listing->customer->city}}, {{$listing->customer->country->name}}</td>
                  @endif
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
            <a href="tel: {{Str::mask($listing->customer->phone, '*', -2)}}" class="listing-details-phone">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                style="width: 16px"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"
                />
              </svg>

              {{Str::mask($listing->customer->phone, '*', -2)}}
              <span class="listing-details-phone-view">view full number</span>
            </a>
            <a href="mailto:{{$listing->customer->email}}" class="listing-details-email btn">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                style="width: 16px"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M21 12 12 6 3 12m0 0 9 6 9-6Z"
                />
              </svg>
              Message Customer
            </a>
            <a href="{{route('quotes.create', $listing->id)}}" class="listing-details-email btn">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                style="width: 16px"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M21 12 12 6 3 12m0 0 9 6 9-6Z"
                />
              </svg>
              Create Quote
            </a>
          </div>
        </div>
      </div>
  </main>
</x-app-layout>