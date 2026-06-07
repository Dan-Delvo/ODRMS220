@php
    $emailTitle = 'Request Submitted';
    $eyebrow = 'Request Update';
    $accent = '#0f766e';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $name }}</strong>,</p>
    <p style="margin:0 0 16px;">Your document request has been received and added to our records.</p>
    <div style="margin:22px 0; padding:16px 18px; background:#f0fdfa; border-left:4px solid {{ $accent }}; border-radius:8px;">
        The registrar will review your request and notify you as its status changes.
    </div>
    <p style="margin:0;">No action is required from you at this time.</p>
@endsection
