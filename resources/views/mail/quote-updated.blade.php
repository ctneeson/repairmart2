<h1>Quote Updated</h1>
<h2>
    {{$listing->title}}
</h2>
<p>
    A quote received for your listing <a href="{{ route('listings.show', $listing->id) }}">{{ $listing->title }}</a> has been updated.<br>
    To view the quote, click <a href="{{ route('quotes.show', $quote->id) }}">here</a>.<br>
    To review all quotes received for this listing, click <a href="{{ route('quotes.index', ['listing_id' => $listing->id, 'tab' => 'received']) }}">here</a>.
</p>