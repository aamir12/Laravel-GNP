@extends('emails.master', ['branding' => $branding])
@section('content')

@php
    $greeting = 'Hi';

    if (isset($recipient->first_name) && $recipient->first_name !== '') {
        $greeting .= ' ' . $recipient->first_name;
    } else if (isset($recipient->username) && $recipient->username !== '') {
        $greeting .= ' ' . $recipient->username;
    }
@endphp

<div style="font-size: 16px; margin-bottom: 20px;">
    <strong>{{ $greeting }}</strong>
    {!! $emailConfig->body !!}
</div>

<a
    href="#"
    style="
    display: inline-block;
    padding: 10px 16px;
    text-decoration: none;
    font-size: 18px;
    background: {{ $branding->primary_color }};
    text-align: center;
    border-radius: 5px;
    border: 1px solid {{ \App\Classes\Helper::darkenColor($branding->primary_color) }};
    color: {{ \App\Classes\Helper::getContrastColor($branding->primary_color) }};
    margin-bottom: 20px;"
>
    Join League
</a>
@endsection