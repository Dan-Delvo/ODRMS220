@php
    $emailTitle = 'Your Document Is Being Processed';
    $eyebrow = 'Request Update';
    $accent = '#b7791f';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $name }}</strong>,</p>
    <p style="margin:0 0 16px;">The registrar is now processing your requested document.</p>
    <div style="margin:22px 0; padding:16px 18px; background:#fffbeb; border-left:4px solid {{ $accent }}; border-radius:8px;">
        You will receive another email when the document is ready for release.
    </div>
    <p style="margin:0;">No action is required from you at this time.</p>
@endsection
