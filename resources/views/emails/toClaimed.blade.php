@php
    $emailTitle = 'Document Successfully Claimed';
    $eyebrow = 'Request Completed';
    $accent = '#15803d';
@endphp

@extends('emails.layout')

@section('content')
    <p style="margin:0 0 16px;">Hello <strong>{{ $name }}</strong>,</p>
    <p style="margin:0 0 16px;">This confirms that your requested document has been successfully claimed.</p>
    <div style="margin:22px 0; padding:16px 18px; background:#f0fdf4; border-left:4px solid {{ $accent }}; border-radius:8px;">
        Your request is now complete.
    </div>
    <p style="margin:0;">Thank you for using the Online Document Request Management System.</p>
@endsection
