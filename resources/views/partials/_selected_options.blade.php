{{--
    Reusable partial: resources/views/partials/_selected_options.blade.php

    Usage (anywhere you have an $item with selected_options):
        @include('partials._selected_options', ['options' => $item->selected_options])

    $options is the JSON-decoded array, e.g.:
    [
      {"option_name":"Color","value":"Red","image_url":"https://..."},
      {"option_name":"Size","value":"XL","image_url":null}
    ]
--}}

@php
    $opts = is_string($options ?? null) ? json_decode($options, true) : ($options ?? []);
@endphp

@if(!empty($opts))
<div class="d-flex flex-wrap gap-1 mt-1">
    @foreach($opts as $opt)
    <span style="
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2px 8px 2px 4px;
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
    ">
        @if(!empty($opt['image_url']))
        <img src="{{ $opt['image_url'] }}"
             style="width:16px;height:16px;border-radius:50%;object-fit:cover;flex-shrink:0;"
             alt="{{ $opt['value'] ?? '' }}">
        @endif
        <span style="color:#6b7280;font-weight:400;">{{ $opt['option_name'] ?? '' }}:</span>
        <span>{{ $opt['value'] ?? '' }}</span>
    </span>
    @endforeach
</div>
@endif