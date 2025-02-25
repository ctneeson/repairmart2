<div class="card">
    <div class="card-header">{{$title}}</div>
    @if($slot->isEmpty())
        <p>Content missing</p>
    @else
        {{$slot}}
    @endif
    <div class="card-footer">{{$footer}}</div>
</div>