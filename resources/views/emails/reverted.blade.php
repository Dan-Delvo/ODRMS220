@php
    $emailTitle = "Request Reverted to {$targetStatus}";
    $eyebrow = 'Important Request Update';
    $accent = $targetStatus === 'Processing' ? '#b7791f' : '#2563eb';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $name }}</strong>,</p>
    <p style="margin:0 0 16px;">
        Your document request has been moved back to <strong>{{ $targetStatus }}</strong>.
    </p>
    <div style="margin:22px 0; padding:16px 18px; background:#f8fafc; border-left:4px solid {{ $accent }}; border-radius:8px;">
        <strong>Request number:</strong> {{ $requestNumber ?: 'Not available' }}<br>
        <strong>Current status:</strong> {{ $targetStatus }}
    </div>
    <p style="margin:0 0 10px;"><strong>Reason provided by the registrar:</strong></p>
    <div style="margin:0 0 22px; padding:16px 18px; background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; color:#9a3412;">
        {{ $reason }}
    </div>
    <p style="margin:0;">You will receive another notification when the request status changes again.</p>
@endsection
