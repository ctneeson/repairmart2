<h1>Listing Created</h1>
<h2>
    {{$listing->title}}
</h2>
<p>
    Thank you. Your listing is now live on RepairMart.
</p>
<p>
    <a href="{{ url('/listings/' . $listing->id) }}">View your listing</a>
</p>
