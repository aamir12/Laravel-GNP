@extends('emails.master', ['branding' => $branding])
@section('content')

@php
    $greeting = 'Hi';

    if (isset($user->first_name) && $user->first_name !== '') {
        $greeting .= ' ' . $user->first_name;
    } else if (isset($user->username) && $user->username !== '') {
        $greeting .= ' ' . $user->username;
    }
@endphp

<div style="font-size: 16px; margin-bottom: 20px;">
    <strong>{{ $greeting }}</strong>
    {!! $emailConfig->body !!}
</div>

<a
    href="{{ url('') }}/email/{{ $user->email }}"
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
    Confirm your email address
</a>
@endsection
