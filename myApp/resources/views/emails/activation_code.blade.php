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

<a href="{{ env('ACTIVATION_CODE_LINK') }}{{ $user->activation_code }}">Activate your account now</a>
@endsection