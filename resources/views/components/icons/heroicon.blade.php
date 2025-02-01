@props(['icon', 'class' => 'w-5 h-5'])

@php
    $iconHtml = FilamentIcon::resolve($icon)?->toHtml();
@endphp

@if($iconHtml)
    <span class="{{ $class }}">{!! $iconHtml !!}</span>
@endif 