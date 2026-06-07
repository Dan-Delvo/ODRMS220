@php
    $emailTitle = 'Email Verification Code';
    $eyebrow = 'Security Notice';
    $accent = '#4f46e5';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Use the verification code below to confirm your email address:</p>
    <div style="margin:24px 0; padding:20px; background:#eef2ff; border:1px solid #c7d2fe; border-radius:10px; color:#312e81; font-size:30px; font-weight:700; letter-spacing:8px; text-align:center;">
        {{ $name }}
    </div>
    <p style="margin:0 0 12px;"><strong>Never share this code.</strong> ODRMS will only ask for it inside the official web application.</p>
    <p style="margin:0;">If you did not request this verification, you can safely ignore this email.</p>
@endsection
