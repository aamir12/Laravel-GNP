@extends('emails.master', ['branding' => $branding])
@section('content')

<p>A user  has requested to withdraw funds to their paypal account.</p>
<ul>
    <li><strong>Earnie Email -</strong> {{ $user->email }}</li>
    <li><strong>Username -</strong> {{ $user->username }}</li>
    <li><strong>Paypal Email -</strong> {{ $user->paypal_email }}</li>
    <li><strong>Amount -</strong> £{{ number_format($amount, 2) }}</li>
</ul>
@endsection