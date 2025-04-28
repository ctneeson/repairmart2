<h1>Quote Received</h1>
<h2>
    {{$listing->title}}
</h2>
<p>
    You have received a quote for your listing <a href="{{ route('listings.show', $listing->id) }}">{{ $listing->title }}</a>.<br>
    To view the quote, click <a href="{{ route('quotes.show', $quote->id) }}">here</a>.<br>
    To review all quotes received for this listing, click <a href="{{ route('quotes.index', ['listing_id' => $listing->id, 'tab' => 'received']) }}">here</a>.
</p>