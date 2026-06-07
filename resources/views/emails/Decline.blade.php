@php
    $emailTitle = 'Document Request Declined';
    $eyebrow = 'Request Update';
    $accent = '#dc2626';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $name }}</strong>,</p>
    <p style="margin:0 0 16px;">Your document request was declined. The registrar provided the following reason:</p>
    <div style="margin:22px 0; padding:16px 18px; background:#fef2f2; border-left:4px solid {{ $accent }}; border-radius:8px; color:#991b1b;">
        <strong>{{ $reason }}</strong>
    </div>
    <p style="margin:0;">Please contact the registrar's office if you need clarification or assistance.</p>
@endsection
