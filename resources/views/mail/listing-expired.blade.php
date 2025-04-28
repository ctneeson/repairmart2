<h1>Listing Expired</h1>
<h2>
    {{$listing->title}}
</h2>
<p>
    Your listing has expired.</br>
    You can view your listing and renew it by clicking the link below.</br>
    <a href="{{ route('listings.show', $listing->id) }}">
        View Listing
    </a></br>
</p>