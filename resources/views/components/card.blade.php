@props(['color', 'bgColor'=>'white'])

<div {{$attributes->merge(['lang'=>'pt'])
                  ->class('card card-text-$color card-bg-$bgColor')}}>
    <div {{ $title->attributes->class('card-header') }}>
        {{ $title }}
    </div>
    @if($slot->isEmpty())
        <p>Content missing</p>
    @else
        {{$slot}}
    @endif
    <div class="card-footer">{{$footer}}</div>
</div>