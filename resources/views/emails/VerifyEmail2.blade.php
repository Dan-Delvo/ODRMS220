@php
    $emailTitle = 'Verify Your Account Update';
    $eyebrow = 'Security Notice';
    $accent = '#4f46e5';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $student->account->username }}</strong>,</p>
    <p style="margin:0 0 16px;">A request was made to update your account details. To confirm the request, open the verification address below:</p>
    <div style="margin:22px 0; padding:16px 18px; background:#f8fafc; border:1px solid #dbe4ee; border-radius:8px; word-break:break-all;">
        <a href="{{ $verifyUrl }}" style="color:#3730a3; text-decoration:underline;">{{ $verifyUrl }}</a>
    </div>
    <p style="margin:0;">If you did not request this update, do not open the link and contact the registrar's office.</p>
@endsection
