@php
    $emailTitle = 'Password Change Verification';
    $eyebrow = 'Security Notice';
    $accent = '#0f766e';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $studentName }}</strong>,</p>
    <p style="margin:0 0 16px;">Use the verification code below to continue changing your password:</p>
    <div style="margin:24px 0; padding:20px; background:#f0fdfa; border:1px solid #99f6e4; border-radius:10px; color:#115e59; font-size:30px; font-weight:700; letter-spacing:8px; text-align:center;">
        {{ $otp }}
        <div style="margin-top:10px; color:#64748b; font-size:12px; font-weight:400; letter-spacing:0;">
            Expires in {{ $expiresIn }}
        </div>
    </div>
    <p style="margin:0 0 12px;"><strong>Never share this code.</strong> Our team will never ask you to send it by email, text, or social media.</p>
    <p style="margin:0;">If you did not request this change, ignore this email and contact the registrar's office.</p>
@endsection
