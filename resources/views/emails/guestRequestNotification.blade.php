@php
    $emailTitle = 'Document Request Submitted';
    $eyebrow = 'Guest Request';
    $accent = '#0f766e';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $requestorName }}</strong>,</p>
    <p style="margin:0 0 16px;">This confirms that we received the document request you submitted for <strong>{{ $studentName }}</strong>.</p>
    <div style="margin:22px 0; padding:16px 18px; background:#f0fdfa; border-left:4px solid {{ $accent }}; border-radius:8px;">
        <strong>Student:</strong> {{ $studentName }}<br>
        <strong>Requested by:</strong> {{ $requestorName }}<br>
        <strong>Submitted:</strong> {{ now()->format('F d, Y \a\t h:i A') }}
    </div>
    <p style="margin:0 0 16px;">This is a guest request and no online account was created. You will receive email updates as the request progresses.</p>
    <p style="margin:0;">Please bring a valid ID when claiming the document.</p>
@endsection
