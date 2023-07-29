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

    <a
        href="{{ url('') }}/password/reset/{{ $passwordResetToken }}"
        style="
        display: block;
        width: 280px;
        height: 18px;
        background: {{ $branding->primary_color }};
        padding: 10px;
        text-align: center;
        border-radius: 5px;
        border: 1px solid {{ \App\Classes\Helper::darkenColor($branding->primary_color) }};
        color: {{ \App\Classes\Helper::getContrastColor($branding->primary_color) }};
        font-weight: bold;
        text-decoration: none;
    ">
        Click here to reset your password
    </a>

</div>
@endsection