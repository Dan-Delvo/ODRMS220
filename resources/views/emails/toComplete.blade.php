@php
    $emailTitle = 'Document Ready for Release';
    $eyebrow = 'Request Update';
    $accent = '#2563eb';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $name }}</strong>,</p>
    <p style="margin:0 0 16px;">Your requested document is ready for release.</p>
    <div style="margin:22px 0; padding:16px 18px; background:#eff6ff; border-left:4px solid {{ $accent }}; border-radius:8px;">
        Please bring the required identification or proof of request when claiming your document.
    </div>
    <p style="margin:0;">Contact the registrar's office if you need assistance before claiming.</p>
@endsection
