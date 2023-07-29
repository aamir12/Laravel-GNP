@if (isset($logo) && $logo)
    <img src="{{ $logo }}" width="60%" class="mx-auto mb-8"/>
@else
    <h1 {{ $attributes }}>{{ config('app.name', 'EARNIE') }}</h1>
@endif