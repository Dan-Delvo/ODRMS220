@php
    $emailTitle = 'Temporary Login Details';
    $eyebrow = 'Account Created';
    $accent = '#2563eb';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $name }}</strong>,</p>
    <p style="margin:0 0 16px;">A temporary password was created for your account. Use these details to sign in, then change your password immediately.</p>
    <div style="margin:22px 0; padding:16px 18px; background:#eff6ff; border-left:4px solid {{ $accent }}; border-radius:8px;">
        <p style="margin:0 0 8px;"><strong>Email address:</strong> {{ $email }}</p>
        <p style="margin:0;"><strong>Temporary password:</strong> {{ $tempPassword }}</p>
    </div>
    <p style="margin:0 0 12px;"><strong>For your security:</strong></p>
    <ul style="margin:0; padding-left:20px;">
        <li>Sign in as soon as possible.</li>
        <li>Change the temporary password immediately.</li>
        <li>Do not share your login details.</li>
    </ul>
@endsection
