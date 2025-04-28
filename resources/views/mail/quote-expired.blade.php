<h1>Quote Rejected for Expired Listing</h1>
<h2>
    {{$listing->title}}
</h2>
<p>
    A quote that you submitted for Listing <a href="{{ route('listings.show', $listing->id) }}">{{ $listing->title }}</a> has been rejected, due to the listing's expiry.<br>
    To browse listings and submit further quotes, click <a href="{{ route('home') }}">here</a>.
</p>