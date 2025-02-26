@props(['title' => ''])

<x-base-layout :title='$title'>
    @include('layouts.partials.header');
    {{$slot}}
    @section('footerLinks')
        <a href="#">Link 1</a>
        <a href="#">Link 2</a>
    @show
</x-base-layout>